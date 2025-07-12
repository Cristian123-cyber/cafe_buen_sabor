<?php
namespace Config;

// Clase para manejar la conexión a la base de datos usando Singleton
class Database {
    private static $instance = null;
    private $host = "cafeteria_mysql"; // Usa el nombre del servicio MySQL en Docker
    private $db_name = "cafe_buen_sabor_db";
    private $username = "root";
    private $password = "root123";
    private $conn = null;

    // Constructor privado para evitar instanciación directa
    private function __construct() {}

    // Método para obtener la instancia única (Singleton)
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection() {
        if ($this->conn === null) {
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
        }
        return $this->conn;
    }

    // Prevenir clonación del objeto
    private function __clone() {}

    // Prevenir deserialización del objeto (debe ser público)
    public function __wakeup() {
        throw new \Exception("No se puede deserializar un singleton");
    }
}