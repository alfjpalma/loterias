<?php
class CuadresController
{
    private CuadreCajaModel $model;
    private AgenciaModel    $agenciaModel;

    public function __construct()
    {
        $this->model        = new CuadreCajaModel();
        $this->agenciaModel = new AgenciaModel();
    }

    public function index(): void
    {
        Auth::check();
        $desde  = $_GET['desde'] ?? date('Y-m-01');
        $hasta  = $_GET['hasta'] ?? date('Y-m-d');
        $cuadres = $this->model->getAllWithAgencia($desde, $hasta);
        require ROOT_PATH . '/views/cuadres/index.php';
    }

    public function form(): void
    {
        Auth::check();
        $agencias  = $this->agenciaModel->getActivas();
        $fecha     = $_GET['fecha']      ?? date('Y-m-d');
        $agenciaId = (int)($_GET['agencia_id'] ?? 0);

        $cuadre = null;
        if ($agenciaId) {
            $cuadre = $this->model->getByFechaAgencia($fecha, $agenciaId);
        }
        require ROOT_PATH . '/views/cuadres/form.php';
    }

    public function store(): void
    {
        Auth::check();
        Csrf::check();

        if (empty($_POST['fecha']) || empty($_POST['agencia_id'])) {
            Flash::set('error', 'Fecha y agencia son requeridas.');
            header('Location: ' . BASE_URL . '/cuadres/form'); exit;
        }

        $_POST['usuario_id'] = Auth::id();
        $id = $this->model->saveOrUpdate($_POST);
        (new AuditoriaModel())->log('GUARDAR_CUADRE', 'cuadres_caja', $id);
        Flash::set('success', 'Cuadre guardado correctamente.');
        header('Location: ' . BASE_URL . '/cuadres/form?fecha=' . $_POST['fecha']
            . '&agencia_id=' . $_POST['agencia_id']);
        exit;
    }

    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->model->delete($id);
        Flash::set('success', 'Cuadre eliminado.');
        header('Location: ' . BASE_URL . '/cuadres'); exit;
    }
}
