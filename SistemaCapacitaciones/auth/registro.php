<?php
session_start();
require_once "../config/db.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $correo   = trim($_POST["correo"]);
    $pass     = trim($_POST["password"]);

    // 1. Verificar que el correo no exista
    $stmt = $mysqli->prepare("SELECT IdUsu FROM Usuario WHERE EmailUsu = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "Ya existe un usuario con ese correo.";
    } else {

        $stmt->close();

        // 2. Insertar en Usuario (rol estudiante)
        $claveHash   = password_hash($pass, PASSWORD_BCRYPT);  // puedes dejarlo así aunque las inserts de prueba sean texto plano
        $fechaRegistro = date('Y-m-d');

        $insertU = $mysqli->prepare("
            INSERT INTO Usuario (NomUsu, ApeUsu, EmailUsu, PassUsu, RolUsu, FechaRegUsu)
            VALUES (?, ?, ?, ?, 'estudiante', ?)
        ");
        $insertU->bind_param("sssss", $nombre, $apellido, $correo, $claveHash, $fechaRegistro);

        if ($insertU->execute()) {
            $idUsuario = $insertU->insert_id;

            // 3. Crear registro en Estudiante
            $insertE = $mysqli->prepare("
                INSERT INTO Estudiante (IdUsu) VALUES (?)
            ");
            $insertE->bind_param("i", $idUsuario);
            $insertE->execute();
            $insertE->close();

            $mensaje = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
        } else {
            $mensaje = "Ocurrió un error al registrar al usuario.";
        }

        $insertU->close();
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="container" style="margin-top:110px; max-width:500px;">
    <h2>Registro de Estudiante</h2>

    <?php if ($mensaje): ?>
        <div style="background:#d1e7dd;padding:10px;margin-bottom:15px;border-radius:5px;color:#0f5132;">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Nombres:</label>
        <input type="text" name="nombre" required class="form-control">

        <label>Apellidos:</label>
        <input type="text" name="apellido" required class="form-control">

        <label>Correo:</label>
        <input type="email" name="correo" required class="form-control">

        <label>Contraseña:</label>
        <input type="password" name="password" required class="form-control">

        <button class="btn btn-primary" style="margin-top:15px;width:100%;">Registrarme</button>
    </form>

    <p style="margin-top:15px;">
        ¿Ya tienes cuenta?
        <a href="login.php">Inicia sesión</a>
    </p>
</div>

<?php include "../includes/footer.php"; ?>
