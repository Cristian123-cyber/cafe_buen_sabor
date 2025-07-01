<?php

namespace App\Models;

class RefreshToken extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'refresh_tokens';
        $this->primary_key = 'id';
    }

    public function getValidTokenByEmployee(int $employeeId)
    {
        $sql = "SELECT id, token_hash AS token FROM {$this->table_name} 
                WHERE employe_id = :employe_id 
                AND expires_at > NOW() 
                AND is_revoked = FALSE
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':employe_id', $employeeId);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Crear un nuevo refresh token
     */
     public function createRefreshToken(array $data)
    {


        $sql = "INSERT INTO {$this->table_name} 
                (employe_id, token_hash, expires_at, created_at, is_revoked) 
                VALUES (:employe_id, :token_hash, :expires_at, NOW(), FALSE)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':employe_id', $data['employe_id']);
        $stmt->bindParam(':token_hash', $data['token_hash']);
        $stmt->bindParam(':expires_at', $data['expires_at']);


        return $stmt->execute();
    }

     public function validateToken(string $token)
    {
        $tokenHash = hash('sha256', $token);
        
        $sql = "SELECT {$this->primary_key}, employe_id FROM {$this->table_name} 
                WHERE token_hash = :token_hash 
                AND expires_at > NOW() 
                AND is_revoked = FALSE";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':token_hash', $tokenHash);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Revocar un token
     */
    public function revokeToken(string $token)
    {
        $tokenHash = hash('sha256', $token);
        
        $sql = "UPDATE {$this->table_name} 
                SET is_revoked = TRUE 
                WHERE token_hash = :token_hash
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':token_hash', $tokenHash);
        return $stmt->execute();
    }

    /**
     * Forzar la generación de un nuevo token (para admin)
     */
    public function forceNewToken(int $employeeId)
    {
        // Revocar cualquier token existente
        $sql = "UPDATE {$this->table_name} 
                SET is_revoked = TRUE 
                WHERE employe_id = :employe_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':employe_id', $employeeId);
        return $stmt->execute();
    }
}
