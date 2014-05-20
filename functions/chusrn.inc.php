<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
include_once 'functions.php';
 
    if (isset($_POST['email'], $_POST['newname'])) {
        
        $username = filter_input(INPUT_POST, 'newname', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($update_stmt = $dbrobots->prepare("UPDATE members SET username=? WHERE email=?")) {
            if ( ! $update_stmt->execute(array($username, $email))) {
                header('Location: error.php?err=Username change failure: UPDATE');
            }
        }
        
        
        // TODO: check if the username already exists
        
        
        
        sec_session_start();
        
        if (isset($_SESSION['username'])){
            $_SESSION['username'] = $username;
        } else {
            header('Location: error.php?err=Username change failure: lolwut');
        }

        header('Location: ./usrinfo.php');
    }