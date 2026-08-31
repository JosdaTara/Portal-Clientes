<?php
/**
 * Modulo de Inteligencia Artificial - Dashboard principal
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/ia_service.php';
requiereRol('agente', 'administrador');

$datos = recopilarDatosParaIA();
$respuestaIA = analisisAutomatico();

$tituloPagina = 'Analisis con IA';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-robot"></i> Analisis Inteligente</h2>
    <?php if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)): ?>
        <span class="badge bg-warning text-dark">
            <i class="bi bi-info-circle"></i> Modo Analisis Local (sin API key)
        </span>
    <?php else: ?>
        <span class="badge bg-success">
            <i class="bi bi-check-circle"></i> Gemini Conectado
        </span>
    <?php endif; ?>
</div>

<?php if (empty(GEMINI_API_KEY)): ?>
    <div class="alert alert-info">
        <h5><i class="bi bi-key"></i> Configurar API Key de Google Gemini</h5>
        <p class="mb-1">Para usar la IA completa, edita el archivo <code>config/api_key.php</code> y agrega tu API key:</p>
        <ol>
            <li>Ve a <a href="https://aistudio.google.com/apikey" target="_blank">aistudio.google.com/apikey</a></li>
            <li>Crea una cuenta Google (es gratis)</li>
            <li>Genera una API key (GRATIS - 15 req/min, 1M tokens/dia)</li>
            <li>Pegala en <code>config/api_key.php</code> como: <code>define('GEMINI_API_KEY', 'AIza...');</code></li>
        </ol>
        <p class="mb-0"><strong>Mientras tanto, el sistema funciona con analisis local basado en reglas.</strong></p>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-folder display-4"></i>
                <h2 class="mt-2"><?= $datos['total_solicitudes'] ?></h2>
                <small>Total Solicitudes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock display-4"></i>
                <h2 class="mt-2"><?= $datos['tiempo_promedio_h'] ?>h</h2>
                <small>Tiempo Prom. Atencion</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4"></i>
                <h2 class="mt-2">
                    <?= ($datos['total_solicitudes'] > 0)
                        ? round((($datos['por_estado']['atendida'] ?? 0) + ($datos['por_estado']['cerrada'] ?? 0)) / $datos['total_solicitudes'] * 100, 1)
                        : 0 ?>%
                </h2>
                <small>Tasa Resolucion</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger h-100">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle display-4"></i>
                <h2 class="mt-2"><?= $datos['por_estado']['pendiente'] ?? 0 ?></h2>
                <small>Pendientes</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-robot"></i> Analisis Automatico</h5>
        <form method="POST" action="" class="d-flex">
            <button type="submit" name="refrescar" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-clockwise"></i> Refrescar
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="analisis-ia" style="white-space: pre-wrap; line-height: 1.8;"><?= e($respuestaIA) ?></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-pie-chart"></i> Por Categoria</div>
            <div class="card-body">
                <?php foreach ($datos['por_categoria'] as $cat):
                    $pct = $datos['total_solicitudes'] > 0 ? round($cat['cantidad'] / $datos['total_solicitudes'] * 100) : 0;
                ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= e($cat['nombre']) ?></span>
                        <span class="fw-bold"><?= $cat['cantidad'] ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-clock-history"></i> Por Hora del Dia</div>
            <div class="card-body">
                <?php if (empty($datos['por_hora'])): ?>
                    <p class="text-muted">Sin datos.</p>
                <?php else: ?>
                    <?php foreach ($datos['por_hora'] as $h):
                        $horaLabel = str_pad($h['hora'], 2, '0', STR_PAD_LEFT) . ':00';
                        $maxHora = max(array_column($datos['por_hora'], 'cantidad'));
                        $pctH = $maxHora > 0 ? round($h['cantidad'] / $maxHora * 100) : 0;
                    ?>
                        <div class="d-flex align-items-center mb-1">
                            <small class="me-2" style="width:45px;"><?= $horaLabel ?></small>
                            <div class="progress flex-grow-1" style="height: 14px;">
                                <div class="progress-bar bg-info" style="width: <?= $pctH ?>%"></div>
                            </div>
                            <small class="ms-2 fw-bold"><?= $h['cantidad'] ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-flag"></i> Por Prioridad</div>
            <div class="card-body">
                <?php foreach ($datos['por_prioridad'] as $pri): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <?= badgePrioridad($pri['prioridad']) ?>
                        <span class="fw-bold"><?= $pri['cantidad'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-people"></i> Clientes Mas Frecuentes</div>
            <div class="card-body">
                <?php if (empty($datos['clientes_frecuentes'])): ?>
                    <p class="text-muted">Sin datos suficientes.</p>
                <?php else: ?>
                    <?php foreach ($datos['clientes_frecuentes'] as $cli): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= e($cli['nombre'] . ' ' . $cli['apellido']) ?></span>
                            <span class="badge bg-dark"><?= $cli['total'] ?> solicitudes</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
