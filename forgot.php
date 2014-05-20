<?php
include_once 'functions/forgot.inc.php';
include_once 'functions/functions.php';
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Forgot password</title>
        <script type="text/JavaScript" src="functions/js/sha512.js"></script> 
        <script type="text/JavaScript" src="functions/js/forms.js"></script>
        <link rel="stylesheet" href="web/styles/main.css" />
    </head>
    <body>

        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }?>
        
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="forgot_form">
            
            Your email: <input type="text" name="email" id="email" /><br /><br />
            
            <input type="button" value="Send mail" onclick="return forgothash(this.form,
                                                                    this.form.email);" />
        </form>

    </body>
</html>