<?php

namespace Domain\Repository;

use Domain\IRepository;

class Images extends Base implements IRepository {

    public function __construct()
    {
        $this->field = 'id';
        $this->table = 'images';
        $this->entity = 'Image';
    }
}