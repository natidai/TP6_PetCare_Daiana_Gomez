
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

// Solo se permite eliminar mediante POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// Validar el identificador recibido.
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de mascota inválido.');
}

try {
    // Comprobar que la mascota exista.
    $buscar = $conexion->prepare(
        "SELECT id_mascota
         FROM mascotas
         WHERE id_mascota = :id"
    );

    $buscar->execute([':id' => $id]);

    if (!$buscar->fetch()) {
        header('Location: index.php?no_encontrado=1');
        exit;
    }

    // No eliminar mascotas con historial médico.
    $verificar = $conexion->prepare(
        "SELECT COUNT(*)
         FROM consultas
         WHERE id_mascota = :id"
    );

    $verificar->execute([':id' => $id]);

    if ((int)$verificar->fetchColumn() > 0) {
        header('Location: index.php?con_consultas=1');
        exit;
    }

    // Eliminar la mascota.
    $eliminar = $conexion->prepare(
        "DELETE FROM mascotas
         WHERE id_mascota = :id"
    );

    $eliminar->execute([':id' => $id]);

    header('Location: index.php?eliminado=1');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?error_eliminar=1');
    exit;
}
?>
