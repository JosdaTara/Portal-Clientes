<?php
/**
 * Panel principal de reportes
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('agente', 'administrador');

$pdo = obtenerConexion();

$mesSeleccionado = intval($_GET['mes'] ?? date('m'));
$anioSeleccionado = intval($_GET['anio'] ?? date('Y'));

if ($mesSeleccionado < 1 || $mesSeleccionado > 12) $mesSeleccionado = date('m');
if ($anioSeleccionado < 2020 || $anioSeleccionado > 2099) $anioSeleccionado = date('Y');

$resumen = [
    'total' => 0, 'pendientes' => 0, 'en_proceso' => 0, 'atendidas' => 0, 'cerradas' => 0,
    'tiempo_promedio' => 0,
];

$stmt = $pdo->prepare(
    'SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) AS pendientes,
        SUM(CASE WHEN estado = "en_proceso" THEN 1 ELSE 0 END) AS en_proceso,
        SUM(CASE WHEN estado = "atendida" THEN 1 ELSE 0 END) AS atendidas,
        SUM(CASE WHEN estado = "cerrada" THEN 1 ELSE 0 END) AS cerradas,
        ROUND(AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, COALESCE(fecha_cierre, NOW()))), 2) AS tiempo_promedio
     FROM solicitudes
     WHERE MONTH(fecha_creacion) = ? AND YEAR(fecha_creacion) = ?'
);
$stmt->execute([$mesSeleccionado, $anioSeleccionado]);
$datos = $stmt->fetch();

if ($datos) {
    $resumen['total']          = intval($datos['total'] ?? 0);
    $resumen['pendientes']     = intval($datos['pendientes'] ?? 0);
    $resumen['en_proceso']     = intval($datos['en_proceso'] ?? 0);
    $resumen['atendidas']      = intval($datos['atendidas'] ?? 0);
    $resumen['cerradas']       = intval($datos['cerradas'] ?? 0);
    $resumen['tiempo_promedio'] = floatval($datos['tiempo_promedio'] ?? 0);
}

$tasaResolucion = $resumen['total'] > 0
    ? round(($resumen['atendidas'] + $resumen['cerradas']) / $resumen['total'] * 100, 1)
    : 0;

// Por categoria
$stmtCat = $pdo->prepare(
    'SELECT c.nombre, COUNT(*) AS total
     FROM solicitudes s
     JOIN categorias c ON s.id_categoria = c.id_categoria
     WHERE MONTH(s.fecha_creacion) = ? AND YEAR(s.fecha_creacion) = ?
     GROUP BY c.nombre ORDER BY total DESC'
);
$stmtCat->execute([$mesSeleccionado, $anioSeleccionado]);
$porCategoria = $stmtCat->fetchAll();

// Por prioridad
$stmtPri = $pdo->prepare(
    'SELECT prioridad, COUNT(*) AS total
     FROM solicitudes
     WHERE MONTH(fecha_creacion) = ? AND YEAR(fecha_creacion) = ?
     GROUP BY prioridad ORDER BY FIELD(prioridad, "urgente", "alta", "media", "baja")'
);
$stmtPri->execute([$mesSeleccionado, $anioSeleccionado]);
$porPrioridad = $stmtPri->fetchAll();

// Tendencia diaria
$stmtDia = $pdo->prepare(
    'SELECT DAY(fecha_creacion) AS dia, COUNT(*) AS total
     FROM solicitudes
     WHERE MONTH(fecha_creacion) = ? AND YEAR(fecha_creacion) = ?
     GROUP BY dia ORDER BY dia'
);
$stmtDia->execute([$mesSeleccionado, $anioSeleccionado]);
$porDia = $stmtDia->fetchAll();

$nombreMeses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

$tituloPagina = 'Reporte Mensual';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-graph-up"></i> Reporte Mensual</h2>
</div>

<form method="GET" action="" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label fw-bold">Mes:</label>
        <select name="mes" class="form-select form-select-sm">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $mesSeleccionado === $m ? 'selected' : '' ?>><?= $nombreMeses[$m] ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label fw-bold">Anio:</label>
        <select name="anio" class="form-select form-select-sm">
            <?php for ($a = date('Y'); $a >= 2024; $a--): ?>
                <option value="<?= $a ?>" <?= $anioSeleccionado === $a ? 'selected' : '' ?>><?= $a ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Generar reporte</button>
    </div>
</form>

<h4 class="mb-3"><?= $nombreMeses[$mesSeleccionado] . ' ' . $anioSeleccionado ?></h4>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-bg-primary h-100">
            <div class="card-body text-center">
                <h3><?= $resumen['total'] ?></h3>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-bg-warning h-100">
            <div class="card-body text-center">
                <h3><?= $resumen['pendientes'] ?></h3>
                <small>Pendientes</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-bg-info h-100">
            <div class="card-body text-center">
                <h3><?= $resumen['en_proceso'] ?></h3>
                <small>En proceso</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-bg-success h-100">
            <div class="card-body text-center">
                <h3><?= $resumen['atendidas'] ?></h3>
                <small>Atendidas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-bg-secondary h-100">
            <div class="card-body text-center">
                <h3><?= $resumen['cerradas'] ?></h3>
                <small>Cerradas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-bg-dark h-100">
            <div class="card-body text-center">
                <h3><?= $tasaResolucion ?>%</h3>
                <small>Tasa resolucion</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5><i class="bi bi-speedometer2"></i> Indicador clave</h5>
        <p>Tiempo promedio de atencion: <strong><?= $resumen['tiempo_promedio'] ?> horas</strong></p>
        <p>Tasa de resolucion: <strong><?= $tasaResolucion ?>%</strong> (atendidas + cerradas / total)</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Solicitudes por Categoria</div>
            <div class="card-body">
                <?php if (empty($porCategoria)): ?>
                    <p class="text-muted">Sin datos para este mes.</p>
                <?php else: ?>
                    <?php foreach ($porCategoria as $cat):
                        $porcentaje = $resumen['total'] > 0 ? round($cat['total'] / $resumen['total'] * 100, 1) : 0;
                    ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?= e($cat['nombre']) ?></span>
                                <span class="fw-bold"><?= $cat['total'] ?> (<?= $porcentaje ?>%)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-primary" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Solicitudes por Prioridad</div>
            <div class="card-body">
                <?php if (empty($porPrioridad)): ?>
                    <p class="text-muted">Sin datos para este mes.</p>
                <?php else: ?>
                    <?php foreach ($porPrioridad as $pri):
                        $porcentaje = $resumen['total'] > 0 ? round($pri['total'] / $resumen['total'] * 100, 1) : 0;
                        $colores = ['urgente' => 'bg-danger', 'alta' => 'bg-warning text-dark', 'media' => 'bg-info', 'baja' => 'bg-success'];
                        $color = $colores[$pri['prioridad']] ?? 'bg-secondary';
                    ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?= badgePrioridad($pri['prioridad']) ?></span>
                                <span class="fw-bold"><?= $pri['total'] ?> (<?= $porcentaje ?>%)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?= $color ?>" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-bold">Tendencia Diaria de Solicitudes</div>
    <div class="card-body">
        <?php if (empty($porDia)): ?>
            <p class="text-muted">Sin datos para este mes.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <?php foreach ($porDia as $d): ?>
                                <th>Dia <?= $d['dia'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($porDia as $d): ?>
                                <td class="fw-bold fs-5"><?= $d['total'] ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<button onclick="window.print()" class="btn btn-outline-dark mb-4">
    <i class="bi bi-printer"></i> Imprimir reporte
</button>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
