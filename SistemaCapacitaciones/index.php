<?php
// index.php (raíz del proyecto SistemaCapacitaciones)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya inició sesión → lo enviamos directo al catálogo de cursos
if (isset($_SESSION['usuario_id'])) {
    header("Location: /SistemaCapacitaciones/cursos/index.php");
    exit;
}

// Si no hay sesión → mostrar la página de inicio futurista
header("Location: /SistemaCapacitaciones/inicio/index.php");
exit;
