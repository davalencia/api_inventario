<?php
require_once 'controllers/LoginController.php';
function validateToken()
{

    $controller = new LoginController();

    $headers = apache_request_headers();

    if (isset($headers['session_token'])) {
        $token = $headers['session_token'];

        $user_id = $controller->validateToken($token);

        if ($user_id) {
            return $user_id;
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Acceso no autorizado."));
            exit();
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Token no proporcionado."));
        exit();
    }
}
