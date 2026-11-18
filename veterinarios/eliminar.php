
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

// Solo permitir solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de veterinario inválido.');
}

try {
    // Verificar que el veterinario exista.
    $buscar = $conexion->prepare(
        "SELECT id_veterinario
         FROM veterinarios
         WHERE id_veterinario = :id"
    );

    $buscar->execute([':id' => $id]);

    if (!$buscar->fetch()) {
        header('Location: index.php?no_encontrado=1');
        exit;
    }

    // Proteger las consultas médicas existentes.
    $verificar = $conexion->prepare(
        "SELECT COUNT(*)
         FROM consultas
         WHERE id_veterinario = :id"
    );

    $verificar->execute([':id' => $id]);

    if ((int)$verificar->fetchColumn() > 0) {
        header('Location: index.php?con_consultas=1');
        exit;
    }

    // Eliminar veterinario sin consultas asociadas.
    $eliminar = $conexion->prepare(
        "DELETE FROM veterinarios
         WHERE id_veterinario = :id"
    );

    $eliminar->execute([':id' => $id]);

    header('Location: index.php?eliminado=1');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?error_eliminar=1');
    exit;
}
