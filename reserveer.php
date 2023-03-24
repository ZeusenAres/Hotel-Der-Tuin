<?php
session_start();
require_once('Database/UserCredentialsController.php');
require_once('Database/RoomReservationController.php');
require_once('Database/FactuurPDF.php');
$credentials = new UserCredentialsController();
$roomReservation = new RoomReservationController();
$today = date('Y') . '-' . date('m') . '-' . date('d');
$year = intval(date('Y')) + 1;
$maximumDate = $year . '-' . date('m') . '-' . date('d');
$factuurPDF = new FactuurPDF();
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $roomReservation->createReservation($_SESSION['custMail'], $_POST['rooms'], $_POST['startDate'], $_POST['endDate']);
            $roomNum = ['kamernummer' => $_POST['rooms']];
            foreach($roomReservation->getReservation($roomNum) as $reservation)
            {
                $factuurPDF->AddPage();
                $factuurPDF->AcceptPageBreak();
                $factuurPDF->SetFont('Times', '', 14);
                $factuurPDF->Cell(0, 10, 'Reserveringnummer: ' . $reservation['reservering_nummer'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Naam: ' . $reservation['klant_naam'], 0, 1);
                $factuurPDF->Cell(0, 10, 'E-mail: ' . $reservation['email'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Tel: ' . $reservation['klant_tel'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Adres: ' . $reservation['adres'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Postcode: ' . $reservation['postcode'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Reservering van: ' . $reservation['begin_datum'] . ' tot ' . $reservation['eind_datum'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Kamer: ' . $reservation['kamer_nummer'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Duur van verblijf: ' . $reservation['verblijfsduur'], 0, 1);
                $factuurPDF->Cell(0, 10, 'Totaalprijs: €' . str_replace('.', ',', $reservation['totaal_prijs']) . '-', 0, 1);
                $factuurPDF->Image($reservation['afbeelding'], 60, 30, 90, 0, 'JPG');
                $factuurPDF->Output();
            }
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
    echo $credentials->navbar();
    $available = count($roomReservation->getAvailableRooms());
    if($available <= 2)
    {
        echo '<h3 style="color:red"> Er zijn momenteel ' . $available . ' kamers beschikbaar</h3>';
    }
    ?>
    <h1>Beschikbare kamers</h1>
    <form id="roomsForm" action="reserveer.php" method="post">
        <input name="startDate" type="date"  min="<?php echo $today?>" max="<?php echo $maximumDate?>"/>
        <input name="endDate" type="date" min="<?php echo $today?>" max="<?php echo $maximumDate?>"/>
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