<?php
class DashboardController
{
    public function index(): void
    {
        Auth::check();
        $agenciaModel = new AgenciaModel();
        $taquillaModel = new TaquillaModel();
        $sistemaModel  = new SistemaModel();
        $ventaModel    = new VentaModel();
        $cuadreModel   = new CuadreCajaModel();

        $hoy   = date('Y-m-d');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? $hoy;

        $stats = [
            'agencias'   => $agenciaModel->countWhere('estado', 1),
            'taquillas'  => $taquillaModel->countWhere('estado', 1),
            'sistemas'   => $sistemaModel->countWhere('estado', 1),
            'ventas_hoy' => $ventaModel->getVentasHoy(),
            'pendientes' => $cuadreModel->getPendientes($hoy),
        ];

        $chartAgencias = $ventaModel->getVentasPorAgenciaChart($desde, $hasta);
        $chartSistemas = $ventaModel->getVentasPorSistemaChart($desde, $hasta);
        $ranking       = $ventaModel->getRankingTaquillas($desde, $hasta, 10);
        $conciliacion  = $cuadreModel->getConciliacion($hoy);

        require ROOT_PATH . '/views/dashboard/index.php';
    }
}
