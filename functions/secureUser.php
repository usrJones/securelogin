<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of secureUser
 *
 * @author kujaljo1
 */

require_once 'db_connect.php';
class secureUser {
    public $id;
    public $username;
    public $email;
    public $password;
    public $salt;
    public $token;
    
    function registerUser($userName, $userPassword) {
        // pdo connect
        
        // tsikidi https://www.google.fi/search?q=php+oop+login+register&oq=php+oop+login+register&aqs=chrome..69i57j0l2j69i64.4512j0j7&sourceid=chrome&es_sm=122&ie=UTF-8
        
        // insert into with this id
        
        // this username = $username
    }
    
    function verifyPassword() {
        
    }
    
    function changePassword($newPassword) {
        
    }
    
    function displayUser()  {
        
        echo $this->id;
        echo $this->username;
        echo $this->email;
        echo $this->password;
        echo $this->salt;
        echo $this->token;
        
    }
}