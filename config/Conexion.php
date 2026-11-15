
<?php

function conectarBD() {
    $config = require __DIR__ . '/credenciales.php';

    try {
        $conexion = new PDO(
            "mysql:host={$config['host']};port={$config['puerto']};dbname={$config['base']};charset=utf8mb4",
            $config['usuario'],
            $config['clave'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        return $conexion;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("No se pudo conectar con la base de datos.");
    }
}
