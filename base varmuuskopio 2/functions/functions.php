<?php

include_once 'psl-config.php';

// call this funkkari at the top of any page you wish to access
// also do this sometime http://www.wikihow.com/Create-a-Secure-Session-Managment-System-in-PHP-and-MySQL
// prevents xss, no access to session id cookie
// in production environment use https -> $secure = true;

// TODO: Session timeout on idle
// TODO: Session destroy on window close
// TODO: Check if login check sees only the generated session id
// TODO: Change email, send prompt to old email before mysql update

function sec_session_start() {
   
    $session_name = 'sec_session_id';   // custom session name
    $secure = SECURE; 
    // stops JavaScript being able to access session id
    $httponly = true;
    // forces sessions use cookies
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // gets current cookies params
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(  $cookieParams["lifetime"],
                                $cookieParams["path"], 
                                $cookieParams["domain"], 
                                $secure,
                                $httponly);
    // sets session name to the one set above
    session_name($session_name);
    session_start();
    session_regenerate_id();    // regenerated the session, delete the old one
    
}

// checks email, passwd against the database, returns true if matches
function login($email, $password, $dbrobots) {
    
    if ($login_stmt = $dbrobots->prepare("SELECT id, username, password, salt FROM members WHERE email=? LIMIT 1")) {
        
        $login_stmt->execute(array($email));
        while ($rivi = $login_stmt->fetch()) {
            $user_id = htmlspecialchars($rivi['id']);
            $username = htmlspecialchars($rivi['username']);
            $db_password = htmlspecialchars($rivi['password']);
            $salt = htmlspecialchars($rivi['salt']);
        }
        $passwdtodbpass = hash('sha512', $password . $salt);
        
        if ($login_stmt) { // check if there is one row
            
            // if user exists, check if the account is locked
            // from too many login attempts 
            if (checkbrute($user_id, $dbrobots) == false) {
                
                return false; // account locked, return false
                
            } else {
                if ($db_password == $passwdtodbpass) {
                    // get the user-agent string of the user
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['username'] = $username;
                    //$_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                    $_SESSION['login_string'] = hash('sha512', $email . $user_browser . $ip_address);
                    // huom. ip:n tarkistus login_stringissä ei ole maailman parhain idea.
                    // käyttäjillä voi olla vaihtuva ip, joka aiheuttaa session kaatumisen.
                    
                    $_SESSION['email'] = $email;
                    //$_SESSION['password'] = $password;
                    //$_SESSION['password'] = '';
                    //$_SESSION['salt'] = $salt;
                    //$_SESSION['salt'] = '';
                    
                    return true; // login successful
                } else {
                    // password not correct
                    // we record this attempt in the database
                    $now = time();
                    $bruteins_stmt = $dbrobots->prepare("INSERT INTO login_attempts(user_id, time) VALUES (?, ?)");
                    $bruteins_stmt->execute(array($user_id, $now));

                    return false;
                }
            }
        } else {
           
            return false; // no such user
        }
    }
}

// checks login tries
// captcha can be added after 5 login tries
function checkbrute($user_id,  PDO $dbrobots) {

    $now = time();
 
    // all login attempts are counted from the past 2 hours
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($brutesel_stmt = $dbrobots->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
        $brutesel_stmt->execute(array($user_id));
                
        $row = $brutesel_stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = sizeof($row);
        
        //var_dump($count);
        //die();
        
        // if more than 5 failed logins
        if ($count < 5) {
            return true;
        } else {
            return false;
        }
    }
}

// check login status with session variables
// check browser info with password, because unlikely that the user will change browsers mid-session
// prevents session hijacking
function login_check($dbrobots) {
    // check if all session variables are set 
    if (isset(  $_SESSION['user_id'], 
                $_SESSION['username'], 
                $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // get users user-agent string
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
 
        //if ($stmt = $mysqli->prepare("SELECT password FROM members WHERE id = ? LIMIT 1")) {
        if ($lcheck_stmt = $dbrobots->prepare("SELECT email FROM members WHERE id=? LIMIT 1")) {
            $lcheck_stmt->execute(array($user_id));
            
            if ($lcheck_stmt) { // check if there is one row
                // if user exists get variables from result
                //$stmt->bind_result($password);
                while ($row = $lcheck_stmt->fetch()) {
                    $email = htmlspecialchars($row['email']);
                }
                //$login_check = hash('sha512', $password . $user_browser);
                $login_check = hash('sha512', $email . $user_browser . $ip_address);
 
                if ($login_check == $login_string) {
                    // logged in
                    return true;
                } else {
                    // not logged in
                    return false;
                }
            } else {
                // not logged in
                return false;
            }
        } else {
            // not logged in
            return false;
        }
    } else {
        // not logged in 
        return false;
    }
}

// sanitizes URL output from PHP_SELF server variable
// snip from WordPress CMS
// prevents XSS
// prevents iframe clickjacking
function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // we're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}