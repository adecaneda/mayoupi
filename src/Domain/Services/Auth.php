<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 19.04.15
 * Time: 17:53
 */

namespace Domain\Services;

use Domain\Entity\User;
use Domain\Repository\Users;

class Auth {

    protected function __construct() { }

    /**
     * @param $strategy
     * @param $params mixed
     *
     * @return User
     */
    static public function authenticate($strategy, $params = null)
    {
        if ($strategy === 'email') {
            $user = self::authenticateWithEmail($params['email'], $params['password']);

        } else if ($strategy === 'header') {
            $headers = getallheaders();
            $user = isset($headers['Authorization'])
                ? self::authenticate('token', $headers['Authorization'])
                : null;

        } else if ($strategy === 'token') {
            $user = self::authenticateWithToken($params);


        } else {
            $user = null;
        }

        if (!$user || ($_SESSION['token'] && $user->get('token') !== $_SESSION['token'])) {
            return null;
        }

        self::storeInSession($user);

        return $user;
    }

    /**
     */
    static public function unauthenticate()
    {
        self::storeInSession();
    }

    static public function register($params)
    {
        // if a user is authenticated, fail
        if (self::authenticated()) {
            return null;
        }

        // if a user with the same email exists, fail
        if ($oldUser = Users::get()->retrieve($params['email'], 'email')) {
            return null;
        }

        //@todo google plus id, instagram id
        $user = new User($params);


        $user->updateLastSignIn();
        $user->defineRole('user');

        // save in the database
        Users::get()->persist($user);

        // store in session
        self::storeInSession($user);

        return $user;
    }

    /**
     * Returns the current logged in user.
     *
     * @return User
     */
    static public function authenticated()
    {
        if (isset($_SESSION['token'])) {
            return Users::get()->retrieve($_SESSION['token'], 'token');
        }
        return null;
    }

    static public function isAdmin()
    {
        if ($user = self::authenticated()) {
            if ($user->get('role') === 'admin') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $email
     * @param $password
     * @return User
     */
    static protected function authenticateWithEmail($email, $password)
    {
        /** @var $user User */
        if (!$user = Users::get()->retrieve($email, 'email')) {
            return null;
        }

        if (!$user->checkPassword($password)) {
            return null;
        }

        return $user;
    }

    /**
    /**
     * @param $token
     * @return User
     */
    static protected function authenticateWithToken($token)
    {
        /** @var $user User */
        if (!$user = Users::get()->retrieve($token, 'token')) {
            return null;
        }

        return $user;
    }

    /**
     * @param null $user
     */
    static protected function storeInSession($user = null)
    {
        if ($user) {
            $_SESSION['token'] = $user->get('token');
        } else {
            $_SESSION['token'] = null;
        }

    }
} 