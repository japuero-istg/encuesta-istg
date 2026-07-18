<?php
require_once __DIR__ . '/config.php';

function db_connect() {
    $conn = pg_connect(
        "host=" . DB_HOST .
        " port=" . DB_PORT .
        " dbname=" . DB_NAME .
        " user=" . DB_USER .
        " password=" . DB_PASS
    );
    if (!$conn) {
        http_response_code(500);
        die(json_encode(["error" => "Error de conexión a la base de datos"]));
    }
    return $conn;
}

function db_query($sql, $params = []) {
    $conn = db_connect();
    if (empty($params)) {
        $result = pg_query($conn, $sql);
    } else {
        $result = pg_query_params($conn, $sql, $params);
    }
    if (!$result) {
        $err = pg_last_error($conn);
        pg_close($conn);
        http_response_code(500);
        die(json_encode(["error" => "Error en la consulta: " . $err]));
    }
    $rows = [];
    while ($row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }
    pg_close($conn);
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
        $result = pg_query($conn, $sql);
    } else {
        $result = pg_query_params($conn, $sql, $params);
    }
    if (!$result) {
        $err = pg_last_error($conn);
        pg_close($conn);
        http_response_code(500);
        die(json_encode(["error" => "Error al insertar: " . $err]));
    }
    $row = pg_fetch_assoc($result);
    pg_close($conn);
    return $row ? reset($row) : null;
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
