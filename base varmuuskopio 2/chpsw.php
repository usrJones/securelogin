<?php 
include_once 'functions/db_connect.php';
include_once 'functions/chpsw.inc.php';
include_once 'functions/functions.php';

sec_session_start(); ?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Change password</title>
        <script type="text/JavaScript" src="functions/js/sha512.js"></script> 
        <script type="text/JavaScript" src="functions/js/forms.js"></script>
        <link rel="stylesheet" href="web/styles/main.css" />
    </head>
    <body>
        <?php if (login_check($dbrobots) == true) : ?>

        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }?>
        
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="chpsw_form">
            <input type="hidden" name="username" value="<?php echo htmlentities($_SESSION['username']); ?>" id="username" />
            <input type="hidden" name="email" value="<?php echo htmlentities($_SESSION['email']); ?>" id="email" />
            
            Old password: <input type="password" name="oldpassword" id="oldpassword" /><br>
            New password: <input type="password" name="newpassword" id="newpassword" /><br>
            Passwd again: <input type="password" name="confpassword" id="confpassword" /><br><br>
            
            
            <input type="button" value="Change password" onclick="return chpswhash(this.form,
                                                                    this.form.email,
                                                                    this.form.oldpassword,
                                                                    this.form.newpassword,
                                                                    this.form.confpassword);" /> 
        </form>
        
        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>
    </body>
</html>