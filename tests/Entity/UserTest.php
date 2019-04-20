<?php


use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testSetPhone()
    {
        $user = new User();

        $user->setPhone('55555');

        $this->assertSame('55555', $user->getPhone());
    }

    public function testSetLastName()
    {
        $user = new User();

        $user->setLastName('Doe');

        $this->assertSame('Doe', $user->getLastName());
    }

    public function testSetEmail()
    {
        $user = new User();

        $user->setEmail('test@gmail.com');

        $this->assertSame('test@gmail.com', $user->getEmail());
    }

    public function testSetPassword()
    {
        $user = new User();

        $user->setPassword('password');

        $this->assertSame('password', $user->getPassword());
    }

    public function testSetFirstName()
    {
        $user = new User();

        $user->setFirstName('John');

        $this->assertSame('John', $user->getFirstName());
    }

}
