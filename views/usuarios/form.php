<?php
$editing = !is_null($usuario);
$pageTitle = ($editing ? 'Editar' : 'Nuevo') . ' Usuario';
require ROOT_PATH . '/views/layout/header.php';
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0"><i class="bi bi-person me-2"></i><?= $editing ? 'Editar Usuario' : 'Nuevo Usuario' ?></h4>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>/usuarios/<?= $editing ? 'update/' . $usuario['id'] : 'store' ?>">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                        <input type="text" name="usuario" class="form-control"
                               value="<?= htmlspecialchars($usuario['usuario'] ?? '') ?>" required
                               autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Contraseña <?= !$editing ? '<span class="text-danger">*</span>' : '<small class="text-muted">(dejar en blanco para no cambiar)</small>' ?>
                        </label>
                        <input type="password" name="password" class="form-control"
                               <?= !$editing ? 'required' : '' ?> autocomplete="new-password"
                               minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol</label>
                        <select name="rol" class="form-select">
                            <option value="operador"       <?= ($usuario['rol'] ?? 'operador') === 'operador'       ? 'selected' : '' ?>>Operador</option>
                            <option value="administrador"  <?= ($usuario['rol'] ?? '') === 'administrador'          ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?= ($usuario['estado'] ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= ($usuario['estado'] ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check2 me-1"></i><?= $editing ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require ROOT_PATH . '/views/layout/footer.php'; ?>
