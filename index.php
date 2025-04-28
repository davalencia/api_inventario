<?php
require_once __DIR__ . '/utils/load-env.php';
require_once __DIR__ . '/utils/Auth.php';

if ($_REQUEST['debug'] == '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 'ON');
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, session_token, Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    header("Content-Length: 0");
    exit();
}

header("Content-Type: application/json");

if (!isAuthenticated()) {
    header("HTTP/1.0 401 Unauthorized");
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

require_once __DIR__ . '/routes/routes.php';
