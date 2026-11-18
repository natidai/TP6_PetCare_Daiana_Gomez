
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
<?php
$tituloPagina = 'Gestión Veterinaria';
require __DIR__ . '/includes/header.php';
?>

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
   <?php
require __DIR__ . '/includes/footer.php';
?> 
