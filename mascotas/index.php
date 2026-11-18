<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Auth.php';
requerirRol(['administrador', 'veterinario', 'usuario']);
$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$error = '';
$datos = [
    'nombre' => '',
    'especie' => '',
    'raza' => '',
    'fecha_nacimiento' => '',
    'id_cliente' => ''
];

// Registrar una mascota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requerirRol(['administrador']);

    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim((string)($_POST[$campo] ?? ''));
    }

    if (
        $datos['nombre'] === '' ||
        $datos['especie'] === '' ||
        $datos['id_cliente'] === ''
    ) {
        $error = 'Completá nombre, especie y propietario.';
    } elseif (
        !ctype_digit($datos['id_cliente']) ||
        (int)$datos['id_cliente'] < 1
    ) {
        $error = 'Seleccioná un propietario válido.';
    } else {
        try {
            // Comprobar que el propietario exista
            $verificar = $conexion->prepare(
                "SELECT COUNT(*) FROM clientes
                 WHERE id_cliente = :id_cliente"
            );
            $verificar->execute([
                ':id_cliente' => (int)$datos['id_cliente']
            ]);

            if ((int)$verificar->fetchColumn() === 0) {
                $error = 'El propietario seleccionado no existe.';
            } else {
                $insertar = $conexion->prepare(
                    "INSERT INTO mascotas
                     (nombre, especie, raza, fecha_nacimiento, id_cliente)
                     VALUES
                     (:nombre, :especie, :raza, :fecha_nacimiento, :id_cliente)"
                );

                $insertar->execute([
                    ':nombre' => $datos['nombre'],
                    ':especie' => $datos['especie'],
                    ':raza' => $datos['raza'] !== ''
                        ? $datos['raza'] : null,
                    ':fecha_nacimiento' =>
                        $datos['fecha_nacimiento'] !== ''
                        ? $datos['fecha_nacimiento'] : null,
                    ':id_cliente' => (int)$datos['id_cliente']
                ]);

                header('Location: index.php?creado=1');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo registrar la mascota.';
        }
    }
}

// Obtener propietarios
$clientes = $conexion->query(
    "SELECT id_cliente, nombre, apellido
     FROM clientes
     ORDER BY apellido, nombre"
)->fetchAll();

// Obtener mascotas y sus propietarios
$mascotas = $conexion->query(
    "SELECT m.id_mascota,
            m.nombre,
            m.especie,
            m.raza,
            m.fecha_nacimiento,
            m.id_cliente,
            c.nombre AS nombre_cliente,
            c.apellido AS apellido_cliente
     FROM mascotas m
     INNER JOIN clientes c
         ON m.id_cliente = c.id_cliente
     ORDER BY m.id_mascota DESC"
)->fetchAll();

$tituloPagina = 'Gestión de mascotas';
require __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-heart-fill"></i>
        Gestión de Mascotas 🐾
    </h1>

    <?php if (isset($_GET['creado'])): ?>
        <div class="alert alert-success">
            Mascota registrada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['actualizado'])): ?>
        <div class="alert alert-success">
            Mascota actualizada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success">
            Mascota eliminada correctamente.
        </div>
    <?php endif; ?>

<?php if (isset($_GET['con_consultas'])): ?>
    <div class="alert alert-warning">
        No se puede eliminar esta mascota porque tiene
        consultas médicas registradas.
    </div>
<?php endif; ?>

<?php if (isset($_GET['no_encontrado'])): ?>
    <div class="alert alert-warning">
        La mascota seleccionada no existe.
    </div>
<?php endif; ?>

<?php if (isset($_GET['error_eliminar'])): ?>
    <div class="alert alert-danger">
        No se pudo eliminar la mascota.
    </div>
<?php endif; ?>


    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>
    <?php if (tieneRol(['administrador'])): ?>
    <div class="card mb-4">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-plus-circle"></i>
                Registrar nueva mascota
            </h2>

            <form method="POST" action="index.php">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="nombre" class="form-label">
                            Nombre
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nombre"
                               name="nombre"
                               maxlength="100"
                               value="<?= escapar($datos['nombre']) ?>"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label for="especie" class="form-label">
                            Especie
                        </label>
                        <input type="text"
                               class="form-control"
                               id="especie"
                               name="especie"
                               maxlength="100"
                               value="<?= escapar($datos['especie']) ?>"
                               placeholder="Ej.: Perro, Gato"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label for="raza" class="form-label">
                            Raza
                        </label>
                        <input type="text"
                               class="form-control"
                               id="raza"
                               name="raza"
                               maxlength="100"
                               value="<?= escapar($datos['raza']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_nacimiento" class="form-label">
                            Fecha de nacimiento
                        </label>
                        <input type="date"
                               class="form-control"
                               id="fecha_nacimiento"
                               name="fecha_nacimiento"
                               max="<?= date('Y-m-d') ?>"
                               value="<?= escapar($datos['fecha_nacimiento']) ?>">
                    </div>

                    <div class="col-md-8">
                        <label for="id_cliente" class="form-label">
                            Propietario
                        </label>
                        <select class="form-select"
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
                                        $datos['id_cliente']
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

                <div class="mt-4">
                    <button type="submit" class="btn btn-petcare"
                        <?= empty($clientes) ? 'disabled' : '' ?>>
                        <i class="bi bi-floppy-fill"></i>
                        Guardar mascota
                    </button>
                </div>

                <?php if (empty($clientes)): ?>
                    <p class="text-danger mt-3">
                        Primero debés registrar un cliente.
                    </p>
                <?php endif; ?>

            </form>

        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-list-ul"></i>
                Mascotas registradas
            </h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Especie</th>
                            <th>Raza</th>
                            <th>Nacimiento</th>
                            <th>Propietario</th>
                            <?php if (tieneRol(['administrador'])): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($mascotas as $mascota): ?>
                            <tr>
                                <td>
                                    <?= escapar($mascota['nombre']) ?>
                                </td>
                                <td>
                                    <?= escapar($mascota['especie']) ?>
                                </td>
                                <td>
                                    <?= escapar($mascota['raza'] ?? '-') ?>
                                </td>
                                <td>
                                    <?= escapar(
                                        $mascota['fecha_nacimiento'] ?? '-'
                                    ) ?>
                                </td>
                                <td>
                                    <?= escapar(
                                        $mascota['apellido_cliente'] . ', ' .
                                        $mascota['nombre_cliente']
                                    ) ?>
                                </td>
                                <?php if (tieneRol(['administrador'])): ?>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-outline-primary btn-sm"
                                           href="editar.php?id=<?= (int)$mascota['id_mascota'] ?>">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="eliminar.php"
                                              onsubmit="return confirm('¿Eliminar esta mascota?');">
                                            <input type="hidden"
                                                   name="id"
                                                   value="<?= (int)$mascota['id_mascota'] ?>">

                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($mascotas)): ?>
                            <tr>
                                <td colspan="<?= tieneRol(['administrador']) ? 6 : 5 ?>" class="text-center py-4">
                                    Todavía no hay mascotas registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</main>

<?php
require __DIR__ . '/../includes/footer.php';
?>
