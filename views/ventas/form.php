<?php
$pageTitle = 'Registro de Ventas';
$extraJs = ['ventas.js'];
require ROOT_PATH . '/views/layout/header.php';

// Mapear detalles existentes por sistema_id para prellenar
$detalleMap = [];
foreach ($detalle as $d) {
    $detalleMap[$d['sistema_id']] = $d;
}
?>

<div class="d-flex align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-receipt-cutoff me-2 text-success"></i>Registro Diario de Ventas</h4>
</div>

<!-- Selector de fecha/agencia/taquilla -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/ventas/form" id="formSelector" class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold small">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control"
                       value="<?= htmlspecialchars($fecha) ?>" required>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold small">Agencia</label>
                <select name="agencia_id" id="agenciaSelect" class="form-select" required>
                    <option value="">Seleccione agencia...</option>
                    <?php foreach ($agencias as $ag): ?>
                    <option value="<?= $ag['id'] ?>" <?= $agenciaId == $ag['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ag['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold small">Taquilla</label>
                <select name="taquilla_id" id="taquillaSelect" class="form-select" required>
                    <option value="">Seleccione taquilla...</option>
                    <?php foreach ($taquillas as $tq): ?>
                    <option value="<?= $tq['id'] ?>" <?= $taquillaId == $tq['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tq['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i>Cargar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($taquillaId > 0): ?>
<!-- Formulario de registro -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-1"></i>Ventas por Sistema</span>
        <?php if ($ventaExistente): ?>
        <span class="badge bg-warning text-dark">
            <i class="bi bi-info-circle me-1"></i>Editando registro existente
        </span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/ventas/store" id="formVentas">
            <?= Csrf::field() ?>
            <input type="hidden" name="fecha"       value="<?= htmlspecialchars($fecha) ?>">
            <input type="hidden" name="agencia_id"  value="<?= $agenciaId ?>">
            <input type="hidden" name="taquilla_id" value="<?= $taquillaId ?>">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="tablaVentas">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Sistema</th>
                            <th style="min-width:160px">Total Bs</th>
                            <th style="min-width:160px">Total USD ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sistemas as $idx => $s): ?>
                    <?php $d = $detalleMap[$s['id']] ?? []; ?>
                    <tr>
                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($s['nombre']) ?></td>
                        <td>
                            <input type="hidden" name="detalle[<?= $idx ?>][sistema_id]" value="<?= $s['id'] ?>">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-muted">Bs</span>
                                <input type="number" step="0.01" min="0"
                                       name="detalle[<?= $idx ?>][total_bs]"
                                       class="form-control text-end bs-input"
                                       value="<?= number_format((float)($d['total_bs'] ?? 0), 2, '.', '') ?>"
                                       placeholder="0.00">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-muted">$</span>
                                <input type="number" step="0.01" min="0"
                                       name="detalle[<?= $idx ?>][total_usd]"
                                       class="form-control text-end usd-input"
                                       value="<?= number_format((float)($d['total_usd'] ?? 0), 2, '.', '') ?>"
                                       placeholder="0.00">
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="2" class="fw-bold text-end">TOTAL</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs</span>
                                    <input type="text" id="totalBs" class="form-control text-end fw-bold bg-dark text-success"
                                           value="0.00" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="text" id="totalUsd" class="form-control text-end fw-bold bg-dark text-warning"
                                           value="0.00" readonly>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarForm()">
                    <i class="bi bi-eraser me-1"></i>Limpiar
                </button>
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-save me-1"></i>Guardar Ventas
                </button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Seleccione una <strong>fecha</strong>, <strong>agencia</strong> y <strong>taquilla</strong> para registrar ventas.
</div>
<?php endif; ?>

<!-- AJAX para cargar taquillas -->
<script>
const BASE_URL = '<?= BASE_URL ?>';
const selectedAgencia  = <?= $agenciaId ?>;
const selectedTaquilla = <?= $taquillaId ?>;
</script>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
