<?php
class ReportesController
{
    private VentaModel      $ventaModel;
    private CuadreCajaModel $cuadreModel;

    public function __construct()
    {
        $this->ventaModel  = new VentaModel();
        $this->cuadreModel = new CuadreCajaModel();
    }

    public function index(): void
    {
        Auth::check();
        require ROOT_PATH . '/views/reportes/index.php';
    }

    public function generar(): void
    {
        Auth::check();
        $tipo   = $_GET['tipo']   ?? 'agencia';
        $desde  = $_GET['desde']  ?? date('Y-m-01');
        $hasta  = $_GET['hasta']  ?? date('Y-m-d');
        $formato = $_GET['formato'] ?? 'pdf';

        $data = match ($tipo) {
            'agencia'     => $this->ventaModel->getReportePorAgencia($desde, $hasta),
            'taquilla'    => $this->ventaModel->getReportePorTaquilla($desde, $hasta),
            'sistema'     => $this->ventaModel->getReportePorSistema($desde, $hasta),
            'cuadre'      => $this->cuadreModel->getAllWithAgencia($desde, $hasta),
            'comparativo' => $this->getComparativo($desde, $hasta),
            default       => [],
        };

        $titulo = match ($tipo) {
            'agencia'     => 'Ventas por Agencia',
            'taquilla'    => 'Ventas por Taquilla',
            'sistema'     => 'Ventas por Sistema',
            'cuadre'      => 'Cuadre de Caja Diario',
            'comparativo' => 'Comparativo Ventas vs Caja',
            default       => 'Reporte',
        };

        if ($formato === 'excel') {
            $this->exportExcel($data, $titulo, $tipo);
        } else {
            $this->exportPdf($data, $titulo, $tipo, $desde, $hasta);
        }
    }

    private function getComparativo(string $desde, string $hasta): array
    {
        $ventas  = $this->ventaModel->getReportePorAgencia($desde, $hasta);
        $cuadres = $this->cuadreModel->getAllWithAgencia($desde, $hasta);
        // Agrupar cuadres por agencia
        $cuadresPorAgencia = [];
        foreach ($cuadres as $c) {
            $ag = $c['agencia_nombre'];
            if (!isset($cuadresPorAgencia[$ag])) {
                $cuadresPorAgencia[$ag] = ['total_bs' => 0, 'total_usd' => 0];
            }
            $cuadresPorAgencia[$ag]['total_bs']  += $c['total_bs'];
            $cuadresPorAgencia[$ag]['total_usd'] += $c['total_usd'];
        }
        $resultado = [];
        foreach ($ventas as $v) {
            $cb = $cuadresPorAgencia[$v['agencia']]['total_bs']  ?? 0;
            $cu = $cuadresPorAgencia[$v['agencia']]['total_usd'] ?? 0;
            $resultado[] = [
                'agencia'        => $v['agencia'],
                'ventas_bs'      => $v['total_bs'],
                'ventas_usd'     => $v['total_usd'],
                'caja_bs'        => $cb,
                'caja_usd'       => $cu,
                'diferencia_bs'  => $v['total_bs']  - $cb,
                'diferencia_usd' => $v['total_usd'] - $cu,
            ];
        }
        return $resultado;
    }

    private function exportPdf(array $data, string $titulo, string $tipo, string $desde, string $hasta): void
    {
        require_once ROOT_PATH . '/reports/ReportePDF.php';
        $pdf = new ReportePDF($data, $titulo, $tipo, $desde, $hasta);
        $pdf->render();
    }

    private function exportExcel(array $data, string $titulo, string $tipo): void
    {
        require_once ROOT_PATH . '/reports/ReporteExcel.php';
        $excel = new ReporteExcel($data, $titulo, $tipo);
        $excel->render();
    }
}
