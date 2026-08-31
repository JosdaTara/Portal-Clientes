<?php
/**
 * Detalle de solicitud + Formulario de seguimiento
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('agente', 'administrador');

$idSolicitud = intval($_GET['id'] ?? 0);
if ($idSolicitud <= 0) {
    redirigir('/portal-atencion-cliente/modulo_atencion/index.php');
}

$pdo = obtenerConexion();

$stmt = $pdo->prepare(
    'SELECT s.*, c.nombre AS categoria_nombre,
            CONCAT(cl.nombre, " ", cl.apellido) AS cliente_nombre,
            cl.email AS cliente_email, cl.telefono AS cliente_telefono
     FROM solicitudes s
     JOIN categorias c ON s.id_categoria = c.id_categoria
     JOIN usuarios cl ON s.id_cliente = cl.id_usuario
     WHERE s.id_solicitud = ?'
);
$stmt->execute([$idSolicitud]);
$solicitud = $stmt->fetch();

if (!$solicitud) {
    setFlash('error', 'Solicitud no encontrada.');
    redirigir('/portal-atencion-cliente/modulo_atencion/index.php');
}

$stmtSeg = $pdo->prepare(
    'SELECT seg.*, CONCAT(u.nombre, " ", u.apellido) AS usuario_nombre, u.rol AS usuario_rol
     FROM seguimientos seg
     JOIN usuarios u ON seg.id_usuario = u.id_usuario
     WHERE seg.id_solicitud = ?
     ORDER BY seg.fecha_seguimiento ASC'
);
$stmtSeg->execute([$idSolicitud]);
$seguimientos = $stmtSeg->fetchAll();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $errores[] = 'Token de seguridad invalido.';
    } else {
        $accion       = $_POST['accion'] ?? '';
        $comentario   = trim($_POST['comentario'] ?? '');
        $nuevoEstado  = $_POST['nuevo_estado'] ?? '';

        if (empty($comentario)) $errores[] = 'El comentario es obligatorio.';

        $estadosPermitidos = ['pendiente', 'en_proceso', 'atendida', 'cerrada'];

        if ($accion === 'cambiar_estado') {
            if (!in_array($nuevoEstado, $estadosPermitidos)) {
                $errores[] = 'Estado no valido.';
            }
            $transiciones = [
                'pendiente'  => ['en_proceso', 'atendida', 'cerrada'],
                'en_proceso' => ['atendida', 'cerrada'],
                'atendida'   => ['cerrada', 'en_proceso'],
                'cerrada'    => [],
            ];
            if (!in_array($nuevoEstado, $transiciones[$solicitud['estado']] ?? [])) {
                $errores[] = 'No se puede cambiar de "' . ucfirst(str_replace('_', ' ', $solicitud['estado'])) . '" a "' . ucfirst(str_replace('_', ' ', $nuevoEstado)) . '".';
            }
        }

        if (empty($errores)) {
            $estadoAnterior = $solicitud['estado'];
            $estadoNuevo    = ($accion === 'cambiar_estado') ? $nuevoEstado : null;

            $stmtSegIns = $pdo->prepare(
                'INSERT INTO seguimientos (id_solicitud, id_usuario, comentario, estado_anterior, estado_nuevo)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmtSegIns->execute([$idSolicitud, $_SESSION['id_usuario'], $comentario, $estadoAnterior, $estadoNuevo]);

            if ($accion === 'cambiar_estado') {
                $sqlUpd = 'UPDATE solicitudes SET estado = ?';
                $paramsUpd = [$nuevoEstado];

                if ($nuevoEstado === 'cerrada') {
                    $sqlUpd .= ', fecha_cierre = NOW()';
                } elseif ($solicitud['estado'] === 'cerrada' && $nuevoEstado !== 'cerrada') {
                    $sqlUpd .= ', fecha_cierre = NULL';
                }

                $sqlUpd .= ' WHERE id_solicitud = ?';
                $paramsUpd[] = $idSolicitud;

                $stmtUpd = $pdo->prepare($sqlUpd);
                $stmtUpd->execute($paramsUpd);
            }

            setFlash('success', 'Seguimiento registrado correctamente.');
            redirigir('/portal-atencion-cliente/modulo_atencion/detalle_solicitud.php?id=' . $idSolicitud);
        }
    }
}

$tituloPagina = 'Detalle Solicitud ' . $solicitud['numero_caso'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <a href="/portal-atencion-cliente/modulo_atencion/index.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-folder2-open"></i> <?= e($solicitud['numero_caso']) ?> — <?= e($solicitud['asunto']) ?>
                </h5>
                <?= badgeEstado($solicitud['estado']) ?>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <strong>Cliente:</strong><br>
                        <?= e($solicitud['cliente_nombre']) ?><br>
                        <small class="text-muted"><?= e($solicitud['cliente_email']) ?></small><br>
                        <small class="text-muted"><?= e($solicitud['cliente_telefono'] ?? 'Sin telefono') ?></small>
                    </div>
                    <div class="col-md-3">
                        <strong>Categoria:</strong><br>
                        <?= badgeCategoria($solicitud['categoria_nombre']) ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Prioridad:</strong><br>
                        <?= badgePrioridad($solicitud['prioridad']) ?>
                    </div>
                    <div class="col-md-2">
                        <strong>Estado actual:</strong><br>
                        <?= badgeEstado($solicitud['estado']) ?>
                    </div>
                    <div class="col-12">
                        <strong>Descripcion:</strong><br>
                        <div class="bg-light p-3 rounded border"><?= nl2br(e($solicitud['descripcion'])) ?></div>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha creacion:</strong><br>
                        <?= formatearFecha($solicitud['fecha_creacion']) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Ultima actualizacion:</strong><br>
                        <?= $solicitud['fecha_actualizacion'] ? formatearFecha($solicitud['fecha_actualizacion']) : 'N/A' ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha cierre:</strong><br>
                        <?= $solicitud['fecha_cierre'] ? formatearFecha($solicitud['fecha_cierre']) : 'Sin cerrar' ?>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-3"><i class="bi bi-clock-history"></i> Historial de Seguimiento</h5>

        <?php if (empty($seguimientos)): ?>
            <div class="alert alert-secondary">No hay seguimientos registrados.</div>
        <?php else: ?>
            <?php foreach ($seguimientos as $seg): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong class="text-primary"><?= e($seg['usuario_nombre']) ?></strong>
                                <span class="badge bg-secondary ms-1"><?= e(ucfirst($seg['usuario_rol'])) ?></span>
                            </div>
                            <small class="text-muted"><?= formatearFecha($seg['fecha_seguimiento']) ?></small>
                        </div>
                        <?php if ($seg['estado_anterior'] || $seg['estado_nuevo']): ?>
                            <div class="my-1">
                                <?php if ($seg['estado_anterior']): ?>
                                    <?= badgeEstado($seg['estado_anterior']) ?>
                                <?php endif; ?>
                                <i class="bi bi-arrow-right mx-1"></i>
                                <?php if ($seg['estado_nuevo']): ?>
                                    <?= badgeEstado($seg['estado_nuevo']) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <p class="mb-0 mt-2"><?= nl2br(e($seg['comentario'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($solicitud['estado'] !== 'cerrada'): ?>
            <div class="card border-primary mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Registrar Seguimiento</h5>
                </div>
                <div class="card-body">

                    <?php foreach ($errores as $error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endforeach; ?>

                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                        <div class="mb-3">
                            <label for="comentario" class="form-label fw-bold">Comentario / Observacion *</label>
                            <textarea class="form-control" id="comentario" name="comentario"
                                      rows="4" required placeholder="Describa la accion realizada o la observacion..."><?= e($_POST['comentario'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cambiar estado (opcional):</label><br>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="accion" id="accion_seg" value="solo_seguimiento" checked>
                                    <label class="form-check-label" for="accion_seg">Solo registrar seguimiento</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="accion" id="accion_estado" value="cambiar_estado">
                                    <label class="form-check-label" for="accion_estado">Cambiar estado de la solicitud</label>
                                </div>
                            </div>
                        </div>

                        <div id="div_nuevo_estado" class="mb-3" style="display:none;">
                            <label for="nuevo_estado" class="form-label fw-bold">Nuevo estado:</label>
                            <select class="form-select" id="nuevo_estado" name="nuevo_estado">
                                <option value="">-- Seleccionar --</option>
                                <option value="en_proceso">En proceso</option>
                                <option value="atendida">Atendida</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mt-4">
                <i class="bi bi-lock"></i> Esta solicitud esta cerrada. No se pueden agregar mas seguimientos.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
