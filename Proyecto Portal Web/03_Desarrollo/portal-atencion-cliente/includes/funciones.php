<?php
/**
 * Funciones auxiliares del sistema
 */

require_once __DIR__ . '/../config/conexion.php';

/**
 * Genera el siguiente numero de caso unico
 */
function generarNumeroCaso(): string
{
    $pdo  = obtenerConexion();
    $anio = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM solicitudes WHERE YEAR(fecha_creacion) = ?");
    $stmt->execute([$anio]);
    $consecutivo = $stmt->fetchColumn();
    return 'CASO-' . $anio . '-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);
}

/**
 * Escapa un valor para impresion segura en HTML
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatea una fecha para mostrar
 */
function formatearFecha(string $fecha, string $formato = 'd/m/Y H:i'): string
{
    return date($formato, strtotime($fecha));
}

/**
 * Retorna el badge HTML para un estado de solicitud
 */
function badgeEstado(string $estado): string
{
    $clases = [
        'pendiente'  => 'bg-warning text-dark',
        'en_proceso' => 'bg-info text-dark',
        'atendida'   => 'bg-success',
        'cerrada'    => 'bg-secondary',
    ];
    $clase = $clases[$estado] ?? 'bg-secondary';
    $texto = ucfirst(str_replace('_', ' ', $estado));
    return '<span class="badge ' . $clase . '">' . $texto . '</span>';
}

/**
 * Retorna el badge HTML para una prioridad
 */
function badgePrioridad(string $prioridad): string
{
    $clases = [
        'baja'   => 'bg-success',
        'media'  => 'bg-warning text-dark',
        'alta'   => 'bg-danger',
        'urgente'=> 'bg-danger fw-bold',
    ];
    $clase = $clases[$prioridad] ?? 'bg-secondary';
    return '<span class="badge ' . $clase . '">' . ucfirst($prioridad) . '</span>';
}

/**
 * Retorna el badge HTML para una categoria de solicitud
 */
function badgeCategoria(string $categoria): string
{
    return '<span class="badge bg-primary">' . $categoria . '</span>';
}

/**
 * Obtiene todas las categorias activas
 */
function obtenerCategorias(): array
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->query("SELECT id_categoria, nombre, descripcion FROM categorias WHERE estado = 'activa' ORDER BY nombre");
    return $stmt->fetchAll();
}

/**
 * Flash messages para notificaciones en sesion
 */
function setFlash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function getFlash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

function renderFlash(): string
{
    $flash = getFlash();
    $html  = '';
    foreach ($flash as $msg) {
        $tipo = $msg['tipo'] === 'error' ? 'danger' : $msg['tipo'];
        $html .= '<div class="alert alert-' . $tipo . ' alert-dismissible fade show" role="alert">';
        $html .= $msg['mensaje'];
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        $html .= '</div>';
    }
    return $html;
}
