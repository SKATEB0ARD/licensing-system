<?php
namespace Licensing\Data;

class Database {
    public $host, $name, $username, $password;
    public $conn;

    /**
     * Database constructor.
     * @param $host
     * @param $name
     * @param $username
     * @param $password
     */
    public function __construct($host, $name, $username, $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }

    public function getConnection() {
        $this->conn = null;

        try{
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(\PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}