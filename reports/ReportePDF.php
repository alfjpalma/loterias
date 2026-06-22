<?php
/**
 * ReportePDF — Genera reportes en PDF usando FPDF puro
 * Requiere: composer require setasign/fpdf
 * O descargar fpdf.php desde http://www.fpdf.org y colocar en libs/fpdf/
 */
class ReportePDF
{
    private array  $data;
    private string $titulo;
    private string $tipo;
    private string $desde;
    private string $hasta;

    public function __construct(array $data, string $titulo, string $tipo, string $desde, string $hasta)
    {
        $this->data   = $data;
        $this->titulo = $titulo;
        $this->tipo   = $tipo;
        $this->desde  = $desde;
        $this->hasta  = $hasta;
    }

    public function render(): void
    {
        // Intentar cargar FPDF
        $fpdfPath = ROOT_PATH . '/vendor/setasign/fpdf/fpdf.php';
        $fpdfAlt  = ROOT_PATH . '/libs/fpdf/fpdf.php';

        if (file_exists($fpdfPath)) {
            require_once $fpdfPath;
        } elseif (file_exists($fpdfAlt)) {
            require_once $fpdfAlt;
        } else {
            // Fallback: generar HTML imprimible
            $this->renderHtml();
            return;
        }

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);

        // Encabezado
        $pdf->SetFillColor(13, 110, 253);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 12, APP_NAME, 0, 1, 'C', true);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0);
        $pdf->Cell(0, 8, $this->titulo, 0, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(100);
        $pdf->Cell(0, 6,
            'Período: ' . date('d/m/Y', strtotime($this->desde))
            . ' al ' . date('d/m/Y', strtotime($this->hasta))
            . '   |   Generado: ' . date('d/m/Y H:i'),
            0, 1, 'C'
        );
        $pdf->Ln(4);

        // Cabecera de tabla
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(233, 236, 239);
        $pdf->SetTextColor(0);

        [$cols, $widths] = $this->getColumns();

        foreach ($cols as $i => $col) {
            $pdf->Cell($widths[$i], 7, $col, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Datos
        $pdf->SetFont('Arial', '', 8);
        $rowNum = 0;
        foreach ($this->data as $row) {
            $pdf->SetFillColor($rowNum % 2 === 0 ? 255 : 248, $rowNum % 2 === 0 ? 255 : 249, $rowNum % 2 === 0 ? 255 : 250);
            $values = $this->getRowValues($row);
            foreach ($values as $i => $val) {
                $align = is_numeric(str_replace([',', '.', ' ', '$'], '', $val)) && $i > 0 ? 'R' : 'L';
                $pdf->Cell($widths[$i], 6, $val, 1, 0, $align, true);
            }
            $pdf->Ln();
            $rowNum++;
        }

        // Totales
        if (!empty($this->data)) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(13, 110, 253);
            $pdf->SetTextColor(255, 255, 255);
            $totals = $this->getTotals();
            foreach ($totals as $i => $val) {
                $pdf->Cell($widths[$i], 7, $val, 1, 0, $i === 0 ? 'C' : 'R', true);
            }
            $pdf->Ln();
        }

        // Pie de página
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->SetTextColor(150);
        $pdf->Cell(0, 5, 'Total de registros: ' . count($this->data), 0, 1, 'R');

        $filename = strtolower(str_replace(' ', '_', $this->titulo)) . '_' . date('Ymd') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }

    private function renderHtml(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        [$cols, ] = $this->getColumns();
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <title>' . $this->titulo . '</title>
        <style>
        body{font-family:Arial,sans-serif;font-size:12px;margin:20px}
        h2{text-align:center;color:#0d6efd}
        table{width:100%;border-collapse:collapse;margin-top:15px}
        th{background:#0d6efd;color:#fff;padding:6px 8px;text-align:center}
        td{padding:5px 8px;border:1px solid #dee2e6}
        tr:nth-child(even){background:#f8f9fa}
        .footer{text-align:right;margin-top:10px;color:#666;font-size:10px}
        @media print{body{margin:0}}
        </style>
        <script>window.onload=()=>window.print()</script>
        </head><body>
        <h2>' . $this->titulo . '</h2>
        <p style="text-align:center;color:#666">Período: ' . date('d/m/Y', strtotime($this->desde)) . ' al ' . date('d/m/Y', strtotime($this->hasta)) . '</p>
        <table><thead><tr>';
        foreach ($cols as $c) echo '<th>' . $c . '</th>';
        echo '</tr></thead><tbody>';
        foreach ($this->data as $row) {
            echo '<tr>';
            foreach ($this->getRowValues($row) as $val) echo '<td>' . htmlspecialchars($val) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>
        <div class="footer">Generado: ' . date('d/m/Y H:i') . ' | Total: ' . count($this->data) . ' registros</div>
        </body></html>';
        exit;
    }

    private function getColumns(): array
    {
        return match ($this->tipo) {
            'agencia'    => [['Agencia', 'Total Bs', 'Total USD', 'Días'], [80, 55, 50, 35]],
            'taquilla'   => [['Agencia', 'Taquilla', 'Total Bs', 'Total USD'], [70, 70, 55, 50]],
            'sistema'    => [['Sistema', 'Total Bs', 'Total USD'], [80, 60, 55]],
            'cuadre'     => [['Fecha', 'Agencia', 'Punto1', 'Punto2', 'Efectivo Bs', 'Total Bs', 'Total $'], [30, 55, 35, 35, 40, 45, 35]],
            'comparativo'=> [['Agencia', 'Ventas Bs', 'Caja Bs', 'Dif. Bs', 'Ventas $', 'Caja $', 'Dif. $'], [50, 45, 45, 40, 35, 35, 35]],
            default      => [['Dato'], [100]],
        };
    }

    private function getRowValues(array $row): array
    {
        $n = fn($v) => number_format((float)$v, 2, ',', '.');
        return match ($this->tipo) {
            'agencia'    => [$row['agencia'],    $n($row['total_bs']),   $n($row['total_usd']),   $row['dias']],
            'taquilla'   => [$row['agencia'],    $row['taquilla'],        $n($row['total_bs']),   $n($row['total_usd'])],
            'sistema'    => [$row['sistema'],    $n($row['total_bs']),    $n($row['total_usd'])],
            'cuadre'     => [
                date('d/m/Y', strtotime($row['fecha'])),
                $row['agencia_nombre'],
                $n($row['punto_banco1']),
                $n($row['punto_banco2']),
                $n($row['efectivo_bs']),
                $n($row['total_bs']),
                $n($row['total_usd']),
            ],
            'comparativo'=> [
                $row['agencia'],
                $n($row['ventas_bs']),  $n($row['caja_bs']),  $n($row['diferencia_bs']),
                $n($row['ventas_usd']), $n($row['caja_usd']), $n($row['diferencia_usd']),
            ],
            default => [json_encode($row)],
        };
    }

    private function getTotals(): array
    {
        $n = fn($k) => number_format(array_sum(array_column($this->data, $k)), 2, ',', '.');
        return match ($this->tipo) {
            'agencia'    => ['TOTAL', $n('total_bs'), $n('total_usd'), ''],
            'taquilla'   => ['', 'TOTAL', $n('total_bs'), $n('total_usd')],
            'sistema'    => ['TOTAL', $n('total_bs'), $n('total_usd')],
            'cuadre'     => ['', 'TOTAL', '', '', '', $n('total_bs'), $n('total_usd')],
            'comparativo'=> ['TOTAL', $n('ventas_bs'), $n('caja_bs'), $n('diferencia_bs'), $n('ventas_usd'), $n('caja_usd'), $n('diferencia_usd')],
            default => [''],
        };
    }
}
