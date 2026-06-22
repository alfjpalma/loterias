<?php
class SistemasController
{
    private SistemaModel $model;

    public function __construct()
    {
        $this->model = new SistemaModel();
    }

    public function index(): void
    {
        Auth::check();
        $sistemas = $this->model->findAll('orden');
        require ROOT_PATH . '/views/sistemas/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $sistema = null;
        require ROOT_PATH . '/views/sistemas/form.php';
    }

    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::check();
        if (empty(trim($_POST['nombre'] ?? ''))) {
            Flash::set('error', 'El nombre es requerido.');
            header('Location: ' . BASE_URL . '/sistemas/create'); exit;
        }
        $this->model->save($_POST);
        Flash::set('success', 'Sistema creado correctamente.');
        header('Location: ' . BASE_URL . '/sistemas'); exit;
    }

    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $sistema = $this->model->find($id);
        if (!$sistema) { http_response_code(404); die('No encontrado.'); }
        require ROOT_PATH . '/views/sistemas/form.php';
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->model->save($_POST, $id);
        Flash::set('success', 'Sistema actualizado.');
        header('Location: ' . BASE_URL . '/sistemas'); exit;
    }

    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->model->delete($id);
        Flash::set('success', 'Sistema eliminado.');
        header('Location: ' . BASE_URL . '/sistemas'); exit;
    }
}
