<?php

namespace api;

use Domain\Services\Auth;
use Library\Controller;

class AuthController extends Controller{

    public function login()
    {
        $email = $this->getPost('email');
        $password = $this->getPost('password');

        if (!$email || !$password) {
            $this->json(array('error' => 1));
            return;
        }

        if (!$user = Auth::authenticate('email', array(
            'email' => $email,
            'password' => $password
        ))) {
            $this->json(array('error' => 1));
            return;
        }

        $this->json(array(
            'user' => $user->get(),
            'totalCount' => 1,
        ));
        return;
    }
}