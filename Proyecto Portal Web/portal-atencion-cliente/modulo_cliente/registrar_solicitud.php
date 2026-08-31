<?php
/**
 * Registro de nueva solicitud por parte del cliente
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('cliente');

$pdo       = obtenerConexion();
$categorias = obtenerCategorias();
$errores    = [];
$exito      = false;
$numeroCaso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $errores[] = 'Token de seguridad invalido.';
    } else {
        $idCategoria = intval($_POST['id_categoria'] ?? 0);
        $asunto      = trim($_POST['asunto'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $prioridad   = $_POST['prioridad'] ?? 'media';

        if ($idCategoria <= 0) $errores[] = 'Seleccione una categoria.';
        if (empty($asunto))    $errores[] = 'El asunto es obligatorio.';
        if (strlen($asunto) > 200) $errores[] = 'El asunto no puede exceder 200 caracteres.';
        if (empty($descripcion)) $errores[] = 'La descripcion es obligatoria.';
        if (strlen($descripcion) < 20) $errores[] = 'La descripcion debe tener al menos 20 caracteres.';
        if (!in_array($prioridad, ['baja', 'media', 'alta', 'urgente'])) $errores[] = 'Prioridad no valida.';

        if (empty($errores)) {
            $numeroCaso = generarNumeroCaso();

            $stmt = $pdo->prepare(
                'INSERT INTO solicitudes (numero_caso, id_cliente, id_categoria, asunto, descripcion, prioridad)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $numeroCaso,
                $_SESSION['id_usuario'],
                $idCategoria,
                $asunto,
                $descripcion,
                $prioridad,
            ]);

            $idSolicitud = $pdo->lastInsertId();

            $stmtSeg = $pdo->prepare(
                'INSERT INTO seguimientos (id_solicitud, id_usuario, comentario, estado_anterior, estado_nuevo)
                 VALUES (?, ?, ?, NULL, ?)'
            );
            $stmtSeg->execute([$idSolicitud, $_SESSION['id_usuario'], 'Solicitud creada por el cliente.', 'pendiente']);

            setFlash('success', 'Solicitud registrada correctamente. Numero de caso: ' . $numeroCaso);
            $exito = true;
        }
    }
}

$tituloPagina = 'Registrar Solicitud';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <?php if ($exito): ?>
            <div class="card border-success shadow">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-success display-1"></i>
                    <h3 class="mt-3">Solicitud Registrada</h3>
                    <p>Su numero de caso es:</p>
                    <h2 class="text-primary fw-bold"><?= e($numeroCaso) ?></h2>
                    <p class="text-muted mt-2">Guarde este numero para consultar el estado de su solicitud.</p>
                    <a href="/portal-atencion-cliente/modulo_cliente/index.php" class="btn btn-primary mt-2">
                        <i class="bi bi-house"></i> Volver al panel
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Registrar Nueva Solicitud</h4>
                </div>
                <div class="card-body p-4">

                    <?php foreach ($errores as $error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endforeach; ?>

                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                        <div class="mb-3">
                            <label for="id_categoria" class="form-label fw-bold">Categoria *</label>
                            <select class="form-select" id="id_categoria" name="id_categoria" required>
                                <option value="">-- Seleccione una categoria --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"
                                        <?= intval($_POST['id_categoria'] ?? 0) === $cat['id_categoria'] ? 'selected' : '' ?>>
                                        <?= e($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="asunto" class="form-label fw-bold">Asunto *</label>
                            <input type="text" class="form-control" id="asunto" name="asunto"
                                   maxlength="200" required
                                   value="<?= e($_POST['asunto'] ?? '') ?>"
                                   placeholder="Breve resumen de su solicitud">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripcion detallada *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"
                                      rows="6" required minlength="20"
                                      placeholder="Describa su solicitud, reclamo o consulta con el mayor detalle posible. (minimo 20 caracteres)"><?= e($_POST['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Prioridad *</label><br>
                            <?php foreach (['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'] as $val => $lab): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="prioridad"
                                           id="pri_<?= $val ?>" value="<?= $val ?>"
                                           <?= ($_POST['prioridad'] ?? 'media') === $val ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="pri_<?= $val ?>"><?= $lab ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send"></i> Enviar Solicitud
                        </button>
                        <a href="/portal-atencion-cliente/modulo_cliente/index.php" class="btn btn-outline-secondary btn-lg ms-2">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
