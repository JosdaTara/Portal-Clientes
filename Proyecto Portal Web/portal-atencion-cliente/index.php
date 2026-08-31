<?php
/**
 * Index - Página principal / redireccionador
 */
require_once __DIR__ . '/includes/auth.php';
iniciarSesion();

if (estaAutenticado()) {
    $rol = $_SESSION['rol'];
    if ($rol === 'cliente') {
        redirigir('/portal-atencion-cliente/modulo_cliente/index.php');
    } elseif (in_array($rol, ['agente', 'administrador'])) {
        redirigir('/portal-atencion-cliente/modulo_atencion/index.php');
    }
}

redirigir('/portal-atencion-cliente/auth/login.php');
