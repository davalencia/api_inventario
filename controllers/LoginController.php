<?php
require_once 'models/Login.php';

class LoginController
{
    private $model;

    public function __construct()
    {
        $this->model = new Login();
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->model->login($data);
        echo json_encode($result);
    }

    public function logout()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->model->logout($data);
        echo json_encode($result);
    }

    public function get($id)
    {
        $result = $this->model->getToken($id);
        echo json_encode($result);
    }

    public function validateToken($token)
    {
        $result = $this->model->validateToken($token);
        return $result;
    }
}
