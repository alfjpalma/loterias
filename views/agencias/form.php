<?php
$editing = !is_null($agencia);
$pageTitle = ($editing ? 'Editar' : 'Nueva') . ' Agencia';
require ROOT_PATH . '/views/layout/header.php';
?>

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= BASE_URL ?>/agencias" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0">
        <i class="bi bi-building me-2 text-primary"></i>
        <?= $editing ? 'Editar Agencia' : 'Nueva Agencia' ?>
    </h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST"
                      action="<?= BASE_URL ?>/agencias/<?= $editing ? 'update/' . $agencia['id'] : 'store' ?>"
                      novalidate>
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($agencia['nombre'] ?? '') ?>"
                               placeholder="Ej: Agencia Centro" required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección</label>
                        <textarea name="direccion" class="form-control" rows="2"
                                  placeholder="Dirección completa"><?= htmlspecialchars($agencia['direccion'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="tel" name="telefono" class="form-control"
                               value="<?= htmlspecialchars($agencia['telefono'] ?? '') ?>"
                               placeholder="0412-0000000">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?= ($agencia['estado'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= ($agencia['estado'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-1"></i><?= $editing ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/agencias" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
