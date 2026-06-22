<?php
class TaquillasController
{
    private TaquillaModel $model;
    private AgenciaModel  $agenciaModel;

    public function __construct()
    {
        $this->model        = new TaquillaModel();
        $this->agenciaModel = new AgenciaModel();
    }

    public function index(): void
    {
        Auth::check();
        $taquillas = $this->model->getAllWithAgencia();
        require ROOT_PATH . '/views/taquillas/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $taquilla = null;
        $agencias = $this->agenciaModel->getActivas();
        require ROOT_PATH . '/views/taquillas/form.php';
    }

    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->validateForm();
        $id = $this->model->save($_POST);
        (new AuditoriaModel())->log('CREAR_TAQUILLA', 'taquillas', (int)$id, null, $_POST);
        Flash::set('success', 'Taquilla creada correctamente.');
        header('Location: ' . BASE_URL . '/taquillas');
        exit;
    }

    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $taquilla = $this->model->find($id);
        if (!$taquilla) { $this->notFound(); }
        $agencias = $this->agenciaModel->getActivas();
        require ROOT_PATH . '/views/taquillas/form.php';
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->validateForm();
        $this->model->save($_POST, $id);
        Flash::set('success', 'Taquilla actualizada.');
        header('Location: ' . BASE_URL . '/taquillas');
        exit;
    }

    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->model->delete($id);
        Flash::set('success', 'Taquilla eliminada.');
        header('Location: ' . BASE_URL . '/taquillas');
        exit;
    }

    private function validateForm(): void
    {
        if (empty(trim($_POST['nombre'] ?? '')) || empty($_POST['agencia_id'])) {
            Flash::set('error', 'Nombre y agencia son requeridos.');
            header('Location: ' . BASE_URL . '/taquillas/create');
            exit;
        }
    }

    private function notFound(): never
    {
        http_response_code(404); die('Taquilla no encontrada.');
    }
}
