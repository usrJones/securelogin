<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
include_once 'functions.php';
 
    if (isset($_POST['email'])) {
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        $now = time();
        
        // check if the email is in database
        
        $select_stmt = $dbrobots->prepare("SELECT id FROM members WHERE email=?");
        $select_stmt->execute(array($email));
        
        while ($rivi = $select_stmt->fetch()) {
            $id = htmlspecialchars($rivi['id']);
        }
        
        if (strlen($id) > 0) {
        
            // construct a hash link (timestamp, email)
            // insert it to database
            $token = hash('sha512', $email . $now);

            $update_stmt = $dbrobots->prepare("UPDATE members SET token=? WHERE email=?");
            $update_stmt->execute(array($token, $email));

            // send the link to above email
            $message="You activation link is: http://localhost/secureLogin/forgot.php?email='$email'&code='$token";
            //Warning: mail(): Failed to connect to mailserver at &quot;localhost&quot; port 25, verify your &quot;SMTP&quot; and &quot;smtp_port&quot; setting in php.ini or use ini_set() in C:\dev\wamp\www\secureLogin\functions\forgot.inc.php on line 34

            mail($email, 'Forgotten password', $message);

            // even if not valid, redirect to successlessness
        
        } else {
            
        }
    }
    
    /*Generate a token (maybe hash a timestamp with a salt) and store it into the database in the user's record.
Send an email to the user along with a link to your http*s* reset page (token and email address in the url).
Use the token and email address to validate the user.
Let them choose a new password, replacing the old one.
Additionally, it's a good idea to expire those tokens after a certain time frame, usually 24 hours.
Optionally, record how many "forgot" attempts have happened, and perhaps implement more complex functionality if people are requesting a ton of emails.
Optionally, record (in a separate table) the IP address of the individual requesting the reset. Increment a count from that IP. If it ever reaches more than, say, 10... Ignore their future requests.