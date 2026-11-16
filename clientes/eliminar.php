
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

// Solo se permiten solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php?error=id');
    exit;
}

try {
    // Verificar si el cliente tiene mascotas asociadas.
    $consulta = $conexion->prepare(
        "SELECT COUNT(*)
         FROM mascotas
         WHERE id_cliente = :id"
    );

    $consulta->execute([':id' => $id]);

    if ((int)$consulta->fetchColumn() > 0) {
        header('Location: index.php?error=mascotas');
        exit;
    }

    // Eliminar el cliente si no tiene mascotas.
    $eliminar = $conexion->prepare(
        "DELETE FROM clientes WHERE id_cliente = :id"
    );

    $eliminar->execute([':id' => $id]);

    header('Location: index.php?eliminado=1');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?error=eliminar');
    exit;
}
