<?php
require_once "../config/db.php";
require_once "../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /SistemaCapacitaciones/inicio/index.php");
    exit;
}

include "../includes/header.php";

$idCur = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ==========================
// Traer detalle del curso
// (JOIN directo; en el SQL final puedes crear vw_cursos_detalle)
// ==========================
$sql = "
    SELECT 
        c.IdCur,
        c.TituloCur,
        c.DescCur,
        c.PrecioCur,
        c.EstadoCur,
        c.FechaCur,
        n.NomNivel,
        t.NomTipoCur,
        p.NomPlat,
        u.NomUsu AS ProfesorNombre,
        u.ApeUsu AS ProfesorApellido
    FROM Cursos c
    JOIN Niveles n    ON c.IdNivel = n.IdNivel
    JOIN TipoCurso t  ON c.IdTipoCur = t.IdTipoCur
    JOIN Plataforma p ON c.IdPlat  = p.IdPlat
    JOIN Profesor pr  ON c.IdProf = pr.IdProf
    JOIN Usuario u    ON pr.IdUsu = u.IdUsu
    WHERE c.IdCur = ?
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idCur);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    echo "<div class='container mt-5'><p>Curso no encontrado.</p></div>";
    include "../includes/footer.php";
    exit;
}

$img = obtenerImagenCurso($curso['TituloCur']);

$ok  = $_SESSION['flash_ok']   ?? "";
$err = $_SESSION['flash_err']  ?? "";
unset($_SESSION['flash_ok'], $_SESSION['flash_err']);
?>

<div class="container" style="margin-top: 40px;">

    <?php if ($ok): ?>
        <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <?php if ($err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= $img ?>" class="img-fluid rounded shadow" alt="Imagen del curso">
        </div>

        <div class="col-md-6">
            <h2><?= htmlspecialchars($curso['TituloCur']) ?></h2>

            <p class="text-muted">
                <?= htmlspecialchars($curso['NomNivel']) ?> ·
                <?= htmlspecialchars($curso['NomTipoCur']) ?> ·
                <?= htmlspecialchars($curso['NomPlat']) ?>
            </p>

            <p><strong>Profesor:</strong>
                <?= htmlspecialchars($curso['ProfesorNombre']." ".$curso['ProfesorApellido']) ?>
            </p>

            <p><strong>Precio:</strong> <?= formatearPrecio($curso['PrecioCur']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($curso['EstadoCur']) ?></p>

            <p class="mt-3"><?= nl2br(htmlspecialchars($curso['DescCur'])) ?></p>

            <?php if ($_SESSION['rol'] === 'estudiante'): ?>
                <hr>
                <h5>Inscribirme en este curso</h5>

                <form method="post" action="inscribir.php" class="mt-3">
                    <input type="hidden" name="idCur" value="<?= $curso['IdCur'] ?>">

                    <div class="mb-2">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo" class="form-select" required>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="EFECTIVO">Efectivo</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" name="monto"
                               value="<?= htmlspecialchars($curso['PrecioCur']) ?>"
                               class="form-control" required>
                    </div>

                    <button class="btn btn-primary mt-2">Confirmar inscripción</button>
                </form>

            <?php else: ?>
                <p class="mt-4 text-warning">
                    Solo los usuarios con rol <strong>estudiante</strong> pueden inscribirse.
                </p>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
