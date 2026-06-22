<?php
class UsuariosController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function index(): void
    {
        Auth::requireAdmin();
        $usuarios = $this->model->findAll();
        require ROOT_PATH . '/views/usuarios/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $usuario = null;
        require ROOT_PATH . '/views/usuarios/form.php';
    }

    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::check();
        if ($err = $this->validate()) {
            Flash::set('error', $err);
            header('Location: ' . BASE_URL . '/usuarios/create'); exit;
        }
        $this->model->save($_POST);
        Flash::set('success', 'Usuario creado correctamente.');
        header('Location: ' . BASE_URL . '/usuarios'); exit;
    }

    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $usuario = $this->model->find($id);
        if (!$usuario) { http_response_code(404); die('No encontrado.'); }
        require ROOT_PATH . '/views/usuarios/form.php';
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        if ($err = $this->validate($id)) {
            Flash::set('error', $err);
            header('Location: ' . BASE_URL . '/usuarios/edit/' . $id); exit;
        }
        $this->model->save($_POST, $id);
        Flash::set('success', 'Usuario actualizado.');
        header('Location: ' . BASE_URL . '/usuarios'); exit;
    }

    public function delete(int $id): void
    {
        Auth::requireAdmin();
        Csrf::check();
        if ($id === Auth::id()) {
            Flash::set('error', 'No puedes eliminar tu propio usuario.');
            header('Location: ' . BASE_URL . '/usuarios'); exit;
        }
        $this->model->delete($id);
        Flash::set('success', 'Usuario eliminado.');
        header('Location: ' . BASE_URL . '/usuarios'); exit;
    }

    private function validate(int $id = 0): string
    {
        if (empty(trim($_POST['nombre'] ?? '')))   return 'El nombre es requerido.';
        if (empty(trim($_POST['usuario'] ?? '')))  return 'El usuario es requerido.';
        if ($this->model->usernameExists($_POST['usuario'], $id))
            return 'El nombre de usuario ya existe.';
        if ($id === 0 && empty($_POST['password'])) return 'La contraseña es requerida.';
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6)
            return 'La contraseña debe tener al menos 6 caracteres.';
        return '';
    }
}
