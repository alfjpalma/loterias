/* ============================================================
   app.js — Lógica general del sistema
   ============================================================ */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    // ---- Sidebar toggle ----
    const sidebar  = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (sidebar && toggleBtn) {
        // Crear overlay para móvil
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        const isMobile = () => window.innerWidth < 768;

        toggleBtn.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('visible');
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
        });

        // Restaurar estado en desktop
        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }

        // Actualizar en resize
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('open');
                overlay.classList.remove('visible');
            }
        });
    }

    // ---- Auto-dismiss flash alerts ----
    setTimeout(() => {
        document.querySelectorAll('.alert.alert-success, .alert.alert-info').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);

    // ---- Confirm delete buttons ----
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm(btn.dataset.confirm)) e.preventDefault();
        });
    });

    // ---- AJAX cargar taquillas por agencia ----
    const agenciaSelect  = document.getElementById('agenciaSelect');
    const taquillaSelect = document.getElementById('taquillaSelect');

    if (agenciaSelect && taquillaSelect) {
        agenciaSelect.addEventListener('change', function () {
            const agenciaId = this.value;
            taquillaSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!agenciaId) {
                taquillaSelect.innerHTML = '<option value="">Seleccione taquilla...</option>';
                return;
            }

            fetch(`${BASE_URL}/ajax/get_taquillas.php?agencia_id=${agenciaId}`)
                .then(r => r.json())
                .then(data => {
                    taquillaSelect.innerHTML = '<option value="">Seleccione taquilla...</option>';
                    data.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.textContent = t.nombre;
                        if (t.id == (typeof selectedTaquilla !== 'undefined' ? selectedTaquilla : 0))
                            opt.selected = true;
                        taquillaSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    taquillaSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    }

    // ---- Formato numérico en campos de solo lectura ----
    function fmtNum(n) {
        return parseFloat(n || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    window.fmtNum = fmtNum;
});
