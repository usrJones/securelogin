<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
 
$error_msg = "";
 
    if (isset($_POST['username'], $_POST['email'], $_POST['op'], $_POST['p'])) {
        
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        $dboldpass = '';
        $oldpass = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
        $newpassword = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
        if (strlen($newpassword) != 128) {
            $error_msg .= '<p class="error">Invalid password configuration. This is relatively rare, please try again.</p>';
        }

        $selects_stmt = $dbrobots->prepare("SELECT salt FROM members WHERE username=?");
        $selects_stmt->execute(array($username));

        while ($rivi = $selects_stmt->fetch()) {
            $dbolds = htmlspecialchars($rivi['salt']);
        }
        
        $oldpassword = hash('sha512', $oldpass . $dbolds);
        
        $selectp_stmt = $dbrobots->prepare("SELECT password FROM members WHERE password=?");
        $selectp_stmt->execute(array($oldpassword));

        while ($rivi = $selectp_stmt->fetch()) {
            $dboldpass = htmlspecialchars($rivi['password']);
        }

        if ($oldpassword != $dboldpass) {
            $error_msg .= '<p class="error">Syötä vanha salasanasi uudestaan</p>';
        } else {

            if (empty($error_msg)) {
                // create salt
                $newrandom_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

                // create new password
                $newpassword = hash('sha512', $newpassword . $newrandom_salt);

                if ($update_stmt = $dbrobots->prepare("UPDATE members SET password=?, salt=? WHERE username=? AND email=?")) {
                    if ( ! $update_stmt->execute(array($newpassword, $newrandom_salt, $username, $email))) {
                        header('Location: ../error.php?err=Password change failure: UPDATE');
                    }
                }

            header('Location: ./chpsw_success.php');
            }

        }
    }