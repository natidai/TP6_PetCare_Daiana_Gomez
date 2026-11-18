
<?php
// Encabezado compartido de PetCare

// Iniciar sesión si todavía no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/TP6_PetCare_Daiana_Gomez/';
$tituloPagina = $tituloPagina ?? 'Gestión Veterinaria';

function escaparHtml($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>PetCare | <?= escaparHtml($tituloPagina) ?></title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- Estilos generales de PetCare -->
    <link
        href="<?= escaparHtml($baseUrl) ?>css/styles.css"
        rel="stylesheet">
</head>

<body>

<!-- MENÚ PRINCIPAL -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">

        <a class="navbar-brand fw-bold"
           href="<?= escaparHtml($baseUrl) ?>index.php">
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

                <a class="nav-link"
                   href="<?= escaparHtml($baseUrl) ?>index.php">
                    Inicio
                </a>

                <a class="nav-link"
                   href="<?= escaparHtml($baseUrl) ?>clientes/index.php">
                    Clientes
                </a>

                <a class="nav-link"
                   href="<?= escaparHtml($baseUrl) ?>mascotas/index.php">
                    Mascotas
                </a>

                <a class="nav-link"
                   href="<?= escaparHtml($baseUrl) ?>veterinarios/index.php">
                    Veterinarios
                </a>

                <a class="nav-link"
                   href="<?= escaparHtml($baseUrl) ?>consultas/index.php">
                    Consultas
                </a>

                <?php if (isset($_SESSION['id_usuario'])): ?>

                    <!-- Usuario autenticado -->
                    <a class="nav-link"
                       href="<?= escaparHtml($baseUrl) ?>logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </a>

                <?php else: ?>

                    <!-- Visitante -->
                    <a class="nav-link"
                       href="<?= escaparHtml($baseUrl) ?>login.php">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar sesión
                    </a>

                    <a class="nav-link"
                       href="<?= escaparHtml($baseUrl) ?>registro.php">
                        <i class="bi bi-person-plus"></i>
                        Registrarse
                    </a>

                <?php endif; ?>

            </div>
        </div>

    </div>
</nav>

