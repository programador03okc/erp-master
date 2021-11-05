<?php

namespace App\Helpers\mgcp\CuadroCosto\Exportar;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ListaCuadroCostoExport
{
    private $libro;
    private $hoja;

    function __construct()
    {
        $this->libro = new Spreadsheet();
        $this->libro->getProperties()->setCreator("Módulo de Gestión Comercial")->setDescription('Lista de cuadros de presupuesto');
        $this->hoja = $this->libro->getActiveSheet();
        $this->hoja->setTitle("Lista");
    }

    public function exportar($data)
    {
        $this->generarLogo();
        $this->generarCabecera();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnas();
        $this->generarContenido($data);
        $this->darFormatoContenido($data);
        $this->crearHeadersParaExportar();

        $writer = new Xlsx($this->libro);
        $writer = IOFactory::createWriter($this->libro, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function generarContenido($data)
    {
        $fila = 7;
        foreach ($data as $cuadro) {
            $this->hoja->setCellValue("A$fila", $cuadro->codigo_oportunidad);
            $fechaCreacion = new Carbon($cuadro->fecha_creacion);
            $this->hoja->setCellValue("B$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fechaCreacion->month, $fechaCreacion->day, $fechaCreacion->year)));
            //$this->hoja->setCellValue("B$fila", $cuadro->fecha_creacion);
            $this->hoja->setCellValue("C$fila", $cuadro->descripcion_oportunidad);
            $fechaLimite = new Carbon($cuadro->fecha_limite);
            $this->hoja->setCellValue("D$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fechaLimite->month, $fechaLimite->day, $fechaLimite->year)));
            $this->hoja->setCellValue("E$fila", $cuadro->nombre_entidad);
            $this->hoja->setCellValue("F$fila", $cuadro->name);
            $this->hoja->setCellValue("G$fila", ($cuadro->tipo_cuadro == 'directa' ? 'Venta directa' : 'Acuerdo marco'));
            $this->hoja->setCellValue("H$fila", $cuadro->nro_orden);
            $this->hoja->setCellValue("I$fila", ($cuadro->tiene_transformacion ? 'Sí' : 'No'));
            $montoGanancia=str_replace(['S', '$', '/', ' ', ','], '', $cuadro->monto_ganancia);
            $this->hoja->setCellValue("J$fila", $montoGanancia);
            $formatoMoneda=$cuadro->moneda=='s' ? '"S/"#,##0.00_-' : '"$"#,##0.00_-';
            $this->hoja->getStyle("J$fila")->getNumberFormat()->setFormatCode($formatoMoneda);
            $montoGananciaSoles=$cuadro->moneda=='s' ? $montoGanancia : $montoGanancia*$cuadro->tipo_cambio;
            $this->hoja->setCellValue("K$fila",$montoGananciaSoles);
            $this->hoja->setCellValue("L$fila", floatval(str_replace(['%', ','], '', $cuadro->margen_ganancia))/100);
            $this->hoja->setCellValue("M$fila",$cuadro->flete_total);
            $this->hoja->setCellValue("N$fila", $cuadro->estado_aprobacion);
            $this->hoja->setCellValue("O$fila", $cuadro->responsable_aprobacion);
            $fila++;
        }
    }

    private function darFormatoContenido($data)
    {
        $filaInicial = 7;
        $totalFilas = $filaInicial + $data->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $this->hoja->getStyle("A$filaInicial:B$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("D$filaInicial:D$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("G$filaInicial:I$totalFilas")->applyFromArray($arrayCentrar);

        $this->hoja->getStyle("B$filaInicial:B$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("D$filaInicial:D$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("K$filaInicial:K$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
        $this->hoja->getStyle("L$filaInicial:L$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->hoja->getStyle("M$filaInicial:M$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
    }

    private function crearHeadersParaExportar()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CuadrosDePresupuesto.xlsx"');
        header('Cache-Control: max-age=0');
    }

    private function generarFechaExportacion()
    {
        $fila = 5;
        $this->hoja->setCellValue("A$fila", "Generado el " . (new Carbon())->format("d/m/Y"));
    }

    private function generarLogo()
    {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/mgcp/img/logo.png');
        $drawing->setHeight(65);
        $drawing->setWorksheet($this->hoja);
    }

    private function generarCabecera()
    {
        //Título
        $fila = 3;
        $this->hoja->setCellValue("A$fila", "Lista de cuadros de presupuesto");
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $this->hoja->getStyle("A$fila")->applyFromArray($styleArray);
        $this->hoja->mergeCells("A$fila:O$fila");
        //Cabecera de columnas
        $fila = 6;
        $this->hoja->getRowDimension($fila)->setRowHeight(32);
        $this->hoja->setCellValue("A$fila", "Código");
        $this->hoja->setCellValue("B$fila", "Fecha de creación");
        $this->hoja->setCellValue("C$fila", "Oportunidad");
        $this->hoja->setCellValue("D$fila", "Fecha límite de oportunidad");
        $this->hoja->setCellValue("E$fila", "Cliente");
        $this->hoja->setCellValue("F$fila", "Responsable oportunidad");
        $this->hoja->setCellValue("G$fila", "Tipo de cuadro");
        $this->hoja->setCellValue("H$fila", "O/C vinculada");
        $this->hoja->setCellValue("I$fila", "Tiene transform.");
        $this->hoja->setCellValue("J$fila", "Monto de ganancia");
        $this->hoja->setCellValue("K$fila", "Monto de ganancia en soles");
        $this->hoja->setCellValue("L$fila", "Margen de ganancia");
        $this->hoja->setCellValue("M$fila", "Flete total");
        $this->hoja->setCellValue("N$fila", "Estado de aprobación");
        $this->hoja->setCellValue("O$fila", "Responsable aprobación");

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                ]
            ]
        ];
        $this->hoja->getStyle("A$fila:O$fila")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
    }

    private function establecerAnchoColumnas()
    {
        $this->hoja->getColumnDimension('A')->setWidth(15);
        $this->hoja->getColumnDimension('B')->setWidth(15);
        $this->hoja->getColumnDimension('C')->setWidth(35);
        $this->hoja->getColumnDimension('D')->setWidth(15);
        $this->hoja->getColumnDimension('E')->setWidth(35);
        $this->hoja->getColumnDimension('F')->setWidth(21);
        $this->hoja->getColumnDimension('G')->setWidth(15);
        $this->hoja->getColumnDimension('H')->setWidth(33);
        $this->hoja->getColumnDimension('I')->setWidth(15);
        $this->hoja->getColumnDimension('J')->setWidth(19);
        $this->hoja->getColumnDimension('K')->setWidth(19);
        $this->hoja->getColumnDimension('L')->setWidth(15);
        $this->hoja->getColumnDimension('M')->setWidth(19);
        $this->hoja->getColumnDimension('N')->setWidth(29);
        $this->hoja->getColumnDimension('O')->setWidth(21);
    }
}
