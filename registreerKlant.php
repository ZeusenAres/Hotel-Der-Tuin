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
        $user = $credentials->register($_POST['username'], $_POST['email'], $_POST['password'], $_POST['tel'], $_POST['adres'], $_POST['postcode']);
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
    <form action="registreerKlant.php" method="post">
        <input type="text" placeholder="username" name="username" />
        <input type="text" placeholder="password" name="password" />
        <input type="text" placeholder="tel." name="tel" maxlength="10" />
        <input type="text" placeholder="email" name="email" />
        <input type="text" placeholder="adres" name="adres" />
        <input type="text" placeholder="postcode" name="postcode" />
        <input type="submit" value="login" name="submit" />
    </form>
</body>
</html>