
<?php

require_once __DIR__ . '/config/Conexion.php';

$conexion = conectarBD();

if ($conexion instanceof PDO) {
    echo "¡Conexión exitosa con PetCare!";
}
