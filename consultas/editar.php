
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de consulta inválido.');
}

// Buscar consulta médica.
$buscar = $conexion->prepare(
    "SELECT id_consulta, fecha, diagnostico, precio,
            id_mascota, id_veterinario
     FROM consultas
     WHERE id_consulta = :id"
);

$buscar->execute([':id' => $id]);
$consultaMedica = $buscar->fetch(PDO::FETCH_ASSOC);

if (!$consultaMedica) {
    http_response_code(404);
    exit('Consulta médica no encontrada.');
}

// Obtener mascotas.
$mascotas = $conexion->query(
    "SELECT id_mascota, nombre
     FROM mascotas
     ORDER BY nombre"
)->fetchAll(PDO::FETCH_ASSOC);

// Obtener veterinarios.
$veterinarios = $conexion->query(
    "SELECT id_veterinario, nombre, apellido
     FROM veterinarios
     ORDER BY apellido, nombre"
)->fetchAll(PDO::FETCH_ASSOC);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fecha = trim($_POST['fecha'] ?? '');
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $precio = trim((string)($_POST['precio'] ?? ''));
    $idMascota = trim((string)($_POST['id_mascota'] ?? ''));
    $idVeterinario = trim((string)($_POST['id_veterinario'] ?? ''));

    $consultaMedica['fecha'] = $fecha;
    $consultaMedica['diagnostico'] = $diagnostico;
    $consultaMedica['precio'] = $precio;
    $consultaMedica['id_mascota'] = $idMascota;
    $consultaMedica['id_veterinario'] = $idVeterinario;

    if (
        $fecha === '' ||
        $diagnostico === '' ||
        $precio === '' ||
        $idMascota === '' ||
        $idVeterinario === ''
    ) {
        $error = 'Completá todos los campos.';

    } elseif (
        !ctype_digit($idMascota) ||
        !ctype_digit($idVeterinario) ||
        (int)$idMascota < 1 ||
        (int)$idVeterinario < 1
    ) {
        $error = 'Seleccioná una mascota y un veterinario válidos.';

    } elseif (
        !is_numeric($precio) ||
        !is_finite((float)$precio) ||
        (float)$precio < 0
    ) {
        $error = 'Ingresá un precio válido.';

    } elseif (strlen($diagnostico) > 5000) {
        $error = 'El diagnóstico es demasiado extenso.';

    } else {

        $fechaValida = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fecha
        );

        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !== $fecha ||
            $fecha > date('Y-m-d')
        ) {
            $error = 'Ingresá una fecha válida que no sea futura.';
        }

        if ($error === '') {
            try {
                $verificarMascota = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM mascotas
                     WHERE id_mascota = :id"
                );

                $verificarMascota->execute([
                    ':id' => (int)$idMascota
                ]);

                $verificarVeterinario = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM veterinarios
                     WHERE id_veterinario = :id"
                );

                $verificarVeterinario->execute([
                    ':id' => (int)$idVeterinario
                ]);

                if (
                    (int)$verificarMascota->fetchColumn() === 0 ||
                    (int)$verificarVeterinario->fetchColumn() === 0
                ) {
                    $error = 'La mascota o el veterinario no existen.';

                } else {
                    $actualizar = $conexion->prepare(
                        "UPDATE consultas
                         SET fecha = :fecha,
                             diagnostico = :diagnostico,
                             precio = :precio,
                             id_mascota = :id_mascota,
                             id_veterinario = :id_veterinario
                         WHERE id_consulta = :id"
                    );

                    $actualizar->execute([
                        ':fecha' => $fecha,
                        ':diagnostico' => $diagnostico,
                        ':precio' => $precio,
                        ':id_mascota' => (int)$idMascota,
                        ':id_veterinario' => (int)$idVeterinario,
                        ':id' => $id
                    ]);

                    header('Location: index.php?actualizado=1');
                    exit;
                }

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'No se pudo actualizar la consulta.';
            }
        }
    }
}

$tituloPagina = 'Editar consulta médica';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
        Volver a consultas
    </a>
</div>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-pencil-square"></i>
        Editar consulta médica 🐾
    </h1>

    <div class="card">
        <div class="card-body p-4">

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="editar.php?id=<?= (int)$id ?>">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="fecha" class="form-label">
                            Fecha
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            id="fecha"
                            name="fecha"
                            max="<?= date('Y-m-d') ?>"
                            value="<?= escapar($consultaMedica['fecha']) ?>"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label for="id_mascota" class="form-label">
                            Mascota
                        </label>

                        <select
                            class="form-select"
                            id="id_mascota"
                            name="id_mascota"
                            required>

                            <?php foreach ($mascotas as $mascota): ?>
                                <option
                                    value="<?= (int)$mascota['id_mascota'] ?>"
                                    <?= (string)$mascota['id_mascota'] ===
                                        (string)$consultaMedica['id_mascota']
                                        ? 'selected' : '' ?>>

                                    <?= escapar($mascota['nombre']) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="id_veterinario" class="form-label">
                            Veterinario
                        </label>

                        <select
                            class="form-select"
                            id="id_veterinario"
                            name="id_veterinario"
                            required>

                            <?php foreach ($veterinarios as $veterinario): ?>
                                <option
                                    value="<?= (int)$veterinario['id_veterinario'] ?>"
                                    <?= (string)$veterinario['id_veterinario'] ===
                                        (string)$consultaMedica['id_veterinario']
                                        ? 'selected' : '' ?>>

                                    <?= escapar(
                                        $veterinario['apellido'] . ', ' .
                                        $veterinario['nombre']
                                    ) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="diagnostico" class="form-label">
                            Diagnóstico
                        </label>

                        <textarea
                            class="form-control"
                            id="diagnostico"
                            name="diagnostico"
                            rows="4"
                            maxlength="5000"
                            required><?= escapar($consultaMedica['diagnostico']) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="precio" class="form-label">
                            Precio ($)
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="precio"
                            name="precio"
                            min="0"
                            step="0.01"
                            value="<?= escapar($consultaMedica['precio']) ?>"
                            required>
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">

                    <button type="submit" class="btn btn-petcare">
                        <i class="bi bi-floppy-fill"></i>
                        Guardar cambios
                    </button>

                    <a href="index.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>
    </div>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
