<?php
require_once "../config/db.php";
require_once "../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo usuarios logueados pueden ver cursos
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /SistemaCapacitaciones/inicio/index.php");
    exit;
}

include "../includes/header.php";

// =====================
// Cargar filtros
// =====================
$plataformas = $mysqli->query("
    SELECT IdPlat, NomPlat 
    FROM Plataforma 
    ORDER BY NomPlat
")->fetch_all(MYSQLI_ASSOC);

$tipos = $mysqli->query("
    SELECT IdTipoCur, NomTipoCur 
    FROM TipoCurso 
    ORDER BY NomTipoCur
")->fetch_all(MYSQLI_ASSOC);

// Parámetros de búsqueda
$idPlat = isset($_GET['idPlat']) ? (int)$_GET['idPlat'] : 0;
$idTipo = isset($_GET['idTipo']) ? (int)$_GET['idTipo'] : 0;
$texto  = isset($_GET['q']) ? trim($_GET['q']) : "";

// =====================
// Consulta de cursos (JOIN directo)
// Luego, en el SQL final, esto puede reemplazarse por un SP o vista.
// =====================
$sql = "
    SELECT 
        c.IdCur,
        c.TituloCur,
        c.DescCur,
        c.PrecioCur,
        n.NomNivel,
        p.NomPlat,
        t.NomTipoCur,
        u.NomUsu AS ProfesorNombre,
        u.ApeUsu AS ProfesorApellido
    FROM Cursos c
    JOIN Niveles n    ON c.IdNivel = n.IdNivel
    JOIN Plataforma p ON c.IdPlat  = p.IdPlat
    JOIN TipoCurso t  ON c.IdTipoCur = t.IdTipoCur
    JOIN Profesor pr  ON c.IdProf = pr.IdProf
    JOIN Usuario u    ON pr.IdUsu = u.IdUsu
    WHERE 1 = 1
";

$params = [];
$types  = "";

// Filtros dinámicos
if ($idPlat > 0) {
    $sql .= " AND c.IdPlat = ? ";
    $types .= "i";
    $params[] = $idPlat;
}

if ($idTipo > 0) {
    $sql .= " AND c.IdTipoCur = ? ";
    $types .= "i";
    $params[] = $idTipo;
}

if ($texto !== "") {
    $sql .= " AND (c.TituloCur LIKE ? OR c.DescCur LIKE ?) ";
    $types .= "ss";
    $like = "%".$texto."%";
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY c.FechaCur DESC";

$stmt = $mysqli->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$cursos = [];
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}
$stmt->close();

// =====================
// Paginación simple
// =====================
$pagina    = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$porPagina = 10;
$total     = count($cursos);
$paginas   = max(1, ceil($total / $porPagina));
$offset    = ($pagina - 1) * $porPagina;
$cursosPagina = array_slice($cursos, $offset, $porPagina);
?>

<div class="container" style="margin-top: 40px;">

    <h2>Catálogo de Cursos</h2>

    <!-- Barra de búsqueda y filtros -->
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control"
                   placeholder="Buscar por título o descripción..."
                   value="<?= htmlspecialchars($texto) ?>">
        </div>

        <div class="col-md-3">
            <select name="idPlat" class="form-select">
                <option value="0">Todas las plataformas</option>
                <?php foreach ($plataformas as $p): ?>
                    <option value="<?= $p['IdPlat'] ?>"
                        <?= $idPlat == $p['IdPlat'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['NomPlat']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="idTipo" class="form-select">
                <option value="0">Todos los tipos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['IdTipoCur'] ?>"
                        <?= $idTipo == $t['IdTipoCur'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['NomTipoCur']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <!-- Grid de cursos -->
    <div class="row">
        <?php foreach ($cursosPagina as $c): 
            $img = obtenerImagenCurso($c['TituloCur']);
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="<?= $img ?>" class="card-img-top" alt="Imagen curso">

                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($c['TituloCur']) ?></h5>
                    <p class="card-text">
                        <?= htmlspecialchars($c['NomNivel']) ?> ·
                        <?= htmlspecialchars($c['NomPlat']) ?><br>
                        Profesor: <?= htmlspecialchars($c['ProfesorNombre']." ".$c['ProfesorApellido']) ?>
                    </p>
                    <p class="card-text fw-bold">
                        <?= formatearPrecio($c['PrecioCur']) ?>
                    </p>
                    <p class="card-text">
                        <?= htmlspecialchars(mb_substr($c['DescCur'], 0, 100)) ?>...
                    </p>
                </div>

                <div class="card-footer text-end">
                    <a href="detalle.php?id=<?= $c['IdCur'] ?>" class="btn btn-sm btn-secondary">
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($cursosPagina)): ?>
            <p>No se encontraron cursos con los criterios seleccionados.</p>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <nav aria-label="Paginación de cursos" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?page=<?= $i ?>&q=<?= urlencode($texto) ?>&idPlat=<?= $idPlat ?>&idTipo=<?= $idTipo ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

</div>

<?php include "../includes/footer.php"; ?>
