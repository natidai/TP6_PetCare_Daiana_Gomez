
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

// La eliminación solo se permite mediante POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de consulta inválido.');
}

try {
    $buscar = $conexion->prepare(
        "SELECT id_consulta
         FROM consultas
         WHERE id_consulta = :id"
    );

    $buscar->execute([':id' => $id]);

    if (!$buscar->fetch()) {
        header('Location: index.php?error_eliminar=1');
        exit;
    }

    $eliminar = $conexion->prepare(
        "DELETE FROM consultas
         WHERE id_consulta = :id"
    );

    $eliminar->execute([':id' => $id]);

    header('Location: index.php?eliminado=1');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?error_eliminar=1');
    exit;
}
