<?php
require_once 'controllers/ProductoController.php';
require_once 'controllers/LoginController.php';

$request_method = $_SERVER["REQUEST_METHOD"];
$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$resource = $uri[1] ?? null;
$id = $uri[2] ?? null;

function sendJsonResponse($status_code, $data)
{
    header('Content-Type: application/json');
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

$controller = null;
switch ($resource) {
    case 'producto':
        $controller = new ProductoController();
        break;
    case 'session':
        $controller = new LoginController();
        break;
    default:
        sendJsonResponse(404, ["error" => "Recurso no encontrado"]);
}

try {
    switch ($request_method) {
        case 'GET':
            if ($id === 'suma') {
                $controller->getTotalPrice();
            } elseif ($id === 'count') {
                $controller->getCount();
            } elseif ($id === 'getYear') {
                $controller->getForYear();
            } elseif ($id) {
                $controller->get($id);
            } else {
                $controller->getAll();
            }
            break;
        case 'POST':
            if ($id == 'option') {
                $controller->getAllOption();
            } else if ($id == 'login') {
                $controller->login();
            } else if ($id == 'logout') {
                $controller->logout();
            } else {
                $controller->create();
            }
            break;
        case 'PUT':
            if (!$id) {
                sendJsonResponse(400, ["error" => "ID requerido para actualizar"]);
            }
            $controller->update($id);
            break;
        case 'DELETE':
            if (!$id) {
                sendJsonResponse(400, ["error" => "ID requerido para eliminar"]);
            }
            $controller->delete($id);
            break;
        default:
            sendJsonResponse(405, ["error" => "Método no permitido"]);
    }
} catch (Exception $e) {
    sendJsonResponse(500, ["error" => "Error interno del servidor", "details" => $e->getMessage()]);
}
