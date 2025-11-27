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

// Traer notas por curso
$sql = "
    SELECT 
        c.TituloCur,
        i.NotaFinal,
        i.FechaIns
    FROM Inscripcion i
    JOIN Cursos c ON i.IdCur = c.IdCur
    WHERE i.IdUsu = ?
    ORDER BY i.FechaIns DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUsu);
$stmt->execute();
$res = $stmt->get_result();
$notas = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container" style="margin-top: 40px;">
    <h2>Mis Notas</h2>

    <a href="estudiante.php" class="btn btn-link">&laquo; Volver al panel</a>

    <?php if (empty($notas)): ?>
        <p>Aún no tienes notas registradas.</p>
    <?php else: ?>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Nota</th>
                    <th>Fecha de Inscripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notas as $n): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['TituloCur']) ?></td>
                        <td><?= htmlspecialchars($n['NotaFinal']) ?></td>
                        <td><?= htmlspecialchars($n['FechaIns']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
