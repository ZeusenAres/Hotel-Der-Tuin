<?php
require_once('Database.php');
require_once('UserCredentialsInterface.php');
class UserCredentialsController extends Database implements UserCredentialsInterface
{
    public function __contruct()
    {
        parent::__construct();
    }

    public function getId(string $username) : int
    {
        $this->setTable('klanten');
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("SELECT klant_id FROM $this->table WHERE email = :user");
            $statement->execute(
                [
                    'user' => $username
                ]
            );
            $result = $statement->fetch();
        }
        return $result['klant_id'];
    }

    public function login(string $username, string $password) : string
    {
        $personalTag = '';
        if($this->table == 'medewerkers')
        {
            $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE gebruikersnaam = :user");
            $statement->execute(
                [
                    'user' => $username
                ]
            );
            $result = $statement->fetch();
            if($result['gebruikersnaam'] == null)
            {
                throw new Exception("Ongeldige gebruikersnaam of wachtwoord");
            }
            if(!password_verify($password, $result['wachtwoord']))
            {
                throw new Exception("Ongeldige gebruikersnaam of wachtwoord");
            }
            $personalTag = $result['gebruikersnaam'];
        }
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE email = :user");
            $statement->execute(
                [
                    'user' => $username
                ]
            );
            $result = $statement->fetch();
            if($result['email'] == null)
            {
                throw new Exception("Ongeldige gebruikersnaam of wachtwoord");
            }
            if(!password_verify($password, $result['wachtwoord']))
            {
                throw new Exception("Ongeldige gebruikersnaam of wachtwoord");
            }
            $personalTag = $result['klant_naam'] . '/' . $result['email'];
        }
        return $personalTag;
    }

    public function register(string $username, string $email, string $password, string $tel = '', string $adres = '', string $postcode = '') : void
    {
        if($this->table == 'medewerkers')
        {
            $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE gebruikersnaam = :user AND email = :email");
            $statement->execute([
                    'user' => $username,
                    'email' => $email
            ]);
            $result = $statement->fetch();
            if($result['gebruikersnaam'] == null && $result['email'] == null)
            {
                $this->createEntity([$username, $email, $password]);
            }
            if($result['gebruikersnaam'] != null && $result['email'] != null)
            {
                throw new Exception("Gebruiker $username bestaat al");
            }
        }
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE email = :user AND wachtwoord = :pass");
            $statement->execute([
                    'user' => $username,
                    'pass' => $email
            ]);
            $result = $statement->fetch();
            if($result['email'] == null && $result['wachtwoord'] == null)
            {
                $this->createEntity([$username, $email, $password, $tel, $adres, $postcode]);
            }
            if($result['gebruikersnaam'] != null && $result['email'] != null)
            {
                throw new Exception("Gebruiker $username bestaat al");
            }
        }
    }

    private function createEntity(array $entity) : void
    {
        if($this->table == 'medewerkers')
        {
            $statement = $this->conn->prepare("INSERT INTO $this->table(gebruikersnaam, email, wachtwoord) VALUES(:user, :email, :pass)");
            $statement->execute([
                'user' => $entity[0],
                'email' => $entity[1],
                'pass' => password_hash($entity[2], PASSWORD_DEFAULT)
            ]);
        }
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("INSERT INTO $this->table(klant_naam, wachtwoord, klant_tel, email, adres, postcode)
                VALUES(:name, :pass, :tel, :email, :adres, :postcode)");
            $statement->execute([
                'name' => $entity[0],
                'pass' => password_hash($entity[2], PASSWORD_DEFAULT),
                'tel' => $entity[3],
                'email' => $entity[1],
                'adres' => $entity[4],
                'postcode' => $entity[5]
            ]);
        }
    }
}