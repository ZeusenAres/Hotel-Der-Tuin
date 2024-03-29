<?php

use PDO;
use PDOException;

class Database
{
    private string $host = 'localhost';
    private string $user = 'HighServe';
    private string $password = 'ass';
    private string $dbname = 'hotel_ter_duin';
    protected string $table;
    private int $port = 3308;
    protected PDO $conn;

    public function __construct()
    {
        try
        {
            $this->conn = new PDO("mysql:server=$this->host;port=$this->port;dbname=$this->dbname", $this->user, $this->password);
        } catch(PDOException $pdoEx)
        {
            die("failed to connect to database $pdoEx");
        }
    }

    public function setTable($table) : string
    {
        return $this->table = $table;
    }
}