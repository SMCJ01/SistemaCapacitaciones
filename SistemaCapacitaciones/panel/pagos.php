<?php
require_once "../config/db.php";
require_once "../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: /SistemaCapacitaciones/inicio/index.php");
    exit;
}

include "../includes/header.php";

$idUsu = (int) $_SESSION['usuario_id'];

// Traer pagos relacionados a las inscripciones del estudiante
$sql = "
    SELECT 
        c.TituloCur,
        p.MontoPag,
        p.FechaPag,
        p.MetodoPag,
        p.EstadoPag
    FROM Pago p
    JOIN Inscripcion i ON p.IdIns = i.IdIns
    JOIN Cursos c      ON i.IdCur = c.IdCur
    WHERE i.IdUsu = ?
    ORDER BY p.FechaPag DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUsu);
$stmt->execute();
$res = $stmt->get_result();
$pagos = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container" style="margin-top: 40px;">
    <h2>Mis Pagos</h2>

    <a href="estudiante.php" class="btn btn-link">&laquo; Volver al panel</a>

    <?php if (empty($pagos)): ?>
        <p>No tienes pagos registrados todavía.</p>
    <?php else: ?>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Método</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?= htmlspecialchars($pago['TituloCur']) ?></td>
                        <td><?= formatearPrecio($pago['MontoPag']) ?></td>
                        <td><?= htmlspecialchars($pago['FechaPag']) ?></td>
                        <td><?= htmlspecialchars($pago['MetodoPag']) ?></td>
                        <td><?= htmlspecialchars($pago['EstadoPag']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
