<?php
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/config/Auth.php';

$autenticado = usuarioAutenticado();
$rol = rolActual();

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
    return (int)$consulta->fetchColumn();
}

$secciones = [];

if ($autenticado) {
    $conexion = conectarBD();

    if ($rol === 'administrador') {
        $secciones[] = [
            'nombre' => 'Clientes',
            'cantidad' => contarRegistros($conexion, 'clientes'),
            'icono' => 'bi-people-fill',
            'ruta' => 'clientes',
            'accion' => 'Administrar'
        ];
    }

    $secciones[] = [
        'nombre' => 'Mascotas',
        'cantidad' => contarRegistros($conexion, 'mascotas'),
        'icono' => 'bi-heart-fill',
        'ruta' => 'mascotas',
        'accion' => $rol === 'administrador'
            ? 'Administrar' : 'Consultar'
    ];

    if ($rol === 'administrador') {
        $secciones[] = [
            'nombre' => 'Veterinarios',
            'cantidad' => contarRegistros($conexion, 'veterinarios'),
            'icono' => 'bi-bandaid-fill',
            'ruta' => 'veterinarios',
            'accion' => 'Administrar'
        ];
    }

    if (in_array($rol, ['administrador', 'veterinario'], true)) {
        $secciones[] = [
            'nombre' => 'Consultas',
            'cantidad' => contarRegistros($conexion, 'consultas'),
            'icono' => 'bi-clipboard2-pulse-fill',
            'ruta' => 'consultas',
            'accion' => 'Gestionar'
        ];
    }
}

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

    <?php if ($autenticado): ?>

        <h2 class="h4 fw-bold mb-4">
            <i class="bi bi-grid-fill"
               style="color: var(--rosa-principal);"></i>

            <?= $rol === 'administrador'
                ? 'Panel de administración'
                : 'Panel de PetCare' ?>
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
                                <?= (int)$seccion['cantidad'] ?>
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

                                <?= htmlspecialchars(
                                    $seccion['accion'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </a>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="text-center mb-5">
            <h2 class="h4 titulo mb-3">
                Accedé a PetCare
            </h2>

            <p>
                Iniciá sesión o registrate para utilizar
                las funcionalidades del sistema.
            </p>

            <a href="login.php" class="btn btn-petcare me-2">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar sesión
            </a>

            <a href="registro.php"
               class="btn btn-outline-secondary">
                <i class="bi bi-person-plus"></i>
                Registrarse
            </a>
        </div>

    <?php endif; ?>

    <div class="huellitas" aria-hidden="true">
        🐾 🐾 🐾
    </div>

</main>

<!-- FOOTER INSTITUCIONAL -->
<?php require __DIR__ . '/includes/footer.php'; ?>
