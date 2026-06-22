<?php $pageTitle = 'Agencias'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-building me-2 text-primary"></i>Agencias</h4>
        <p class="text-muted small mb-0">Gestión de agencias de loterías</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= BASE_URL ?>/agencias/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nueva Agencia
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaAgencias">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th class="d-none d-md-table-cell">Dirección</th>
                        <th class="d-none d-md-table-cell">Teléfono</th>
                        <th class="text-center">Taquillas</th>
                        <th class="text-center">Estado</th>
                        <?php if (Auth::isAdmin()): ?><th class="text-center">Acciones</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($agencias)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay agencias registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($agencias as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($a['nombre']) ?></td>
                        <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars($a['direccion'] ?? '—') ?></td>
                        <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars($a['telefono'] ?? '—') ?></td>
                        <td class="text-center"><span class="badge bg-info"><?= $a['total_taquillas'] ?></span></td>
                        <td class="text-center">
                            <span class="badge <?= $a['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $a['estado'] ? 'Activa' : 'Inactiva' ?>
                            </span>
                        </td>
                        <?php if (Auth::isAdmin()): ?>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/agencias/edit/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>/agencias/toggle/<?= $a['id'] ?>" class="d-inline">
                                <?= Csrf::field() ?>
                                <button class="btn btn-sm <?= $a['estado'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $a['estado'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-<?= $a['estado'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?= BASE_URL ?>/agencias/delete/<?= $a['id'] ?>" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta agencia?')">
                                <?= Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
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
