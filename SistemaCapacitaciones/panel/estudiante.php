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

// Cursos donde el usuario está inscrito
$sql = "
    SELECT 
        i.IdIns,
        i.FechaIns,
        i.EstadoIns,
        c.IdCur,
        c.TituloCur,
        c.PrecioCur,
        c.EstadoCur,
        n.NomNivel,
        p.NomPlat
    FROM Inscripcion i
    JOIN Cursos c    ON i.IdCur  = c.IdCur
    JOIN Niveles n   ON c.IdNivel = n.IdNivel
    JOIN Plataforma p ON c.IdPlat = p.IdPlat
    WHERE i.IdUsu = ?
    ORDER BY i.FechaIns DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUsu);
$stmt->execute();
$res = $stmt->get_result();

$cursos = [];
while ($row = $res->fetch_assoc()) {
    $cursos[] = $row;
}
$stmt->close();
?>

<div class="container" style="margin-top: 40px;">
    <h2>Mi Panel de Estudiante</h2>

    <div class="mb-3">
        <a href="notas.php" class="btn btn-outline-primary btn-sm">Ver mis notas</a>
        <a href="pagos.php" class="btn btn-outline-secondary btn-sm">Ver mis pagos</a>
    </div>

    <?php if (empty($cursos)): ?>
        <p>No estás inscrito en ningún curso todavía.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($cursos as $c): 
                $img = obtenerImagenCurso($c['TituloCur']);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $img ?>" class="card-img-top" alt="Curso">

                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($c['TituloCur']) ?></h5>
                        <p class="card-text">
                            <?= htmlspecialchars($c['NomNivel']) ?> ·
                            <?= htmlspecialchars($c['NomPlat']) ?><br>
                            <strong>Estado curso:</strong> <?= htmlspecialchars($c['EstadoCur']) ?><br>
                            <strong>Inscripción:</strong> <?= htmlspecialchars($c['EstadoIns']) ?>
                        </p>
                        <p class="fw-bold"><?= formatearPrecio($c['PrecioCur']) ?></p>
                        <p class="text-muted">Inscrito el: <?= htmlspecialchars($c['FechaIns']) ?></p>
                    </div>

                    <div class="card-footer text-end">
                        <a href="/SistemaCapacitaciones/cursos/detalle.php?id=<?= $c['IdCur'] ?>"
                           class="btn btn-sm btn-secondary">
                            Ir al curso
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
