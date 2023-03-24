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
    <table>
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
                    if(empty($_GET['kamer_reservering']))
                    {
                        $_SESSION['kamernummer'] = $reservation['kamer_nummer'];
                    }
                    if(!empty($_GET['kamer_reservering']))
                    {
                        $_SESSION['kamernummer'] = $_GET['kamer_reservering'];
                    }
                }
                if(!empty($_SESSION['custMail']))
                {
                    echo '<tr>
                        <td>Reservering Nummer</td>
                        <td>Naam</td>
                        <td>Telefoonnummer</td>
                        <td>Email</td>
                        <td>Adres</td>
                        <td>Postcode</td>
                        <td>Kamer</td>
                    </tr>
                    <tr>
                        <td>' . $reservation['reservering_nummer'] . '</td>
                        <td>' . $reservation['klant_naam'] . '</td>
                        <td>' . $reservation['klant_tel'] . '</td>
                        <td>' . $reservation['email'] . '</td>
                        <td>' . $reservation['adres'] . '</td>
                        <td>' . $reservation['postcode'] . '</td>
                        <td>' . $reservation['kamer_nummer'] . '</td>
                    </tr>';
                }
                else
                {
            ?>
            <select name="kamer" form="reservering_edit">
                <option style="background-color: yellow;" value="<?php echo $reservation['kamer_nummer'];?>"><?php echo $reservation['kamer_nummer'];?></option>
                <?php
                foreach($roomReservation->getAvailableRooms() as $availableRooms)
                {
                    echo '<option value="' . $availableRooms['kamernummer'] . '">' . $availableRooms['kamernummer'] . '</option>';
                }?>
            </select>
            <input type="text" name="begin" value="<?php echo $reservation['begin_datum']?>" />
            <input type="text" name="eind" value="<?php echo $reservation['eind_datum']?>" />
            <?php
                }
            }
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if(!empty($_SESSION['kamernummer']))
                {
                    $roomReservation->updateReservation($_POST, $_SESSION['kamernummer']);
                }
                header('Location: home.php');
            }
            ?>
            <input type="submit" name="submit"/>
        </form>
    </table>
</body>
</html>