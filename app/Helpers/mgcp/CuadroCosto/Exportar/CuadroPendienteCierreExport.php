<?php

namespace App\Helpers\mgcp\CuadroCosto\Exportar;

use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class CuadroPendienteCierreExport
{
    private $libro;
    private $hoja;
    private $fila;
    private $montoTotal;

    function __construct()
    {
        $this->libro = new Spreadsheet();
        $this->libro->getProperties()->setCreator("Módulo de Gestión Comercial")->setDescription('Lista de cuadros pendientes de cierre');
        $this->hoja = $this->libro->getActiveSheet();
        $this->hoja->setTitle("Lista");
        $this->montoTotal = 0;
    }

    public function exportar()
    {
        $this->generarLogo();
        $this->generarTitulo();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnas();
        $this->generarCabecera("1. Pendientes de aprobar");
        $this->generarContenidoTabla($this->obtenerCuadrosPendientesAprobacion());
        $this->generarCabecera("2. En etapa inicial");
        $this->generarContenidoTabla($this->obtenerCuadrosEtapaInicial());
        $this->generarCabecera("3. Aprobados - Pendientes de regularización");
        $this->generarContenidoTabla($this->obtenerCuadrosPendientesRegularizar());
        $this->generarCabecera("4. Sin cuadro de presupuesto");
        $this->generarContenidoTabla($this->obtenerOrdenesSinCuadro());
        $this->generarCabecera("5. Mercadería en tránsito o por transformar");
        $this->generarContenidoTabla($this->obtenerCuadrosAprobadosNoDespachados());
        $this->darFormatoContenido();
        $this->generaraMontoTotal();
        $this->descargarArchivo();
    }

    private function generaraConsultaBase()
    {
        return OrdenCompraPropiaView::whereBetween('fecha_entrega', [Carbon::now()->addDays(-15), Carbon::now()->day(31)->month(12)])
            ->where('estado_oc', '!=', 'RECHAZADA')->orderBy('fecha_entrega', 'desc');
    }

    private function generaraMontoTotal()
    {
        $this->fila += 3;
        $this->hoja->setCellValue("D" . $this->fila, "Monto total");
        $this->hoja->setCellValue("E" . $this->fila, $this->montoTotal);
        
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 13
            ]
        ];
        $this->hoja->getStyle("D" . $this->fila . ":E" . $this->fila)->applyFromArray($styleArray);
        $this->hoja->getStyle("D" . $this->fila . ":E" . $this->fila)->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
    }

    private function obtenerCuadrosPendientesAprobacion()
    {
        return $this->generaraConsultaBase()->where('id_estado_aprobacion', 2)->get();
    }

    private function obtenerCuadrosAprobadosNoDespachados()
    {
        return $this->generaraConsultaBase()->where('id_estado_aprobacion', 3)->whereNull('fecha_salida')->get();
    }

    private function obtenerOrdenesSinCuadro()
    {
        return $this->generaraConsultaBase()->whereNull('id_oportunidad')->get();
    }

    private function obtenerCuadrosEtapaInicial()
    {
        return $this->generaraConsultaBase()->where('id_estado_aprobacion', 1)->get();
    }

    private function obtenerCuadrosPendientesRegularizar()
    {
        return $this->generaraConsultaBase()->where('id_estado_aprobacion', 5)->get();
    }

    private function calcularSubtotalYTotalCuadro($ordenes)
    {
        //$this->fila += 1;
        $this->hoja->setCellValue("D" . $this->fila, "Subtotal");
        $subtotal = 0;
        foreach ($ordenes as $orden) {
            $subtotal += $orden->monto_soles;
        }
        $this->hoja->setCellValue("E" . $this->fila, $subtotal);
        $this->montoTotal+=$subtotal;
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        $this->hoja->getStyle("D" . $this->fila . ":E" . $this->fila)->applyFromArray($styleArray);
    }

    private function generarContenidoTabla($ordenes)
    {
        $this->fila += 1;
        foreach ($ordenes as $orden) {
            $this->hoja->setCellValue("A" . $this->fila, $orden->nro_orden);
            $this->hoja->setCellValue("B" . $this->fila, $orden->nombre_empresa);
            $this->hoja->setCellValue("C" . $this->fila, $orden->nombre_entidad);
            $fechaEntrega = new Carbon($orden->fecha_entrega);
            $this->hoja->setCellValue("D" . $this->fila, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fechaEntrega->month, $fechaEntrega->day, $fechaEntrega->year)));
            $this->hoja->setCellValue("E" . $this->fila, $orden->monto_soles);
            $this->hoja->setCellValue("F" . $this->fila, $orden->nombre_largo_responsable);
            $this->hoja->setCellValue("G" . $this->fila, $orden->codigo_oportunidad);
            $this->hoja->setCellValue("H" . $this->fila, $orden->estado_aprobacion_cuadro);
            if (!is_null($orden->fecha_salida)) {
                $fechaSalida = new Carbon($orden->fecha_salida);
                $this->hoja->setCellValue("I" . $this->fila, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fechaSalida->month, $fechaSalida->day, $fechaSalida->year)));
            }
            if ($orden->oportunidad != null && $orden->oportunidad->cuadroCosto != null) {
                $this->hoja->setCellValue("J" . $this->fila, floatval(str_replace(['%', ','], '', $orden->oportunidad->cuadroCosto->margen_ganancia)) / 100);
            }
            $this->hoja->setCellValue("K" . $this->fila, $orden->penalidad_diaria);
            $this->fila += 1;
        }
        $this->calcularSubtotalYTotalCuadro($ordenes);
    }

    private function darFormatoContenido()
    {
        $filaInicial = 7;
        //$totalFilas = $filaInicial + $data->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $this->hoja->getStyle("B" . $filaInicial . ":B" . $this->fila)->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("D" . $filaInicial . ":D" . $this->fila)->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("G" . $filaInicial . ":G" . $this->fila)->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("I" . $filaInicial . ":I" . $this->fila)->applyFromArray($arrayCentrar);

        $this->hoja->getStyle("D" . $filaInicial . ":D" . $this->fila)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("I" . $filaInicial . ":I" . $this->fila)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("J" . $filaInicial . ":J" . $this->fila)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->hoja->getStyle("E" . $filaInicial . ":E" . $this->fila)->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
        $this->hoja->getStyle("K" . $filaInicial . ":K" . $this->fila)->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
    }

    private function descargarArchivo()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CuadrosPendientesCierre.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->libro);
        $writer = IOFactory::createWriter($this->libro, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function generarFechaExportacion()
    {
        $this->fila += 2;
        $this->hoja->setCellValue("A" . $this->fila, "Generado el " . (new Carbon())->format("d/m/Y"));
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

    private function generarTitulo()
    {
        $this->fila = 3;
        $this->hoja->setCellValue("A" . $this->fila, "Lista de cuadros de presupuesto pendientes de cierre");
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $this->hoja->getStyle("A" . $this->fila)->applyFromArray($styleArray);
        $this->hoja->mergeCells("A" . $this->fila . ":O" . $this->fila);
    }

    private function generarCabecera($titulo)
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 14
            ]
        ];

        $this->fila += 2;
        //Titulo del cuadro
        $this->hoja->setCellValue("A" . $this->fila, $titulo);
        $this->hoja->getStyle("A" . $this->fila)->applyFromArray($styleArray);
        $this->hoja->mergeCells("A" . $this->fila . ":K" . $this->fila);
        //Cabecera de columnas
        $this->fila += 1;
        $this->hoja->getRowDimension($this->fila)->setRowHeight(32);
        $this->hoja->setCellValue("A" . $this->fila, "Nro O/C");
        $this->hoja->setCellValue("B" . $this->fila, "Empresa");
        $this->hoja->setCellValue("C" . $this->fila, "Entidad");
        $this->hoja->setCellValue("D" . $this->fila, "Fecha de entrega");
        $this->hoja->setCellValue("E" . $this->fila, "Monto total en soles");
        $this->hoja->setCellValue("F" . $this->fila, "Responsable");
        $this->hoja->setCellValue("G" . $this->fila, "Cuadro de presupuesto");
        $this->hoja->setCellValue("H" . $this->fila, "Estado");
        $this->hoja->setCellValue("I" . $this->fila, "Fecha de salida de O/C");
        $this->hoja->setCellValue("J" . $this->fila, "Margen del cuadro");
        $this->hoja->setCellValue("K" . $this->fila, "Monto de penalización diaria");


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
        $this->hoja->getStyle("A" . $this->fila . ":K" . $this->fila)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
    }

    private function establecerAnchoColumnas()
    {
        $this->hoja->getColumnDimension('A')->setWidth(22);
        $this->hoja->getColumnDimension('B')->setWidth(14);
        $this->hoja->getColumnDimension('C')->setWidth(45);
        $this->hoja->getColumnDimension('D')->setWidth(15);
        $this->hoja->getColumnDimension('E')->setWidth(19);
        $this->hoja->getColumnDimension('F')->setWidth(18);
        $this->hoja->getColumnDimension('G')->setWidth(13);
        $this->hoja->getColumnDimension('H')->setWidth(21);
        $this->hoja->getColumnDimension('I')->setWidth(15);
        $this->hoja->getColumnDimension('J')->setWidth(14);
        $this->hoja->getColumnDimension('K')->setWidth(20);
    }
}
