<?php
$pageTitle = 'Dashboard — ' . APP_NAME;
$extraJs = ['app.js'];
require ROOT_PATH . '/views/layout/header.php';
?>

<div class="page-header mb-4">
    <h4 class="mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
    <p class="text-muted small mb-0">Resumen general del sistema</p>
</div>

<!-- Filtro de fechas -->
<form method="GET" class="row g-2 mb-4 align-items-end">
    <div class="col-6 col-md-3">
        <label class="form-label small">Desde</label>
        <input type="date" name="desde" class="form-control form-control-sm"
               value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small">Hasta</label>
        <input type="date" name="hasta" class="form-control form-control-sm"
               value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-primary btn-sm w-100">
            <i class="bi bi-funnel me-1"></i>Filtrar
        </button>
    </div>
</form>

<!-- Tarjetas de estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-primary text-white rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-building fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-3 fw-bold"><?= $stats['agencias'] ?></div>
            <div class="small opacity-90">Agencias</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-info text-white rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-shop fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-3 fw-bold"><?= $stats['taquillas'] ?></div>
            <div class="small opacity-90">Taquillas</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-secondary text-white rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-grid-3x3-gap fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-3 fw-bold"><?= $stats['sistemas'] ?></div>
            <div class="small opacity-90">Sistemas</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-success text-white rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-currency-dollar fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-4 fw-bold"><?= number_format($stats['ventas_hoy']['bs'], 0, ',', '.') ?></div>
            <div class="small opacity-90">Ventas Hoy Bs</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-warning text-dark rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-dollar fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-4 fw-bold">$<?= number_format($stats['ventas_hoy']['usd'], 2) ?></div>
            <div class="small opacity-90">Ventas Hoy $</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card <?= $stats['pendientes'] > 0 ? 'bg-danger' : 'bg-success' ?> text-white rounded-3 p-3 text-center shadow-sm">
            <i class="bi bi-clock-history fs-2 mb-1 d-block opacity-75"></i>
            <div class="fs-3 fw-bold"><?= $stats['pendientes'] ?></div>
            <div class="small opacity-90">Cuadres Pendientes</div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-bar-chart me-1 text-primary"></i>Ventas por Agencia (Bs)
            </div>
            <div class="card-body">
                <canvas id="chartAgencias" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-pie-chart me-1 text-success"></i>Ventas por Sistema (Bs)
            </div>
            <div class="card-body">
                <canvas id="chartSistemas" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Conciliación del día y Ranking -->
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-patch-check me-1 text-info"></i>Conciliación Hoy (<?= date('d/m/Y') ?>)
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Agencia</th>
                            <th class="text-end">Ventas Bs</th>
                            <th class="text-end">Caja Bs</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($conciliacion)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Sin datos hoy</td></tr>
                    <?php else: ?>
                        <?php foreach ($conciliacion as $c):
                            $difBs = $c['ventas_bs'] - $c['caja_bs'];
                            $ok = abs($difBs) < 0.01;
                        ?>
                        <tr class="<?= !$ok ? 'table-danger' : '' ?>">
                            <td><?= htmlspecialchars($c['agencia']) ?></td>
                            <td class="text-end"><?= number_format($c['ventas_bs'], 2, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($c['caja_bs'], 2, ',', '.') ?></td>
                            <td class="text-center">
                                <?php if ($c['ventas_bs'] == 0 && $c['caja_bs'] == 0): ?>
                                    <span class="badge bg-secondary">Sin datos</span>
                                <?php elseif ($ok): ?>
                                    <span class="badge bg-success"><i class="bi bi-check2"></i> Cuadrado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Diferencia</span>
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

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-trophy me-1 text-warning"></i>Ranking Taquillas (período)
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Taquilla</th>
                            <th>Agencia</th>
                            <th class="text-end">Total Bs</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ranking)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Sin datos</td></tr>
                    <?php else: ?>
                        <?php foreach ($ranking as $i => $r): ?>
                        <tr>
                            <td>
                                <?php if ($i === 0): ?><i class="bi bi-trophy-fill text-warning"></i>
                                <?php elseif ($i === 1): ?><i class="bi bi-trophy-fill text-secondary"></i>
                                <?php elseif ($i === 2): ?><i class="bi bi-trophy-fill text-danger"></i>
                                <?php else: ?><?= $i + 1 ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['taquilla']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($r['agencia']) ?></small></td>
                            <td class="text-end fw-semibold"><?= number_format($r['total_bs'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para gráficos
const agenciasData = <?= json_encode(array_column($chartAgencias, 'label')) ?>;
const agenciasBs   = <?= json_encode(array_map(fn($r) => (float)$r['bs'],  $chartAgencias)) ?>;

const sistemasData = <?= json_encode(array_column($chartSistemas, 'label')) ?>;
const sistemasBs   = <?= json_encode(array_map(fn($r) => (float)$r['bs'],  $chartSistemas)) ?>;

const colors = [
    '#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0',
    '#6610f2','#fd7e14','#20c997','#6c757d','#d63384'
];

// Gráfico agencias
new Chart(document.getElementById('chartAgencias'), {
    type: 'bar',
    data: {
        labels: agenciasData,
        datasets: [{
            label: 'Ventas Bs',
            data: agenciasBs,
            backgroundColor: colors,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('es-VE') } } }
    }
});

// Gráfico sistemas
new Chart(document.getElementById('chartSistemas'), {
    type: 'doughnut',
    data: {
        labels: sistemasData,
        datasets: [{
            data: sistemasBs,
            backgroundColor: colors,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toLocaleString('es-VE') + ' Bs' } }
        }
    }
});
</script>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
