<?php
class AuthController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function login(): void
    {
        if (Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        require ROOT_PATH . '/views/auth/login.php';
    }

    public function doLogin(): void
    {
        Csrf::check();
        $usuario  = trim($_POST['usuario']  ?? '');
        $password = trim($_POST['password'] ?? '');

        $user = $this->model->findByUsuario($usuario);
        if ($user && password_verify($password, $user['password'])) {
            Auth::login($user);
            (new AuditoriaModel())->log('LOGIN', 'usuarios', (int)$user['id']);
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        Flash::set('error', 'Usuario o contraseña incorrectos.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function logout(): void
    {
        (new AuditoriaModel())->log('LOGOUT', 'usuarios', Auth::id());
        Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
