<?php
/**
 * IA para Clientes - Consulta sobre sus propias solicitudes + ayuda general
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/ia_service.php';
requiereRol('cliente');

$pdo     = obtenerConexion();
$idCli   = $_SESSION['id_usuario'];
$respuestaIA = null;
$pregunta    = '';
$usandoIA    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pregunta = trim($_POST['pregunta'] ?? '');
    $tipo     = $_POST['tipo'] ?? 'general';

    if (!empty($pregunta)) {
        if ($tipo === 'mis_casos') {
            $respuestaIA = consultarIAMisCasos($idCli, $pregunta);
        } else {
            $respuestaIA = consultarIAAyuda($pregunta);
        }
        $usandoIA = $respuestaIA['usando_ia'];
        $respuestaIA = $respuestaIA['respuesta'];
    }
}

$tituloPagina = 'Asistente IA';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9">

        <h2><i class="bi bi-robot"></i> Asistente Inteligente</h2>
        <p class="text-muted">Haz una pregunta sobre tus solicitudes o sobre como usar el portal. La IA te ayudara.</p>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-primary h-100">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-folder2-open display-6 text-primary"></i>
                        <h5 class="mt-2">Consultar mis casos</h5>
                        <p class="small text-muted mb-0">Pregunta sobre el estado, que hacer con tu solicitud, o por que tarda en resolverse.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success h-100">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-question-circle display-6 text-success"></i>
                        <h5 class="mt-2">Ayuda general</h5>
                        <p class="small text-muted mb-0">Como registrar una solicitud, que categoria elegir, como consultar el estado, etc.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de consulta:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo_casos" value="mis_casos" checked>
                                <label class="form-check-label" for="tipo_casos">
                                    <i class="bi bi-folder2-open"></i> Sobre mis casos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo_general" value="general">
                                <label class="form-check-label" for="tipo_general">
                                    <i class="bi bi-question-circle"></i> Ayuda general
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pregunta" class="form-label fw-bold">Tu pregunta:</label>
                        <textarea class="form-control" id="pregunta" name="pregunta" rows="3" required
                                  placeholder="Ejemplo: ¿Cual es el estado de mi ultima solicitud? ¿Deberia crear una nueva solicitud o dar seguimiento a una existente?"
                        ><?= e($pregunta) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Preguntar
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <strong>Preguntas sugeridas:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2" id="sugerencias">
                <button type="button" class="btn btn-outline-primary btn-sm btn-sug" data-tipo="mis_casos"
                        data-pregunta="¿Cual es el estado de todas mis solicitudes? Resumelas para mi.">
                    Estado de mis solicitudes
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm btn-sug" data-tipo="mis_casos"
                        data-pregunta="Basandote en mis solicitudes anteriores, que deberia hacer con mi proxima solicitud?">
                    Que hacer con mi proxima solicitud
                </button>
                <button type="button" class="btn btn-outline-success btn-sm btn-sug" data-tipo="general"
                        data-pregunta="¿Como puedo registrar una solicitud en el portal? Paso a paso.">
                    Como registrar una solicitud
                </button>
                <button type="button" class="btn btn-outline-success btn-sm btn-sug" data-tipo="general"
                        data-pregunta="¿Cuales son las categorias disponibles y cual debo elegir segun mi problema?">
                    Que categoria elegir
                </button>
                <button type="button" class="btn btn-outline-success btn-sm btn-sug" data-tipo="general"
                        data-pregunta="¿Como puedo consultar el estado de mi solicitud usando el numero de caso?">
                    Como consultar mi solicitud
                </button>
            </div>
        </div>

        <?php if ($respuestaIA): ?>
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-robot"></i> Respuesta del Asistente</h5>
                    <?php if ($usandoIA): ?>
                        <span class="badge bg-light text-dark"><i class="bi bi-stars"></i> Gemini IA</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-info-circle"></i> Respuesta automatica</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div style="white-space: pre-wrap; line-height: 1.8;"><?= e($respuestaIA) ?></div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.querySelectorAll('.btn-sug').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var tipo = this.getAttribute('data-tipo');
        var pregunta = this.getAttribute('data-pregunta');
        document.getElementById('pregunta').value = pregunta;
        if (tipo === 'mis_casos') {
            document.getElementById('tipo_casos').checked = true;
        } else {
            document.getElementById('tipo_general').checked = true;
        }
        this.closest('form').submit();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
