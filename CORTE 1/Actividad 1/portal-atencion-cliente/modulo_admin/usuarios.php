<?php
/**
 * Gestion de usuarios - Solo administradores
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('administrador');

$pdo = obtenerConexion();
$errores = [];

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    if ($idEliminar > 0 && $idEliminar !== $_SESSION['id_usuario']) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM solicitudes WHERE id_cliente = ?');
        $stmt->execute([$idEliminar]);
        $cantSol = $stmt->fetchColumn();

        if ($cantSol > 0) {
            $stmt = $pdo->prepare("UPDATE usuarios SET estado_cuenta = 'inactivo' WHERE id_usuario = ?");
            $stmt->execute([$idEliminar]);
            setFlash('success', 'Usuario desactivado (tiene solicitudes asociadas).');
        } else {
            $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
            $stmt->execute([$idEliminar]);
            setFlash('success', 'Usuario eliminado.');
        }
    }
    redirigir('/portal-atencion-cliente/modulo_admin/usuarios.php');
}

// Crear o editar usuario
$editando = null;
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id_usuario = ?');
    $stmt->execute([$idEditar]);
    $editando = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $errores[] = 'Token de seguridad invalido.';
    } else {
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['apellido'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $rol       = $_POST['rol'] ?? 'cliente';
        $estado    = $_POST['estado_cuenta'] ?? 'activo';
        $contrasena = $_POST['contrasena'] ?? '';
        $idUsuario  = intval($_POST['id_usuario'] ?? 0);

        if (empty($nombre))  $errores[] = 'El nombre es obligatorio.';
        if (empty($apellido)) $errores[] = 'El apellido es obligatorio.';
        if (empty($email))   $errores[] = 'El email es obligatorio.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email no valido.';
        if (!in_array($rol, ['cliente', 'agente', 'administrador'])) $errores[] = 'Rol no valido.';
        if ($idUsuario <= 0 && empty($contrasena)) $errores[] = 'La contrasena es obligatoria para nuevos usuarios.';
        if (!empty($contrasena) && strlen($contrasena) < 6) $errores[] = 'La contrasena debe tener minimo 6 caracteres.';

        if (empty($errores)) {
            if ($idUsuario > 0) {
                $sqlUpd = 'UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, rol = ?, estado_cuenta = ?';
                $paramsUpd = [$nombre, $apellido, $email, $telefono ?: null, $rol, $estado];
                if (!empty($contrasena)) {
                    $sqlUpd .= ', contrasena = ?';
                    $paramsUpd[] = password_hash($contrasena, PASSWORD_BCRYPT);
                }
                $sqlUpd .= ' WHERE id_usuario = ?';
                $paramsUpd[] = $idUsuario;
                $stmtUpd = $pdo->prepare($sqlUpd);
                $stmtUpd->execute($paramsUpd);
                setFlash('success', 'Usuario actualizado correctamente.');
            } else {
                $stmtIns = $pdo->prepare(
                    'INSERT INTO usuarios (nombre, apellido, email, telefono, contrasena, rol)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmtIns->execute([$nombre, $apellido, $email, $telefono ?: null, password_hash($contrasena, PASSWORD_BCRYPT), $rol]);
                setFlash('success', 'Usuario creado correctamente.');
            }
            redirigir('/portal-atencion-cliente/modulo_admin/usuarios.php');
        }
    }
}

$stmtUsuarios = $pdo->query(
    'SELECT u.*, 
            (SELECT COUNT(*) FROM solicitudes s WHERE s.id_cliente = u.id_usuario) AS total_solicitudes
     FROM usuarios u
     ORDER BY u.fecha_registro DESC'
);
$usuarios = $stmtUsuarios->fetchAll();

$tituloPagina = 'Gestionar Usuarios';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-people"></i> Gestionar Usuarios</h2>
    <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formUsuario">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
    </button>
</div>

<div class="collapse <?= ($editando || !empty($errores)) ? 'show' : '' ?> mb-4" id="formUsuario">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <?= $editando ? 'Editar Usuario' : 'Crear Nuevo Usuario' ?>
        </div>
        <div class="card-body">
            <?php foreach ($errores as $error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endforeach; ?>

            <form method="POST" action="" novalidate>
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="id_usuario" value="<?= $editando ? $editando['id_usuario'] : 0 ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input type="text" class="form-control" name="nombre"
                               value="<?= e($editando['nombre'] ?? $_POST['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Apellido *</label>
                        <input type="text" class="form-control" name="apellido"
                               value="<?= e($editando['apellido'] ?? $_POST['apellido'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control" name="email"
                               value="<?= e($editando['email'] ?? $_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Telefono</label>
                        <input type="text" class="form-control" name="telefono"
                               value="<?= e($editando['telefono'] ?? $_POST['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Rol *</label>
                        <select class="form-select" name="rol" required>
                            <?php foreach (['cliente' => 'Cliente', 'agente' => 'Agente', 'administrador' => 'Administrador'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= ($editando['rol'] ?? ($_POST['rol'] ?? 'cliente')) === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado</label>
                        <select class="form-select" name="estado_cuenta">
                            <option value="activo" <?= ($editando['estado_cuenta'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= ($editando['estado_cuenta'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Contrasena <?= $editando ? '(dejar vacio para no cambiar)' : '*' ?></label>
                        <input type="password" class="form-control" name="contrasena"
                               minlength="6" <?= $editando ? '' : 'required' ?>>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> <?= $editando ? 'Actualizar' : 'Crear' ?>
                    </button>
                    <?php if ($editando): ?>
                        <a href="/portal-atencion-cliente/modulo_admin/usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
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
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Solicitudes</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr class="<?= $u['estado_cuenta'] === 'inactivo' ? 'table-secondary' : '' ?>">
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= e($u['nombre'] . ' ' . $u['apellido']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge bg-dark"><?= e(ucfirst($u['rol'])) ?></span></td>
                    <td>
                        <span class="badge <?= $u['estado_cuenta'] === 'activo' ? 'bg-success' : 'bg-danger' ?>">
                            <?= e(ucfirst($u['estado_cuenta'])) ?>
                        </span>
                    </td>
                    <td><?= $u['total_solicitudes'] ?></td>
                    <td><small><?= formatearFecha($u['fecha_registro']) ?></small></td>
                    <td>
                        <a href="?editar=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id_usuario'] !== $_SESSION['id_usuario']): ?>
                            <a href="?eliminar=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Seguro que desea eliminar/desactivar este usuario?')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
