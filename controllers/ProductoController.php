<?php
require_once 'models/Producto.php';
require_once 'utils/Auth_session.php';

class ProductoController
{
    private $model;

    public function __construct()
    {
        validateToken();
        $this->model = new Producto();
    }

    public function get($id)
    {
        $result = $this->model->find($id);
        echo json_encode($result);
    }

    public function getAll()
    {
        $result = $this->model->all();
        echo json_encode($result);
    }

    public function getAllOption()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->model->allOption($data);
        echo json_encode($result);
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->model->create($data);
        echo json_encode($result);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->model->update($id, $data);
        echo json_encode($result);
    }

    public function delete($id)
    {
        $result = $this->model->delete($id);
        echo json_encode($result);
    }

    public function getCount()
    {
        $result = $this->model->getCount();
        echo json_encode($result);
    }
}
