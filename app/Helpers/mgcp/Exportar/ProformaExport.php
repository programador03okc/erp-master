<?php

namespace App\Helpers\mgcp\Exportar;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Row;
use Carbon\Carbon;

class ProformaExport
{
    private $libro;
    private $hoja;

    function __construct()
    {
        /*$this->libro = new Spreadsheet();
        $this->libro->getProperties()->setCreator("Módulo de Gestión Comercial")->setDescription('Lista de proformas');
        $this->hoja = $this->libro->getActiveSheet();
        $this->hoja->setTitle("Lista");*/
        $this->libro = WriterEntityFactory::createXLSXWriter();
        
    }

    public function exportar($data)
    {
        $this->libro->openToBrowser("Proformas.xlsx");
        $this->generarLogo();
        $this->generarCabecera();
        $this->generarFechaExportacion();
        $this->establecerAnchoColumnas();
        $this->generarContenido($data);
        $this->darFormatoContenido($data);
        $this->crearHeadersParaExportar();
        $this->libro->close();
        /*$writer = new Xlsx($this->libro);
        $writer = IOFactory::createWriter($this->libro, 'Xlsx');
        $writer->save('php://output');
        exit;*/
    }

    private function generarContenido($data)
    {
        //$filas = [];
        $estiloFecha=(new StyleBuilder())->setCellAlignment(CellAlignment::CENTER)->setFormat('dd-mm-YYYY')->build();
        $estiloFechaHora=(new StyleBuilder())->setCellAlignment(CellAlignment::CENTER)->setFormat('dd-mm-YYYY hh:mm AM/PM')->build();
        $estiloNumero=(new StyleBuilder())->setFormat('#,##0.00')->build();
        $estiloCentrar=(new StyleBuilder())
        ->setCellAlignment(CellAlignment::CENTER)->build();
        foreach ($data as $proforma) {
            $fechaEmision = new Carbon($proforma->fecha_emision);
            $fechaLimite = new Carbon($proforma->fecha_limite);
            $fechaCotizacion=$proforma->fecha_cotizacion ==null ? null : new Carbon($proforma->fecha_cotizacion);
            $celdas = [
                WriterEntityFactory::createCell($proforma->requerimiento),
                WriterEntityFactory::createCell($proforma->proforma),
                WriterEntityFactory::createCell($proforma->tipo=='co' ? 'COMPRA ORDINARIA' : 'GRAN COMPRA'),
                WriterEntityFactory::createCell($proforma->entidad),
                WriterEntityFactory::createCell($proforma->departamento),
                WriterEntityFactory::createCell(25569+(gmmktime(0, 0, 0, $fechaEmision->month, $fechaEmision->day, $fechaEmision->year)/86400),$estiloFecha),
                WriterEntityFactory::createCell(25569+(gmmktime(0, 0, 0, $fechaLimite->month, $fechaLimite->day, $fechaLimite->year)/86400),$estiloFecha),
                
                WriterEntityFactory::createCell($proforma->acuerdo_marco),
                WriterEntityFactory::createCell($proforma->catalogo),
                WriterEntityFactory::createCell($proforma->categoria),
                WriterEntityFactory::createCell($proforma->marca),
                WriterEntityFactory::createCell($proforma->modelo),
                WriterEntityFactory::createCell($proforma->part_no),
                WriterEntityFactory::createCell($proforma->descripcion_producto),
                WriterEntityFactory::createCell($proforma->empresa,$estiloCentrar),
                WriterEntityFactory::createCell($proforma->moneda_ofertada,$estiloCentrar),
                WriterEntityFactory::createCell((float)$proforma->precio_unitario_base,$estiloNumero),
                WriterEntityFactory::createCell($proforma->software_educativo,$estiloCentrar),
                WriterEntityFactory::createCell((int)$proforma->cantidad),
                WriterEntityFactory::createCell($proforma->estado,$estiloCentrar),
                WriterEntityFactory::createCell($proforma->plazo_publicar),
                WriterEntityFactory::createCell((float)$proforma->precio_publicar,$estiloNumero),
                WriterEntityFactory::createCell((float)$proforma->costo_envio_publicar,$estiloNumero),
                WriterEntityFactory::createCell($proforma->usuario),
                WriterEntityFactory::createCell($fechaCotizacion==null ? "" : 25569+(gmmktime($fechaCotizacion->hour, $fechaCotizacion->minute, $fechaCotizacion->second, $fechaCotizacion->month, $fechaCotizacion->day, $fechaCotizacion->year)/86400),$estiloFechaHora),
                WriterEntityFactory::createCell($proforma->ultimo_comentario)
            ];
            //array_push($filas, WriterEntityFactory::createRow($celdas));
            $this->libro->addRow(WriterEntityFactory::createRow($celdas));
        }

        /*$celdas = [
            WriterEntityFactory::createCell('Carl'),
            WriterEntityFactory::createCell('Carl')
        ];
        $fila=WriterEntityFactory::createRow($celdas);
        array_push($filas, $fila);*/
        //$this->libro->addRows($filas);
        /** add a row at a time */
        /* $fila = 7;
        foreach ($data as $proforma) {
            
            $fEmi = new Carbon($proforma->fecha_emision);
            $this->hoja->setCellValue("F$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fEmi->month, $fEmi->day, $fEmi->year)));
            //$this->hoja->setCellValue("F$fila", $proforma->fecha_emision);
            $fLim = new Carbon($proforma->fecha_limite);
            $this->hoja->setCellValue("G$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime(0, 0, 0, $fLim->month, $fLim->day, $fLim->year)));
            //$this->hoja->setCellValue("G$fila", $proforma->fecha_limite);
           
            if (!empty($proforma->fecha_cotizacion)) {
                $fCot = new Carbon($proforma->fecha_cotizacion);
                $this->hoja->setCellValue("Y$fila", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(gmmktime($fCot->hour, $fCot->minute, $fCot->second, $fCot->month, $fCot->day, $fCot->year)));
            }
            $this->hoja->setCellValue("Z$fila", $proforma->ultimo_comentario);
            $fila++;
        }*/
    }

