<?php

namespace tests\Domain;

use Domain\Entity\User;
use Domain\Repository\Users;

class EntityBaseTest  extends \PHPUnit_Framework_TestCase {

    public function testConstructorSetAttributes()
    {
        $attrs = array('name' => 'Alejandro');
        $user = new User($attrs);
        $this->assertSame('Alejandro', $user->get('name'));
    }

    public function testGetMissingAttributeThrowsException()
    {
        $attrs = array('name' => 'Alejandro');
        $user = new User($attrs);

        $this->setExpectedException('Exception');
        $user->get('asdf');
    }

    public function testHasAttribute()
    {
        $method = $this->getPrivateMethod('User', 'hasAttr');

        $user = new User(array('name' => 'Alejandro'));
        $this->assertTrue($method->invoke($user, 'name'));
    }

    public function testDoesNotHaveAttribute()
    {
        $method = $this->getPrivateMethod('User', 'hasAttr');

        $user = new User(array('name' => 'Alejandro'));
        $this->assertNotTrue($method->invoke($user, 'surname'));
    }

    public function testSetAttribute()
    {
        $method = $this->getPrivateMethod('User', 'set');

        $user = new User(array('name' => 'Alejandro'));
        $method->invoke($user, 'name', 'Antonio');
        $this->assertEquals('Antonio', $user->get('name'));
    }

    protected function getPrivateMethod($entity, $method)
    {
        $method = new \ReflectionMethod("Domain\\Entity\\$entity", $method);
        $method->setAccessible(true);
        return $method;
    }
}