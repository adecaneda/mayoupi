<?php

namespace api;

use Domain\Repository\Users;
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
            $this->json(array('error' => 2));
            return;
        }

        // update last login date
        $user->updateLastSignIn();
        Users::get()->persist($user);

        $this->json(array(
            'user' => $user->getWithRelations(),
            'token' => $user->get('token')
        ));
    }

    public function me()
    {
        $token = '';
        if (!$user = Auth::authenticate('token', $token)) {
            $this->json(array('error' => 2));
            return;
        }

        $this->json(array(
            'user' => $user->get(),
            'token' => $user->get('token')
        ));

    }

    public function logout()
    {
        Auth::unauthenticate();

        $this->json(array());
    }

    public function loggedIn()
    {
        if (!$user = Auth::authenticated()) {
            $this->json(array());
            return;
        }

        $this->json(array(
            'user' => $user->get(),
            'token' => $user->get('token')
        ));
    }

    public function register()
    {
        $params = array(
            'name' => $this->getPost('name'),
            'email' => $this->getPost('email'),
            'password' => $this->getPost('password'),
            'tac_accepted' => (int)(bool)$this->getPost('tac_accepted'),
        );

        if (!$user = Auth::register($params)) {
            $this->json(array());
            return;
        }

        $this->json(array(
            'user' => $user->get(),
            'token' => $user->get('token')));
    }

}