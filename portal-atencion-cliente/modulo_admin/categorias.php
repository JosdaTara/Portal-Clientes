<?php
/**
 * Gestion de categorias - Solo administradores
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('administrador');

$pdo = obtenerConexion();
$errores = [];

// Eliminar (desactivar) categoria
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    if ($idEliminar > 0) {
        $stmt = $pdo->prepare("UPDATE categorias SET estado = 'inactiva' WHERE id_categoria = ?");
        $stmt->execute([$idEliminar]);
        setFlash('success', 'Categoria desactivada.');
    }
    redirigir('/portal-atencion-cliente/modulo_admin/categorias.php');
}

// Activar categoria
if (isset($_GET['activar'])) {
    $idActivar = intval($_GET['activar']);
    if ($idActivar > 0) {
        $stmt = $pdo->prepare("UPDATE categorias SET estado = 'activa' WHERE id_categoria = ?");
        $stmt->execute([$idActivar]);
        setFlash('success', 'Categoria activada.');
    }
    redirigir('/portal-atencion-cliente/modulo_admin/categorias.php');
}

// Editar
$editando = null;
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id_categoria = ?');
    $stmt->execute([$idEditar]);
    $editando = $stmt->fetch();
}

// Crear o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $errores[] = 'Token de seguridad invalido.';
    } else {
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $idCategoria = intval($_POST['id_categoria'] ?? 0);

        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (strlen($nombre) > 100) $errores[] = 'El nombre no puede exceder 100 caracteres.';

        if (empty($errores)) {
            if ($idCategoria > 0) {
                $stmtUpd = $pdo->prepare('UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?');
                $stmtUpd->execute([$nombre, $descripcion ?: null, $idCategoria]);
                setFlash('success', 'Categoria actualizada.');
            } else {
                try {
                    $stmtIns = $pdo->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)');
                    $stmtIns->execute([$nombre, $descripcion ?: null]);
                    setFlash('success', 'Categoria creada.');
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = 'Ya existe una categoria con ese nombre.';
                    } else {
                        throw $e;
                    }
                }
            }
            if (empty($errores)) {
                redirigir('/portal-atencion-cliente/modulo_admin/categorias.php');
            }
        }
    }
}

$categorias = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM solicitudes s WHERE s.id_categoria = c.id_categoria) AS total_solicitudes FROM categorias c ORDER BY c.nombre')->fetchAll();

$tituloPagina = 'Gestionar Categorias';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-tags"></i> Gestionar Categorias</h2>
    <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formCategoria">
        <i class="bi bi-plus-circle"></i> Nueva Categoria
    </button>
</div>

<div class="collapse <?= ($editando || !empty($errores)) ? 'show' : '' ?> mb-4" id="formCategoria">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <?= $editando ? 'Editar Categoria' : 'Crear Nueva Categoria' ?>
        </div>
        <div class="card-body">
            <?php foreach ($errores as $error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endforeach; ?>

            <form method="POST" action="" novalidate>
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="id_categoria" value="<?= $editando ? $editando['id_categoria'] : 0 ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input type="text" class="form-control" name="nombre" maxlength="100" required
                               value="<?= e($editando['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Descripcion</label>
                        <input type="text" class="form-control" name="descripcion"
                               value="<?= e($editando['descripcion'] ?? $_POST['descripcion'] ?? '') ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> <?= $editando ? 'Actualizar' : 'Crear' ?>
                    </button>
                    <?php if ($editando): ?>
                        <a href="/portal-atencion-cliente/modulo_admin/categorias.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Estado</th>
                <th>Solicitudes</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $cat): ?>
                <tr class="<?= $cat['estado'] === 'inactiva' ? 'table-secondary' : '' ?>">
                    <td><?= $cat['id_categoria'] ?></td>
                    <td class="fw-bold"><?= e($cat['nombre']) ?></td>
                    <td><?= e($cat['descripcion'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $cat['estado'] === 'activa' ? 'bg-success' : 'bg-danger' ?>">
                            <?= e(ucfirst($cat['estado'])) ?>
                        </span>
                    </td>
                    <td><?= $cat['total_solicitudes'] ?></td>
                    <td>
                        <a href="?editar=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($cat['estado'] === 'activa'): ?>
                            <a href="?eliminar=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-outline-warning"
                               onclick="return confirm('Desactivar esta categoria?')" title="Desactivar">
                                <i class="bi bi-eye-slash"></i>
                            </a>
                        <?php else: ?>
                            <a href="?activar=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-outline-success" title="Activar">
                                <i class="bi bi-eye"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
