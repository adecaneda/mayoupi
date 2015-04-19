<?php

namespace api;

use Domain\EntityCollection;
use Domain\Repository\Users;
use Library\Controller;

class UsersController extends Controller{

    public function index()
    {
        /** @var $users EntityCollection */
        $users = Users::get()->retrieveAll();

        $this->json(array(
            'users' => $users->getAttrs(true, false),
            'totalCount' => count($users),
        ));
    }
} 