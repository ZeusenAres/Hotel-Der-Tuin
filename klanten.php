<?php
session_start();
require_once('Database/UserCredentialsController.php');
$credentials = new UserCredentialsController();
$customers = $credentials->getAllCustomers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        Reserveringen
    </title>
</head>
<body>
    <table>
        <tr>
            <td>Klant Nummer</td>
            <td>Naam</td>
            <td>Telefoon Nummer</td>
            <td>Emailadres</td>
            <td>Adres</td>
            <td>Postcode</td>
        </tr>
        <?php
        foreach($customers as $customer)
        {
                echo '<tr>
                    <td><a href="klant.php?klant_id=' . $customer['klant_id'] . '">' . $customer['klant_id'] . '</td>
                    <td>' . $customer['klant_naam'] . '</td>
                    <td>' . $customer['klant_tel'] . '</td>
                    <td>' . $customer['email'] . '</td>
                    <td>' . $customer['adres'] . '</td>
                    <td>' . $customer['postcode'] . '</td>
                    <td><form action="#" method="post"><input value="Verwijder" type="submit" name="' . $customer['klant_id'] . '"/></form></td>
                </tr>';
        }
        ?>
    </table>
    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $credentials->deleteUser(key($_POST));
        header('Location:#');
    }
        ?>
</body>
</html>