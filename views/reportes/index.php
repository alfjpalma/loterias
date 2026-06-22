<?php $pageTitle = 'Reportes'; require ROOT_PATH . '/views/layout/header.php'; ?>

<div class="mb-4">
    <h4 class="mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Reportes</h4>
    <p class="text-muted small mb-0">Generación de reportes en PDF y Excel</p>
</div>

<?php
$hoy = date('Y-m-d');
$inicio = date('Y-m-01');
?>

<div class="row g-3">
    <?php
    $reportes = [
        ['tipo' => 'agencia',     'titulo' => 'Ventas por Agencia',    'icon' => 'building',              'color' => 'primary'],
        ['tipo' => 'taquilla',    'titulo' => 'Ventas por Taquilla',   'icon' => 'shop',                  'color' => 'info'],
        ['tipo' => 'sistema',     'titulo' => 'Ventas por Sistema',    'icon' => 'grid-3x3-gap',          'color' => 'secondary'],
        ['tipo' => 'cuadre',      'titulo' => 'Cuadre de Caja',        'icon' => 'cash-stack',            'color' => 'success'],
        ['tipo' => 'comparativo', 'titulo' => 'Comparativo Ventas/Caja','icon' => 'bar-chart-line',       'color' => 'warning'],
    ];
    foreach ($reportes as $r):
    ?>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-<?= $r['color'] ?> <?= $r['color'] === 'warning' ? 'text-dark' : 'text-white' ?>">
                <i class="bi bi-<?= $r['icon'] ?> me-2"></i><?= $r['titulo'] ?>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>/reportes/generar" target="_blank">
                    <input type="hidden" name="tipo" value="<?= $r['tipo'] ?>">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small">Desde</label>
                            <input type="date" name="desde" class="form-control form-control-sm" value="<?= $inicio ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Hasta</label>
                            <input type="date" name="hasta" class="form-control form-control-sm" value="<?= $hoy ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="formato" value="pdf" class="btn btn-sm btn-danger flex-grow-1">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-sm btn-success flex-grow-1">
                            <i class="bi bi-file-excel me-1"></i>Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
