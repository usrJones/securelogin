<?php

include_once 'psl-config.php';

// call this funkkari at the top of any page you wish to access
// also do this sometime http://www.wikihow.com/Create-a-Secure-Session-Managment-System-in-PHP-and-MySQL
// prevents xss, no access to session id cookie
// in production environment use https -> $secure = true;

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
    session_set_cookie_params($cookieParams["lifetime"],
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

function login($email, $password, $mysqli) {
    // prevents SQL injection
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
        WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // bind $email to parameter
        $stmt->execute();
        $stmt->store_result();
 
        // get variables from result
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // if user exists, check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                
                return false; // account locked, return false
                
            } else {
                // check passwd
                if ($db_password == $password) {
                    // correct
                    // get the user-agent string of the user
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    
                    return true; // login successful
                } else {
                    // password not correct
                    // we record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
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

function checkbrute($user_id, $mysqli) {

    $now = time();
 
    // all login attempts are counted from the past 2 hours
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts <code><pre>
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        $stmt->execute();
        $stmt->store_result();
 
        // if more than 5 failed logins
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}


// check login status with session variables
// check browser info with password, because unlikely that the user will change browsers mid-session
// prevents session hijacking

function login_check($mysqli) {
    // check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // get users user-agent string
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM members 
                                      WHERE id = ? LIMIT 1")) {
            // bind "$user_id" to parameter
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // if user exists get variables from result
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
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


