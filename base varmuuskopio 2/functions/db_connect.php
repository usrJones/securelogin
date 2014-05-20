<?php


$conf = array (
    'host'      => 'localhost',
    'dbname'    => 'secure_login',
    'username'  => 'root',
    'password'  => ''
    );

try {
    //$dbrobots = new PDO("mysql:host=localhost;dbname=secure_login", "root", "");
    $dbrobots = new PDO('mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'] , $conf['username'] , $conf['password']);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$dbrobots->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);