
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de cliente inválido.');
}

$consulta = $conexion->prepare(
    "SELECT id_cliente, dni, nombre, apellido, telefono
     FROM clientes WHERE id_cliente = :id"
);
$consulta->execute([':id' => $id]);
$cliente = $consulta->fetch();

if (!$cliente) {
    http_response_code(404);
    exit('Cliente no encontrado.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    $cliente['dni'] = $dni;
    $cliente['nombre'] = $nombre;
    $cliente['apellido'] = $apellido;
    $cliente['telefono'] = $telefono;

    if ($dni === '' || $nombre === '' || $apellido === '') {
        $error = 'Completá DNI, nombre y apellido.';
    } elseif (!ctype_digit($dni)) {
        $error = 'El DNI debe contener solamente números.';
    } else {
        try {
            $actualizar = $conexion->prepare(
                "UPDATE clientes
                 SET dni = :dni,
                     nombre = :nombre,
                     apellido = :apellido,
                     telefono = :telefono
                 WHERE id_cliente = :id"
            );

            $actualizar->execute([
                ':dni' => $dni,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':telefono' => $telefono,
                ':id' => $id
            ]);

            header('Location: index.php?actualizado=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudieron actualizar los datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar cliente | PetCare</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #fff7fb;
            color: #55334a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar { background: #c04483; }
        .titulo { color: #9d2864; }
        .card {
            border: 1px solid #f3d4e3;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(157,40,100,.07);
        }
        .btn-petcare {
            background: #c04483;
            color: white;
        }
        .btn-petcare:hover {
            background: #9d2864;
            color: white;
        }
        main { flex: 1; }
        footer {
            background: #f8d9e8;
            border-top: 3px solid #c04483;
            text-align: center;
            padding: 20px;
        }
        footer p {
            margin-bottom: 5px;
            font-size: .9rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">
            🐾 PetCare
        </a>
        <a href="index.php" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Volver a clientes
        </a>
    </div>
</nav>

<main class="container py-5">
    <h1 class="titulo fw-bold mb-4">
        <i class="bi bi-pencil-square"></i>
        Editar cliente 🐾
    </h1>

    <div class="card">
        <div class="card-body p-4">
            <h2 class="h5 titulo mb-4">
                Modificar datos del cliente
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= escapar($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="editar.php?id=<?= (int)$id ?>">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input class="form-control"
                               type="text"
                               id="dni"
                               name="dni"
                               maxlength="15"
                               value="<?= escapar($cliente['dni']) ?>"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input class="form-control"
                               type="text"
                               id="nombre"
                               name="nombre"
                               maxlength="100"
                               value="<?= escapar($cliente['nombre']) ?>"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input class="form-control"
                               type="text"
                               id="apellido"
                               name="apellido"
                               maxlength="100"
                               value="<?= escapar($cliente['apellido']) ?>"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="telefono" class="form-label">
                            Teléfono
                        </label>
                        <input class="form-control"
                               type="text"
                               id="telefono"
                               name="telefono"
                               maxlength="30"
                               value="<?= escapar($cliente['telefono']) ?>">
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

<footer>
    <p class="fw-bold">Trabajo Práctico N.º 6 – PetCare</p>
    <p>Prácticas Profesionalizantes I</p>
    <p>Profesor: Galo Nahuel Olguín Fernández</p>
    <p>Instituto Superior de Formación Docente y Técnica N.º 24</p>
    <p class="fw-bold">Desarrollado por Daiana Gómez</p>
    <p>© 2026 – PetCare | Gestión Veterinaria</p>
</footer>

</body>
</html>
