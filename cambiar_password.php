<?php
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/config/Auth.php';

requerirSesion();

$conexion = conectarBD();
$error = '';
$mensaje = '';

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['contrasena_actual'] ?? '';
    $nueva = $_POST['contrasena_nueva'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    if ($actual === '' || $nueva === '' || $confirmar === '') {
        $error = 'Completá todos los campos.';
    } elseif ($nueva !== $confirmar) {
        $error = 'Las contraseñas nuevas no coinciden.';
    } elseif (strlen($nueva) < 8) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $buscar = $conexion->prepare(
                "SELECT contrasena
                 FROM usuarios
                 WHERE id_usuario = :id_usuario
                 AND activo = 1
                 LIMIT 1"
            );

            $buscar->execute([
                ':id_usuario' => $_SESSION['id_usuario']
            ]);

            $usuario = $buscar->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || !password_verify($actual, $usuario['contrasena'])) {
                $error = 'La contraseña actual es incorrecta.';
            } elseif (password_verify($nueva, $usuario['contrasena'])) {
                $error = 'La nueva contraseña debe ser diferente de la actual.';
            } else {
                $hash = password_hash($nueva, PASSWORD_DEFAULT);

                $actualizar = $conexion->prepare(
                    "UPDATE usuarios
                     SET contrasena = :contrasena
                     WHERE id_usuario = :id_usuario
                     AND activo = 1"
                );

                $actualizar->execute([
                    ':contrasena' => $hash,
                    ':id_usuario' => $_SESSION['id_usuario']
                ]);

                $mensaje = 'Contraseña actualizada correctamente.';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo actualizar la contraseña.';
        }
    }
}

$tituloPagina = 'Cambiar contraseña';
require __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="card-body p-4">

                    <h1 class="titulo h3 fw-bold mb-4">
                        <i class="bi bi-shield-lock-fill"></i>
                        Cambiar contraseña
                    </h1>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= escapar($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($mensaje !== ''): ?>
                        <div class="alert alert-success">
                            <?= escapar($mensaje) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="cambiar_password.php">

                        <div class="mb-3">
                            <label class="form-label" for="contrasena_actual">
                                Contraseña actual
                            </label>
                            <input type="password"
                                   class="form-control"
                                   id="contrasena_actual"
                                   name="contrasena_actual"
                                   autocomplete="current-password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="contrasena_nueva">
                                Nueva contraseña
                            </label>
                            <input type="password"
                                   class="form-control"
                                   id="contrasena_nueva"
                                   name="contrasena_nueva"
                                   minlength="8"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="confirmar_contrasena">
                                Confirmar nueva contraseña
                            </label>
                            <input type="password"
                                   class="form-control"
                                   id="confirmar_contrasena"
                                   name="confirmar_contrasena"
                                   minlength="8"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-petcare">
                            <i class="bi bi-check-circle"></i>
                            Guardar nueva contraseña
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
