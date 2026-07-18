<?php
require_once __DIR__ . '/config.php';

function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function db_query($sql, $params = []) {
    $conn = db_connect();
    if (empty($params)) {
        $result = $conn->query($sql);
    } else {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            http_response_code(500);
            die(json_encode(["error" => "Error al preparar: " . $err]));
        }
        $types = '';
        foreach ($params as $p) {
            if (is_int($p)) $types .= 'i';
            elseif (is_float($p)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute() ? $stmt->get_result() : false;
        $stmt->close();
    }
    if (!$result) {
        $err = $conn->error;
        $conn->close();
        http_response_code(500);
        die(json_encode(["error" => "Error en la consulta: " . $err]));
    }
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $conn->close();
    return $rows;
}

function db_query_one($sql, $params = []) {
    $rows = db_query($sql, $params);
    return $rows[0] ?? null;
}

function db_query_val($sql, $params = []) {
    $row = db_query_one($sql, $params);
    if (!$row) return null;
    return reset($row);
}

function db_insert($sql, $params = []) {
    $conn = db_connect();
    if (empty($params)) {
        $result = $conn->query($sql);
        $id = $conn->insert_id;
    } else {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            http_response_code(500);
            die(json_encode(["error" => "Error al preparar: " . $err]));
        }
        $types = '';
        foreach ($params as $p) {
            if (is_int($p)) $types .= 'i';
            elseif (is_float($p)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
    }
    $conn->close();
    return $id > 0 ? $id : null;
}

function check_auth() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Dashboard"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'No autorizado';
        exit;
    }
    if ($_SERVER['PHP_AUTH_USER'] !== ADMIN_USER || $_SERVER['PHP_AUTH_PW'] !== ADMIN_PASS) {
        header('WWW-Authenticate: Basic realm="Dashboard"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'No autorizado';
        exit;
    }
}
