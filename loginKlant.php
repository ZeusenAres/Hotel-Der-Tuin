<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
$credentials->setTable('klanten');
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        $user = $credentials->login($_POST['email'], $_POST['password']);
        $userIds = explode('/', $user);
        $_SESSION['username'] = $userIds[0];
        $_SESSION['custMail'] = $userIds[1];
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
    <?php echo $credentials->navbar();?>
    <form action="loginKlant.php" method="post">
        <input type="text" placeholder="email" name="email" />
        <input type="password" placeholder="password" name="password" />
        <input type="submit" value="login" name="submit" />
    </form>
</body>
</html>