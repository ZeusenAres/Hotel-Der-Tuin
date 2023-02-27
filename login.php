<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
$credentials->setTable('medewerkers');
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    /*if($credentials->login($_POST['username'], $_POST['password']))
    {
        $_SESSION['user'] = $_POST['username'];
    }*/
    try
    {
        $user = $credentials->login($_POST['username'], $_POST['password']);
        $_SESSION['username'] = $user;
        header('Location:home.php');
    }
    catch(\Exception $e)
    {
        echo $e->getMessage();
    }
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
            <input type="password" placeholder="password" name="password">
            <input type="submit" value="login" name="submit">
        </form>
    </body>
</html>