<?php
/**
 * Consultar estado de una solicitud por numero de caso
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAutenticacion();

$numeroBuscado = trim($_GET['numero_caso'] ?? $_POST['numero_caso'] ?? '');
$solicitud     = null;
$seguimientos  = [];

if (!empty($numeroBuscado)) {
    $pdo = obtenerConexion();

    if (esRol('cliente')) {
        $stmt = $pdo->prepare(
            'SELECT s.*, c.nombre AS categoria_nombre
             FROM solicitudes s
             JOIN categorias c ON s.id_categoria = c.id_categoria
             WHERE s.numero_caso = ? AND s.id_cliente = ?'
        );
        $stmt->execute([$numeroBuscado, $_SESSION['id_usuario']]);
    } else {
        $stmt = $pdo->prepare(
            'SELECT s.*, c.nombre AS categoria_nombre
             FROM solicitudes s
             JOIN categorias c ON s.id_categoria = c.id_categoria
             WHERE s.numero_caso = ?'
        );
        $stmt->execute([$numeroBuscado]);
    }

    $solicitud = $stmt->fetch();

    if ($solicitud) {
        $stmtSeg = $pdo->prepare(
            'SELECT seg.*, CONCAT(u.nombre, " ", u.apellido) AS usuario_nombre
             FROM seguimientos seg
             JOIN usuarios u ON seg.id_usuario = u.id_usuario
             WHERE seg.id_solicitud = ?
             ORDER BY seg.fecha_seguimiento ASC'
        );
        $stmtSeg->execute([$solicitud['id_solicitud']]);
        $seguimientos = $stmtSeg->fetchAll();
    }
}

$tituloPagina = 'Consultar Solicitud';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <h2><i class="bi bi-search"></i> Consultar Solicitud</h2>
        <p class="text-muted">Ingrese el numero de caso para ver los detalles y el historial de seguimiento.</p>

        <form method="GET" action="" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control form-control-lg"
                       name="numero_caso" placeholder="CASO-2026-00001"
                       value="<?= e($numeroBuscado) ?>" required>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>

        <?php if (!empty($numeroBuscado) && !$solicitud): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                No se encontro ninguna solicitud con el numero <strong><?= e($numeroBuscado) ?></strong>.
                <?php if (esRol('cliente')): ?>
                    <br><small>Si es su solicitud, verifique que el numero sea correcto.</small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($solicitud): ?>
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder2-open"></i> <?= e($solicitud['numero_caso']) ?>
                    </h5>
                    <?= badgeEstado($solicitud['estado']) ?>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Asunto:</strong><br>
                            <?= e($solicitud['asunto']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Categoria:</strong><br>
                            <?= badgeCategoria($solicitud['categoria_nombre']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Prioridad:</strong><br>
                            <?= badgePrioridad($solicitud['prioridad']) ?>
                        </div>
                        <div class="col-12">
                            <strong>Descripcion:</strong><br>
                            <div class="bg-light p-3 rounded"><?= nl2br(e($solicitud['descripcion'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha de creacion:</strong><br>
                            <?= formatearFecha($solicitud['fecha_creacion']) ?>
                        </div>
                        <div class="col-md-6">
                            <?php if ($solicitud['fecha_cierre']): ?>
                                <strong>Fecha de cierre:</strong><br>
                                <?= formatearFecha($solicitud['fecha_cierre']) ?>
                            <?php else: ?>
                                <strong>Ultima actualizacion:</strong><br>
                                <?= $solicitud['fecha_actualizacion'] ? formatearFecha($solicitud['fecha_actualizacion']) : 'N/A' ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($seguimientos)): ?>
                <h5 class="mb-3"><i class="bi bi-clock-history"></i> Historial de Seguimiento</h5>
                <div class="timeline">
                    <?php foreach ($seguimientos as $i => $seg): ?>
                        <div class="card mb-3 <?= $i === count($seguimientos) - 1 ? 'border-primary' : '' ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="text-primary"><?= e($seg['usuario_nombre']) ?></strong>
                                    <small class="text-muted"><?= formatearFecha($seg['fecha_seguimiento']) ?></small>
                                </div>
                                <?php if ($seg['estado_anterior'] || $seg['estado_nuevo']): ?>
                                    <div class="mb-1">
                                        <?php if ($seg['estado_anterior']): ?>
                                            <?= badgeEstado($seg['estado_anterior']) ?>
                                        <?php endif; ?>
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        <?php if ($seg['estado_nuevo']): ?>
                                            <?= badgeEstado($seg['estado_nuevo']) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <p class="mb-0"><?= nl2br(e($seg['comentario'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
