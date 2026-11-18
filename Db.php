<?php
class Db
{
    private $connection;

    public function __construct($host = 'localhost', $dbname = 'site_vente_voitures', $username = 'root', $password = 'root')
    {
        $this->connection = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getConnection()
    {
        return $this->connection;
    }
}