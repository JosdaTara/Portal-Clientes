<?php
/**
 * Panel de gestion de solicitudes - Agentes y Administradores
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('agente', 'administrador');

$pdo = obtenerConexion();

$filtroEstado   = $_GET['estado'] ?? '';
$filtroCategoria = $_GET['categoria'] ?? '';
$filtroPrioridad = $_GET['prioridad'] ?? '';
$busqueda       = trim($_GET['buscar'] ?? '');

$sql = "SELECT s.*, c.nombre AS categoria_nombre,
               CONCAT(cl.nombre, ' ', cl.apellido) AS cliente_nombre, cl.email AS cliente_email
        FROM solicitudes s
        JOIN categorias c ON s.id_categoria = c.id_categoria
        JOIN usuarios cl ON s.id_cliente = cl.id_usuario
        WHERE 1=1";

$params = [];

if (!empty($filtroEstado) && in_array($filtroEstado, ['pendiente', 'en_proceso', 'atendida', 'cerrada'])) {
    $sql .= " AND s.estado = ?";
    $params[] = $filtroEstado;
}
if (!empty($filtroCategoria)) {
    $sql .= " AND s.id_categoria = ?";
    $params[] = intval($filtroCategoria);
}
if (!empty($filtroPrioridad) && in_array($filtroPrioridad, ['baja', 'media', 'alta', 'urgente'])) {
    $sql .= " AND s.prioridad = ?";
    $params[] = $filtroPrioridad;
}
if (!empty($busqueda)) {
    $sql .= " AND (s.numero_caso LIKE ? OR s.asunto LIKE ? OR cl.nombre LIKE ? OR cl.email LIKE ?)";
    $busq = "%{$busqueda}%";
    $params = array_merge($params, [$busq, $busq, $busq, $busq]);
}

$sql .= " ORDER BY 
            FIELD(s.prioridad, 'urgente', 'alta', 'media', 'baja'),
            s.fecha_creacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$solicitudes = $stmt->fetchAll();

$categorias = obtenerCategorias();

$tituloPagina = 'Gestionar Solicitudes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-inbox"></i> Gestionar Solicitudes</h2>
    <span class="badge bg-dark fs-6"><?= count($solicitudes) ?> caso(s)</span>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Buscar</label>
                <input type="text" class="form-control form-control-sm" name="buscar"
                       placeholder="Numero, asunto, cliente..." value="<?= e($busqueda) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach (['pendiente' => 'Pendiente', 'en_proceso' => 'En proceso', 'atendida' => 'Atendida', 'cerrada' => 'Cerrada'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $filtroEstado === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small">Categoria</label>
                <select name="categoria" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>" <?= intval($filtroCategoria) === $cat['id_categoria'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small">Prioridad</label>
                <select name="prioridad" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach (['urgente' => 'Urgente', 'alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $filtroPrioridad === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm me-1"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="/portal-atencion-cliente/modulo_atencion/index.php" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<?php if (empty($solicitudes)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No se encontraron solicitudes con los filtros aplicados.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Numero</th>
                    <th>Cliente</th>
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
                    <tr class="<?= $sol['estado'] === 'pendiente' ? 'table-warning' : '' ?>">
                        <td class="fw-bold"><?= e($sol['numero_caso']) ?></td>
                        <td>
                            <?= e($sol['cliente_nombre']) ?>
                            <br><small class="text-muted"><?= e($sol['cliente_email']) ?></small>
                        </td>
                        <td><?= e($sol['asunto']) ?></td>
                        <td><?= badgeCategoria($sol['categoria_nombre']) ?></td>
                        <td><?= badgePrioridad($sol['prioridad']) ?></td>
                        <td><?= badgeEstado($sol['estado']) ?></td>
                        <td><small><?= formatearFecha($sol['fecha_creacion']) ?></small></td>
                        <td>
                            <a href="/portal-atencion-cliente/modulo_atencion/detalle_solicitud.php?id=<?= $sol['id_solicitud'] ?>"
                               class="btn btn-sm btn-primary" title="Ver / gestionar">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
