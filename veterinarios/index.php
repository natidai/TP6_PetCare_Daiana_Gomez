
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$error = '';
$datos = [
    'matricula' => '',
    'nombre' => '',
    'apellido' => '',
    'especialidad' => ''
];

// Registrar veterinario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim((string)($_POST[$campo] ?? ''));
    }

    if (
        $datos['matricula'] === '' ||
        $datos['nombre'] === '' ||
        $datos['apellido'] === ''
    ) {
        $error = 'Completá matrícula, nombre y apellido.';
    } elseif (
        strlen($datos['matricula']) > 30 ||
        strlen($datos['nombre']) > 100 ||
        strlen($datos['apellido']) > 100 ||
        strlen($datos['especialidad']) > 100
    ) {
        $error = 'Uno de los campos supera la longitud permitida.';
    } else {
        try {
            $verificar = $conexion->prepare(
                "SELECT COUNT(*)
                 FROM veterinarios
                 WHERE matricula = :matricula"
            );

            $verificar->execute([
                ':matricula' => $datos['matricula']
            ]);

            if ((int)$verificar->fetchColumn() > 0) {
                $error = 'Ya existe un veterinario con esa matrícula.';
            } else {
                $insertar = $conexion->prepare(
                    "INSERT INTO veterinarios
                     (matricula, nombre, apellido, especialidad)
                     VALUES
                     (:matricula, :nombre, :apellido, :especialidad)"
                );

                $insertar->execute([
                    ':matricula' => $datos['matricula'],
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':especialidad' => $datos['especialidad']
                ]);

                header('Location: index.php?creado=1');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo registrar el veterinario.';
        }
    }
}

// Listar veterinarios.
$veterinarios = $conexion->query(
    "SELECT id_veterinario, matricula, nombre,
            apellido, especialidad
     FROM veterinarios
     ORDER BY apellido, nombre"
)->fetchAll(PDO::FETCH_ASSOC);

$tituloPagina = 'Gestión de veterinarios';
require __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-person-hearts"></i>
        Gestión de Veterinarios 🐾
    </h1>

    <?php if (isset($_GET['creado'])): ?>
        <div class="alert alert-success">
            Veterinario registrado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['actualizado'])): ?>
        <div class="alert alert-success">
            Veterinario actualizado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success">
            Veterinario eliminado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['con_consultas'])): ?>
        <div class="alert alert-warning">
            No se puede eliminar este veterinario porque
            tiene consultas médicas registradas.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['no_encontrado'])): ?>
        <div class="alert alert-warning">
            El veterinario seleccionado no existe.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error_eliminar'])): ?>
        <div class="alert alert-danger">
            No se pudo eliminar el veterinario.
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-plus-circle"></i>
                Registrar nuevo veterinario
            </h2>

            <form method="POST" action="index.php">

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
                            value="<?= escapar($datos['matricula']) ?>"
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
                            value="<?= escapar($datos['nombre']) ?>"
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
                            value="<?= escapar($datos['apellido']) ?>"
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
                            value="<?= escapar($datos['especialidad']) ?>"
                            placeholder="Ej.: Clínica general">
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-petcare">
                        <i class="bi bi-floppy-fill"></i>
                        Guardar veterinario
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-list-ul"></i>
                Veterinarios registrados
            </h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Especialidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($veterinarios as $veterinario): ?>
                            <tr>
                                <td>
                                    <?= escapar($veterinario['matricula']) ?>
                                </td>
                                <td>
                                    <?= escapar($veterinario['nombre']) ?>
                                </td>
                                <td>
                                    <?= escapar($veterinario['apellido']) ?>
                                </td>
                                <td>
                                    <?= escapar($veterinario['especialidad']) ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">

                                        <a
                                            href="editar.php?id=<?= (int)$veterinario['id_veterinario'] ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="eliminar.php"
                                            onsubmit="return confirm('¿Eliminar este veterinario?');">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$veterinario['id_veterinario'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($veterinarios)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Todavía no hay veterinarios registrados.
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
