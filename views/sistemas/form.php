<?php
$editing = !is_null($sistema);
$pageTitle = ($editing ? 'Editar' : 'Nuevo') . ' Sistema';
require ROOT_PATH . '/views/layout/header.php';
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= BASE_URL ?>/sistemas" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0"><i class="bi bi-grid-3x3-gap me-2 text-secondary"></i><?= $editing ? 'Editar Sistema' : 'Nuevo Sistema' ?></h4>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>/sistemas/<?= $editing ? 'update/' . $sistema['id'] : 'store' ?>">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($sistema['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" class="form-control"
                               value="<?= htmlspecialchars($sistema['descripcion'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orden</label>
                        <input type="number" name="orden" class="form-control" min="0" max="99"
                               value="<?= $sistema['orden'] ?? 0 ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?= ($sistema['estado'] ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= ($sistema['estado'] ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-secondary text-white">
                            <i class="bi bi-check2 me-1"></i><?= $editing ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/sistemas" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
