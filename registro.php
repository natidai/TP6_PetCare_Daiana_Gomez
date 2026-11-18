
<?php
require_once __DIR__ . '/config/Conexion.php';

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$error = '';
$datos = [
    'nombre' => '',
    'apellido' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim((string)($_POST[$campo] ?? ''));
    }

    $contrasena = (string)($_POST['contrasena'] ?? '');
    $confirmacion = (string)($_POST['confirmacion'] ?? '');

    if (
        $datos['nombre'] === '' ||
        $datos['apellido'] === '' ||
        $datos['email'] === '' ||
        $contrasena === '' ||
        $confirmacion === ''
    ) {
        $error = 'Completá todos los campos.';

    } elseif (
        mb_strlen($datos['nombre']) > 100 ||
        mb_strlen($datos['apellido']) > 100 ||
        strlen($datos['email']) > 150
    ) {
        $error = 'Uno de los campos supera la longitud permitida.';

    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresá un correo electrónico válido.';

    } elseif (strlen($contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';

    } elseif ($contrasena !== $confirmacion) {
        $error = 'Las contraseñas no coinciden.';

    } else {
        try {
            $verificar = $conexion->prepare(
                "SELECT COUNT(*)
                 FROM usuarios
                 WHERE email = :email"
            );

            $verificar->execute([
                ':email' => $datos['email']
            ]);

            if ((int)$verificar->fetchColumn() > 0) {
                $error = 'Ya existe una cuenta con ese correo.';

            } else {
                // Proteger la contraseña antes de almacenarla.
                $hash = password_hash(
                    $contrasena,
                    PASSWORD_DEFAULT
                );

                $insertar = $conexion->prepare(
                    "INSERT INTO usuarios
                     (nombre, apellido, email, contrasena, rol, activo)
                     VALUES
                     (:nombre, :apellido, :email,
                      :contrasena, 'usuario', 1)"
                );

                $insertar->execute([
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':email' => $datos['email'],
                    ':contrasena' => $hash
                ]);

                header('Location: login.php?registrado=1');
                exit;
            }

   } catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No se pudo completar el registro.';
}
    }
}

$tituloPagina = 'Registro de usuarios';
require __DIR__ . '/includes/header.php';
?>

<main class="container py-5">

    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-person-plus"></i>
        Crear cuenta en PetCare 🐾
    </h1>

    <div class="card mx-auto" style="max-width: 650px;">
        <div class="card-body p-4">

            <h2 class="h5 titulo mb-4">
                Registro de usuario
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="registro.php">

                <div class="row g-3">

                    <div class="col-md-6">
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

                    <div class="col-md-6">
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

                    <div class="col-12">
                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="150"
                            value="<?= escapar($datos['email']) ?>"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="contrasena" class="form-label">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="contrasena"
                            name="contrasena"
                            minlength="8"
                            autocomplete="new-password"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="confirmacion" class="form-label">
                            Confirmar contraseña
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="confirmacion"
                            name="confirmacion"
                            minlength="8"
                            autocomplete="new-password"
                            required>
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-petcare">
                        <i class="bi bi-person-check"></i>
                        Registrarme
                    </button>
                </div>

            </form>

            <p class="mt-4 mb-0">
                ¿Ya tenés una cuenta?
                <a href="login.php">Iniciar sesión</a>
            </p>

        </div>
    </div>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
