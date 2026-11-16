
<?php
require_once __DIR__ . '/config/Conexion.php';

$conexion = conectarBD();

function contarRegistros(PDO $conexion, string $tabla): int {
    $tablasPermitidas = [
        'clientes',
        'mascotas',
        'veterinarios',
        'consultas'
    ];

    if (!in_array($tabla, $tablasPermitidas, true)) {
        return 0;
    }

    $consulta = $conexion->query("SELECT COUNT(*) FROM $tabla");
    return (int) $consulta->fetchColumn();
}

$clientes = contarRegistros($conexion, 'clientes');
$mascotas = contarRegistros($conexion, 'mascotas');
$veterinarios = contarRegistros($conexion, 'veterinarios');
$consultas = contarRegistros($conexion, 'consultas');

$secciones = [
    [
        'nombre' => 'Clientes',
        'cantidad' => $clientes,
        'icono' => 'bi-people-fill',
        'ruta' => 'clientes'
    ],
    [
        'nombre' => 'Mascotas',
        'cantidad' => $mascotas,
        'icono' => 'bi-heart-fill',
        'ruta' => 'mascotas'
    ],
    [
        'nombre' => 'Veterinarios',
        'cantidad' => $veterinarios,
        'icono' => 'bi-bandaid-fill',
        'ruta' => 'veterinarios'
    ],
    [
        'nombre' => 'Consultas',
        'cantidad' => $consultas,
        'icono' => 'bi-clipboard2-pulse-fill',
        'ruta' => 'consultas'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>PetCare | Gestión Veterinaria</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Iconos de Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        :root {
            --rosa-principal: #c04483;
            --rosa-oscuro: #9d2864;
            --rosa-pastel: #f8d9e8;
            --rosa-claro: #fce7f1;
            --rosa-fondo: #fff7fb;
            --texto: #55334a;
        }

        body {
            background-color: var(--rosa-fondo);
            color: var(--texto);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Barra de navegación */
        .navbar {
            background-color: var(--rosa-principal);
            box-shadow: 0 3px 12px rgba(157, 40, 100, 0.15);
        }

        .navbar-brand {
            font-size: 1.6rem;
        }

        .navbar .nav-link {
            color: white;
        }

        .navbar .nav-link:hover {
            color: var(--rosa-pastel);
        }

        /* Presentación principal */
        .hero {
            background: linear-gradient(
                120deg,
                var(--rosa-pastel),
                var(--rosa-claro)
            );
            border-radius: 22px;
            padding: 45px 35px;
            position: relative;
            overflow: hidden;
            border: 1px solid #f3c3da;
        }

        .hero h1 {
            color: var(--rosa-oscuro);
        }

        .hero p {
            max-width: 650px;
        }

        .huella-decorativa {
            position: absolute;
            right: 35px;
            top: 25px;
            font-size: 7rem;
            color: rgba(192, 68, 131, 0.15);
            transform: rotate(-20deg);
        }

        /* Tarjetas */
        .stat-card {
            border: 1px solid #f3d4e3;
            border-radius: 20px;
            box-shadow: 0 5px 18px rgba(157, 40, 100, 0.07);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(157, 40, 100, 0.15);
        }

        .icono-tarjeta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 65px;
            height: 65px;
            border-radius: 18px;
            background: var(--rosa-claro);
            color: var(--rosa-principal);
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--rosa-oscuro);
        }

        /* Botones */
        .btn-petcare {
            background-color: var(--rosa-principal);
            color: white;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
        }

        .btn-petcare:hover {
            background-color: var(--rosa-oscuro);
            color: white;
        }

        /* Huellitas */
        .huellitas {
            text-align: center;
            color: #d98aaf;
            font-size: 1.3rem;
            letter-spacing: 12px;
        }

        /* Pie de página institucional */
        footer {
            background-color: var(--rosa-pastel);
            border-top: 3px solid var(--rosa-principal);
            padding: 22px 15px;
            text-align: center;
            margin-top: auto;
        }

        footer p {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        footer .autora {
            font-weight: bold;
            color: var(--rosa-oscuro);
        }

        @media (max-width: 768px) {
            .hero {
                padding: 30px 22px;
            }

            .huella-decorativa {
                font-size: 4rem;
                right: 12px;
                top: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- MENÚ PRINCIPAL -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-heart-pulse-fill"></i>
                PetCare 🐾
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu"
                aria-controls="menu"
                aria-expanded="false"
                aria-label="Abrir menú">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <div class="navbar-nav ms-auto">

                    <a class="nav-link active" href="index.php">
                        Inicio
                    </a>

                    <a class="nav-link" href="clientes/index.php">
                        Clientes
                    </a>

                    <a class="nav-link" href="mascotas/index.php">
                        Mascotas
                    </a>

                    <a class="nav-link" href="veterinarios/index.php">
                        Veterinarios
                    </a>

                    <a class="nav-link" href="consultas/index.php">
                        Consultas
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="container py-5">

        <!-- BIENVENIDA -->
        <section class="hero mb-4">

            <i class="bi bi-heart-fill huella-decorativa"
               aria-hidden="true"></i>

            <h1 class="fw-bold">
                Bienvenidos a PetCare 🐶🐱
            </h1>

            <p class="lead mb-0">
                Cuidamos a quienes dejan huellas en tu corazón.
                Un sistema de gestión veterinaria para administrar
                clientes, mascotas, veterinarios y consultas.
            </p>

        </section>

        <!-- HUELLITAS DECORATIVAS -->
        <div class="huellitas mb-4" aria-hidden="true">
            🐾 🐾 🐾 🐾 🐾
        </div>

        <!-- PANEL -->
        <h2 class="h4 fw-bold mb-4">
            <i class="bi bi-grid-fill"
               style="color: var(--rosa-principal);"></i>
            Panel de administración
        </h2>

        <div class="row g-4 mb-5">

            <?php foreach ($secciones as $seccion): ?>

                <div class="col-md-6 col-lg-3">

                    <div class="card stat-card h-100">

                        <div class="card-body p-4">

                            <div class="icono-tarjeta">
                                <i class="bi <?= htmlspecialchars(
                                    $seccion['icono'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"></i>
                            </div>

                            <h3 class="h5 fw-bold">
                                <?= htmlspecialchars(
                                    $seccion['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h3>

                            <div class="stat-number">
                                <?= $seccion['cantidad'] ?>
                            </div>

                            <p class="text-muted small">
                                Registros existentes
                            </p>

                            <a
                                href="<?= htmlspecialchars(
                                    $seccion['ruta'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>/index.php"
                                class="btn btn-petcare w-100">

                                <i class="bi bi-arrow-right-circle"></i>
                                Administrar

                            </a>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>

        <div class="huellitas" aria-hidden="true">
            🐾 🐾 🐾
        </div>

    </main>

    <!-- FOOTER INSTITUCIONAL -->
    <footer>

        <p class="fw-bold">
            Trabajo Práctico N.º 6 – PetCare
        </p>

        <p>
            Prácticas Profesionalizantes I
        </p>

        <p>
            Profesor: Galo Nahuel Olguín Fernández
        </p>

        <p>
            Instituto Superior de Formación Docente
            y Técnica N.º 24
        </p>

        <p class="autora">
            Desarrollado por Daiana Gómez
        </p>

        <p class="text-muted small mb-0">
            © 2026 – PetCare | Gestión Veterinaria
        </p>

    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>
