<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Método no permitido"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "JSON inválido"]);
    exit;
}

$email = strtolower(trim($input['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Email inválido"]);
    exit;
}

$existing = db_query_val("SELECT id FROM respuestas WHERE email = $1", [$email]);
if ($existing) {
    http_response_code(409);
    echo json_encode(["ok" => false, "error" => "Este email ya fue registrado. No se permiten respuestas duplicadas."]);
    exit;
}

$tiene_app = $input['tiene_app'] ?? '';
$es_emprendedor = $input['es_emprendedor'] ?? '';
if (!in_array($tiene_app, ['si', 'no']) || !in_array($es_emprendedor, ['si', 'no'])) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Valores de filtrado inválidos"]);
    exit;
}

$profesion = $input['profesion'] ?? '';
$edad_rango = $input['edad_rango'] ?? '';
$barrio = $input['barrio'] ?? '';
if (!in_array($profesion, $PROFESIONES) || !in_array($edad_rango, $EDADES_RANGO) || !in_array($barrio, $BARRIOS_GUAYAQUIL)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Datos personales inválidos"]);
    exit;
}

$likert_fields = ['p1','p2','p3','p4','p5','p6','p7','p8','p9'];
foreach ($likert_fields as $f) {
    $val = intval($input[$f] ?? 0);
    if ($val < 1 || $val > 5) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Valor inválido en $f"]);
        exit;
    }
}

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$p10_mejoras = substr(trim($input['p10_mejoras'] ?? ''), 0, 1000);
$duracion = intval($input['duracion_segundos'] ?? 0);

$query = "INSERT INTO respuestas (
    email, tiene_app, es_emprendedor, profesion, edad_rango, barrio,
    p1, p2, p3, p4, p5, p6, p7, p8, p9,
    p10_mejoras, user_agent, duracion_segundos, version_encuesta
) VALUES (
    $1, $2, $3, $4, $5, $6,
    $7, $8, $9, $10, $11, $12, $13, $14, $15,
    $16, $17, $18, $19
) RETURNING id";

$id = db_insert($query, [
    $email, $tiene_app, $es_emprendedor, $profesion, $edad_rango, $barrio,
    intval($input['p1']), intval($input['p2']), intval($input['p3']),
    intval($input['p4']), intval($input['p5']), intval($input['p6']), intval($input['p7']),
    intval($input['p8']), intval($input['p9']),
    $p10_mejoras, $user_agent, $duracion, '1.0'
]);

echo json_encode(["ok" => true, "id" => $id]);
