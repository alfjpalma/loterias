<?php $pageTitle = 'Sistemas'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-grid-3x3-gap me-2 text-secondary"></i>Sistemas</h4>
        <p class="text-muted small mb-0">Sistemas de lotería disponibles</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= BASE_URL ?>/sistemas/create" class="btn btn-secondary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Sistema
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th><th>Orden</th><th>Nombre</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="text-center">Estado</th>
                        <?php if (Auth::isAdmin()): ?><th class="text-center">Acciones</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sistemas as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= $s['orden'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($s['nombre']) ?></td>
                    <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars($s['descripcion'] ?? '—') ?></td>
                    <td class="text-center">
                        <span class="badge <?= $s['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $s['estado'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <?php if (Auth::isAdmin()): ?>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/sistemas/edit/<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>/sistemas/delete/<?= $s['id'] ?>" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este sistema?')">
                            <?= Csrf::field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
