/* ============================================================
   ventas.js — Cálculos en tiempo real del formulario de ventas
   ============================================================ */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    const totalBsEl  = document.getElementById('totalBs');
    const totalUsdEl = document.getElementById('totalUsd');

    function calcularTotales() {
        let sumBs  = 0;
        let sumUsd = 0;

        document.querySelectorAll('.bs-input').forEach(inp => {
            sumBs  += parseFloat(inp.value.replace(',', '.') || 0);
        });
        document.querySelectorAll('.usd-input').forEach(inp => {
            sumUsd += parseFloat(inp.value.replace(',', '.') || 0);
        });

        if (totalBsEl)  totalBsEl.value  = sumBs.toLocaleString('es-VE',  { minimumFractionDigits: 2 });
        if (totalUsdEl) totalUsdEl.value = sumUsd.toLocaleString('es-VE', { minimumFractionDigits: 2 });
    }

    // Escuchar cambios en cada celda
    document.querySelectorAll('.bs-input, .usd-input').forEach(inp => {
        inp.addEventListener('input',  calcularTotales);
        inp.addEventListener('change', calcularTotales);

        // Seleccionar todo al hacer foco
        inp.addEventListener('focus',  () => inp.select());
    });

    // Calcular totales al cargar si hay datos previos
    calcularTotales();

    // Botón limpiar
    window.limpiarForm = function () {
        if (!confirm('¿Limpiar todos los valores?')) return;
        document.querySelectorAll('.bs-input, .usd-input').forEach(inp => inp.value = '0.00');
        calcularTotales();
    };

    // Navegación con Enter entre celdas
    document.querySelectorAll('#tablaVentas input[type=number]').forEach((inp, i, all) => {
        inp.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const next = all[i + 1];
                if (next) next.focus();
            }
        });
    });
});
