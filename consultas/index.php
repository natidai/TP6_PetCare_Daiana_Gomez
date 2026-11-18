<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Auth.php';
requerirRol(['administrador', 'veterinario']);
$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$error = '';

$datos = [
    'fecha' => date('Y-m-d'),
    'diagnostico' => '',
    'precio' => '',
    'id_mascota' => '',
    'id_veterinario' => ''
];

// Obtener mascotas y veterinarios.
$mascotas = $conexion->query(
    "SELECT m.id_mascota, m.nombre,
            c.nombre AS nombre_cliente,
            c.apellido AS apellido_cliente
     FROM mascotas m
     INNER JOIN clientes c
        ON m.id_cliente = c.id_cliente
     ORDER BY m.nombre, c.apellido"
)->fetchAll(PDO::FETCH_ASSOC);

$veterinarios = $conexion->query(
    "SELECT id_veterinario, nombre, apellido, matricula
     FROM veterinarios
     ORDER BY apellido, nombre"
)->fetchAll(PDO::FETCH_ASSOC);

// Registrar consulta médica.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim((string)($_POST[$campo] ?? ''));
    }

    if (
        $datos['fecha'] === '' ||
        $datos['diagnostico'] === '' ||
        $datos['precio'] === '' ||
        $datos['id_mascota'] === '' ||
        $datos['id_veterinario'] === ''
    ) {
        $error = 'Completá todos los campos obligatorios.';

    } elseif (
        !ctype_digit($datos['id_mascota']) ||
        !ctype_digit($datos['id_veterinario']) ||
        (int)$datos['id_mascota'] < 1 ||
        (int)$datos['id_veterinario'] < 1
    ) {
        $error = 'Seleccioná una mascota y un veterinario válidos.';

    } elseif (
        !is_numeric($datos['precio']) ||
        !is_finite((float)$datos['precio']) ||
        (float)$datos['precio'] < 0
    ) {
        $error = 'Ingresá un precio válido, mayor o igual a cero.';

    } elseif (strlen($datos['diagnostico']) > 5000) {
        $error = 'El diagnóstico es demasiado extenso.';

    } else {

        $fecha = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $datos['fecha']
        );

        if (
            !$fecha ||
            $fecha->format('Y-m-d') !== $datos['fecha'] ||
            $datos['fecha'] > date('Y-m-d')
        ) {
            $error = 'Ingresá una fecha válida que no sea futura.';
        }

        if ($error === '') {
            try {
                // Verificar existencia de la mascota.
                $verificarMascota = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM mascotas
                     WHERE id_mascota = :id"
                );

                $verificarMascota->execute([
                    ':id' => (int)$datos['id_mascota']
                ]);

                // Verificar existencia del veterinario.
                $verificarVeterinario = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM veterinarios
                     WHERE id_veterinario = :id"
                );

                $verificarVeterinario->execute([
                    ':id' => (int)$datos['id_veterinario']
                ]);

                if (
                    (int)$verificarMascota->fetchColumn() === 0 ||
                    (int)$verificarVeterinario->fetchColumn() === 0
                ) {
                    $error = 'La mascota o el veterinario no existen.';

                } else {
                    $insertar = $conexion->prepare(
                        "INSERT INTO consultas
                         (fecha, diagnostico, precio,
                          id_mascota, id_veterinario)
                         VALUES
                         (:fecha, :diagnostico, :precio,
                          :id_mascota, :id_veterinario)"
                    );

                    $insertar->execute([
                        ':fecha' => $datos['fecha'],
                        ':diagnostico' => $datos['diagnostico'],
                        ':precio' => $datos['precio'],
                        ':id_mascota' => (int)$datos['id_mascota'],
                        ':id_veterinario' => (int)$datos['id_veterinario']
                    ]);

                    header('Location: index.php?creado=1');
                    exit;
                }

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'No se pudo registrar la consulta médica.';
            }
        }
    }
}

// Filtro de historial por mascota.
$filtroMascota = filter_input(
    INPUT_GET,
    'id_mascota',
    FILTER_VALIDATE_INT
);

$sql = "
    SELECT
        co.id_consulta,
        co.fecha,
        co.diagnostico,
        co.precio,
        co.id_mascota,
        m.nombre AS mascota,
        c.nombre AS nombre_cliente,
        c.apellido AS apellido_cliente,
        v.nombre AS nombre_veterinario,
        v.apellido AS apellido_veterinario
    FROM consultas co
    INNER JOIN mascotas m
        ON co.id_mascota = m.id_mascota
    INNER JOIN clientes c
        ON m.id_cliente = c.id_cliente
    INNER JOIN veterinarios v
        ON co.id_veterinario = v.id_veterinario
";

if ($filtroMascota && $filtroMascota > 0) {
    $sql .= " WHERE co.id_mascota = :id_mascota";
}

$sql .= " ORDER BY co.fecha DESC, co.id_consulta DESC";

$consulta = $conexion->prepare($sql);

if ($filtroMascota && $filtroMascota > 0) {
    $consulta->execute([
        ':id_mascota' => $filtroMascota
    ]);
} else {
    $consulta->execute();
}

$consultas = $consulta->fetchAll(PDO::FETCH_ASSOC);

