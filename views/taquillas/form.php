<?php
$editing = !is_null($taquilla);
$pageTitle = ($editing ? 'Editar' : 'Nueva') . ' Taquilla';
require ROOT_PATH . '/views/layout/header.php';
?>

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= BASE_URL ?>/taquillas" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0"><i class="bi bi-shop me-2 text-info"></i><?= $editing ? 'Editar Taquilla' : 'Nueva Taquilla' ?></h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST"
                      action="<?= BASE_URL ?>/taquillas/<?= $editing ? 'update/' . $taquilla['id'] : 'store' ?>">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Agencia <span class="text-danger">*</span></label>
                        <select name="agencia_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($agencias as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= ($taquilla['agencia_id'] ?? 0) == $a['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($taquilla['nombre'] ?? '') ?>"
                               placeholder="Ej: Taquilla 1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($taquilla['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?= ($taquilla['estado'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= ($taquilla['estado'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-check2 me-1"></i><?= $editing ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/taquillas" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
