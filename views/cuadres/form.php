<?php
$pageTitle = 'Cuadre de Caja';
$extraJs = ['cuadre.js'];
require ROOT_PATH . '/views/layout/header.php';

// Prellenar con cero si no hay cuadre
$c = $cuadre ?? [];
?>

<div class="d-flex align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Cuadre de Caja</h4>
</div>

<!-- Selector fecha/agencia -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/cuadres/form" class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label fw-semibold small">Fecha</label>
                <input type="date" name="fecha" class="form-control"
                       value="<?= htmlspecialchars($fecha) ?>" required>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label fw-semibold small">Agencia</label>
                <select name="agencia_id" class="form-select" required>
                    <option value="">Seleccione agencia...</option>
                    <?php foreach ($agencias as $ag): ?>
                    <option value="<?= $ag['id'] ?>" <?= $agenciaId == $ag['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ag['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-lg-4">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i>Cargar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($agenciaId > 0): ?>
<div class="row g-3">
    <!-- Formulario Cuadre -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-plus me-1"></i>Ingresos</span>
                <?php if ($cuadre): ?>
                <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>Editando</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/cuadres/store" id="formCuadre">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="fecha"      value="<?= htmlspecialchars($fecha) ?>">
                    <input type="hidden" name="agencia_id" value="<?= $agenciaId ?>">

                    <?php
                    $campos = [
                        'punto_banco1'    => 'Punto Banco 1',
                        'punto_banco2'    => 'Punto Banco 2',
                        'punto_banco3'    => 'Punto Banco 3',
                        'efectivo_bs'     => 'Efectivo Bs',
                        'efectivo_usd'    => 'Efectivo USD ($)',
                        'pago_movil'      => 'Pago Móvil',
                        'premios_pagados' => 'Premios Pagados',
                        'compras'         => 'Compras',
                        'otros'           => 'Otros',
                    ];
                    $usdFields = ['efectivo_usd'];
                    ?>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-3" id="tablaCuadre">
                            <thead class="table-success">
                                <tr>
                                    <th>Concepto</th>
                                    <th style="min-width:160px">Monto</th>
                                    <th class="text-center" style="width:60px">Moneda</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($campos as $field => $label): ?>
                            <tr>
                                <td class="fw-semibold"><?= $label ?></td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                           name="<?= $field ?>"
                                           class="form-control form-control-sm text-end cuadre-input"
                                           data-type="<?= in_array($field, $usdFields) ? 'usd' : 'bs' ?>"
                                           value="<?= number_format((float)($c[$field] ?? 0), 2, '.', '') ?>"
                                           placeholder="0.00">
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= in_array($field, $usdFields) ? 'bg-warning text-dark' : 'bg-primary' ?>">
                                        <?= in_array($field, $usdFields) ? '$' : 'Bs' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td class="fw-bold">TOTAL CAJA Bs</td>
                                    <td colspan="2">
                                        <input type="text" id="totalCajaBs" class="form-control form-control-sm text-end fw-bold bg-dark text-success"
                                               value="0.00" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">TOTAL CAJA USD</td>
                                    <td colspan="2">
                                        <input type="text" id="totalCajaUsd" class="form-control form-control-sm text-end fw-bold bg-dark text-warning"
                                               value="0.00" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($c['observaciones'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="bi bi-save me-1"></i>Guardar Cuadre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resumen lado derecho -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 sticky-top" style="top:80px">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="bi bi-calculator me-1"></i>Resumen del Cuadre
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Total Caja Bs</div>
                    <div class="display-6 fw-bold text-success" id="resumenBs">0,00</div>
                </div>
                <hr>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Total Caja USD</div>
                    <div class="display-6 fw-bold text-warning" id="resumenUsd">0,00</div>
                </div>

                <?php if ($cuadre): ?>
                <hr>
                <div class="small text-muted">
                    <i class="bi bi-clock me-1"></i>Último guardado: <?= date('d/m/Y H:i', strtotime($cuadre['updated_at'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Seleccione <strong>fecha</strong> y <strong>agencia</strong> para registrar el cuadre.
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
