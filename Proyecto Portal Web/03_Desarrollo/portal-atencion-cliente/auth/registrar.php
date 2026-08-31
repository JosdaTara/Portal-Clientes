<?php
/**
 * Registro - Crear cuenta de cliente
 */
require_once __DIR__ . '/../includes/auth.php';
iniciarSesion();

if (estaAutenticado()) {
    redirigir('/portal-atencion-cliente/index.php');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $errores[] = 'Token de seguridad invalido.';
    } else {
        $nombre     = trim($_POST['nombre'] ?? '');
        $apellido   = trim($_POST['apellido'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $telefono   = trim($_POST['telefono'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $confirmar  = $_POST['confirmar_contrasena'] ?? '';

        if (empty($nombre))  $errores[] = 'El nombre es obligatorio.';
        if (empty($apellido)) $errores[] = 'El apellido es obligatorio.';
        if (empty($email))   $errores[] = 'El correo electronico es obligatorio.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo electronico no es valido.';
        if (strlen($contrasena) < 6) $errores[] = 'La contrasena debe tener al menos 6 caracteres.';
        if ($contrasena !== $confirmar) $errores[] = 'Las contrasenas no coinciden.';

        if (empty($errores)) {
            $pdo = obtenerConexion();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errores[] = 'Este correo electronico ya esta registrado.';
            }
        }

        if (empty($errores)) {
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (nombre, apellido, email, telefono, contrasena, rol)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$nombre, $apellido, $email, $telefono ?: null, $hash, 'cliente']);

            setFlash('success', 'Cuenta creada correctamente. Ahora puede iniciar sesion.');
            redirigir('/portal-atencion-cliente/auth/login.php');
        }
    }
}

$tituloPagina = 'Crear Cuenta';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">
                    <i class="bi bi-person-plus"></i><br>
                    Crear Cuenta
                </h3>

                <?php foreach ($errores as $error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endforeach; ?>

                <form method="POST" action="" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <div class="row mb-3">
                        <div class="col">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                   value="<?= e($_POST['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="col">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido"
                                   value="<?= e($_POST['apellido'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electronico *</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Telefono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono"
                               value="<?= e($_POST['telefono'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contrasena * <small class="text-muted">(min. 6 caracteres)</small></label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required minlength="6">
                    </div>

                    <div class="mb-3">
                        <label for="confirmar_contrasena" class="form-label">Confirmar contrasena *</label>
                        <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-person-plus"></i> Crear Cuenta
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="/portal-atencion-cliente/auth/login.php">Ya tengo una cuenta</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
