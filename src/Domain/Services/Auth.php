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
            return self::authenticateByEmail($params['email'], $params['password']);
        }
        return null;
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
} 