<?php
/**
 * Login - Inicio de sesion
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
        $email      = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        if (empty($email) || empty($contrasena)) {
            $errores[] = 'Debe completar todos los campos.';
        } elseif (login($email, $contrasena)) {
            $rol = $_SESSION['rol'];
            if ($rol === 'cliente') {
                redirigir('/portal-atencion-cliente/modulo_cliente/index.php');
            } elseif (in_array($rol, ['agente', 'administrador'])) {
                redirigir('/portal-atencion-cliente/modulo_atencion/index.php');
            }
            redirigir('/portal-atencion-cliente/index.php');
        } else {
            $errores[] = 'Credenciales incorrectas o cuenta inactiva.';
        }
    }
}

$tituloPagina = 'Iniciar Sesion';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">
                    <i class="bi bi-box-arrow-in-right"></i><br>
                    Iniciar Sesion
                </h3>

                <?php foreach ($errores as $error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endforeach; ?>

                <form method="POST" action="" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electronico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contrasena</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="/portal-atencion-cliente/auth/registrar.php">Crear cuenta nueva</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
