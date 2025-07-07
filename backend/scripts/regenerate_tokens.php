<?php

if (php_sapi_name() !== 'cli') {
    die("Solo ejecutable por CLI.");
}

require_once __DIR__ . '/../src/config/database.php';
use Config\Database;

function generarTokenSeguro() {
    return bin2hex(random_bytes(32));
}

try {
    $pdo = Database::getInstance()->getConnection();

    $mesas = $pdo->query("SELECT id_table FROM `tables` WHERE table_status = 'FREE'")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        UPDATE `tables`
        SET qr_token = :token,
            token_expiration = DATE_ADD(NOW(), INTERVAL 10 MINUTE)
        WHERE id_table = :id
    ");

    foreach ($mesas as $mesa) {
        $token = generarTokenSeguro();
        $stmt->execute([
            ':token' => $token,
            ':id' => $mesa['id_table']
        ]);
    }

    file_put_contents('/var/log/mesa_cron.log', "[✔] Tokens regenerados a " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

} catch (Exception $e) {
    file_put_contents('/var/log/mesa_cron_error.log', "[✖] Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

