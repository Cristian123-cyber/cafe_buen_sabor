<?php
namespace Config;

// Clase para manejar la conexión a la base de datos
class Database {
    private $host = "cafeteria_mysql"; // Usa el nombre del servicio MySQL en Docker
    private $db_name = "cafe_buen_sabor_bd";
    private $username = "root";
    private $password = "root123";
    public $conn;

    // Método para obtener la conexión PDO
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new \PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(\PDOException $e) {
            // Lanza la excepción para que el controlador la maneje
            throw $e;
        }
        return $this->conn;
    }
}
?>