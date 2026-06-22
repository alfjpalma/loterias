<?php $pageTitle = 'Cuadres de Caja'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-journal-check me-2 text-success"></i>Cuadres de Caja</h4>
        <p class="text-muted small mb-0">Historial de cuadres por agencia</p>
    </div>
    <a href="<?= BASE_URL ?>/cuadres/form" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Cuadre
    </a>
</div>

<form method="GET" class="row g-2 mb-4 align-items-end">
    <div class="col-6 col-md-3">
        <label class="form-label small">Desde</label>
        <input type="date" name="desde" class="form-control form-control-sm" value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small">Hasta</label>
        <input type="date" name="hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Fecha</th>
                        <th>Agencia</th>
                        <th class="text-end">Punto1 Bs</th>
                        <th class="text-end">Punto2 Bs</th>
                        <th class="text-end">Efectivo Bs</th>
                        <th class="text-end">Total Bs</th>
                        <th class="text-end">Total $</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($cuadres)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Sin cuadres en el período.</td></tr>
                <?php else: ?>
                    <?php foreach ($cuadres as $c): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($c['agencia_nombre']) ?></td>
                        <td class="text-end"><?= number_format($c['punto_banco1'], 2, ',', '.') ?></td>
                        <td class="text-end"><?= number_format($c['punto_banco2'], 2, ',', '.') ?></td>
                        <td class="text-end"><?= number_format($c['efectivo_bs'],  2, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success"><?= number_format($c['total_bs'],  2, ',', '.') ?></td>
                        <td class="text-end fw-bold text-warning">$ <?= number_format($c['total_usd'], 2) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/cuadres/form?fecha=<?= $c['fecha'] ?>&agencia_id=<?= $c['agencia_id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (Auth::isAdmin()): ?>
                            <form method="POST" action="<?= BASE_URL ?>/cuadres/delete/<?= $c['id'] ?>" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar cuadre?')">
                                <?= Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
