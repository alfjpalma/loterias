<?php
class AgenciasController
{
    private AgenciaModel $model;

    public function __construct()
    {
        $this->model = new AgenciaModel();
    }

    public function index(): void
    {
        Auth::check();
        $agencias = $this->model->getWithTaquillasCount();
        require ROOT_PATH . '/views/agencias/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $agencia = null;
        require ROOT_PATH . '/views/agencias/form.php';
    }

    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->validateForm();
        $id = $this->model->save($_POST);
        (new AuditoriaModel())->log('CREAR_AGENCIA', 'agencias', (int)$id, null, $_POST);
        Flash::set('success', 'Agencia creada correctamente.');
        header('Location: ' . BASE_URL . '/agencias');
        exit;
    }

    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $agencia = $this->model->find($id);
        if (!$agencia) { $this->notFound(); }
        require ROOT_PATH . '/views/agencias/form.php';
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $this->validateForm();
        $anterior = $this->model->find($id);
        $this->model->save($_POST, $id);
        (new AuditoriaModel())->log('EDITAR_AGENCIA', 'agencias', $id, $anterior, $_POST);
        Flash::set('success', 'Agencia actualizada correctamente.');
        header('Location: ' . BASE_URL . '/agencias');
        exit;
    }

    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $anterior = $this->model->find($id);
        $this->model->delete($id);
        (new AuditoriaModel())->log('ELIMINAR_AGENCIA', 'agencias', $id, $anterior);
        Flash::set('success', 'Agencia eliminada.');
        header('Location: ' . BASE_URL . '/agencias');
        exit;
    }

    public function toggleEstado(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        $agencia = $this->model->find($id);
        if (!$agencia) { $this->notFound(); }
        $nuevoEstado = $agencia['estado'] ? 0 : 1;
        $this->model->save(['nombre' => $agencia['nombre'], 'estado' => $nuevoEstado], $id);
        Flash::set('success', 'Estado actualizado.');
        header('Location: ' . BASE_URL . '/agencias');
        exit;
    }

    private function validateForm(): void
    {
        if (empty(trim($_POST['nombre'] ?? ''))) {
            Flash::set('error', 'El nombre es requerido.');
            header('Location: ' . BASE_URL . '/agencias/create');
            exit;
        }
    }

    private function notFound(): never
    {
        http_response_code(404);
        die('Agencia no encontrada.');
    }
}
