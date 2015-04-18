<?php

namespace Domain\Repository;

use Domain\IRepository;

class Users extends Base implements IRepository {

    public function __construct()
    {
        $this->field = 'id';
        $this->table = 'users';
        $this->entity = 'User';
    }

}