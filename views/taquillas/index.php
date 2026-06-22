<?php $pageTitle = 'Taquillas'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-shop me-2 text-info"></i>Taquillas</h4>
        <p class="text-muted small mb-0">Gestión de taquillas por agencia</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= BASE_URL ?>/taquillas/create" class="btn btn-info text-white">
        <i class="bi bi-plus-lg me-1"></i>Nueva Taquilla
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-info">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Agencia</th>
                        <th class="d-none d-md-table-cell">Descripción</th>
                        <th class="text-center">Estado</th>
                        <?php if (Auth::isAdmin()): ?><th class="text-center">Acciones</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($taquillas)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay taquillas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($taquillas as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($t['nombre']) ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($t['agencia_nombre']) ?></span></td>
                        <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars($t['descripcion'] ?? '—') ?></td>
                        <td class="text-center">
                            <span class="badge <?= $t['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $t['estado'] ? 'Activa' : 'Inactiva' ?>
                            </span>
                        </td>
                        <?php if (Auth::isAdmin()): ?>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/taquillas/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>/taquillas/delete/<?= $t['id'] ?>" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta taquilla?')">
                                <?= Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
