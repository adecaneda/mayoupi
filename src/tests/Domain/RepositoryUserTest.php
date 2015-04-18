<?php

namespace tests\Domain;

use Domain\Entity\User;
use Domain\Repository\Users;
use Library\Application;

class RepositoryUserTest extends \PHPUnit_Framework_TestCase{

    public function __construct()
    {
        require_once 'src/tests/index.php';
    }

    public function testRetrieveUserOnId()
    {
        $user = Users::get()->retrieve(1);
        $this->assertEquals(1, $user->get('id'));
    }

    public function testRetrieveUserOnEmail()
    {
        $email = 'alejandro@mail.com';
        $user = Users::get()->retrieve($email, 'email');
        $this->assertEquals($email, $user->get('email'));
    }

    public function testRetrieveUserOnGooglePlusId()
    {
        $this->markTestSkipped();

        $googleId = 'asdfasdf';
        $user = Users::get()->retrieve($googleId, 'google_id');
        $this->assertEquals($googleId, $user->get('google_id'));
    }

    public function testRetrieveUserOnInstagramId()
    {
        $this->markTestSkipped();

        $instagramId = 'asdfasdf';
        $user = Users::get()->retrieve($instagramId, 'instagram_id');
        $this->assertEquals($instagramId, $user->get('instagram_id'));
    }

    public function testInsertUser()
    {
        $name = 'Kevin Johansen';
        $email = 'kevin.johansen@mail.com';

        // remove if it exists
        if ($user = Users::get()->retrieve($email, 'email')) {
            Users::get()->remove($user);
        }

        $user = new User(array(
            'name' => $name,
            'email' => $email
        ));

        // store it
        $user = Users::get()->persist($user);
        $id = $user->get('id');
        $this->assertNotEmpty($id);

//        // remove it
//        Users::get()->remove($user);
//
//        // try to retrieve it
//        $user = Users::get()->retrieve($id);
//
//        $this->assertEmpty($user);
    }
} 