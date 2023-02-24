<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
$credentials->setTable('klanten');
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    /*if($credentials->login($_POST['username'], $_POST['password']))
    {
    $_SESSION['user'] = $_POST['username'];
    }*/
    try
    {
        $user = $credentials->login($_POST['email'], $_POST['password']);
        $_SESSION['user'] = $user;
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
    <?php echo $_SESSION['user'] ?>
    <form action="loginKlant.php" method="post">
        <input type="text" placeholder="email" name="email" />
        <input type="text" placeholder="password" name="password" />
        <input type="submit" value="login" name="submit" />
    </form>
</body>
</html>