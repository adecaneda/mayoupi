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
     * @param $params
     *
     * @return User
     */
    static public function authenticate($strategy, $params)
    {
        if ($strategy === 'email') {
            $user = self::authenticateByEmail($params['email'], $params['password']);
        } else {
            $user = null;
        }

        self::storeInSession($user);

        // update last login date
        $user->updateLastSignIn();
        Users::get()->persist($user);

        return $user;
    }

    /**
     */
    static public function unauthenticate()
    {
        $_SESSION['user'] = null;
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
        if (isset($_SESSION['user'])) {
            return Users::get()->retrieve($_SESSION['user']);
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
    static protected function authenticateByEmail($email, $password)
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
     * @param null $user
     */
    static protected function storeInSession($user = null)
    {
        if ($user) {
            $_SESSION['user'] = $user->get('id');
        } else {
            $_SESSION['user'] = null;
        }

    }
} 