$tituloPagina = 'Gestión de consultas médicas';
require __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-clipboard2-pulse"></i>
        Consultas médicas 🐾
    </h1>

    <?php if (isset($_GET['creado'])): ?>
        <div class="alert alert-success">
            Consulta médica registrada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['actualizado'])): ?>
        <div class="alert alert-success">
            Consulta médica actualizada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success">
            Consulta médica eliminada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error_eliminar'])): ?>
        <div class="alert alert-danger">
            No se pudo eliminar la consulta médica.
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de registro -->
    <div class="card mb-4">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-plus-circle"></i>
                Registrar nueva consulta
            </h2>

            <form method="POST" action="index.php">

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
                            value="<?= escapar($datos['fecha']) ?>"
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

                            <option value="">
                                Seleccionar mascota
                            </option>

                            <?php foreach ($mascotas as $mascota): ?>
                                <option
                                    value="<?= (int)$mascota['id_mascota'] ?>"
                                    <?= (string)$mascota['id_mascota'] ===
                                        $datos['id_mascota']
                                        ? 'selected' : '' ?>>

                                    <?= escapar(
                                        $mascota['nombre'] . ' — ' .
                                        $mascota['apellido_cliente'] . ', ' .
                                        $mascota['nombre_cliente']
                                    ) ?>

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

                            <option value="">
                                Seleccionar veterinario
                            </option>

                            <?php foreach ($veterinarios as $veterinario): ?>
                                <option
                                    value="<?= (int)$veterinario['id_veterinario'] ?>"
                                    <?= (string)$veterinario['id_veterinario'] ===
                                        $datos['id_veterinario']
                                        ? 'selected' : '' ?>>

                                    <?= escapar(
                                        $veterinario['apellido'] . ', ' .
                                        $veterinario['nombre'] . ' — Mat. ' .
                                        $veterinario['matricula']
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
                            rows="3"
                            maxlength="5000"
                            required><?= escapar($datos['diagnostico']) ?></textarea>
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
                            value="<?= escapar($datos['precio']) ?>"
                            required>
                    </div>

                </div>

                <div class="mt-4">
                    <button
                        type="submit"
                        class="btn btn-petcare"
                        <?= empty($mascotas) || empty($veterinarios)
                            ? 'disabled' : '' ?>>

                        <i class="bi bi-floppy-fill"></i>
                        Guardar consulta
                    </button>
                </div>

                <?php if (empty($mascotas) || empty($veterinarios)): ?>
                    <p class="text-danger mt-3">
                        Para registrar una consulta, primero debe
                        existir al menos una mascota y un veterinario.
                    </p>
                <?php endif; ?>

            </form>

        </div>
    </div>

    <!-- Historial médico -->
    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-journal-medical"></i>
                Historial de consultas
            </h2>

            <form method="GET" action="index.php" class="mb-4">
                <div class="row g-3 align-items-end">

                    <div class="col-md-8">
                        <label for="filtro_mascota" class="form-label">
                            Filtrar historial por mascota
                        </label>

                        <select
                            class="form-select"
                            id="filtro_mascota"
                            name="id_mascota">

                            <option value="">
                                Todas las mascotas
                            </option>

                            <?php foreach ($mascotas as $mascota): ?>
                                <option
                                    value="<?= (int)$mascota['id_mascota'] ?>"
                                    <?= (int)$mascota['id_mascota'] ===
                                        (int)$filtroMascota
                                        ? 'selected' : '' ?>>

                                    <?= escapar(
                                        $mascota['nombre'] . ' — ' .
                                        $mascota['apellido_cliente'] . ', ' .
                                        $mascota['nombre_cliente']
                                    ) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-petcare">
                            <i class="bi bi-search"></i>
                            Buscar
                        </button>

                        <a
                            href="index.php"
                            class="btn btn-outline-secondary">
                            Limpiar
                        </a>
                    </div>

                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Mascota</th>
                            <th>Propietario</th>
                            <th>Veterinario</th>
                            <th>Diagnóstico</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($consultas as $consultaMedica): ?>
                            <tr>
                                <td>
                                    <?= escapar($consultaMedica['fecha']) ?>
                                </td>

                                <td>
                                    <?= escapar($consultaMedica['mascota']) ?>
                                </td>

                                <td>
                                    <?= escapar(
                                        $consultaMedica['apellido_cliente'] .
                                        ', ' .
                                        $consultaMedica['nombre_cliente']
                                    ) ?>
                                </td>

                                <td>
                                    <?= escapar(
                                        $consultaMedica['apellido_veterinario'] .
                                        ', ' .
                                        $consultaMedica['nombre_veterinario']
                                    ) ?>
                                </td>

                                <td>
                                    <?= nl2br(escapar(
                                        $consultaMedica['diagnostico']
                                    )) ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float)$consultaMedica['precio'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">

                                        <a
                                            href="editar.php?id=<?= (int)$consultaMedica['id_consulta'] ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>
                                        <?php if (tieneRol(['administrador'])): ?>
                                        <form
                                            method="POST"
                                            action="eliminar.php"
                                            onsubmit="return confirm('¿Eliminar esta consulta médica?');">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$consultaMedica['id_consulta'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($consultas)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No se encontraron consultas médicas.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
