<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
$user = $credentials->getCustomer(($_GET['klant_id']));
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        Klantgegevens Wijzigen
    </title>
</head>
<body>
    <form action="klant.php" method="post">
        <?php
        foreach($cu as $customer)
        {
            if($_SESSION['klant_id'] == null || $customer['klant_id'] != $_SESSION['klant_id'])
            {
                $_SESSION['klant_id'] = $_GET['klant_id'];
            }
        ?>
        <input type="text" value="<?php echo $customer['klant_naam']?>" name="custName"/>
        <input type="text" value="<?php echo $customer['klant_tel']?>" name="tel"/>
        <input type="text" value="<?php echo $customer['email']?>" name="email"/>
        <input type="text" value="<?php echo $customer['adres']?>" name="address"/>
        <input type="text" value="<?php echo $customer['postcode']?>" name="zipcode"/>
        <?php
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $roomReservation->updateReservation($_POST, $_SESSION['klant_id']);
            header('Location: klanten.php');
        }
        ?>
        <input type="submit" name="submit"/>
    </form>
</body>
</html>