<?php

namespace api;

use Domain\Entity\User;
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
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        if (!$user = Auth::authenticate('token', $token)) {
            $this->json(array('error' => 2));
            return;
        }

        $this->json(array(
            'user' => $user->getWithRelations(),
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

    /**
     *
     */
    public function googleplus()
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        if (!$token) {
            $this->json(array());
            return;
        }

        $name = $this->getPost('name');
        $email = $this->getPost('email');
        $googleId = $this->getPost('google_id');

        // existing user..
        $attrs = array(
            'name' => $name,
            'email' => $email,
            'google_id' => $googleId,
            'token' => $token
        );

        // existing user
        if (!$user = Users::get()->retrieve($email, 'email')) {
            if (!$user = Users::get()->retrieve($googleId, 'google_id')) {
                // ..or new user
                $user = new User(array('role' => 'user'));
            }
        }

        // set new token
        $user->setAttrs($attrs);

        // update last sign-in date
        $user->updateLastSignIn();

        Users::get()->persist($user);

        $this->json(array(
            'user' => $user->get(),
            'token' => $user->get('token')));
    }
}