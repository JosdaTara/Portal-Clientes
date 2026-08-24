<?php
/**
 * Consulta personalizada a la IA
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/ia_service.php';
requiereRol('agente', 'administrador');

$respuestaIA = null;
$pregunta    = '';
$usandoIA    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pregunta = trim($_POST['pregunta'] ?? '');
    if (!empty($pregunta)) {
        $resultado   = analizarConIA($pregunta);
        $respuestaIA = $resultado['respuesta'];
        $usandoIA    = $resultado['usando_ia'];
    }
}

$tituloPagina = 'Consultar IA';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <h2><i class="bi bi-chat-dots"></i> Consulta Inteligente</h2>
        <p class="text-muted">Haz una pregunta sobre los datos de las solicitudes y la IA te respondera con analisis y recomendaciones.</p>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="" novalidate>
                    <div class="mb-3">
                        <label for="pregunta" class="form-label fw-bold">Tu pregunta:</label>
                        <textarea class="form-control" id="pregunta" name="pregunta" rows="3" required
                                  placeholder="Ejemplo: ¿Qué categorías de solicitudes deberíamos priorizar? ¿Hay patrones de problemas recurrentes? ¿Cómo mejorar el tiempo de respuesta?"
                        ><?= e($pregunta) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Consultar IA
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <strong>Preguntas sugeridas:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <?php
                $sugerencias = [
                    '¿Cuáles son los principales problemas reportados por los clientes?',
                    '¿Qué categorías de solicitudes deberían tener más personal asignado?',
                    '¿Cómo podemos reducir el tiempo promedio de atención?',
                    '¿Qué tendencias se observan en las solicitudes del último mes?',
                    '¿Qué acciones inmediatas recomiendas para mejorar la satisfacción del cliente?',
                ];
                foreach ($sugerencias as $sug): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-sugerencia"
                            data-pregunta="<?= e($sug) ?>">
                        <?= e($sug) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($respuestaIA): ?>
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-robot"></i> Respuesta de la IA</h5>
                    <?php if ($usandoIA): ?>
                        <span class="badge bg-light text-dark"><i class="bi bi-cloud"></i> Gemini</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-cpu"></i> Analisis Local</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="respuesta-ia" style="white-space: pre-wrap; line-height: 1.8;"><?= e($respuestaIA) ?></div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.querySelectorAll('.btn-sugerencia').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('pregunta').value = this.getAttribute('data-pregunta');
        this.closest('form').submit();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
