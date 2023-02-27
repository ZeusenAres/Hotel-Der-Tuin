<?php
Require_once('Database.php');
Require_once('UserCredentialsController.php');
class RoomReservationController extends Database
{
    private UserCredentialsController $userCredentialsController;

    public function __construct()
    {
        parent::__construct();
        $this->setTable('kamers');
        $this->userCredentialsController = new UserCredentialsController();
    }

    public function getAllRooms()
    {
        $statement = $this->conn->prepare("SELECT * FROM $this->table");
        $statement->execute();
        foreach($statement->fetchAll() as $row)
        {
            echo '<option value="' . $row['kamernummer'] . '">' . $row['kamernummer'] . '</option>';
        }
    }

    public function getAvailableRooms()
    {
        $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE gereserveerd = false");
        $statement->execute();
        foreach($statement->fetchAll() as $row)
        {
            echo '<option value="' . $row['kamernummer'] . '">' . $row['kamernummer'] . '</option>';
        }

        if(count($statement->fetchAll()) <= 2)
        {
            echo '<h3 color="red">' . count($statement->fetchAll()) . "kamers beschikbaar</h3>";
        }
    }

    private function setReservationStatus(bool $status, int $roomNum) : int
    {
        $this->setTable('kamers');
        $statement = $this->conn->prepare("UPDATE $this->table SET gereserveerd=:status WHERE kamernummer=:roomNum");
        $statement->execute([
            'status' => $status,
            'roomNum' => $roomNum
            ]);
        return $roomNum;
    }

    public function createReservation(string $customerMail, int $roomNum, $startDate, $endDate) : void
    {
        $this->setTable('reserveringen');
        $id = $this->userCredentialsController->getId($customerMail);
        $this->setTable('reserveringen');
        $statement = $this->conn->prepare("INSERT INTO $this->table(klant_id, kamer_nummer, begin_datum, eind_datum) VALUES(:id, :roomNum, :start, :end)");
        $statement->execute([
            'id' => $id,
            'roomNum' => $roomNum,
            'start' => $startDate,
            'end' => $endDate
            ]);
        $this->setReservationStatus(true, $roomNum);
    }

    public function getAllReservations()
    {
        $this->setTable('reserveringen');
        $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id");
        $statement->execute();
        foreach($statement->fetchAll() as $row)
        {
            echo '<tr>
                <td>' . $row['reservering_nummer'] . '</td>
                <td>' . $row['klant_naam'] . '</td>
                <td>' . $row['klant_tel'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . $row['adres'] . '</td>
                <td>' . $row['postcode'] . '</td>
                <td>' . $row['kamer_nummer'] . '</td>
            </tr>';
        }
    }
}