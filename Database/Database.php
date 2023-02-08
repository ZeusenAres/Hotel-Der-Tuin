<?php

use PDO;
use PDOException;

class Database
{
    private string $host = 'localhost';
    private string $user = 'root';
    private string $password = '';
    private string $dbname = 'hotel_der_tuin';
    protected $conn;

    public function __construct()
    {
        try
        {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;", $this->user, $this->password);
        } catch(PDOException $pdoEx)
        {
            die("failed to connect to database $pdoEx");
        }
    }
}