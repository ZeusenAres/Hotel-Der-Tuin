<?php
session_start();
require_once('Database/UserCredentialsController.php');
require_once('Database/RoomReservationController.php');
$credentials = new UserCredentialsController();
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
    <?php echo $credentials->navbar();?>
    <form id="reservering_edit" name="reservering_edit" action="reservering.php" method="post">
        <?php
        if(!empty($_GET['kamer_reservering']))
        {
            $_SESSION['id'] = ['kamernummer' => $_GET['kamer_reservering']];
        }
        if(!empty($_GET['id']))
        {
            $_SESSION['id'] = ['custMail' => $_GET['id']];
        }
        foreach($roomReservation->getReservation($_SESSION['id']) as $reservation)
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
            if(!empty($_SESSION['kamernummer']))
            {
                $roomReservation->updateReservation($_POST, $_SESSION['kamernummer']);
            }
            if(!empty($_SESSION['custMail']))
            {
                $roomReservation->updateReservation($_POST, $_SESSION['kamernummer']);
            }
            header('Location: home.php');
        }
        ?>
        <input type="submit" name="submit"/>
    </form>
</body>
</html>