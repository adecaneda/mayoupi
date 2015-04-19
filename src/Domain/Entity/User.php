<?php

namespace Domain\Entity;

use Domain\Repository\Images;

class User extends Base {

    /**
     * @var Image
     */
    protected $avatar;

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
        $attrs = parent::get();

        // avatar attrs
        $avatar = $this->avatar();
        $attrs['_avatar'] = $avatar ? $avatar->getWithRelations() : null;

        return $attrs;

    }
}