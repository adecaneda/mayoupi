<?php

namespace Domain\Entity;

use Domain\Repository\Images;
use Domain\Repository\Users;

class User extends Base {

    /**
     * @var Image
     */
    protected $avatar;

    public function __construct($params)
    {
        parent::__construct($params);

        if (!isset($params[$this->getIdentifierField()]) && $params['password']) {
            $this->attrs['password'] = $this->encryptPassword($params['password']);

            $this->attrs['token'] = $this->generateToken();
        }
    }

    protected function generateToken()
    {
        //@todo improve token generation
        return md5(time());
    }

    public function avatar()
    {
        // lazy loading
        if ($this->avatar === null) {
            // retrieve fresh from repository
            $this->avatar = Images::get()->retrieve($this->get('id_image'));
        }
        return $this->avatar;
    }

    /**
     * @param $filename
     */
    public function updateAvatar($filename)
    {
        // change the type to the old 'avatar' to 'old avatar', if any
        if ($oldAvatar = $this->avatar()) {
            $oldAvatar->setAttrs(array('type' => 'old_avatar'));
            Images::get()->persist($oldAvatar);
        }

        // create the new avatar entity
        $newAvatar = new Image(array(
            'id_user' => $this->getId(),
            'url' => $filename,
            'type' => 'avatar'
        ));
        Images::get()->persist($newAvatar);

        // maintain entity consistency
        $this->avatar = $newAvatar;
        $this->attrs['id_image'] = $newAvatar->getId();
        $this->updateLastImageUpload();

        Users::get()->persist($this);
    }

    /**
     * Accept the Terms and Conditions
     */
    public function acceptTerms()
    {
        $this->attrs['tac_accepted'] = 1;
    }

    /**
     * Returns the attributes of the entity and its related sub-entities.
     *
     * @return array
     */
    public function getWithRelations()
    {
        $attrs = $this->get();

        // avatar attrs
        $avatar = $this->avatar();
        $attrs['_avatar'] = $avatar ? $avatar->getWithRelations() : null;

        return $attrs;
    }

    /**
     * Gets the value of an (existing) attribute
     *
     * @param $attr
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($attr = null)
    {
        if ($attr !== null) {
            return parent::get($attr);
        }

        $attrs = parent::get();
        unset($attrs['password']);

        return $attrs;
    }

    /**
     * Updates the last sign in date
     */
    public function updateLastSignIn()
    {
        $now = new \DateTime();
        $this->attrs['last_sign_in'] = $now->format('Y-m-d H:i:s');
    }

    /**
     * Updates the last image upload date
     */
    public function updateLastImageUpload()
    {
        $now = new \DateTime();
        $this->attrs['last_image_upload'] = $now->format('Y-m-d H:i:s');
    }

    /**
     * @param $role
     */
    public function defineRole($role)
    {
        $this->attrs['role'] = $role;
    }

    protected function encryptPassword($password)
    {
        return md5($password);
    }

    /**
     * @param $password
     *
     * @return bool
     */
    public function checkPassword($password)
    {
        $encrypted = $this->encryptPassword($password);
        return !empty($encrypted) && $encrypted === $this->attrs['password'];
    }
}