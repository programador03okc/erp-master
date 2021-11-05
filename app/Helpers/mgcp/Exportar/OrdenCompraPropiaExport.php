<?php

namespace App\Helpers\mgcp\Exportar;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class OrdenCompraPropiaExport
{
    private $libro;
    private $hoja;
    private $estiloTitulo;
    private $estiloCabeceraColumnas;

    function __construct()
    {
        $this->libro = new Spreadsheet();
        $this->libro->getProperties()->setCreator("Módulo de Gestión Comercial")->setDescription('Lista de órdenes de compra propias');
        $this->definitEstilos();
    }

    private function definitEstilos()
    {
        $this->estiloTitulo = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $this->estiloCabeceraColumnas = [
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
    }

    public function generarHojaLista($data)
    {
        $this->generarTituloCabecera();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnasCabecera();
        $this->generarContenidoCabecera($data);
        $this->darFormatoContenidoCabecera($data);
    }

    public function generarHojaDetalles($data)
    {
        $this->generarTituloDetalles();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnasDetalles();
        $this->generarContenidoDetalles($data);
        $this->darFormatoContenidoDetalles($data);
        $this->libro->setActiveSheetIndex(0);
    }

    public function descargarArchivo()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="OrdenesCompraPropias.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->libro);
        $writer = IOFactory::createWriter($this->libro, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function establecerAnchoColumnasDetalles()
    {
        $this->hoja->getColumnDimension('A')->setWidth(23);
        $this->hoja->getColumnDimension('B')->setWidth(15);
        $this->hoja->getColumnDimension('C')->setWidth(19);
        $this->hoja->getColumnDimension('D')->setWidth(20);
        $this->hoja->getColumnDimension('E')->setWidth(15);
        $this->hoja->getColumnDimension('F')->setWidth(16);
        $this->hoja->getColumnDimension('G')->setWidth(15);

        $this->hoja->getColumnDimension('H')->setWidth(14);
        $this->hoja->getColumnDimension('I')->setWidth(30);
        $this->hoja->getColumnDimension('J')->setWidth(17);
        $this->hoja->getColumnDimension('K')->setWidth(13);
        $this->hoja->getColumnDimension('L')->setWidth(20);
        $this->hoja->getColumnDimension('M')->setWidth(16);
        $this->hoja->getColumnDimension('N')->setWidth(25);
        $this->hoja->getColumnDimension('O')->setWidth(25);
        $this->hoja->getColumnDimension('P')->setWidth(15);
        $this->hoja->getColumnDimension('Q')->setWidth(20);
        $this->hoja->getColumnDimension('R')->setWidth(20);
        $this->hoja->getColumnDimension('S')->setWidth(35);
        $this->hoja->getColumnDimension('T')->setWidth(10);
        $this->hoja->getColumnDimension('U')->setWidth(15);
    }

    private function darFormatoContenidoDetalles($detalles)
    {
        $filaInicial = 7;
        $totalFilas = $filaInicial + $detalles->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $arrayIzquierda = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $this->hoja->getStyle("F$filaInicial:F$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("H$filaInicial:H$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
        $this->hoja->getStyle("J$filaInicial:J$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("U$filaInicial:U$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
        $this->hoja->getStyle("B$filaInicial:C$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("F$filaInicial:F$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("H$filaInicial:H$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("J$filaInicial:K$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("M$filaInicial:M$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("R$filaInicial:R$totalFilas")->applyFromArray($arrayIzquierda);
    }

    private function generarContenidoDetalles($detalles)
    {
        $fila = 7;
        foreach ($detalles as $detalle) {
            $this->hoja->setCellValue("A$fila", $detalle->nro_orden);
            $this->hoja->setCellValue("B$fila", $detalle->nombre_empresa);
            switch ($detalle->id_tipo) {
                case 1:
                    $tipo = 'COMPRA ORDINARIA';
                    break;
                case 2:
                    $tipo = 'GRAN COMPRA';
                    break;
                default:
                    $tipo = 'GRAN COMPRA';
                    break;
            }
            $this->hoja->setCellValue("C$fila", $tipo);
            $this->hoja->setCellValue("D$fila", $detalle->nombre_entidad);
            $this->hoja->setCellValue("E$fila", $detalle->nombre_largo_responsable);

            if (!empty($detalle->fecha_publicacion)) {
                $fPub = new Carbon($detalle->fecha_publicacion);
                $this->hoja->setCellValue("F$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fPub->month, $fPub->day, $fPub->year)));
            }
            $this->hoja->setCellValue("G$fila", $detalle->estado_oc);
            if (!empty($detalle->fecha_estado)) {
                $fEs = new Carbon($detalle->fecha_estado);
                $this->hoja->setCellValue("H$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime($fEs->hour, $fEs->minute, $fEs->second, $fEs->month, $fEs->day, $fEs->year)));
            }
            $this->hoja->setCellValue("I$fila", $detalle->estado_entrega);
            if (!empty($detalle->fecha_entrega)) {
                $fEn = new Carbon($detalle->fecha_entrega);
                $this->hoja->setCellValue("J$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fEn->month, $fEn->day, $fEn->year)));
            }
            $this->hoja->setCellValue("K$fila", $detalle->codigo_oportunidad);
            $this->hoja->setCellValue("L$fila", $detalle->estado_aprobacion_cuadro);

            $this->hoja->setCellValue("M$fila", $detalle->codigo_am);
            $this->hoja->setCellValue("N$fila", $detalle->catalogo);
            $this->hoja->setCellValue("O$fila", $detalle->categoria);
            $this->hoja->setCellValue("P$fila", $detalle->marca);
            $this->hoja->setCellValue("Q$fila", $detalle->modelo);
            $this->hoja->setCellValue("R$fila", $detalle->part_no);
            $this->hoja->setCellValue("S$fila", $detalle->descripcion_producto);
            $this->hoja->setCellValue("T$fila", $detalle->cantidad);
            $this->hoja->setCellValue("U$fila", $detalle->precio_unitario);
            $fila++;
        }
    }

    private function generarTituloDetalles()
    {
        $this->libro->createSheet();
        $this->libro->setActiveSheetIndex(1);
        $this->hoja = $this->libro->getActiveSheet();
        $this->generarLogo();
        $this->hoja->setTitle("Detalles");
        $fila = 3;
        $this->hoja->setCellValue("A$fila", "Detalles de órdenes de compra propias (sólo AM)");

        $this->hoja->getStyle("A$fila")->applyFromArray($this->estiloTitulo);
        $this->hoja->mergeCells("A$fila:U$fila");
        //Cabecera de columnas
        $fila = 6;
        $this->hoja->getRowDimension($fila)->setRowHeight(32);
        $this->hoja->setCellValue("A$fila", "Nro. O/C");
        $this->hoja->setCellValue("B$fila", "Empresa");
        $this->hoja->setCellValue("C$fila", "Tipo de orden");
        $this->hoja->setCellValue("D$fila", "Entidad");
        $this->hoja->setCellValue("E$fila", "Responsable");
        $this->hoja->setCellValue("F$fila", "Fecha de publicación");
        $this->hoja->setCellValue("G$fila", "Estado de O/C");
        $this->hoja->setCellValue("H$fila", "Fecha de estado de O/C");
        $this->hoja->setCellValue("I$fila", "Estado de entrega");
        $this->hoja->setCellValue("J$fila", "Fecha de entrega");
        $this->hoja->setCellValue("K$fila", "Cuadro de presupuesto");
        $this->hoja->setCellValue("L$fila", "Estado del cuadro");
        $this->hoja->setCellValue("M$fila", "Acuerdo marco");
        $this->hoja->setCellValue("N$fila", "Catálogo");
        $this->hoja->setCellValue("O$fila", "Categoría");
        $this->hoja->setCellValue("P$fila", "Marca");
        $this->hoja->setCellValue("Q$fila", "Modelo");
        $this->hoja->setCellValue("R$fila", "Nro. parte");
        $this->hoja->setCellValue("S$fila", "Descripción completa del producto");
        $this->hoja->setCellValue("T$fila", "Cantidad");
        $this->hoja->setCellValue("U$fila", "Precio unitario");

        $this->hoja->getStyle("A$fila:U$fila")->applyFromArray($this->estiloCabeceraColumnas)->getAlignment()->setWrapText(true);
    }


    private function generarContenidoCabecera($data)
    {
        $fila = 7;
        foreach ($data as $orden) {
            $this->hoja->setCellValue("A$fila", $orden->nro_orden);
            $this->hoja->setCellValue("B$fila", $orden->nombre_empresa);
            $this->hoja->setCellValue("C$fila", $orden->codigo_am);
            $this->hoja->setCellValue("D$fila", $orden->descripcion_larga_am);
            //$this->hoja->setCellValue("E$fila", $orden->descripcion_catalogo);
            switch ($orden->id_tipo) {
                case 0:
                    $tipo = 'DIRECTA';
                    break;
                case 1:
                    $tipo = 'COMPRA ORDINARIA';
                    break;
                default:
                    $tipo = 'GRAN COMPRA';
                    break;
            }
            $this->hoja->setCellValue("E$fila", $tipo);
            $this->hoja->setCellValue("F$fila", $orden->nombre_entidad);
            if (!empty($orden->fecha_publicacion)) {
                $fPub = new Carbon($orden->fecha_publicacion);
                $this->hoja->setCellValue("G$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fPub->month, $fPub->day, $fPub->year)));
            }
            $this->hoja->setCellValue("H$fila", $orden->estado_oc);
            if (!empty($orden->fecha_estado)) {
                $fEs = new Carbon($orden->fecha_estado);
                $this->hoja->setCellValue("I$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime($fEs->hour, $fEs->minute, $fEs->second, $fEs->month, $fEs->day, $fEs->year)));
            }
            $this->hoja->setCellValue("J$fila", $orden->estado_entrega);
            if (!empty($orden->fecha_entrega)) {
                $fEn = new Carbon($orden->fecha_entrega);
                $this->hoja->setCellValue("K$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fEn->month, $fEn->day, $fEn->year)));
            }
            $this->hoja->setCellValue("L$fila", $orden->lugar_entrega);
            $this->hoja->setCellValue("M$fila", $orden->monto_soles);
            $this->hoja->setCellValue("N$fila", $orden->orden_compra);
            $this->hoja->setCellValue("O$fila", $orden->siaf);
            $this->hoja->setCellValue("P$fila", $orden->factura);
            $this->hoja->setCellValue("Q$fila", $orden->occ);
            $this->hoja->setCellValue("R$fila", $orden->guia);
            if (!empty($orden->fecha_guia)) {
                $fGuia = new Carbon($orden->fecha_guia);
                $this->hoja->setCellValue("S$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fGuia->month, $fGuia->day, $fGuia->year)));
            }
            $this->hoja->setCellValue("T$fila", $orden->etapa);
            $this->hoja->setCellValue("U$fila", $orden->conformidad ? 'Sí' : 'No');
            $this->hoja->setCellValue("V$fila", $orden->cobrado ? 'Sí' : 'No');
            $this->hoja->setCellValue("W$fila", $orden->id_despacho == null ? 'No' : 'Sí');
            $this->hoja->setCellValue("X$fila", $orden->nombre_largo_responsable);

            if ($orden->tiene_comentarios > 0) {
                $comentario = $orden->ultimoComentario();
                $this->hoja->setCellValue("Y$fila", $comentario->comentario);
                $fComent = new Carbon($comentario->fecha);
                $this->hoja->setCellValue("Z$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fComent->month, $fComent->day, $fComent->year)));
            }
            $this->hoja->setCellValue("AA$fila", $orden->codigo_oportunidad);
            $this->hoja->setCellValue("AB$fila", $orden->estado_aprobacion_cuadro);
            if (!empty($orden->fecha_aprobacion)) {
                $fAprobacion = Carbon::createFromFormat('d-m-Y', $orden->fecha_aprobacion);
                $this->hoja->setCellValue("AC$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fAprobacion->month, $fAprobacion->day, $fAprobacion->year)));
            }
            /*if ($orden->oportunidad != null && $orden->oportunidad->cuadroCosto != null) {
                $this->hoja->setCellValue("AD$fila", floatval(str_replace(['%', ','], '', $orden->oportunidad->cuadroCosto->margen_ganancia)) / 100);
            }*/
            if (!empty($orden->fecha_salida)) {
                $fSalida = new Carbon($orden->fecha_salida);
                $this->hoja->setCellValue("AE$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fSalida->month, $fSalida->day, $fSalida->year)));
            }
            if (!empty($orden->fecha_llegada)) {
                $fLlegada = new Carbon($orden->fecha_llegada);
                $this->hoja->setCellValue("AF$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fLlegada->month, $fLlegada->day, $fLlegada->year)));
            }
            $this->hoja->setCellValue("AG$fila", $orden->flete_real);
            $this->hoja->setCellValue("AH$fila", $orden->transportista);
            $this->hoja->setCellValue("AI$fila", $orden->unidad);
            $this->hoja->setCellValue("AJ$fila", $orden->division);
            $this->hoja->setCellValue("AK$fila", $orden->segmento);
            /*$penalidad=$orden->penalidad();
            if ($penalidad!=null)
            {
                $this->hoja->setCellValue("AL$fila", $penalidad->moneda==2 ? $penalidad->monto*$orden->tipo_cambio_oc : $penalidad->monto);
                $this->hoja->setCellValue("AM$fila", $penalidad->observacion);
            }*/
            
            $fila++;
        }
    }

    private function darFormatoContenidoCabecera($data)
    {
        $filaInicial = 7;
        $totalFilas = $filaInicial + $data->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $this->hoja->getStyle("G$filaInicial:G$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("I$filaInicial:I$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
        $this->hoja->getStyle("K$filaInicial:K$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("S$filaInicial:S$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("Z$filaInicial:Z$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("AC$filaInicial:AC$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("AE$filaInicial:AF$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("M$filaInicial:M$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
        $this->hoja->getStyle("AG$filaInicial:AG$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');
        $this->hoja->getStyle("AL$filaInicial:AL$totalFilas")->getNumberFormat()->setFormatCode('"S/"#,##0.00_-');

        $this->hoja->getStyle("B$filaInicial:C$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("E$filaInicial:E$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("G$filaInicial:I$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("K$filaInicial:K$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("N$filaInicial:W$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("Z$filaInicial:AA$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("AC$filaInicial:AC$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("AE$filaInicial:AF$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("AD$filaInicial:AD$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
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

    private function generarTituloCabecera()
    {
        $this->hoja = $this->libro->getActiveSheet();
        $this->hoja->setTitle("Lista");
        $this->generarLogo();
        $fila = 3;
        $this->hoja->setCellValue("A$fila", "Lista de órdenes de compra propias");

        $this->hoja->getStyle("A$fila")->applyFromArray($this->estiloTitulo);
        $this->hoja->mergeCells("A$fila:AD$fila");
        //Cabecera de columnas
        $fila = 6;
        $this->hoja->getRowDimension($fila)->setRowHeight(32);
        $this->hoja->setCellValue("A$fila", "Nro. O/C");
        $this->hoja->setCellValue("B$fila", "Empresa");
        $this->hoja->setCellValue("C$fila", "Acuerdo marco");
        $this->hoja->setCellValue("D$fila", "Descripción acuerdo");
        $this->hoja->setCellValue("E$fila", "Tipo de orden");
        $this->hoja->setCellValue("F$fila", "Entidad");
        $this->hoja->setCellValue("G$fila", "Fecha de publicación");
        $this->hoja->setCellValue("H$fila", "Estado de O/C");
        $this->hoja->setCellValue("I$fila", "Fecha de estado de O/C");
        $this->hoja->setCellValue("J$fila", "Estado de entrega");
        $this->hoja->setCellValue("K$fila", "Fecha de entrega");
        $this->hoja->setCellValue("L$fila", "Lugar de entrega");
        $this->hoja->setCellValue("M$fila", "Monto total en soles");
        $this->hoja->setCellValue("N$fila", "O/C empresa");
        $this->hoja->setCellValue("O$fila", "SIAF");
        $this->hoja->setCellValue("P$fila", "Factura");
        $this->hoja->setCellValue("Q$fila", "OCC");
        $this->hoja->setCellValue("R$fila", "Nro. Guía");
        $this->hoja->setCellValue("S$fila", "Fecha de guía");
        $this->hoja->setCellValue("T$fila", "Etapa de adquisición");
        $this->hoja->setCellValue("U$fila", "Conformidad");
        $this->hoja->setCellValue("V$fila", "Cobrada");
        $this->hoja->setCellValue("W$fila", "Despachada");
        $this->hoja->setCellValue("X$fila", "Responsable");
        $this->hoja->setCellValue("Y$fila", "Último comentario");
        $this->hoja->setCellValue("Z$fila", "Fecha último comentario");
        $this->hoja->setCellValue("AA$fila", "Cuadro de presupuesto");
        $this->hoja->setCellValue("AB$fila", "Estado del cuadro");
        $this->hoja->setCellValue("AC$fila", "Fecha aprob. del cuadro");
        $this->hoja->setCellValue("AD$fila", "Margen del cuadro");
        $this->hoja->setCellValue("AE$fila", "Fecha de salida");
        $this->hoja->setCellValue("AF$fila", "Fecha de llegada");
        $this->hoja->setCellValue("AG$fila", "Flete real");
        $this->hoja->setCellValue("AH$fila", "Transportista");
        $this->hoja->setCellValue("AI$fila", "Unidad");
        $this->hoja->setCellValue("AJ$fila", "División");
        $this->hoja->setCellValue("AK$fila", "Segmento");
        $this->hoja->setCellValue("AL$fila", "Monto penalidad en soles");
        $this->hoja->setCellValue("AM$fila", "Obs. penalidad");
        $this->hoja->getStyle("A$fila:AM$fila")->applyFromArray($this->estiloCabeceraColumnas)->getAlignment()->setWrapText(true);
    }

    private function establecerAnchoColumnasCabecera()
    {
        $this->hoja->getColumnDimension('A')->setWidth(23);
        $this->hoja->getColumnDimension('B')->setWidth(15);
        $this->hoja->getColumnDimension('C')->setWidth(14);
        $this->hoja->getColumnDimension('D')->setWidth(25);
        //$this->hoja->getColumnDimension('E')->setWidth(25);
        $this->hoja->getColumnDimension('E')->setWidth(21);
        $this->hoja->getColumnDimension('F')->setWidth(25);
        $this->hoja->getColumnDimension('G')->setWidth(16);
        $this->hoja->getColumnDimension('H')->setWidth(13);
        $this->hoja->getColumnDimension('I')->setWidth(15);
        $this->hoja->getColumnDimension('J')->setWidth(16);
        $this->hoja->getColumnDimension('K')->setWidth(15);
        $this->hoja->getColumnDimension('L')->setWidth(35);
        $this->hoja->getColumnDimension('M')->setWidth(15);
        $this->hoja->getColumnDimension('N')->setWidth(15);
        $this->hoja->getColumnDimension('O')->setWidth(15);
        $this->hoja->getColumnDimension('P')->setWidth(12);
        $this->hoja->getColumnDimension('Q')->setWidth(12);
        $this->hoja->getColumnDimension('R')->setWidth(12);
        $this->hoja->getColumnDimension('S')->setWidth(12);
        $this->hoja->getColumnDimension('T')->setWidth(12);
        $this->hoja->getColumnDimension('U')->setWidth(13);
        $this->hoja->getColumnDimension('V')->setWidth(13);
        $this->hoja->getColumnDimension('W')->setWidth(13);
        $this->hoja->getColumnDimension('X')->setWidth(22);
        $this->hoja->getColumnDimension('Y')->setWidth(22);
        $this->hoja->getColumnDimension('Z')->setWidth(16);
        $this->hoja->getColumnDimension('AA')->setWidth(16);
        $this->hoja->getColumnDimension('AB')->setWidth(27);
        $this->hoja->getColumnDimension('AC')->setWidth(16);
        $this->hoja->getColumnDimension('AD')->setWidth(16);
        $this->hoja->getColumnDimension('AE')->setWidth(16);
        $this->hoja->getColumnDimension('AF')->setWidth(16);
        $this->hoja->getColumnDimension('AG')->setWidth(14);
        $this->hoja->getColumnDimension('AH')->setWidth(26);
        $this->hoja->getColumnDimension('AI')->setWidth(17);
        $this->hoja->getColumnDimension('AJ')->setWidth(17);
        $this->hoja->getColumnDimension('AK')->setWidth(17);
        $this->hoja->getColumnDimension('AL')->setWidth(15);
        $this->hoja->getColumnDimension('AM')->setWidth(27);
        
    }
}
