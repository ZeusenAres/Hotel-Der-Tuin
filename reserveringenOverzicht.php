<?php
session_start();
require_once('Database/RoomReservationController.php');
$roomReservation = new RoomReservationController();
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
            <td>Reservering Nummer</td>
            <td>Naam</td>
            <td>Telefoonnummer</td>
            <td>Email</td>
            <td>Adres</td>
            <td>Postcode</td>
            <td>Kamer</td>
        </tr>
        <?php $roomReservation->getAllReservations() ?>
    </table>
</body>
</html>