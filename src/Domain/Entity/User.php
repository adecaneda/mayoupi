<?php

namespace Domain\Entity;

use Domain\Repository\Images;

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