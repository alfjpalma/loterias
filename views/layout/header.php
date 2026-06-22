<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <?php require ROOT_PATH . '/views/layout/sidebar.php'; ?>

    <!-- Main content -->
    <div class="content-area flex-grow-1 d-flex flex-column">
        <!-- Navbar top -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 sticky-top">
            <button class="btn btn-sm btn-outline-light me-2" id="sidebarToggle" title="Menú">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="navbar-brand mb-0 h6 d-none d-md-block">
                <i class="bi bi-bank2 me-1"></i><?= APP_NAME ?>
            </span>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="text-white-50 small d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(Auth::name()) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">
                            <i class="bi bi-shield me-1"></i><?= ucfirst(Auth::rol()) ?>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?= BASE_URL ?>/logout">
                                <?= Csrf::field() ?>
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Flash messages -->
        <div class="container-fluid px-4 pt-3">
            <?= Flash::render() ?>
        </div>

        <!-- Page content -->
        <main class="container-fluid px-4 pb-4 flex-grow-1">
