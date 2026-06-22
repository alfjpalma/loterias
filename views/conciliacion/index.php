<?php $pageTitle = 'Conciliación'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-patch-check me-2 text-info"></i>Conciliación Ventas vs Caja</h4>
        <p class="text-muted small mb-0">Comparación automática de ventas versus cuadre de caja</p>
    </div>
</div>

<!-- Selector de fecha -->
<form method="GET" class="row g-2 mb-4 align-items-end">
    <div class="col-auto">
        <label class="form-label small">Fecha de conciliación</label>
        <input type="date" name="fecha" class="form-control form-control-sm"
               value="<?= htmlspecialchars($fecha) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-info text-white btn-sm">
            <i class="bi bi-search me-1"></i>Consultar
        </button>
    </div>
</form>

<!-- Resumen global -->
<?php
$totalVtaBs = array_sum(array_column($datos, 'ventas_bs'));
$totalVtaUsd = array_sum(array_column($datos, 'ventas_usd'));
$totalCajaBs = array_sum(array_column($datos, 'caja_bs'));
$totalCajaUsd = array_sum(array_column($datos, 'caja_usd'));
$difBsTotal  = $totalVtaBs  - $totalCajaBs;
$difUsdTotal = $totalVtaUsd - $totalCajaUsd;
$cuadradoTotal = abs($difBsTotal) < 0.01 && abs($difUsdTotal) < 0.01;
?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Total Ventas Bs</div>
            <div class="fs-5 fw-bold text-success"><?= number_format($totalVtaBs, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Total Caja Bs</div>
            <div class="fs-5 fw-bold text-primary"><?= number_format($totalCajaBs, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Diferencia Bs</div>
            <div class="fs-5 fw-bold <?= abs($difBsTotal) < 0.01 ? 'text-success' : 'text-danger' ?>">
                <?= number_format($difBsTotal, 2, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Estado General</div>
            <div class="fs-5 fw-bold">
                <?php if ($cuadradoTotal): ?>
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Cuadrado</span>
                <?php else: ?>
                    <span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Diferencia</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabla detalle por agencia -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white fw-semibold">
        <i class="bi bi-table me-1"></i>Detalle por Agencia — <?= date('d/m/Y', strtotime($fecha)) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-info">
                <tr>
                    <th>Agencia</th>
                    <th class="text-end">Ventas Bs</th>
                    <th class="text-end">Caja Bs</th>
                    <th class="text-end">Dif. Bs</th>
                    <th class="text-end">Ventas $</th>
                    <th class="text-end">Caja $</th>
                    <th class="text-end">Dif. $</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($datos)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">No hay datos para esta fecha.</td></tr>
            <?php else: ?>
                <?php foreach ($datos as $d):
                    $dBs  = round($d['ventas_bs']  - $d['caja_bs'],  2);
                    $dUsd = round($d['ventas_usd'] - $d['caja_usd'], 2);
                    $ok   = abs($dBs) < 0.01 && abs($dUsd) < 0.01;
                    $sinDatos = ($d['ventas_bs'] == 0 && $d['caja_bs'] == 0);
                ?>
                <tr class="<?= !$ok && !$sinDatos ? 'table-danger' : '' ?>">
                    <td class="fw-semibold"><?= htmlspecialchars($d['agencia']) ?></td>
                    <td class="text-end"><?= number_format($d['ventas_bs'],  2, ',', '.') ?></td>
                    <td class="text-end"><?= number_format($d['caja_bs'],    2, ',', '.') ?></td>
                    <td class="text-end <?= abs($dBs)  < 0.01 ? '' : 'text-danger fw-bold' ?>">
                        <?= ($dBs > 0 ? '+' : '') . number_format($dBs,  2, ',', '.') ?>
                    </td>
                    <td class="text-end">$ <?= number_format($d['ventas_usd'], 2) ?></td>
                    <td class="text-end">$ <?= number_format($d['caja_usd'],   2) ?></td>
                    <td class="text-end <?= abs($dUsd) < 0.01 ? '' : 'text-danger fw-bold' ?>">
                        <?= ($dUsd > 0 ? '+' : '') . number_format($dUsd, 2) ?> $
                    </td>
                    <td class="text-center">
                        <?php if ($sinDatos): ?>
                            <span class="badge bg-secondary">Sin datos</span>
                        <?php elseif ($ok): ?>
                            <span class="badge bg-success"><i class="bi bi-check2"></i> Cuadrado ✅</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Diferencia ⚠️</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
