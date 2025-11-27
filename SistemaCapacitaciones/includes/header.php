<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Capacitaciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilos Globales -->
    <link rel="stylesheet" href="/SistemaCapacitaciones/assets/style.css">

    <!-- Bootstrap CDN (opcional y no afecta el inicio futurista) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Script global -->
    <script src="/SistemaCapacitaciones/assets/app.js" defer></script>
</head>

<body>

<!-- ==========================================
     NAVBAR GENERAL (NO AFECTA EL INICIO FUTURISTA)
=========================================== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="padding: 10px 25px;">
    <a class="navbar-brand" href="/SistemaCapacitaciones/index.php">
        SISCAP
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav ms-auto">

            <!-- Si NO está logueado -->
            <?php if (!isset($_SESSION['usuario_id'])): ?>

                <li class="nav-item">
                    <a class="nav-link" href="/SistemaCapacitaciones/auth/login.php">Ingresar</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/SistemaCapacitaciones/auth/registro.php">Registrarse</a>
                </li>

            <?php else: ?>

                <?php if ($_SESSION['rol'] === 'estudiante'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/SistemaCapacitaciones/panel/estudiante.php">Mi Panel</a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['rol'] === 'profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/SistemaCapacitaciones/panel/profesor.php">Panel Profesor</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="/SistemaCapacitaciones/auth/logout.php">Salir</a>
                </li>

            <?php endif; ?>
        </ul>
    </div>
</nav>

<div style="margin-bottom: 30px;"></div>
