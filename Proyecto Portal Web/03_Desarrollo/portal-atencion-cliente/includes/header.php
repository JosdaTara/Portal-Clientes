<?php
/**
 * Cabecera HTML comun del portal
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/funciones.php';
iniciarSesion();

$usuarioActual = usuarioActual();
$rol           = $usuarioActual['rol'] ?? '';
$nombreUsuario = trim(($usuarioActual['nombre'] ?? '') . ' ' . ($usuarioActual['apellido'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina ?? 'Portal de Atencion al Cliente') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/portal-atencion-cliente/assets/css/estilo.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/portal-atencion-cliente/index.php">
            <i class="bi bi-headset"></i> Portal de Atencion
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navPrincipal">
            <ul class="navbar-nav me-auto">
                <?php if (estaAutenticado()): ?>
                    <?php if ($rol === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/portal-atencion-cliente/modulo_cliente/registrar_solicitud.php">
                                <i class="bi bi-plus-circle"></i> Nueva Solicitud
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/portal-atencion-cliente/modulo_cliente/historial.php">
                                <i class="bi bi-list-ul"></i> Mis Solicitudes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/portal-atencion-cliente/modulo_cliente/ia_cliente.php">
                                <i class="bi bi-robot"></i> Asistente IA
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($rol, ['agente', 'administrador'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/portal-atencion-cliente/modulo_atencion/index.php">
                                <i class="bi bi-inbox"></i> Gestionar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/portal-atencion-cliente/modulo_reportes/index.php">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-robot"></i> IA
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/portal-atencion-cliente/modulo_ia/index.php"><i class="bi bi-graph-up"></i> Analisis Automatico</a></li>
                                <li><a class="dropdown-item" href="/portal-atencion-cliente/modulo_ia/consultar.php"><i class="bi bi-chat-dots"></i> Consultar IA</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($rol === 'administrador'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i> Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/portal-atencion-cliente/modulo_admin/usuarios.php">Usuarios</a></li>
                                <li><a class="dropdown-item" href="/portal-atencion-cliente/modulo_admin/categorias.php">Categorias</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if (estaAutenticado()): ?>
                    <li class="nav-item">
                        <span class="nav-link text-light">
                            <i class="bi bi-person-circle"></i>
                            <?= e($nombreUsuario) ?>
                            <span class="badge bg-light text-dark ms-1"><?= e(ucfirst($rol)) ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/portal-atencion-cliente/auth/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/portal-atencion-cliente/auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?= renderFlash() ?>
