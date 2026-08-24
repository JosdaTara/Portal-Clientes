<?php
/**
 * Panel principal del cliente
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereRol('cliente');

$pdo   = obtenerConexion();
$idCli = $_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudes WHERE id_cliente = ?");
$stmt->execute([$idCli]);
$total = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudes WHERE id_cliente = ? AND estado IN ('pendiente','en_proceso')");
$stmt->execute([$idCli]);
$abiertas = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudes WHERE id_cliente = ? AND estado IN ('atendida','cerrada')");
$stmt->execute([$idCli]);
$resueltas = $stmt->fetchColumn();

$tituloPagina = 'Mi Panel';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-person-circle"></i> Bienvenido, <?= e($_SESSION['nombre']) ?></h2>
        <p class="text-muted">Atencion 24/7 - Gestiona y da seguimiento a tus solicitudes.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-folder display-4"></i>
                <h1><?= $total ?></h1>
                <p>Total solicitudes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock-history display-4"></i>
                <h1><?= $abiertas ?></h1>
                <p>Abiertas / En proceso</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4"></i>
                <h1><?= $resueltas ?></h1>
                <p>Resueltas</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="/portal-atencion-cliente/modulo_cliente/registrar_solicitud.php" class="card text-decoration-none h-100 border-primary">
            <div class="card-body text-center py-4">
                <i class="bi bi-plus-circle display-3 text-primary"></i>
                <h5 class="card-title mt-3 text-dark">Registrar nueva solicitud</h5>
                <p class="text-muted">Envia un nuevo requerimiento, reclamo o consulta.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/portal-atencion-cliente/modulo_cliente/consultar_solicitud.php" class="card text-decoration-none h-100 border-info">
            <div class="card-body text-center py-4">
                <i class="bi bi-search display-3 text-info"></i>
                <h5 class="card-title mt-3 text-dark">Consultar solicitud</h5>
                <p class="text-muted">Verifica el estado de un caso por su numero.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/portal-atencion-cliente/modulo_cliente/ia_cliente.php" class="card text-decoration-none h-100 border-success">
            <div class="card-body text-center py-4">
                <i class="bi bi-robot display-3 text-success"></i>
                <h5 class="card-title mt-3 text-dark">Asistente IA</h5>
                <p class="text-muted">Pregunta sobre tus casos o como usar el portal.</p>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
