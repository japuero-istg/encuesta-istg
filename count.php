<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$total = db_query_val("SELECT count(*) FROM respuestas");
echo json_encode(["total" => intval($total)]);
