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

    public function getAllCustomers() : array
    {
        $this->setTable('klanten');
        $statement = $this->conn->prepare("SELECT * FROM $this->table");
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getCustomer(int $id) : array
    {
        $this->setTable('klanten');
        $statement = $this->conn->prepare("SELECT * FROM $this->table WHERE klant_id = :id");
        $statement->execute([
            'id' => $id
            ]);
        return $statement->fetchAll();
    }

    public function deleteUser(int $id) : void
    {
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("DELETE FROM $this->table WHERE klant_id = :id");
            $statement->execute([
                'id' => $id
                ]);
        }

        if($this->table == 'medewerkers')
        {
            $statement = $this->conn->prepare("DELETE FROM $this->table WHERE medewerker_id = :id");
            $statement->execute([
                'id' => $id
                ]);
        }
    }

    public function updateUser(array $user, int $id) : void
    {
        if($this->table == 'klanten')
        {
            $statement = $this->conn->prepare("UPDATE $this->table SET klant_naam=:name, klant_tel=:tel, email=:email, adres=:adres, postcode=:zipcode WHERE klant_id=:id");
            $statement->execute([
                'id' => $id,
                'name' => $user['klant_naam'],
                'tel' => $user['klant_tel'],
                'email' => $user['email'],
                'adres' => $user['adres'],
                'zipcode' => $user['postcode']
                ]);
        }

        if($this->table == 'medewerkers')
        {
            $statement = $this->conn->prepare("UPDATE $this->table SET gebruikersnaam=:name, wachtwoord=:pass, email=:email WHERE medewerker_id=:id");
            $statement->execute([
                'id' => $id,
                'name' => $user['gebruikersnaam'],
                'pass' => $user['wachtwoord'],
                'email' => $user['email']
                ]);
        }
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

    private function userLoginState() : void
    {
        if(empty($_SESSION['username']) && empty($_SESSION['custMail']))
        {
            echo '<a href="loginKlant.php">Login Klant</a>
            <a href="registreerKlant.php">Registreer Klant</a>
            <a href="login.php">Login Medewerker</a>';
        }
        if(!empty($_SESSION['username']) && empty($_SESSION['custMail']))
        {
            echo '<h3 class="personal_tag">' . $_SESSION['username'] . '
                    <div class="profile_actions">
                        <a href="reserveringenOverzicht.php">Reservering Overzicht</a>
                    </div>
                </h3>
                <form action="#" method="post">
                    <input type="submit" name="logout"/>
                </form>';
            if(isset($_POST['logout']))
            {
                unset($_SESSION['username']);
                header('Location:#');
            }
        }
        if(!empty($_SESSION['username']) && !empty($_SESSION['custMail']))
        {
            echo '<h3 class="personal_tag">' . $_SESSION['custMail'] . '
                    <div class="profile_actions">
                        <a href="profiel.php">Profiel</a>
                    </div>
                </h3>
                <form action="#" method="post">
                    <input type="submit" name="logout"/>
                </form>';
            if(isset($_POST['logout']))
            {
                unset($_SESSION['username']);
                unset($_SESSION['custMail']);
                header('Location:#');
            }
        }
    }

    public function navbar() : string
    {
        return '<a href="klanten.php">Klanten</a>
                <a href="contact.php">Contact</a>' . $this->userLoginState();
    }
}