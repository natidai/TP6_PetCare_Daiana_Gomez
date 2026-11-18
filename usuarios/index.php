<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Auth.php';

requerirRol(['administrador']);

$conexion = conectarBD();
$error = '';
$mensaje = '';

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$rolesPermitidos = ['administrador', 'veterinario', 'usuario'];

// Actualizar el rol de un usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
    $rol = $_POST['rol'] ?? '';

    if (!$id || !in_array($rol, $rolesPermitidos, true)) {
        $error = 'Los datos ingresados no son válidos.';
    } elseif ($id === (int)$_SESSION['id_usuario']) {
        $error = 'No podés modificar tu propio rol.';
    } else {
        try {
            $actualizar = $conexion->prepare(
                "UPDATE usuarios
                 SET rol = :rol
                 WHERE id_usuario = :id_usuario"
            );
            $actualizar->execute([
                ':rol' => $rol,
                ':id_usuario' => $id
            ]);

            header('Location: index.php?actualizado=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo actualizar el rol.';
        }
    }
}

if (isset($_GET['actualizado'])) {
    $mensaje = 'Rol actualizado correctamente.';
}

try {
    $usuarios = $conexion->query(
        "SELECT id_usuario, nombre, email, rol
         FROM usuarios
         ORDER BY id_usuario ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $usuarios = [];
    $error = 'No se pudo obtener la lista de usuarios.';
}

$tituloPagina = 'Administración de usuarios';
require __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-people-fill"></i>
        Administración de Usuarios
    </h1>

    <?php if ($mensaje !== ''): ?>
        <div class="alert alert-success">
            <?= escapar($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                <i class="bi bi-person-gear"></i>
                Usuarios registrados
            </h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Rol actual</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <?= (int)$usuario['id_usuario'] ?>
                            </td>
                            <td>
                                <?= escapar($usuario['nombre']) ?>
                            </td>
                            <td>
                                <?= escapar($usuario['email']) ?>
                            </td>
                            <td>
                                <?= escapar($usuario['rol']) ?>
                            </td>
                            <td>
                                <?php if (
                                    (int)$usuario['id_usuario'] !==
                                    (int)$_SESSION['id_usuario']
                                ): ?>

                                <form method="POST"
                                      action="index.php"
                                      class="d-flex gap-2">

                                    <input type="hidden"
                                           name="id_usuario"
                                           value="<?= (int)$usuario['id_usuario'] ?>">

                                    <select name="rol"
                                            class="form-select form-select-sm">
                                        <?php foreach ($rolesPermitidos as $rol): ?>
                                            <option
                                                value="<?= escapar($rol) ?>"
                                                <?= $usuario['rol'] === $rol
                                                    ? 'selected' : '' ?>>
                                                <?= ucfirst($rol) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit"
                                            class="btn btn-petcare btn-sm">
                                        Guardar
                                    </button>
                                </form>

                                <?php else: ?>
                                    <span class="text-muted">
                                        Tu cuenta
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5"
                                class="text-center py-4">
                                No hay usuarios para mostrar.
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