    private function darFormatoContenido($data)
    {
        /*$filaInicial = 7;
        $totalFilas = $filaInicial + $data->count();
        $arrayCentrar = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $this->hoja->getStyle("F$filaInicial:H$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("P$filaInicial:P$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("R$filaInicial:R$totalFilas")->applyFromArray($arrayCentrar);
        $this->hoja->getStyle("Y$filaInicial:Y$totalFilas")->applyFromArray($arrayCentrar);

        $this->hoja->getStyle("F$filaInicial:G$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $this->hoja->getStyle("Y$filaInicial:Y$totalFilas")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
        $this->hoja->getStyle("Q$filaInicial:Q$totalFilas")->getNumberFormat()->setFormatCode('#,##0.00_-');
        $this->hoja->getStyle("V$filaInicial:W$totalFilas")->getNumberFormat()->setFormatCode('#,##0.00_-');*/
    }

    private function crearHeadersParaExportar()
    {
        /*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Proformas.xlsx"');
        header('Cache-Control: max-age=0');*/
    }

    private function generarFechaExportacion()
    {
        /*$fila = 5;
        $this->hoja->setCellValue("A$fila", "Generado el " . (new Carbon())->format("d/m/Y"));*/
    }

    private function generarLogo()
    {
        /*$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/mgcp/img/logo.png');
        $drawing->setHeight(65);
        $drawing->setWorksheet($this->hoja);*/
    }

