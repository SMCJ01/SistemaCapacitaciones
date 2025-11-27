<?php
require_once "../config/db.php";
require_once "../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: /SistemaCapacitaciones/inicio/index.php");
    exit;
}

include "../includes/header.php";

$idUsu = (int) $_SESSION['usuario_id'];

// Buscar IdProf asociado a este usuario
$stmt = $mysqli->prepare("SELECT IdProf FROM Profesor WHERE IdUsu = ?");
$stmt->bind_param("i", $idUsu);
$stmt->execute();
$prof = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$prof) {
    echo "<div class='container mt-5'><p>No existe registro de profesor asociado a este usuario.</p></div>";
    include "../includes/footer.php";
    exit;
}

$idProf = (int) $prof['IdProf'];

// Cursos que dicta este profesor
$sql = "
    SELECT 
        c.IdCur,
        c.TituloCur,
        c.PrecioCur,
        c.EstadoCur,
        c.FechaCur,
        n.NomNivel,
        p.NomPlat,
        (SELECT COUNT(*) FROM Inscripcion i WHERE i.IdCur = c.IdCur) AS TotalInscritos
    FROM Cursos c
    JOIN Niveles n    ON c.IdNivel = n.IdNivel
    JOIN Plataforma p ON c.IdPlat  = p.IdPlat
    WHERE c.IdProf = ?
    ORDER BY c.FechaCur DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idProf);
$stmt->execute();
$res = $stmt->get_result();

$cursos = [];
while ($row = $res->fetch_assoc()) {
    $cursos[] = $row;
}
$stmt->close();
?>

<div class="container" style="margin-top: 40px;">
    <h2>Panel del Profesor</h2>

    <?php if (empty($cursos)): ?>
        <p>No tienes cursos registrados actualmente.</p>
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
                            <strong>Estado:</strong> <?= htmlspecialchars($c['EstadoCur']) ?><br>
                            <strong>Inscritos:</strong> <?= (int)$c['TotalInscritos'] ?>
                        </p>
                        <p class="fw-bold">
                            <?= formatearPrecio($c['PrecioCur']) ?>
                        </p>
                        <p class="text-muted">
                            Creado el: <?= htmlspecialchars($c['FechaCur']) ?>
                        </p>
                    </div>

                    <div class="card-footer text-end">
                        <a href="/SistemaCapacitaciones/cursos/detalle.php?id=<?= $c['IdCur'] ?>"
                           class="btn btn-sm btn-secondary">
                            Ver detalle público
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
