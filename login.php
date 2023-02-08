<?php
require_once('UserCredentialsController.php');
$credentials = new UserCredentialsController();
if(isset($_POST['submit']))
{
    $credentials->login($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Log in</title>
    </head>
    <body>
        <form action="login.php" method="post">
            <input type="text" placeholder="username" name="username">
            <input type="text" placeholder="password" name="password">
            <input type="submit" value="login" name="submit">
        </form>
    </body>
</html>