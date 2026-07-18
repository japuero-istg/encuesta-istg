<?php
require_once __DIR__ . '/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("ERROR: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$sql = file_get_contents(__DIR__ . '/schema.sql');
$statements = preg_split('/;\s*\n/', $sql);
$ok = 0;
$errors = [];

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt)) continue;
    if (preg_match('/^--/m', $stmt)) continue;
    if (!$conn->query($stmt)) {
        $errors[] = $conn->error;
    } else {
        $ok++;
    }
}

$triggers = [
    "DROP TRIGGER IF EXISTS trg_check_email",
    "CREATE TRIGGER trg_check_email BEFORE INSERT ON respuestas FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM respuestas WHERE email = NEW.email) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email duplicado'; END IF; END",
    "DROP TRIGGER IF EXISTS trg_log_respuestas_insert",
    "CREATE TRIGGER trg_log_respuestas_insert AFTER INSERT ON respuestas FOR EACH ROW BEGIN INSERT INTO log_actividad (tabla, operacion, registro_id, detalle) VALUES ('respuestas', 'INSERT', NEW.id, CONCAT('Nueva respuesta de ', NEW.email)); END",
    "DROP TRIGGER IF EXISTS trg_log_respuestas_delete",
    "CREATE TRIGGER trg_log_respuestas_delete AFTER DELETE ON respuestas FOR EACH ROW BEGIN INSERT INTO log_actividad (tabla, operacion, registro_id, detalle) VALUES ('respuestas', 'DELETE', OLD.id, CONCAT('Respuesta eliminada: ', OLD.email)); END",
];

$conn->query("SET SESSION sql_mode = ''");

foreach ($triggers as $t) {
    if (!$conn->query($t)) {
        $errors[] = "Trigger: " . $conn->error;
    } else {
        $ok++;
    }
}

echo "Consultas ejecutadas: $ok\n";
if (!empty($errors)) {
    echo "Errores (" . count($errors) . "):\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
} else {
    echo "Schema + triggers importados correctamente\n";
}

$conn->close();
