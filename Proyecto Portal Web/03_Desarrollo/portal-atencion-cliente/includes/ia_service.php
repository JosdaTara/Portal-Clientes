<?php
/**
 * Servicio de Inteligencia Artificial - Google Gemini
 * 
 * Consulta la API de Gemini para analizar datos de solicitudes.
 * Incluye modo fallback con analisis local si no hay API key.
 */

require_once __DIR__ . '/../config/api_key.php';
require_once __DIR__ . '/../config/conexion.php';

/**
 * Recopila datos de solicitudes para enviar a la IA
 */
function recopilarDatosParaIA(): array
{
    $pdo = obtenerConexion();

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM solicitudes");
    $total = $stmt->fetchColumn();

    $stmt = $pdo->query("
        SELECT estado, COUNT(*) AS cantidad 
        FROM solicitudes 
        GROUP BY estado
    ");
    $estados = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = $pdo->query("
        SELECT c.nombre, COUNT(*) AS cantidad 
        FROM solicitudes s 
        JOIN categorias c ON s.id_categoria = c.id_categoria 
        GROUP BY c.nombre 
        ORDER BY cantidad DESC
    ");
    $categorias = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT prioridad, COUNT(*) AS cantidad 
        FROM solicitudes 
        GROUP BY prioridad 
        ORDER BY FIELD(prioridad, 'urgente', 'alta', 'media', 'baja')
    ");
    $prioridades = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT DAYNAME(fecha_creacion) AS dia, COUNT(*) AS cantidad 
        FROM solicitudes 
        GROUP BY dia 
        ORDER BY FIELD(dia, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
    ");
    $porDiaSemana = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT HOUR(fecha_creacion) AS hora, COUNT(*) AS cantidad 
        FROM solicitudes 
        GROUP BY hora 
        ORDER BY hora
    ");
    $porHora = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, COALESCE(fecha_cierre, NOW()))) AS promedio_horas 
        FROM solicitudes 
        WHERE fecha_cierre IS NOT NULL
    ");
    $tiempoPromedio = round($stmt->fetchColumn() ?? 0, 1);

    $stmt = $pdo->query("
        SELECT s.asunto, s.descripcion, c.nombre AS categoria, s.prioridad, s.estado
        FROM solicitudes s
        JOIN categorias c ON s.id_categoria = c.id_categoria
        ORDER BY s.fecha_creacion DESC
        LIMIT 20
    ");
    $ultimasSolicitudes = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT u.nombre, u.apellido, COUNT(*) AS total
        FROM solicitudes s
        JOIN usuarios u ON s.id_cliente = u.id_usuario
        GROUP BY u.id_usuario
        ORDER BY total DESC
        LIMIT 5
    ");
    $clientesFrecuentes = $stmt->fetchAll();

    return [
        'total_solicitudes'    => $total,
        'por_estado'           => $estados,
        'por_categoria'        => $categorias,
        'por_prioridad'        => $prioridades,
        'por_dia_semana'       => $porDiaSemana,
        'por_hora'             => $porHora,
        'tiempo_promedio_h'    => $tiempoPromedio,
        'ultimas_solicitudes'  => $ultimasSolicitudes,
        'clientes_frecuentes'  => $clientesFrecuentes,
    ];
}

/**
 * Construye el prompt para Gemini
 */
function construirPrompt(string $pregunta, array $datos): string
{
    $resumen = "RESUMEN DE DATOS DEL PORTAL DE ATENCION AL CLIENTE:\n";
    $resumen .= "- Total de solicitudes registradas: {$datos['total_solicitudes']}\n";
    $resumen .= "- Tiempo promedio de atencion: {$datos['tiempo_promedio_h']} horas\n\n";

    $resumen .= "POR ESTADO:\n";
    foreach ($datos['por_estado'] as $estado => $cant) {
        $resumen .= "  - " . ucfirst(str_replace('_', ' ', $estado)) . ": $cant\n";
    }

    $resumen .= "\nPOR CATEGORIA:\n";
    foreach ($datos['por_categoria'] as $cat) {
        $resumen .= "  - {$cat['nombre']}: {$cat['cantidad']}\n";
    }

    $resumen .= "\nPOR PRIORIDAD:\n";
    foreach ($datos['por_prioridad'] as $pri) {
        $resumen .= "  - " . ucfirst($pri['prioridad']) . ": {$pri['cantidad']}\n";
    }

    $resumen .= "\nPOR DIA DE LA SEMANA:\n";
    foreach ($datos['por_dia_semana'] as $d) {
        $resumen .= "  - {$d['dia']}: {$d['cantidad']}\n";
    }

    $resumen .= "\nULTIMAS SOLICITUDES RECIBIDAS:\n";
    foreach ($datos['ultimas_solicitudes'] as $sol) {
        $resumen .= "  - [{$sol['categoria']}] {$sol['asunto']} ({$sol['prioridad']}) - {$sol['estado']}\n";
    }

    if (!empty($datos['clientes_frecuentes'])) {
        $resumen .= "\nCLIENTES MAS FRECUENTES:\n";
        foreach ($datos['clientes_frecuentes'] as $cli) {
            $resumen .= "  - {$cli['nombre']} {$cli['apellido']}: {$cli['total']} solicitudes\n";
        }
    }

    $prompt = "Eres un analista de inteligencia de negocio experto en atencion al cliente. ";
    $prompt .= "Con base en los siguientes datos reales de una empresa, responde la pregunta de forma clara, ";
    $prompt .= "practica y orientada a la accion. Usa formato de listas cuando sea util. Responde en espanol.\n\n";
    $prompt .= $resumen . "\n\n";
    $prompt .= "PREGUNTA DEL USUARIO: " . $pregunta;

    return $prompt;
}

/**
 * Consulta la API de Google Gemini
 */
function consultarGemini(string $prompt): ?string
{
    if (empty(GEMINI_API_KEY)) {
        return null;
    }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;

    $payload = json_encode([
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ],
        'generationConfig' => [
            'maxOutputTokens' => GEMINI_MAX_TOKENS,
            'temperature'     => 0.7,
        ],
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        error_log("Gemini API error (HTTP {$httpCode}): " . ($response ?: 'sin respuesta'));
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return $data['candidates'][0]['content']['parts'][0]['text'];
    }

    error_log("Gemini API: respuesta inesperada: " . json_encode($data));
    return null;
}

/**
 * Analisis local (fallback cuando no hay API key)
 */
function analisisLocal(array $datos): string
{
    $total = $datos['total_solicitudes'];
    if ($total === 0) {
        return "No hay datos suficientes para generar un analisis.";
    }

    $pendientes = $datos['por_estado']['pendiente'] ?? 0;
    $enProceso  = $datos['por_estado']['en_proceso'] ?? 0;
    $atendidas  = $datos['por_estado']['atendida'] ?? 0;
    $cerradas   = $datos['por_estado']['cerrada'] ?? 0;

    $resueltas  = $atendidas + $cerradas;
    $tasaRes    = $total > 0 ? round($resueltas / $total * 100, 1) : 0;
    $tasaPen    = $total > 0 ? round($pendientes / $total * 100, 1) : 0;

    $catMax = '';
    $cantMax = 0;
    foreach ($datos['por_categoria'] as $cat) {
        if ($cat['cantidad'] > $cantMax) {
            $cantMax = $cat['cantidad'];
            $catMax  = $cat['nombre'];
        }
    }

    $priUrgentes = 0;
    foreach ($datos['por_prioridad'] as $pri) {
        if (in_array($pri['prioridad'], ['urgente', 'alta'])) {
            $priUrgentes += $pri['cantidad'];
        }
    }
    $pctUrgentes = $total > 0 ? round($priUrgentes / $total * 100, 1) : 0;

    $diaMax = '';
    $cantDiaMax = 0;
    foreach ($datos['por_dia_semana'] as $d) {
        if ($d['cantidad'] > $cantDiaMax) {
            $cantDiaMax = $d['cantidad'];
            $diaMax = $d['dia'];
        }
    }

    $horaPico = '';
    $cantHoraMax = 0;
    foreach ($datos['por_hora'] as $h) {
        if ($h['cantidad'] > $cantHoraMax) {
            $cantHoraMax = $h['cantidad'];
            $horaPico = str_pad($h['hora'], 2, '0', STR_PAD_LEFT) . ':00';
        }
    }

    $analisis = "## ANALISIS INTELIGENTE DEL PORTAL DE ATENCION\n\n";

    $analisis .= "### 1. INDICADORES CLAVE\n";
    $analisis .= "- **Total de solicitudes:** {$total}\n";
    $analisis .= "- **Tasa de resolucion:** {$tasaRes}%\n";
    $analisis .= "- **Solicitudes pendientes:** {$pendientes} ({$tasaPen}%)\n";
    $analisis .= "- **Tiempo promedio de atencion:** {$datos['tiempo_promedio_h']} horas\n\n";

    $analisis .= "### 2. PATRONES DETECTADOS\n";
    $analisis .= "- **Categoria mas frecuente:** {$catMax} ({$cantMax} solicitudes)\n";
    $analisis .= "- **Dia con mayor demanda:** {$diaMax} ({$cantDiaMax} solicitudes)\n";
    $analisis .= "- **Hora pico:** {$horaPico}\n";
    $analisis .= "- **Solicitudes de prioridad alta/urgente:** {$priUrgentes} ({$pctUrgentes}%)\n\n";

    $analisis .= "### 3. RECOMENDACIONES\n";

    if ($pendientes > 0) {
        $analisis .= "- **URGENTE:** Hay {$pendientes} solicitudes pendientes sin atender. Se recomienda asignar mas recursos o agilizar el proceso de asignacion.\n";
    }

    if ($tasaRes < 70) {
        $analisis .= "- La tasa de resolucion ({$tasaRes}%) es baja. Considerar revisar los procedimientos de atencion y capacitar al personal.\n";
    }

    if ($pctUrgentes > 30) {
        $analisis .= "- El {$pctUrgentes}% de solicitudes son de alta/urgente prioridad. Esto indica un posible problema de calidad en productos o servicios.\n";
    }

    $analisis .= "- **Categoria dominante ({$catMax}):** Se recomienda crear un protocolo especifico y articulos de ayuda frecuentes para esta categoria.\n";

    if ($diaMax === 'Monday' || $diaMax === 'Tuesday') {
        $analisis .= "- La demanda se concentra a inicios de semana. Programar personal extra los lunes y martes.\n";
    }

    if ($datos['tiempo_promedio_h'] > 48) {
        $analisis .= "- El tiempo promedio de atencion ({$datos['tiempo_promedio_h']}h) supera las 48 horas. Evaluar cuellos de botella en el proceso.\n";
    }

    $clientesFreq = $datos['clientes_frecuentes'];
    if (!empty($clientesFreq) && $clientesFreq[0]['total'] > 3) {
        $nombre = $clientesFreq[0]['nombre'] . ' ' . $clientesFreq[0]['apellido'];
        $analisis .= "- El cliente **{$nombre}** ha registrado {$clientesFreq[0]['total']} solicitudes. Se recomienda contacto directo para evaluar su satisfaccion.\n";
    }

    $analisis .= "\n### 4. PROYECCION\n";
    $analisis .= "- Si la tendencia actual se mantiene, se proyectan aproximadamente **{$total}** solicitudes por mes.\n";
    $analisis .= "- Se recomienda implementar un **sistema de autoatencion** o **FAQ** para reducir consultas repetitivas en la categoria \"{$catMax}\".\n";

    return $analisis;
}

/**
 * Funcion principal: analizar datos con IA
 */
function analizarConIA(string $pregunta): array
{
    $datos = recopilarDatosParaIA();

    $respuesta = consultarGemini(construirPrompt($pregunta, $datos));

    $usandoIA = $respuesta !== null;

    if (!$respuesta) {
        $respuesta = analisisLocal($datos);
    }

    return [
        'respuesta'   => $respuesta,
        'usando_ia'   => $usandoIA,
        'datos_base'  => $datos,
    ];
}

/**
 * Analisis automatico rapido (para el dashboard)
 */
function analisisAutomatico(): string
{
    $datos = recopilarDatosParaIA();

    $pregunta = "Dame un resumen ejecutivo de 5 bullet points con los hallazgos mas importantes y 3 recomendaciones accionables para mejorar la atencion al cliente.";

    $respuesta = consultarGemini(construirPrompt($pregunta, $datos));

    if ($respuesta) {
        return $respuesta;
    }

    return analisisLocal($datos);
}

/**
 * IA para clientes: consultar sobre sus propias solicitudes
 */
function consultarIAMisCasos(int $idCliente, string $pregunta): array
{
    $pdo = obtenerConexion();

    $stmt = $pdo->prepare("
        SELECT s.numero_caso, s.asunto, s.descripcion, s.prioridad, s.estado,
               c.nombre AS categoria, s.fecha_creacion, s.fecha_cierre,
               (SELECT seg.comentario FROM seguimientos seg 
                WHERE seg.id_solicitud = s.id_solicitud 
                ORDER BY seg.fecha_seguimiento DESC LIMIT 1) AS ultimo_seguimiento
        FROM solicitudes s
        JOIN categorias c ON s.id_categoria = c.id_categoria
        WHERE s.id_cliente = ?
        ORDER BY s.fecha_creacion DESC
    ");
    $stmt->execute([$idCliente]);
    $solicitudes = $stmt->fetchAll();

    $prompt = "Eres un asistente de atencion al cliente amable y util. ";
    $prompt .= "El usuario es un cliente de la empresa. Responde sus preguntas sobre SUS SOLICITUDES de forma clara y amable. ";
    $prompt .= "En espanol.\n\n";

    $prompt .= "SOLICITUDES DEL CLIENTE:\n";
    if (empty($solicitudes)) {
        $prompt .= "- No tiene solicitudes registradas.\n";
    } else {
        foreach ($solicitudes as $sol) {
            $prompt .= "- Caso {$sol['numero_caso']}: {$sol['asunto']}\n";
            $prompt .= "  Categoria: {$sol['categoria']} | Prioridad: {$sol['prioridad']} | Estado: {$sol['estado']}\n";
            $prompt .= "  Creada: {$sol['fecha_creacion']}\n";
            if ($sol['fecha_cierre']) {
                $prompt .= "  Cerrada: {$sol['fecha_cierre']}\n";
            }
            if ($sol['ultimo_seguimiento']) {
                $prompt .= "  Ultimo seguimiento: {$sol['ultimo_seguimiento']}\n";
            }
            $prompt .= "\n";
        }
    }

    $prompt .= "PREGUNTA DEL CLIENTE: " . $pregunta;

    $respuesta = consultarGemini($prompt);
    $usandoIA = $respuesta !== null;

    if (!$respuesta) {
        $respuesta = generarRespuestaClienteLocal($solicitudes, $pregunta);
    }

    return [
        'respuesta' => $respuesta,
        'usando_ia' => $usandoIA,
    ];
}

/**
 * IA para clientes: ayuda general del portal
 */
function consultarIAAyuda(string $pregunta): array
{
    $contexto = "Eres un asistente virtual del Portal de Atencion al Cliente de una empresa. ";
    $contexto .= "Tu funcion es ayudar a los clientes a entender y usar el portal. Responde en espanol de forma amable y clara.\n\n";
    $contexto .= "INFORMACION DEL PORTAL:\n";
    $contexto .= "- El portal funciona 24/7 para registrar solicitudes.\n";
    $contexto .= "- Los clientes pueden: registrar solicitudes, consultar su estado con el numero de caso, y ver su historial.\n";
    $contexto .= "- Cada solicitud recibe un numero de caso unico (ej: CASO-2026-00001). Guardalo para consultar.\n";
    $contexto .= "- Categorias disponibles: Consulta general, Reclamo, Soporte tecnico, Sugerencia, Solicitud administrativa.\n";
    $contexto .= "- Prioridades: Baja, Media, Alta, Urgente.\n";
    $contexto .= "- Estados de una solicitud: Pendiente, En proceso, Atendida, Cerrada.\n";
    $contexto .= "- Para consultar el estado: ve a 'Consultar solicitud' y escribe tu numero de caso.\n";
    $contexto .= "- Para registrar una solicitud: ve a 'Nueva Solicitud', elige categoria, llena asunto, descripcion y prioridad.\n";
    $contexto .= "- La descripcion debe tener minimo 20 caracteres.\n";
    $contexto .= "- Puedes dar seguimiento a tus solicitudes consultando el historial.\n\n";
    $contexto .= "PREGUNTA DEL CLIENTE: " . $pregunta;

    $respuesta = consultarGemini($contexto);
    $usandoIA = $respuesta !== null;

    if (!$respuesta) {
        $respuesta = generarRespuestaAyudaLocal($pregunta);
    }

    return [
        'respuesta' => $respuesta,
        'usando_ia' => $usandoIA,
    ];
}

/**
 * Respuesta local para consultas de clientes sobre sus casos
 */
function generarRespuestaClienteLocal(array $solicitudes, string $pregunta): string
{
    if (empty($solicitudes)) {
        return "No tienes solicitudes registradas actualmente. Para crear una, ve a 'Nueva Solicitud' en el menu y completa el formulario con los datos de tu caso.";
    }

    $pendientes = array_filter($solicitudes, fn($s) => $s['estado'] === 'pendiente');
    $enProceso  = array_filter($solicitudes, fn($s) => $s['estado'] === 'en_proceso');
    $atendidas  = array_filter($solicitudes, fn($s) => in_array($s['estado'], ['atendida', 'cerrada']));
    $total      = count($solicitudes);

    $respuesta = "RESUMEN DE TUS SOLICITUDES:\n\n";
    $respuesta .= "Tienes un total de {$total} solicitud(es) registrada(s).\n\n";

    if (!empty($pendientes)) {
        $respuesta .= "PENDIENTES (" . count($pendientes) . "):\n";
        foreach ($pendientes as $s) {
            $respuesta .= "  - {$s['numero_caso']}: {$s['asunto']} ({$s['prioridad']})\n";
        }
        $respuesta .= "\n";
    }

    if (!empty($enProceso)) {
        $respuesta .= "EN PROCESO (" . count($enProceso) . "):\n";
        foreach ($enProceso as $s) {
            $respuesta .= "  - {$s['numero_caso']}: {$s['asunto']} ({$s['prioridad']})\n";
            if ($s['ultimo_seguimiento']) {
                $respuesta .= "    Ultima accion: {$s['ultimo_seguimiento']}\n";
            }
        }
        $respuesta .= "\n";
    }

    if (!empty($atendidas)) {
        $respuesta .= "RESUELTAS (" . count($atendidas) . "):\n";
        foreach ($atendidas as $s) {
            $respuesta .= "  - {$s['numero_caso']}: {$s['asunto']} [{$s['estado']}]\n";
        }
        $respuesta .= "\n";
    }

    $respuesta .= "Para mas detalles sobre un caso, usa 'Consultar solicitud' con el numero de caso.";
    return $respuesta;
}

/**
 * Respuesta local para ayuda general
 */
function generarRespuestaAyudaLocal(string $pregunta): string
{
    $preguntaLower = mb_strtolower($pregunta);

    if (str_contains($preguntaLower, 'registr') || str_contains($preguntaLower, 'crear') || str_contains($preguntaLower, 'nueva solicitud')) {
        return "PARA REGISTRAR UNA SOLICITUD:\n\n"
             . "1. Haz clic en 'Nueva Solicitud' en el menu superior.\n"
             . "2. Selecciona la categoria que mejor describa tu caso:\n"
             . "   - Consulta general: preguntas sobre productos/servicios\n"
             . "   - Reclamo: quejas formales\n"
             . "   - Soporte tecnico: ayuda tecnica\n"
             . "   - Sugerencia: propuestas de mejora\n"
             . "   - Solicitud administrativa: cambios de datos, facturacion\n"
             . "3. Escribe un asunto breve (maximo 200 caracteres).\n"
             . "4. Describe tu caso con detalle (minimo 20 caracteres).\n"
             . "5. Selecciona la prioridad: Baja, Media, Alta o Urgente.\n"
             . "6. Haz clic en 'Enviar Solicitud'.\n"
             . "7. Recibiras un numero de caso (ej: CASO-2026-00001). Guardalo.\n\n"
             . "El equipo de atencion revisara tu solicitud y te dara seguimiento.";
    }

    if (str_contains($preguntaLower, 'consultar') || str_contains($preguntaLower, 'estado') || str_contains($preguntaLower, 'numero caso')) {
        return "PARA CONSULTAR EL ESTADO DE TU SOLICITUD:\n\n"
             . "1. Haz clic en 'Consultar Solicitud' en el menu.\n"
             . "2. Escribe tu numero de caso (ej: CASO-2026-00001).\n"
             . "3. Haz clic en 'Buscar'.\n"
             . "4. Veras los detalles de tu solicitud y el historial de seguimiento.\n\n"
             . "Si no recuerdas tu numero de caso, ve a 'Mis Solicitudes' para ver el listado completo.";
    }

    if (str_contains($preguntaLower, 'categoria') || str_contains($preguntaLower, 'cual elegir') || str_contains($preguntaLower, 'que tipo')) {
        return "CATEGORIAS DISPONIBLES:\n\n"
             . "- CONSULTA GENERAL: Preguntas sobre productos, servicios, precios, horarios, etc.\n"
             . "- RECLAMO: Si no estas satisfecho con un producto o servicio recibido. Incluye quejas formales.\n"
             . "- SOPORTE TECNICO: Si tienes problemas tecnicos: errores, fallas, no funciona algo.\n"
             . "- SUGERENCIA: Si quieres proponer mejoras, nuevas funcionalidades o ideas.\n"
             . "- SOLICITUD ADMINISTRATIVA: Cambios en tus datos personales, facturacion, contratos, etc.\n\n"
             . "Si no estas seguro, elige 'Consulta general' y describe tu caso.";
    }

    if (str_contains($preguntaLower, 'prioridad') || str_contains($preguntaLower, 'urgente')) {
        return "PRIORIDADES:\n\n"
             . "- BAJA: No es urgente, puede esperar.\n"
             . "- MEDIA: Requiere atencion normal.\n"
             . "- ALTA: Es importante y requiere atencion pronta.\n"
             . "- URGENTE: Es critico y requiere atencion inmediata.\n\n"
             . "Selecciona la prioridad que mejor represente la urgencia de tu caso.";
    }

    return "Puedo ayudarte con:\n\n"
         . "- Como registrar una solicitud paso a paso\n"
         . "- Que categoria elegir para tu caso\n"
         . "- Como consultar el estado con tu numero de caso\n"
         . "- Que prioridad asignar\n"
         . "- Informacion general del portal\n\n"
         . "Hazme una pregunta especifica y te ayudo.";
}
