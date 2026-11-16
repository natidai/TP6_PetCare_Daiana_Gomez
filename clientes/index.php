
<?php
require_once __DIR__ . '/../config/Conexion.php';

$conexion = conectarBD();
$mensaje = '';
$error = '';

// Registrar un nuevo cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($dni === '' || $nombre === '' || $apellido === '') {
        $error = 'Completá DNI, nombre y apellido.';
    } elseif (!ctype_digit($dni)) {
        $error = 'El DNI debe contener solamente números.';
    } else {
        try {
            $sql = "INSERT INTO clientes
                    (dni, nombre, apellido, telefono)
                    VALUES (:dni, :nombre, :apellido, :telefono)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                ':dni' => $dni,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':telefono' => $telefono
            ]);

            header('Location: index.php?registrado=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo registrar el cliente.';
        }
    }
}

// Consultar clientes
$consulta = $conexion->query(
    "SELECT id_cliente, dni, nombre, apellido, telefono
     FROM clientes
     ORDER BY id_cliente DESC"
);

$clientes = $consulta->fetchAll();


if (isset($_GET['registrado'])) {
    $mensaje = 'Cliente registrado correctamente.';
}

if (isset($_GET['actualizado'])) {
    $mensaje = 'Los datos del cliente se actualizaron correctamente.';
}

if (isset($_GET['eliminado'])) {
    $mensaje = 'Cliente eliminado correctamente.';
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'mascotas':
            $error = 'No se puede eliminar este cliente porque tiene mascotas registradas.';
            break;

        case 'id':
            $error = 'El identificador del cliente no es válido.';
            break;

        case 'eliminar':
            $error = 'No se pudo eliminar el cliente. Intentá nuevamente.';
            break;
    }
}


function escapar($valor): string {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Clientes | PetCare</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #fff7fb;
            color: #55334a;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #c04483;
        }

        .titulo {
            color: #9d2864;
        }

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

        .table thead th {
            background: #f8d9e8;
            color: #743254;
        }

        .huellitas {
            color: #c04483;
            text-align: center;
            font-size: 1.5rem;
            margin: 20px 0;
        }

        footer {
            background: #f8d9e8;
            border-top: 3px solid #c04483;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
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

        <a href="../index.php" class="btn btn-light btn-sm">
            <i class="bi bi-house"></i> Inicio
        </a>
    </div>
</nav>

<main class="container py-5">

    <h1 class="titulo fw-bold">
        <i class="bi bi-people-fill"></i>
        Gestión de clientes 🐾
    </h1>

    <p class="text-muted">
        Registrá y consultá los clientes de PetCare.
    </p>

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

    <!-- FORMULARIO -->
    <div class="card mb-4">
        <div class="card-body p-4">

            <h2 class="h5 fw-bold titulo mb-3">
                <i class="bi bi-person-plus-fill"></i>
                Registrar cliente
            </h2>

            <form method="POST">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text"
                               class="form-control"
                               id="dni"
                               name="dni"
                               maxlength="15"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text"
                               class="form-control"
                               id="nombre"
                               name="nombre"
                               maxlength="100"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text"
                               class="form-control"
                               id="apellido"
                               name="apellido"
                               maxlength="100"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text"
                               class="form-control"
                               id="telefono"
                               name="telefono"
                               maxlength="30">
                    </div>

                </div>

                <button type="submit"
                        class="btn btn-petcare mt-4">
                    <i class="bi bi-floppy-fill"></i>
                    Guardar cliente
                </button>

            </form>
        </div>
    </div>

    <!-- LISTADO -->
    <div class="card">
        <div class="card-body p-4">

            <h2 class="h5 fw-bold titulo mb-3">
                <i class="bi bi-list-ul"></i>
                Clientes registrados
            </h2>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= escapar($cliente['id_cliente']) ?></td>
                            <td><?= escapar($cliente['dni']) ?></td>
                            <td><?= escapar($cliente['nombre']) ?></td>
                            <td><?= escapar($cliente['apellido']) ?></td>
                            <td><?= escapar($cliente['telefono']) ?></td>
                            <td>
    <div class="d-flex gap-2">

        <a href="editar.php?id=<?= (int)$cliente['id_cliente'] ?>"
           class="btn btn-sm btn-petcare">
            <i class="bi bi-pencil-square"></i>
            Editar
        </a>

        <form method="POST"
              action="eliminar.php"
              onsubmit="return confirm('¿Seguro que querés eliminar este cliente?');">

            <input type="hidden"
                   name="id"
                   value="<?= (int)$cliente['id_cliente'] ?>">

            <button type="submit"
                    class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i>
                Eliminar
            </button>

        </form>

    </div>
</td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($clientes) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                Todavía no hay clientes registrados.
                            </td>

<td>
    <div class="d-flex gap-2">

        <a href="editar.php?id=<?= (int)$cliente['id_cliente'] ?>"
           class="btn btn-sm btn-petcare">
            <i class="bi bi-pencil-square"></i>
            Editar
        </a>

        <form method="POST"
              action="eliminar.php"
              onsubmit="return confirm('¿Seguro que querés eliminar este cliente?');">

            <input type="hidden"
                   name="id"
                   value="<?= (int)$cliente['id_cliente'] ?>">

            <button type="submit"
                    class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i>
                Eliminar
            </button>

        </form>

    </div>
</td>

                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>

    <div class="huellitas" aria-hidden="true">
        🐾 🐾 🐾
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
