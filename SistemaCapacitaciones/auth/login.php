<?php
session_start();
require_once "../config/db.php";

// Si ya está logueado → redirigir según rol
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] === 'estudiante') {
        header("Location: /SistemaCapacitaciones/cursos/index.php");
        exit;
    }
    if ($_SESSION['rol'] === 'profesor') {
        header("Location: /SistemaCapacitaciones/panel/profesor.php");
        exit;
    }
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"]);
    $pass   = trim($_POST["password"]);

    // OJO: nombres REALES de la tabla Usuario
    $stmt = $mysqli->prepare("
        SELECT IdUsu, NomUsu, ApeUsu, EmailUsu, PassUsu, RolUsu
        FROM Usuario
        WHERE EmailUsu = ?
    ");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nom, $ape, $mail, $claveBD, $rol);
        $stmt->fetch();

        // Soporta contraseña hasheada o en texto plano (como en tu script SQL)
        $okPassword = password_verify($pass, $claveBD) || $pass === $claveBD;

        if ($okPassword) {
            $_SESSION["usuario_id"] = $id;
            $_SESSION["nombre"]     = $nom;
            $_SESSION["apellido"]   = $ape;
            $_SESSION["rol"]        = $rol;

            if ($rol === "estudiante") {
                header("Location: /SistemaCapacitaciones/cursos/index.php");
            } elseif ($rol === "profesor") {
                header("Location: /SistemaCapacitaciones/panel/profesor.php");
            } else {
                header("Location: /SistemaCapacitaciones/index.php");
            }
            exit;
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "No se encontró un usuario con ese correo.";
    }

    $stmt->close();
}
?>

<?php include "../includes/header.php"; ?>

<div class="container" style="margin-top: 110px; max-width: 480px;">
    <h2>Iniciar Sesión</h2>

    <?php if ($mensaje): ?>
        <div style="background:#f8d7da;padding:10px;margin-bottom:15px;border-radius:5px;color:#721c24;">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Correo:</label>
        <input type="email" name="correo" required class="form-control">

        <label>Contraseña:</label>
        <input type="password" name="password" required class="form-control">

        <button class="btn btn-primary" style="margin-top:15px;width:100%;">Ingresar</button>
    </form>

    <p style="margin-top:15px;">
        ¿No tienes cuenta?
        <a href="registro.php">Registrarte aquí</a>
    </p>
</div>

<?php include "../includes/footer.php"; ?>
