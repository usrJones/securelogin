<?php

// starts session, destroys it and redirects to China
// TODO: add CSRF protection - http://blog.codinghorror.com/preventing-csrf-and-xsrf-attacks/

include_once 'functions.php';

sec_session_start();
 
// unset all session values 
$_SESSION = array();
 
// get session parameters 
$params = session_get_cookie_params();
 
// delete the actual cookie
setcookie(session_name(),
        '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]);
 
// destroy session 
session_destroy();
header('Location: ../index.php');