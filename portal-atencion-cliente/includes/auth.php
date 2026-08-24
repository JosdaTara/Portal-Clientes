<?php
/**
 * Funciones de autenticacion y control de acceso
 */

require_once __DIR__ . '/../config/conexion.php';

function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function estaAutenticado(): bool
{
    return isset($_SESSION['id_usuario']);
}

function esRol(string $rol): bool
{
    return estaAutenticado() && $_SESSION['rol'] === $rol;
}

function esAgente(): bool
{
    return esRol('agente');
}

function esAdministrador(): bool
{
    return esRol('administrador');
}

function esCliente(): bool
{
    return esRol('cliente');
}

function esAgenteOAdmin(): bool
{
    return estaAutenticado() && in_array($_SESSION['rol'], ['agente', 'administrador']);
}

function usuarioActual(): ?array
{
    if (!estaAutenticado()) {
        return null;
    }
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('SELECT id_usuario, nombre, apellido, email, rol FROM usuarios WHERE id_usuario = ?');
    $stmt->execute([$_SESSION['id_usuario']]);
    return $stmt->fetch();
}

function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

function requiereAutenticacion(): void
{
    iniciarSesion();
    if (!estaAutenticado()) {
        redirigir('/portal-atencion-cliente/auth/login.php');
    }
}

function requiereRol(string ...$roles): void
{
    requiereAutenticacion();
    if (!in_array($_SESSION['rol'], $roles)) {
        redirigir('/portal-atencion-cliente/index.php');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarCSRF(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function login(string $email, string $contrasena): bool
{
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('SELECT id_usuario, nombre, apellido, contrasena, rol, estado_cuenta FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || $usuario['estado_cuenta'] !== 'activo') {
        return false;
    }

    if (!password_verify($contrasena, $usuario['contrasena'])) {
        return false;
    }

    session_regenerate_id(true);

    $_SESSION['id_usuario']  = $usuario['id_usuario'];
    $_SESSION['nombre']      = $usuario['nombre'];
    $_SESSION['apellido']    = $usuario['apellido'];
    $_SESSION['rol']         = $usuario['rol'];

    $stmtActualizar = $pdo->prepare('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?');
    $stmtActualizar->execute([$usuario['id_usuario']]);

    return true;
}

function logout(): void
{
    session_start();
    session_unset();
    session_destroy();
    header('Location: /portal-atencion-cliente/auth/login.php');
    exit;
}
