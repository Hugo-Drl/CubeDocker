<?php

namespace Phpunit;

use App\Controllers\User;
use PHPUnit\Framework\TestCase;
use App\Utility\Hash;
use App\Model\UserRegister;





class LoginTest extends TestCase
{
    public function test_register(){
        $salt = Hash::generateSalt(32);

        $data = array(
            'email' => 'test@test.fr',
            'username' => 'test',
            'password' => Hash::generate('test', $salt),
            'salt' => $salt
        );
        $router = new User(null);
        $this -> assertIsString($router -> registerExec($data));
    }

    public function test_login(){

        $data = array(
            'email' => 'test@test.fr',
            'password' => 'test'
        );
        $router = new User(null);
        $this -> assertTrue($router -> loginExec($data));
    }

    
}