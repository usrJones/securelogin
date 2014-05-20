<?php
include_once 'functions/db_connect.php';
include_once 'functions/functions.php';

// login with email
// remember to use HTTPS protocol in production
 
sec_session_start();
 
if (login_check($dbrobots) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Secure Login: Log In</title>
        <link rel="stylesheet" href="web/styles/main.css" />
        <script type="text/JavaScript" src="functions/js/sha512.js"></script> 
        <script type="text/JavaScript" src="functions/js/forms.js"></script> 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div>
            
            <div id="header">
                <div id="hometag"><a href="index.php">|^|</a></div>
                <div id="headerwrapper">
                    <div id="headertop"></div>
                    <div id="headercenter">

                        <p><a>Currently logged <?php echo $logged ?></a>|

                        <?php
                        if (login_check($dbrobots) == true) {
                            echo '<a href="functions/logout.php">log out</a>';
                        } else {
                            echo '<a id="loggaa" href="#">log in</a>|';
                            echo '<a href="register.php">register</a></p>';
                        } ?>    

                    </div>
                    <div id="headerbottom"></div>
                </div>
            </div>
            
            <div id="login">
                <form action="functions/process_login.php" method="post" name="login_form">                      
                    <input type="text" name="email" placeholder="email" id="mail" />
                    <input type="password" autocomplete="off" name="password" placeholder="password" id="password" onkeydown="pressed(event)" />
                    <input type="button" value="Login" id="button" onclick="formhash(this.form, this.form.password);" />
                    <br /><span style="font-size:0.8em;padding:0;margin:0;"><a href="forgot.php" style="color:#535050;">Forgot ya password?</a></span>
                <?php if (isset($_GET['error'])) { echo '<span class="error">Login error!</span>'; }?>

                    <script type="text/javascript">
                        $("#password").keyup(function(event){
                            if(event.keyCode == 13){
                                $("#button").click();
                            }});
                    </script>
                </form>
            </div>
            
        </div>
    </body>
    
    <script type="text/javascript">
        $("#login").hide();
        $( "#loggaa" ).click(function() {
            $( "#login" ).show();
            $( "#mail" ).focus();
        });
        
        if ($(".error").length) {
            $( "#login" ).show();
        };
    </script>
</html>