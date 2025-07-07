<?php

namespace App\Models;

use PDO;
use PDOException;

class User extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'employees'; // Corregido: La tabla es 'employees'
        $this->primary_key = 'id_employe'; // Corregido: La PK es 'id_employe'
    }

    /**
     * Busca un usuario por su dirección de email y obtiene su rol.
     *
     * @param string $email El email del usuario a buscar.
     * @return mixed Devuelve un array con los datos del usuario y su rol si se encuentra, o false si no.
     */
    public function findByEmail($email)
    {
        // Actualizado: Se une con la tabla de roles para obtener el nombre del rol.
        $query = "SELECT e.*, 
                        r.rol_name,
                        r.id_rol AS role_id,
                        s.id_status AS status_id,
                        s.status_name AS status_name
                    FROM {$this->table_name} e
                    LEFT JOIN employees_rol r 
                        ON e.employees_rol_id_rol = r.id_rol
                    LEFT JOIN employees_statuses s 
                        ON e.employees_statuses_id_status = s.id_status
                    WHERE e.employe_email = :email 
                    LIMIT 1;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        // Actualizado: Se une con la tabla de roles para obtener el nombre del rol.
        $query = "SELECT e.*, 
                        r.rol_name,
                        r.id_rol AS role_id,
                        s.id_status AS status_id,
                        s.status_name AS status_name
                    FROM {$this->table_name} e
                    LEFT JOIN employees_rol r 
                        ON e.employees_rol_id_rol = r.id_rol
                    LEFT JOIN employees_statuses s 
                        ON e.employees_statuses_id_status = s.id_status
                    WHERE e.id_employe = :id 
                    LIMIT 1;";


       
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Asegurate que sea entero
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        

        
    }
}
