<?php
session_start();
require_once('Database/RoomReservationController.php');
$roomReservation = new RoomReservationController();
$reservations = $roomReservation->getAllReservations();
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
        <?php
        foreach($reservations as $reservation)
        {
                echo '<tr>
                    <td><a href="reservering.php?kamer_reservering=' . $reservation['kamer_nummer'] . '">' . $reservation['reservering_nummer'] . '</a></td>
                    <td>' . $reservation['klant_naam'] . '</td>
                    <td>' . $reservation['klant_tel'] . '</td>
                    <td>' . $reservation['email'] . '</td>
                    <td>' . $reservation['adres'] . '</td>
                    <td>' . $reservation['postcode'] . '</td>
                    <td>' . $reservation['kamer_nummer'] . '</td>
                    <td><form action="#" method="post"><input value="Verwijder" type="submit" name="' . $reservation['kamer_nummer'] . '"/></form></td>
                </tr>';
        }
        ?>
    </table>
    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $roomReservation->deleteReservation(key($_POST));
        header('Location:#');
    }
    ?>
</body>
</html>