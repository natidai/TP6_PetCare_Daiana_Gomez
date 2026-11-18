
<?php
// Control de autenticación y permisos de PetCare.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioAutenticado(): bool {
    return isset($_SESSION['id_usuario']);
}

function rolActual(): ?string {
    return $_SESSION['rol'] ?? null;
}

function tieneRol(array $rolesPermitidos): bool {
    return usuarioAutenticado()
        && in_array(rolActual(), $rolesPermitidos, true);
}

function requerirSesion(): void {
    if (!usuarioAutenticado()) {
        header(
            'Location: /TP6_PetCare_Daiana_Gomez/login.php?sesion_requerida=1'
        );
        exit;
    }
}

function requerirRol(array $rolesPermitidos): void {
    requerirSesion();

    if (!tieneRol($rolesPermitidos)) {
        http_response_code(403);
        exit('Acceso denegado. No tenés permisos para realizar esta acción.');
    }
}
