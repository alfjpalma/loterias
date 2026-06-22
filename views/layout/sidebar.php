<aside class="sidebar bg-dark text-white" id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-secondary">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-ticket-perforated-fill text-warning fs-4"></i>
            <span class="fw-bold text-white sidebar-text">Loterías</span>
        </div>
    </div>

    <nav class="sidebar-nav py-2">
        <?php
        $uri = $_SERVER['REQUEST_URI'];
        function isActive(string $path): string {
            return str_contains($_SERVER['REQUEST_URI'], $path) ? 'active' : '';
        }
        ?>

        <div class="nav-section-title sidebar-text px-3 py-1 text-uppercase text-secondary" style="font-size:0.7rem;letter-spacing:1px">
            Principal
        </div>
        <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= isActive('/dashboard') ?>">
            <i class="bi bi-speedometer2"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <div class="nav-section-title sidebar-text px-3 py-2 text-uppercase text-secondary" style="font-size:0.7rem;letter-spacing:1px">
            Operaciones
        </div>
        <a href="<?= BASE_URL ?>/ventas/form" class="nav-link <?= isActive('/ventas/form') ?>">
            <i class="bi bi-receipt-cutoff"></i>
            <span class="sidebar-text">Registro Ventas</span>
        </a>
        <a href="<?= BASE_URL ?>/ventas" class="nav-link <?= str_contains($uri, '/ventas') && !str_contains($uri, '/form') ? 'active' : '' ?>">
            <i class="bi bi-table"></i>
            <span class="sidebar-text">Ver Ventas</span>
        </a>
        <a href="<?= BASE_URL ?>/cuadres/form" class="nav-link <?= isActive('/cuadres/form') ?>">
            <i class="bi bi-cash-stack"></i>
            <span class="sidebar-text">Cuadre de Caja</span>
        </a>
        <a href="<?= BASE_URL ?>/cuadres" class="nav-link <?= str_contains($uri, '/cuadres') && !str_contains($uri, '/form') ? 'active' : '' ?>">
            <i class="bi bi-journal-check"></i>
            <span class="sidebar-text">Ver Cuadres</span>
        </a>
        <a href="<?= BASE_URL ?>/conciliacion" class="nav-link <?= isActive('/conciliacion') ?>">
            <i class="bi bi-patch-check"></i>
            <span class="sidebar-text">Conciliación</span>
        </a>
        <a href="<?= BASE_URL ?>/reportes" class="nav-link <?= isActive('/reportes') ?>">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span class="sidebar-text">Reportes</span>
        </a>

        <?php if (Auth::isAdmin()): ?>
        <div class="nav-section-title sidebar-text px-3 py-2 text-uppercase text-secondary" style="font-size:0.7rem;letter-spacing:1px">
            Administración
        </div>
        <a href="<?= BASE_URL ?>/agencias" class="nav-link <?= isActive('/agencias') ?>">
            <i class="bi bi-building"></i>
            <span class="sidebar-text">Agencias</span>
        </a>
        <a href="<?= BASE_URL ?>/taquillas" class="nav-link <?= isActive('/taquillas') ?>">
            <i class="bi bi-shop"></i>
            <span class="sidebar-text">Taquillas</span>
        </a>
        <a href="<?= BASE_URL ?>/sistemas" class="nav-link <?= isActive('/sistemas') ?>">
            <i class="bi bi-grid-3x3-gap"></i>
            <span class="sidebar-text">Sistemas</span>
        </a>
        <a href="<?= BASE_URL ?>/usuarios" class="nav-link <?= isActive('/usuarios') ?>">
            <i class="bi bi-people"></i>
            <span class="sidebar-text">Usuarios</span>
        </a>
        <?php endif; ?>
    </nav>
</aside>
