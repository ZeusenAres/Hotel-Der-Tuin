<?php
session_start();
require_once('Database/RoomReservationController.php');
$roomReservation = new RoomReservationController();
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        Reservering Wijzigen
    </title>
</head>
<body>
    <form id="reservering_edit" name="reservering_edit" action="reservering.php" method="post">
        <?php
        foreach($roomReservation->getReservation($_GET['kamer_reservering']) as $reservation)
        {
            if($_SESSION['kamernummer'] == null || $reservation['kamer_nummer'] != $_SESSION['kamernummer'])
            {
                $_SESSION['kamernummer'] = $_GET['kamer_reservering'];
            }
        ?>
        <select name="kamer" form="reservering_edit">
            <option style="background-color: yellow;" value="<?php echo $reservation['kamer_nummer'];?>"><?php echo $reservation['kamer_nummer'];?></option>
            <?php
            foreach($roomReservation->getAvailableRooms() as $availableRooms)
            {
                echo '<option value="' . $availableRooms['kamernummer'] . '">' . $availableRooms['kamernummer'] . '</option>';
            }?>
            ?>
        </select>
        <input type="text" name="begin" value="<?php echo $reservation['begin_datum']?>" />
        <input type="text" name="eind" value="<?php echo $reservation['eind_datum']?>" />
        <?php
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $roomReservation->updateReservation($_POST, $_SESSION['kamernummer']);
            header('Location: reserveringenOverzicht.php');
        }
        ?>
        <input type="submit" name="submit"/>
    </form>
</body>
</html>