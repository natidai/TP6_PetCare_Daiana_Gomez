<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Auth.php';
requerirRol(['administrador']);
$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de mascota inválido.');
}

// Buscar la mascota que se desea editar.
$consulta = $conexion->prepare(
    "SELECT id_mascota, nombre, especie, raza,
            fecha_nacimiento, id_cliente
     FROM mascotas
     WHERE id_mascota = :id"
);

$consulta->execute([':id' => $id]);
$mascota = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    http_response_code(404);
    exit('Mascota no encontrada.');
}

$error = '';

// Obtener propietarios para el formulario.
$clientes = $conexion->query(
    "SELECT id_cliente, nombre, apellido
     FROM clientes
     ORDER BY apellido, nombre"
)->fetchAll(PDO::FETCH_ASSOC);

// Procesar la modificación.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $idCliente = trim((string)($_POST['id_cliente'] ?? ''));

    // Conservar los datos ingresados si ocurre un error.
    $mascota['nombre'] = $nombre;
    $mascota['especie'] = $especie;
    $mascota['raza'] = $raza;
    $mascota['fecha_nacimiento'] = $fechaNacimiento;
    $mascota['id_cliente'] = $idCliente;

    if ($nombre === '' || $especie === '' || $idCliente === '') {
        $error = 'Completá nombre, especie y propietario.';

    } elseif (
        !ctype_digit($idCliente) ||
        (int)$idCliente < 1
    ) {
        $error = 'Seleccioná un propietario válido.';

    } elseif (
        strlen($nombre) > 100 ||
        strlen($especie) > 100 ||
        strlen($raza) > 100
    ) {
        $error = 'Los datos ingresados superan la longitud permitida.';

    } else {

        // Validar fecha de nacimiento, si fue ingresada.
        if ($fechaNacimiento !== '') {
            $fecha = DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $fechaNacimiento
            );

            if (
                !$fecha ||
                $fecha->format('Y-m-d') !== $fechaNacimiento ||
                $fechaNacimiento > date('Y-m-d')
            ) {
                $error = 'Ingresá una fecha de nacimiento válida.';
            }
        }

        if ($error === '') {
            try {
                // Comprobar que el propietario exista.
                $verificar = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM clientes
                     WHERE id_cliente = :id_cliente"
                );

                $verificar->execute([
                    ':id_cliente' => (int)$idCliente
                ]);

                if ((int)$verificar->fetchColumn() === 0) {
                    $error = 'El propietario seleccionado no existe.';

                } else {
                    $actualizar = $conexion->prepare(
                        "UPDATE mascotas
                         SET nombre = :nombre,
                             especie = :especie,
                             raza = :raza,
                             fecha_nacimiento = :fecha_nacimiento,
                             id_cliente = :id_cliente
                         WHERE id_mascota = :id"
                    );

                    $actualizar->execute([
                        ':nombre' => $nombre,
                        ':especie' => $especie,
                        ':raza' => $raza !== '' ? $raza : null,
                        ':fecha_nacimiento' =>
                            $fechaNacimiento !== ''
                                ? $fechaNacimiento
                                : null,
                        ':id_cliente' => (int)$idCliente,
                        ':id' => $id
                    ]);

                    header('Location: index.php?actualizado=1');
                    exit;
                }

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'No se pudo actualizar la mascota.';
            }
        }
    }
}

// Encabezado compartido.
$tituloPagina = 'Editar mascota';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
        Volver a mascotas
    </a>
</div>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-pencil-square"></i>
        Editar mascota 🐾
    </h1>

    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                Modificar datos de la mascota
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="editar.php?id=<?= (int)$id ?>">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="nombre" class="form-label">
                            Nombre
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            maxlength="100"
                            value="<?= escapar($mascota['nombre']) ?>"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label for="especie" class="form-label">
                            Especie
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="especie"
                            name="especie"
                            maxlength="100"
                            value="<?= escapar($mascota['especie']) ?>"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label for="raza" class="form-label">
                            Raza
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="raza"
                            name="raza"
                            maxlength="100"
                            value="<?= escapar($mascota['raza'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_nacimiento" class="form-label">
                            Fecha de nacimiento
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            max="<?= date('Y-m-d') ?>"
                            value="<?= escapar(
                                $mascota['fecha_nacimiento'] ?? ''
                            ) ?>">
                    </div>

                    <div class="col-md-8">
                        <label for="id_cliente" class="form-label">
                            Propietario
                        </label>

                        <select
                            class="form-select"
                            id="id_cliente"
                            name="id_cliente"
                            required>

                            <option value="">
                                Seleccionar propietario
                            </option>

                            <?php foreach ($clientes as $cliente): ?>
                                <option
                                    value="<?= (int)$cliente['id_cliente'] ?>"
                                    <?= (string)$cliente['id_cliente'] ===
                                        (string)$mascota['id_cliente']
                                        ? 'selected' : '' ?>>

                                    <?= escapar(
                                        $cliente['apellido'] . ', ' .
                                        $cliente['nombre']
                                    ) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">

                    <button type="submit" class="btn btn-petcare">
                        <i class="bi bi-floppy-fill"></i>
                        Guardar cambios
                    </button>

                    <a href="index.php"
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
