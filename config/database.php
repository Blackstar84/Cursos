<?php
class Database {
    private $host = "localhost";
    private $db_name = "courses";
    private $username = "postgres";
    private $password = "root";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("pgsql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Elimina la línea siguiente porque no es necesaria en PostgreSQL
            // $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
