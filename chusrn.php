<?php 
include_once 'functions/db_connect.php';
include_once 'functions/chusrn.inc.php';
include_once 'functions/functions.php';

sec_session_start(); ?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Change username</title>
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
        
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="chusrn_form">
            <input type="hidden" name="email" value="<?php echo htmlentities($_SESSION['email']); ?>" id="email" />
            
            
            Chang naem: <input type="text" name="newname" id="newname" /><br /><br />
            
            <input type="button" value="Change username" onclick="return chusrnhash(this.form,
                                                                    this.form.email,
                                                                    this.form.newname);" />
        </form>
        
        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>
    </body>
</html>