    private function generarCabecera()
    {
        $this->libro->addRow(WriterEntityFactory::createRow([]));
        $filaTitulo=WriterEntityFactory::createRow([ 
            WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),
            WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),WriterEntityFactory::createCell(''),
            WriterEntityFactory::createCell(''),WriterEntityFactory::createCell('Lista de proformas')]);
        $estilosTitulo= (new StyleBuilder())
        ->setFontBold()->setFontSize(16)
        ->setCellAlignment(CellAlignment::CENTER)
        ->build();
        
        $filaTitulo->setStyle($estilosTitulo);
        $this->libro->addRow($filaTitulo);
        //$this->libro->setMergeRanges(['A2:J2']);
        $this->libro->addRow(WriterEntityFactory::createRow([]));

        $celdas = [
            WriterEntityFactory::createCell('Requerimiento'),
            WriterEntityFactory::createCell('Proforma'),
            WriterEntityFactory::createCell('Tipo de proforma'),
            WriterEntityFactory::createCell('Entidad'),
            WriterEntityFactory::createCell('Departamento entrega'),
            WriterEntityFactory::createCell('Fecha de emisión'),
            WriterEntityFactory::createCell('Fecha límite'),
            WriterEntityFactory::createCell('Acuerdo marco'),
            WriterEntityFactory::createCell('Catálogo'),
            WriterEntityFactory::createCell('Categoría'),
            WriterEntityFactory::createCell('Marca'),
            WriterEntityFactory::createCell('Modelo'),
            WriterEntityFactory::createCell('Nro. Parte'),
            WriterEntityFactory::createCell('Descripción completa producto'),
            WriterEntityFactory::createCell('Empresa'),
            WriterEntityFactory::createCell('Moneda'),
            WriterEntityFactory::createCell('Precio base'),
            WriterEntityFactory::createCell('Software educativo'),
            WriterEntityFactory::createCell('Cantidad'),
            WriterEntityFactory::createCell('Estado de la proforma'),
            WriterEntityFactory::createCell('Plazo de entrega'),
            WriterEntityFactory::createCell('Precio a publicar'),
            WriterEntityFactory::createCell('Flete a publicar'),
            WriterEntityFactory::createCell('Cotizado por'),
            WriterEntityFactory::createCell('Fecha de cotización'),
            WriterEntityFactory::createCell('Último comentario'),
        ];
        $filaColumnas=WriterEntityFactory::createRow($celdas);
        $estilosColumnas= (new StyleBuilder())
        ->setFontBold()
        ->setCellAlignment(CellAlignment::CENTER)
        ->build();
        $filaColumnas->setStyle($estilosColumnas);
        $this->libro->addRow($filaColumnas);
        /*
        
       
        $this->hoja->getRowDimension($fila)->setRowHeight(32);
        $this->hoja->setCellValue("A$fila", "Requerimiento");
        $this->hoja->setCellValue("B$fila", "Proforma");
        $this->hoja->setCellValue("C$fila", "Tipo de proforma");
        $this->hoja->setCellValue("D$fila", "Entidad");
        $this->hoja->setCellValue("E$fila", "Departamento entrega");
        $this->hoja->setCellValue("F$fila", "Fecha de emisión");
        $this->hoja->setCellValue("G$fila", "Fecha límite");
        $this->hoja->setCellValue("H$fila", "Acuerdo marco");
        $this->hoja->setCellValue("I$fila", "Catálogo");
        $this->hoja->setCellValue("J$fila", "Categoría");
        $this->hoja->setCellValue("K$fila", "Marca");
        $this->hoja->setCellValue("L$fila", "Modelo");
        $this->hoja->setCellValue("M$fila", "Nro. Parte");
        $this->hoja->setCellValue("N$fila", "Descripción completa producto");
        $this->hoja->setCellValue("O$fila", "Empresa");
        $this->hoja->setCellValue("P$fila", "Moneda");
        $this->hoja->setCellValue("Q$fila", "Precio base");
        $this->hoja->setCellValue("R$fila", "Software educativo");
        $this->hoja->setCellValue("S$fila", "Cantidad");
        $this->hoja->setCellValue("T$fila", "Estado de la proforma");
        $this->hoja->setCellValue("U$fila", "Plazo de entrega");
        $this->hoja->setCellValue("V$fila", "Precio a publicar");
        $this->hoja->setCellValue("W$fila", "Flete a publicar");
        $this->hoja->setCellValue("X$fila", "Cotizado por");
        $this->hoja->setCellValue("Y$fila", "Fecha de cotización");
        $this->hoja->setCellValue("Z$fila", "Último comentario");

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
        $this->hoja->getStyle("A$fila:Z$fila")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);*/
    }

    private function establecerAnchoColumnas()
    {
        /*$this->hoja->getColumnDimension('A')->setWidth(18);
        $this->hoja->getColumnDimension('B')->setWidth(18);
        $this->hoja->getColumnDimension('C')->setWidth(20);
        $this->hoja->getColumnDimension('D')->setWidth(45);
        $this->hoja->getColumnDimension('E')->setWidth(18);
        $this->hoja->getColumnDimension('F')->setWidth(12);
        $this->hoja->getColumnDimension('G')->setWidth(12);
        $this->hoja->getColumnDimension('H')->setWidth(15);
        $this->hoja->getColumnDimension('I')->setWidth(30);
        $this->hoja->getColumnDimension('J')->setWidth(30);
        $this->hoja->getColumnDimension('K')->setWidth(14);
        $this->hoja->getColumnDimension('L')->setWidth(14);
        $this->hoja->getColumnDimension('M')->setWidth(14);
        $this->hoja->getColumnDimension('N')->setWidth(45);
        $this->hoja->getColumnDimension('O')->setWidth(15);
        $this->hoja->getColumnDimension('P')->setWidth(9);
        $this->hoja->getColumnDimension('Q')->setWidth(13);
        $this->hoja->getColumnDimension('R')->setWidth(12);
        $this->hoja->getColumnDimension('S')->setWidth(10);
        $this->hoja->getColumnDimension('T')->setWidth(19);
        $this->hoja->getColumnDimension('U')->setWidth(12);
        $this->hoja->getColumnDimension('V')->setWidth(14);
        $this->hoja->getColumnDimension('W')->setWidth(14);
        $this->hoja->getColumnDimension('X')->setWidth(28);
        $this->hoja->getColumnDimension('Y')->setWidth(20);
        $this->hoja->getColumnDimension('Z')->setWidth(58);*/
    }
}
