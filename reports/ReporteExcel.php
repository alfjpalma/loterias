<?php
/**
 * ReporteExcel — Genera reportes Excel usando PhpSpreadsheet
 * Requiere: composer require phpoffice/phpspreadsheet
 * Sin composer: genera CSV como fallback
 */
class ReporteExcel
{
    private array  $data;
    private string $titulo;
    private string $tipo;

    public function __construct(array $data, string $titulo, string $tipo)
    {
        $this->data   = $data;
        $this->titulo = $titulo;
        $this->tipo   = $tipo;
    }

    public function render(): void
    {
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $this->renderXlsx();
        } else {
            $this->renderCsv();
        }
    }

    private function renderXlsx(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($this->titulo, 0, 31));

        // Estilos de encabezado
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0D6EFD']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
        ];

        // Título
        $cols = $this->getColumns();
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cols));

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $this->titulo);
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(20);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));

        // Cabeceras
        $row = 4;
        foreach ($cols as $i => $col) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1) . $row;
            $sheet->setCellValue($cell, $col);
        }
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($headerStyle);

        // Datos
        $row++;
        $numStart = $row;
        foreach ($this->data as $record) {
            $values = $this->getRowValues($record);
            foreach ($values as $i => $val) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1) . $row;
                $sheet->setCellValue($cell, is_numeric(str_replace([',', '.'], '', $val)) ? (float)str_replace(',', '.', $val) : $val);
            }
            $row++;
        }

        // Ajustar ancho de columnas
        foreach (range(1, count($cols)) as $i) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Zebra striping
        for ($r = $numStart; $r < $row; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                    ->setFillType('solid')->getStartColor()->setRGB('F8F9FA');
            }
        }

        $filename = strtolower(str_replace(' ', '_', $this->titulo)) . '_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function renderCsv(): void
    {
        $filename = strtolower(str_replace(' ', '_', $this->titulo)) . '_' . date('Ymd') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // BOM para Excel
        fwrite($out, "\xEF\xBB\xBF");

        // Cabecera
        fputcsv($out, $this->getColumns(), ';');

        foreach ($this->data as $row) {
            fputcsv($out, $this->getRowValues($row), ';');
        }
        fclose($out);
        exit;
    }

    private function getColumns(): array
    {
        return match ($this->tipo) {
            'agencia'    => ['Agencia', 'Total Bs', 'Total USD', 'Días'],
            'taquilla'   => ['Agencia', 'Taquilla', 'Total Bs', 'Total USD'],
            'sistema'    => ['Sistema', 'Total Bs', 'Total USD'],
            'cuadre'     => ['Fecha', 'Agencia', 'Punto Banco 1', 'Punto Banco 2', 'Punto Banco 3', 'Efectivo Bs', 'Efectivo USD', 'Pago Móvil', 'Premios Pagados', 'Compras', 'Otros', 'Total Bs', 'Total USD'],
            'comparativo'=> ['Agencia', 'Ventas Bs', 'Caja Bs', 'Diferencia Bs', 'Ventas USD', 'Caja USD', 'Diferencia USD'],
            default      => ['Dato'],
        };
    }

    private function getRowValues(array $row): array
    {
        $n = fn($v) => number_format((float)$v, 2, '.', '');
        return match ($this->tipo) {
            'agencia'    => [$row['agencia'], $n($row['total_bs']), $n($row['total_usd']), $row['dias']],
            'taquilla'   => [$row['agencia'], $row['taquilla'], $n($row['total_bs']), $n($row['total_usd'])],
            'sistema'    => [$row['sistema'], $n($row['total_bs']), $n($row['total_usd'])],
            'cuadre'     => [
                date('d/m/Y', strtotime($row['fecha'])),
                $row['agencia_nombre'],
                $n($row['punto_banco1']), $n($row['punto_banco2']), $n($row['punto_banco3']),
                $n($row['efectivo_bs']),  $n($row['efectivo_usd']),
                $n($row['pago_movil']),   $n($row['premios_pagados']),
                $n($row['compras']),      $n($row['otros']),
                $n($row['total_bs']),     $n($row['total_usd']),
            ],
            'comparativo'=> [
                $row['agencia'],
                $n($row['ventas_bs']),  $n($row['caja_bs']),  $n($row['diferencia_bs']),
                $n($row['ventas_usd']), $n($row['caja_usd']), $n($row['diferencia_usd']),
            ],
            default => [json_encode($row)],
        };
    }
}
