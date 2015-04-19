<?php

namespace api;

use Domain\EntityCollection;
use Domain\Repository\Users;
use Domain\Services\Auth;
use Library\Controller;

class UsersController extends Controller{

    public function index()
    {
        if (!Auth::isAdmin()) {
            $this->json(array('error' => 1));
            return;
        }

        /** @var $users EntityCollection */
        $users = Users::get()->retrieveAll();

        $this->json(array(
            'users' => $users->getAttrs(true, false),
            'totalCount' => count($users),
        ));
    }
}