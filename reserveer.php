<?php
session_start();
require_once('Database/RoomReservationController.php');
$roomReservation = new RoomReservationController();
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['rooms']))
    {
        $roomReservation->createReservation($_SESSION['custMail'], $_POST['rooms'], $_POST['startDate'], $_POST['endDate']);
    }
    header("Location:reserveringenOverzicht.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        Reserveer
    </title>
</head>
<body>
    <?php
    echo $_SESSION['custMail'] . $_SESSION['username'];
    ?>
    <h1>Beschikbare kamers</h1>
    <form id="roomsForm" action="reserveer.php" method="post">
        <input name="startDate" type="date"/>
        <input name="endDate" type="date"/>
        <select placeholder="--Selecteer een Kamer" name="rooms" form="roomsForm">
            <?php
            $roomReservation->getAvailableRooms();
            ?>
        </select>
        <input type="submit" name="chosenRoom"/>
    </form>
</body>
</html>