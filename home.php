<!DOCTYPE html>
<html>
<head>
    <?php
    session_start();
    require_once('Database/UserCredentialsController.php');
    $credentials = new UserCredentialsController();
    ?>
    <title>
        homepage
    </title>
</head>
<body>
    <nav>
        <?php echo $credentials->navbar();?>
    </nav>
    <div>
        <a href="reserveer.php">Reserveer</a>
    </div>
</body>
</html>