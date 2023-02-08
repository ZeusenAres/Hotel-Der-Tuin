<?php
require_once('Database.php');
require_once('UserCredentialsInterface.php');
class UserCredentialsController extends Database implements UserCredentialsInterface
{
    public function login(string $username, string $password) : string
    {
        $statement = $this->conn->prepare("SELECT * FROM employee WHERE username = :user, passphrase = :pass");
        $result = $statement->execute(
            [
                'user' => $username,
                'pass' => $password
            ]
        );

        $message = '';

        if($result)
        {

        }

        return $message;
    }

    public function register(string $username, string $email, string $password) : void
    {
        
    }
}