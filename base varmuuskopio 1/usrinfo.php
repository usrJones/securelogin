<?php
include_once 'functions/functions.php';
session_regenerate_id();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: User info</title>
        <link rel="stylesheet" href="web/styles/main.css" />
    </head>
    <body>
        
        <?php include 'login.php';?>
        
        <?php if (login_check($dbrobots) == true) : ?>

            <br/><br/><p>ID: <?php echo htmlentities($_SESSION['user_id']); ?></p><br/>
            <p>Name: <?php echo htmlentities($_SESSION['username']); ?></p><br />
            <p>Email: <?php echo htmlentities($_SESSION['email']); ?></p><br />
            <p><a href="chpsw">Change password</a></p><br />
            <p><a href="chusrn">Change username</a></p>
            <?php // print_r($_SESSION); ?>
        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>
    </body>
</html>