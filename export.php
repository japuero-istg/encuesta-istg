<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

check_auth();

$format = $_GET['format'] ?? 'json';

$rows = db_query("SELECT * FROM respuestas ORDER BY created_at ASC");
if (empty($rows)) {
    http_response_code(404);
    echo json_encode(["error" => "Sin datos"]);
    exit;
}

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=respuestas_encuesta.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($rows[0]));
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_PRETTY_PRINT);
