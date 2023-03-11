<?php
session_start();
require_once('Database/RoomReservationController.php');
require_once('Database/FactuurPDF.php');
$roomReservation = new RoomReservationController();
$factuurPDF = new FactuurPDF();
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $roomReservation->createReservation($_SESSION['custMail'], $_POST['rooms'], $_POST['startDate'], $_POST['endDate']);
        }
        foreach($roomReservation->getReservation($_POST['rooms']) as $reservation)
        {
            $factuurPDF->AliasNbPages();
            $factuurPDF->AddPage();
            $factuurPDF->SetFont('Times','',14);
            $factuurPDF->AcceptPageBreak();
            $factuurPDF->Cell(0, 10, 'Reserveringnummer: ' . $reservation['reservering_nummer'], 0, 1);
            $factuurPDF->Cell(0, 10, 'Naam: ' . $reservation['klant_naam'], 0, 1);
            $factuurPDF->Cell(0, 10, 'E-mail: ' . $reservation['email'], 0, 1);
            $factuurPDF->Cell(0, 10, 'Tel: ' . $reservation['klant_tel'], 0, 1);
            $factuurPDF->Cell(0, 10, 'Adres: ' . $reservation['adres'], 0, 1);
            $factuurPDF->Cell(0, 10, 'Postcode: ' . $reservation['postcode'], 0, 1);
            $factuurPDF->Cell(0, 10, 'Reservering van: ' . $reservation['begin_datum'] . ' tot ' . $reservation['eind_datum'], 0, 1);
            $factuurPDF->Output();
        }
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
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
    $available = count($roomReservation->getAvailableRooms());
    if($available <= 2)
    {
        echo '<h3 style="color:red"> Er zijn momenteel ' . $available . ' kamers beschikbaar</h3>';
    }
    ?>
    <h1>Beschikbare kamers</h1>
    <form id="roomsForm" action="reserveer.php" method="post">
        <input name="startDate" type="date"/>
        <input name="endDate" type="date"/>
        <select name="rooms" form="roomsForm">
            <option value="0">--Selecteer een kamer--</option>
            <?php
            foreach($roomReservation->getAvailableRooms() as $availableRooms)
            {
                echo '<option value="' . $availableRooms['kamernummer'] . '">' . $availableRooms['kamernummer'] . '</option>';
            }?>
        </select>
        <input type="submit" name="chosenRoom" />
    </form>
</body>
</html>