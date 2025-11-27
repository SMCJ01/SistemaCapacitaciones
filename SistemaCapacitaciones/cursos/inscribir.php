<?php
require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo estudiantes pueden inscribirse
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: /SistemaCapacitaciones/inicio/index.php");
    exit;
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idCur'])) {
    header("Location: index.php");
    exit;
}

$idCur  = (int) $_POST['idCur'];
$idUsu  = (int) $_SESSION['usuario_id'];
$metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : 'TARJETA';
$monto  = isset($_POST['monto']) ? (float) $_POST['monto'] : 0;

// ==========================
// 1. Verificar que el curso exista y esté ACTIVO
// ==========================
$stmt = $mysqli->prepare("SELECT EstadoCur, PrecioCur FROM Cursos WHERE IdCur = ?");
$stmt->bind_param("i", $idCur);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    $_SESSION['flash_err'] = "El curso no existe.";
    header("Location: detalle.php?id=".$idCur);
    exit;
}

if (strtoupper($curso['EstadoCur']) !== 'ACTIVO') {
    $_SESSION['flash_err'] = "El curso no está activo. No se puede inscribir.";
    header("Location: detalle.php?id=".$idCur);
    exit;
}

// ==========================
// 2. Verificar que NO esté ya inscrito
// ==========================
$stmt = $mysqli->prepare("
    SELECT IdIns 
    FROM Inscripcion 
    WHERE IdUsu = ? AND IdCur = ?
    LIMIT 1
");
$stmt->bind_param("ii", $idUsu, $idCur);
$stmt->execute();
$ya = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($ya) {
    $_SESSION['flash_err'] = "Ya estás inscrito en este curso.";
    header("Location: detalle.php?id=".$idCur);
    exit;
}

// ==========================
// 3. Insertar en Inscripcion + Pago (TRANSACCIÓN)
// ==========================
try {
    $mysqli->begin_transaction();

    $fechaIns  = date('Y-m-d');
    $estadoIns = "ACTIVO";

    // Insertar inscripción
    $stmt = $mysqli->prepare("
        INSERT INTO Inscripcion (IdUsu, IdCur, FechaIns, EstadoIns)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $idUsu, $idCur, $fechaIns, $estadoIns);
    $stmt->execute();
    $idIns = $stmt->insert_id;
    $stmt->close();

    // Insertar pago
    $fechaPag  = date('Y-m-d');
    $estadoPag = "PENDIENTE";

    $stmt2 = $mysqli->prepare("
        INSERT INTO Pago (IdIns, MontoPag, FechaPag, MetodoPag, EstadoPag)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt2->bind_param("idsss", $idIns, $monto, $fechaPag, $metodo, $estadoPag);
    $stmt2->execute();
    $stmt2->close();

    $mysqli->commit();

    $_SESSION['flash_ok'] = "Inscripción realizada con éxito. Método: "
        . $metodo . " · Monto: S/ " . number_format($monto, 2);

} catch (mysqli_sql_exception $e) {
    $mysqli->rollback();
    $_SESSION['flash_err'] = "Error durante la inscripción: " . $e->getMessage();
}

header("Location: detalle.php?id=" . $idCur);
exit;
