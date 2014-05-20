<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
 
$error_msg = "";
 
if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
    // sanitize and validate the data passed in
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $emailsel = '';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg .= '<p class="error">The email address you entered is not valid.</p>';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {        // the hashed pwd should be 128 characters long
        $error_msg .= '<p class="error">Invalid password configuration. This is relatively rare, please try again.</p>';
    }
  
    $emailsel_stmt = $dbrobots->prepare("SELECT id FROM members WHERE email = ? LIMIT 1");
    
    if ($emailsel_stmt) {
        $emailsel_stmt->execute(array($email));
        
        while ($row = $emailsel_stmt->fetch()) {
            $emailsel = htmlentities($row['id']);
        }
 
        if ($emailsel) {
            $error_msg .= '<p class="error">A user with this email address already exists.</p>';
        }
    } else {
        $error_msg .= '<p class="error">Database error</p>';
    }
 
    if (empty($error_msg)) {
        // create salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

        // create salted password
        $password = hash('sha512', $password . $random_salt);
 
        if ($insert_stmt = $dbrobots->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
            if ( ! $insert_stmt->execute(array($username, $email, $password, $random_salt))) {
                header('Location: ../error.php?err=Registration failure: INSERT');
            }
        }
        
        header('Location: ./register_success.php');
    }
}