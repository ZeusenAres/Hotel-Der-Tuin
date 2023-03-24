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

    public function getRoom(string $roomNum) : array
    {
        $this->setTable('kamers');
        $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE kamernummer = :roomNum");
        $statement->execute([
            'roomNum' => $roomNum
            ]);
        return $statement->fetchAll();
    }

    private function isDateValid($startDate, $endDate) : bool
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        if($endDate < $startDate)
        {
            throw new Exception('Eind datum kan niet eerder dan start datum');
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

    public function createReservation(string $customerMail, int $roomNum, string $startDate, string $endDate) : void
    {
        if($roomNum == 0)
        {
            throw new Exception('Selecteer een kamer');
        }
        if($this->isDateValid($startDate, $endDate))
        {
            foreach($this->getRoom($roomNum) as $room)
            {
                $id = $this->userCredentialsController->getId($customerMail);
                $durationOfStay = round((strtotime($endDate) - strtotime($startDate)) / 86400);
                $totalPrice = $room['prijs_per_nacht'] * $durationOfStay;
                $this->setTable('reserveringen');
                $statement = $this->conn->prepare("INSERT INTO $this->table(klant_id, kamer_nummer, totaal_prijs, verblijfsduur, begin_datum, eind_datum) VALUES(:id, :roomNum, :total, :stay, :start, :end)");
                $statement->bindParam('id', $id, PDO::PARAM_INT);
                $statement->bindParam('roomNum', $roomNum, PDO::PARAM_INT);
                $statement->bindParam('total', $totalPrice);
                $statement->bindParam('stay', $durationOfStay, PDO::PARAM_INT);
                $statement->bindParam('start', $startDate, PDO::PARAM_STR);
                $statement->bindParam('end', $endDate, PDO::PARAM_STR);
                $statement->execute();
                $this->setReservationStatus(true, $roomNum);
            }
        }
    }

    public function getAllReservations() : array
    {
        $this->setTable('reserveringen');
        $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id");
        $statement2 = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN kamers ON reserveringen.kamer_nummer = kamers.kamernummer");
        $statement->execute();
        $statement2->execute();
        return array_merge($statement->fetchAll(), $statement2->fetchAll());;
    }

    public function getReservation(array $id) : array
    {
        if(!empty($id['kamernummer']))
        {
            $this->setTable('reserveringen');
            $statement = $this->conn->prepare("SELECT * FROM $this->table
            INNER JOIN klanten ON reserveringen.klant_id = klanten.klant_id
            INNER JOIN kamers ON kamers.kamernummer
            WHERE `kamer_nummer` = :roomNum");
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