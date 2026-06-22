<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #1a237e 0%, #0d47a1 50%, #1565c0 100%); min-height:100vh; }
        .login-card { border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.35); }
        .login-logo { width:80px; height:80px; background:linear-gradient(135deg,#ffd600,#ff6f00);
            border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div class="login-wrapper w-100" style="max-width:420px">
        <div class="card login-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="login-logo">
                        <i class="bi bi-ticket-perforated-fill text-white fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Sistema de Loterías</h4>
                    <p class="text-muted small">Control de Agencias</p>
                </div>

                <?= Flash::render() ?>

                <form method="POST" action="<?= BASE_URL ?>/login" novalidate>
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="usuario" class="form-control form-control-lg"
                                placeholder="Ingresa tu usuario" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control form-control-lg"
                                placeholder="Contraseña" required id="passInput">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Usuario: <strong>admin</strong> | Clave: <strong>password</strong></small>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass() {
            const i = document.getElementById('passInput');
            const e = document.getElementById('eyeIcon');
            if (i.type === 'password') { i.type = 'text'; e.className = 'bi bi-eye-slash'; }
            else { i.type = 'password'; e.className = 'bi bi-eye'; }
        }
    </script>
</body>
</html>
