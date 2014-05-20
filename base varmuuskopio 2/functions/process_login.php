<?php

include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start(); // our custom secure way of starting the sesh, yo

if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // hashed password
 
    if (login($email, $password, $dbrobots) == true) {
        // login success 
        //header('Location: ../protected_page.php');
        header('Location: ../index.php');
    } else {
        // login failed 
        header('Location: ../index.php?error=1');
    }
} else {
    // the correct POST variables were not sent to this page
    echo 'Invalid Request';
}