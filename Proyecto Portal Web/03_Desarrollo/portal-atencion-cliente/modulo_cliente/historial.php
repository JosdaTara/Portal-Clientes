<?php
/**
 * Historial de solicitudes del cliente
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('cliente');

$pdo   = obtenerConexion();
$idCli = $_SESSION['id_usuario'];

$filtroEstado = $_GET['estado'] ?? '';
$orden        = $_GET['orden'] ?? 'fecha_creacion';
$dir          = $_GET['dir'] ?? 'DESC';

$permitidosOrden = ['fecha_creacion', 'fecha_actualizacion', 'prioridad', 'estado'];
$permitidosDir   = ['ASC', 'DESC'];
if (!in_array($orden, $permitidosOrden)) $orden = 'fecha_creacion';
if (!in_array(strtoupper($dir), $permitidosDir)) $dir = 'DESC';

$sql = "SELECT s.*, c.nombre AS categoria_nombre
        FROM solicitudes s
        JOIN categorias c ON s.id_categoria = c.id_categoria
        WHERE s.id_cliente = ?";

$params = [$idCli];

if (!empty($filtroEstado) && in_array($filtroEstado, ['pendiente', 'en_proceso', 'atendida', 'cerrada'])) {
    $sql .= " AND s.estado = ?";
    $params[] = $filtroEstado;
}

$sql .= " ORDER BY s.{$orden} {$dir}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$solicitudes = $stmt->fetchAll();

$tituloPagina = 'Mis Solicitudes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-list-ul"></i> Mis Solicitudes</h2>
    <a href="/portal-atencion-cliente/modulo_cliente/registrar_solicitud.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Solicitud
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label fw-bold">Estado:</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach (['pendiente' => 'Pendiente', 'en_proceso' => 'En proceso', 'atendida' => 'Atendida', 'cerrada' => 'Cerrada'] as $val => $lab): ?>
                        <option value="<?= $val ?>" <?= $filtroEstado === $val ? 'selected' : '' ?>><?= $lab ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label fw-bold">Ordenar por:</label>
                <select name="orden" class="form-select form-select-sm">
                    <?php foreach ($permitidosOrden as $o): ?>
                        <option value="<?= $o ?>" <?= $orden === $o ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $o)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($solicitudes)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No tiene solicitudes registradas
        <?php if (!empty($filtroEstado)): ?> con el filtro seleccionado <?php endif; ?>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Numero</th>
                    <th>Asunto</th>
                    <th>Categoria</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $sol): ?>
                    <tr>
                        <td class="fw-bold"><?= e($sol['numero_caso']) ?></td>
                        <td><?= e($sol['asunto']) ?></td>
                        <td><?= badgeCategoria($sol['categoria_nombre']) ?></td>
                        <td><?= badgePrioridad($sol['prioridad']) ?></td>
                        <td><?= badgeEstado($sol['estado']) ?></td>
                        <td><small><?= formatearFecha($sol['fecha_creacion']) ?></small></td>
                        <td>
                            <a href="/portal-atencion-cliente/modulo_cliente/consultar_solicitud.php?numero_caso=<?= e($sol['numero_caso']) ?>"
                               class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted"><small>Total: <?= count($solicitudes) ?> solicitud(es)</small></p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
