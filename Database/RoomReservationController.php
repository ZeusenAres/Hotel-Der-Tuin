<?php
Require_once('Database.php');
Require_once('UserCredentialsController.php');
class RoomReservationController extends Database
{
    private UserCredentialsController $userCredentialsController;

    public function __construct()
    {
        parent::__construct();
        $this->userCredentialsController = new UserCredentialsController();
    }

    public function getAllRooms()
    {
        $this->setTable('kamers');
        $statement = $this->conn->prepare("SELECT * FROM $this->table");
        $statement->execute();
        foreach($statement->fetchAll() as $row)
        {
            echo '<option value="' . $row['kamernummer'] . '">' . $row['kamernummer'] . '</option>';
        }
    }

    public function getAvailableRooms() : array
    {
        $this->setTable('kamers');
        $statement = $this->conn->prepare("SELECT kamernummer FROM $this->table WHERE gereserveerd = false");
        $statement->execute();
        return $statement->fetchAll();
    }

    public function deleteReservation(?int $roomNum) : void
    {
        if($roomNum != null)
        {
            $this->setTable('reserveringen');
            $statement = $this->conn->prepare("DELETE FROM $this->table WHERE kamer_nummer = :roomNum");
            $statement->execute([
                'roomNum' => $roomNum
                ]);
            $this->setReservationStatus(false, $roomNum);
        }
    }

    private function isDateValid($startDate, $endDate) : bool
    {
        $currentDate = strtotime(date('y-m-d'));
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        if($startDate <= $currentDate)
        {
            throw new Exception('Datum kan niet eerder dan de tegenwoordige tijd');
        }
        if($endDate < $startDate)
        {
            throw new Exception('Eind datum kan niet eerder dan start datum');
        }
        if($endDate > strtotime('+1 month'))
        {
            throw new Exception('Reservering kan alleen tot maximaal 1 maand gemaakt worden');
        }
        return true;
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
        if($roomNum == 0)
        {
            throw new Exception('Selecteer een kamer');
        }
        if($this->isDateValid($startDate, $endDate))
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
    }

    public function getAllReservations() : array
    {
        $this->setTable('reserveringen');
        $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id");
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getReservation(array $id) : array
    {
        if(!empty($id['kamernummer']))
        {
            $this->setTable('reserveringen');
            $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id WHERE kamer_nummer = :roomNum");
            $statement->execute([
                'roomNum' => $id['kamernummer']
                ]);
        }
        if(!empty($id['custMail']))
        {
            $this->setTable('reserveringen');
            $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id WHERE email=:custMail");
            $statement->execute([
                'custMail' => $id['custMail']
                ]);
        }
        return $statement->fetchAll();
    }

    public function updateReservation(array $reservation, int $clearedRoom) : void
    {
        $this->setTable('reserveringen');
        $statement = $this->conn->prepare("UPDATE $this->table SET kamer_nummer=:roomNum, begin_datum=:start, eind_datum=:end WHERE kamer_nummer=:clearedRoom");
        $statement->execute([
            'clearedRoom' => $clearedRoom,
            'roomNum' => $reservation['kamer'],
            'start' => $reservation['begin'],
            'end' => $reservation['eind']
            ]);
        $this->setReservationStatus(true, $reservation['kamer']);
        $this->setReservationStatus(false, $clearedRoom);
    }
}