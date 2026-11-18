
<?php
require_once __DIR__ . '/config/Conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim((string)($_POST['email'] ?? ''));
    $contrasena = (string)($_POST['contrasena'] ?? '');

    if ($email === '' || $contrasena === '') {
        $error = 'Ingresá tu correo y contraseña.';

    } else {
        try {
            $buscar = $conexion->prepare(
                "SELECT id_usuario, nombre, apellido,
                        email, contrasena, rol, activo
                 FROM usuarios
                 WHERE email = :email
                 LIMIT 1"
            );

            $buscar->execute([':email' => $email]);
            $usuario = $buscar->fetch(PDO::FETCH_ASSOC);

            if (
                !$usuario ||
                !password_verify(
                    $contrasena,
                    $usuario['contrasena']
                ) ||
                (int)$usuario['activo'] !== 1
            ) {
                $error = 'Correo o contraseña incorrectos.';

            } else {
                // Renovar el identificador de sesión.
                session_regenerate_id(true);

                $_SESSION['id_usuario'] =
                    (int)$usuario['id_usuario'];

                $_SESSION['nombre'] =
                    $usuario['nombre'];

                $_SESSION['apellido'] =
                    $usuario['apellido'];

                $_SESSION['email'] =
                    $usuario['email'];

                $_SESSION['rol'] =
                    $usuario['rol'];

                header('Location: index.php');
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo iniciar sesión.';
        }
    }
}

$tituloPagina = 'Iniciar sesión';
require __DIR__ . '/includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-box-arrow-in-right"></i>
        Iniciar sesión 🐾
    </h1>

    <div class="card mx-auto" style="max-width: 550px;">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                Ingresar a PetCare
            </h2>

            <?php if (isset($_GET['registrado'])): ?>
                <div class="alert alert-success">
                    Cuenta creada correctamente.
                    Ya podés iniciar sesión.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sesion_requerida'])): ?>
                <div class="alert alert-warning">
                    Debés iniciar sesión para continuar.
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <div class="mb-3">
                    <label for="email" class="form-label">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= escapar($email) ?>"
                        autocomplete="username"
                        required>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        class="form-control"
                        id="contrasena"
                        name="contrasena"
                        autocomplete="current-password"
                        required>
                </div>

                <button type="submit" class="btn btn-petcare">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Ingresar
                </button>

            </form>

            <p class="mt-4 mb-0">
                ¿No tenés cuenta?
                <a href="registro.php">Registrate</a>
            </p>

        </div>
    </div>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
