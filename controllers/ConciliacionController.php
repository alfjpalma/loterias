<?php
class ConciliacionController
{
    private CuadreCajaModel $model;

    public function __construct()
    {
        $this->model = new CuadreCajaModel();
    }

    public function index(): void
    {
        Auth::check();
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $datos = $this->model->getConciliacion($fecha);
        require ROOT_PATH . '/views/conciliacion/index.php';
    }
}
