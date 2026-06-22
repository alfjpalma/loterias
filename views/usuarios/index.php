<?php $pageTitle = 'Usuarios'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-people me-2 text-dark"></i>Usuarios</h4>
        <p class="text-muted small mb-0">Gestión de usuarios del sistema</p>
    </div>
    <a href="<?= BASE_URL ?>/usuarios/create" class="btn btn-dark">
        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th><th>Nombre</th><th>Usuario</th>
                        <th class="text-center">Rol</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><code><?= htmlspecialchars($u['usuario']) ?></code></td>
                    <td class="text-center">
                        <span class="badge <?= $u['rol'] === 'administrador' ? 'bg-danger' : 'bg-primary' ?>">
                            <i class="bi bi-<?= $u['rol'] === 'administrador' ? 'shield-fill' : 'person' ?> me-1"></i>
                            <?= ucfirst($u['rol']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge <?= $u['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $u['estado'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/usuarios/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] !== Auth::id()): ?>
                        <form method="POST" action="<?= BASE_URL ?>/usuarios/delete/<?= $u['id'] ?>" class="d-inline"
                              onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($u['nombre']) ?>?')">
                            <?= Csrf::field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
