
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de veterinario inválido.');
}

$consulta = $conexion->prepare(
    "SELECT id_veterinario, matricula, nombre,
            apellido, especialidad
     FROM veterinarios
     WHERE id_veterinario = :id"
);

$consulta->execute([':id' => $id]);
$veterinario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$veterinario) {
    http_response_code(404);
    exit('Veterinario no encontrado.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $matricula = trim($_POST['matricula'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');

    $veterinario['matricula'] = $matricula;
    $veterinario['nombre'] = $nombre;
    $veterinario['apellido'] = $apellido;
    $veterinario['especialidad'] = $especialidad;

    if (
        $matricula === '' ||
        $nombre === '' ||
        $apellido === ''
    ) {
        $error = 'Completá matrícula, nombre y apellido.';

    } elseif (
        strlen($matricula) > 30 ||
        strlen($nombre) > 100 ||
        strlen($apellido) > 100 ||
        strlen($especialidad) > 100
    ) {
        $error = 'Uno de los campos supera la longitud permitida.';

    } else {
        try {
            // Verificar matrícula duplicada.
            $verificar = $conexion->prepare(
                "SELECT COUNT(*)
                 FROM veterinarios
                 WHERE matricula = :matricula
                   AND id_veterinario <> :id"
            );

            $verificar->execute([
                ':matricula' => $matricula,
                ':id' => $id
            ]);

            if ((int)$verificar->fetchColumn() > 0) {
                $error = 'Ya existe otro veterinario con esa matrícula.';

            } else {
                $actualizar = $conexion->prepare(
                    "UPDATE veterinarios
                     SET matricula = :matricula,
                         nombre = :nombre,
                         apellido = :apellido,
                         especialidad = :especialidad
                     WHERE id_veterinario = :id"
                );

                $actualizar->execute([
                    ':matricula' => $matricula,
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':especialidad' => $especialidad,
                    ':id' => $id
                ]);

                header('Location: index.php?actualizado=1');
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudieron actualizar los datos.';
        }
    }
}

$tituloPagina = 'Editar veterinario';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
        Volver a veterinarios
    </a>
</div>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-pencil-square"></i>
        Editar veterinario 🐾
    </h1>

    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                Modificar datos del veterinario
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="editar.php?id=<?= (int)$id ?>">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label for="matricula" class="form-label">
                            Matrícula
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="matricula"
                            name="matricula"
                            maxlength="30"
                            value="<?= escapar($veterinario['matricula']) ?>"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label for="nombre" class="form-label">
                            Nombre
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            maxlength="100"
                            value="<?= escapar($veterinario['nombre']) ?>"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label for="apellido" class="form-label">
                            Apellido
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="apellido"
                            name="apellido"
                            maxlength="100"
                            value="<?= escapar($veterinario['apellido']) ?>"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label for="especialidad" class="form-label">
                            Especialidad
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="especialidad"
                            name="especialidad"
                            maxlength="100"
                            value="<?= escapar($veterinario['especialidad']) ?>">
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">

                    <button type="submit" class="btn btn-petcare">
                        <i class="bi bi-floppy-fill"></i>
                        Guardar cambios
                    </button>

                    <a
                        href="index.php"
                        class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>
    </div>

</main>

<?php
require __DIR__ . '/../includes/footer.php';
?>
