<?php $pageTitle = 'Ventas'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-table me-2 text-primary"></i>Ventas Registradas</h4>
        <p class="text-muted small mb-0">Resumen de ventas por taquilla</p>
    </div>
    <a href="<?= BASE_URL ?>/ventas/form" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Registrar Venta
    </a>
</div>

<!-- Filtros -->
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
                <thead class="table-primary">
                    <tr>
                        <th>Agencia</th>
                        <th>Taquilla</th>
                        <th class="text-end">Total Bs</th>
                        <th class="text-end">Total USD</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Sin ventas en el período seleccionado.</td></tr>
                <?php else: ?>
                <?php
                $totalBs = 0; $totalUsd = 0;
                $currentAgencia = '';
                foreach ($ventas as $v):
                    $totalBs  += $v['total_bs'];
                    $totalUsd += $v['total_usd'];
                ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($v['agencia']) ?></td>
                    <td><?= htmlspecialchars($v['taquilla']) ?></td>
                    <td class="text-end"><?= number_format($v['total_bs'], 2, ',', '.') ?> Bs</td>
                    <td class="text-end">$ <?= number_format($v['total_usd'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-dark fw-bold">
                    <td colspan="2" class="text-end">TOTALES</td>
                    <td class="text-end"><?= number_format($totalBs, 2, ',', '.') ?> Bs</td>
                    <td class="text-end">$ <?= number_format($totalUsd, 2) ?></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
