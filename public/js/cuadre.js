/* ============================================================
   cuadre.js — Cálculos en tiempo real del cuadre de caja
   ============================================================ */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    const totalCajaBsEl  = document.getElementById('totalCajaBs');
    const totalCajaUsdEl = document.getElementById('totalCajaUsd');
    const resumenBsEl    = document.getElementById('resumenBs');
    const resumenUsdEl   = document.getElementById('resumenUsd');

    function calcularCuadre() {
        let sumBs  = 0;
        let sumUsd = 0;

        document.querySelectorAll('.cuadre-input').forEach(inp => {
            const val = parseFloat(inp.value.replace(',', '.') || 0);
            if (inp.dataset.type === 'usd') {
                sumUsd += val;
            } else {
                sumBs += val;
            }
        });

        const fmtBs  = sumBs.toLocaleString('es-VE',  { minimumFractionDigits: 2 });
        const fmtUsd = sumUsd.toLocaleString('es-VE', { minimumFractionDigits: 2 });

        if (totalCajaBsEl)  totalCajaBsEl.value  = fmtBs;
        if (totalCajaUsdEl) totalCajaUsdEl.value = fmtUsd;
        if (resumenBsEl)    resumenBsEl.textContent  = fmtBs;
        if (resumenUsdEl)   resumenUsdEl.textContent = fmtUsd;
    }

    document.querySelectorAll('.cuadre-input').forEach(inp => {
        inp.addEventListener('input',  calcularCuadre);
        inp.addEventListener('change', calcularCuadre);
        inp.addEventListener('focus',  () => inp.select());
    });

    // Calcular al cargar si hay datos previos
    calcularCuadre();

    // Navegación con Enter
    const inputs = Array.from(document.querySelectorAll('.cuadre-input'));
    inputs.forEach((inp, i) => {
        inp.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const next = inputs[i + 1];
                if (next) next.focus();
            }
        });
    });
});
