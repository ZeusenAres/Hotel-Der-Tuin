<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        $credentials->setTable('medewerkers');
        $credentials->register($_POST['username'], $_POST['email'], $_POST['password']);
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
    <form action="register.php" method="post">
        <input type="text" placeholder="username" name="username" />
        <input type="password" placeholder="password" name="password" />
        <input type="email" placeholder="e-mailadres" name="email" />
        <input type="submit" value="login" name="submit" />
    </form>
</body>
</html>