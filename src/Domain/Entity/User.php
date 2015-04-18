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
            // pre-load raw data
            if ($this->hasAttr('_avatar') && $this->get('_avatar')) {
                $this->avatar = new Image($this->get('_avatar'));

            // retrieve fresh from repository
            } else {
                $this->avatar = Images::get()->retrieve($this->get('id_image'));
            }
        }
        return $this->avatar;
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

        // avatar attrs
        $attrs['_avatar'] = $this->avatar()->get();

        return $attrs;
    }
} 