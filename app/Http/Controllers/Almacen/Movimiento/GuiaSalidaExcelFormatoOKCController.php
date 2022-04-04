<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoOKCController extends Controller
{
    public static function insertarSeccionGuia($sheet,$guia)
    {
        $sheet->getDefaultColumnDimension()->setWidth(2, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(65, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(25, 'pt');
        $sheet->getRowDimension(10)->setRowHeight(1.8, 'pt');
   

        $sheet->setCellValue('AR1', '');

        $sheet->setCellValue('AJ2', 'GR'.($guia['serie'].'-'.$guia['numero']));
        $sheet->mergeCells('AJ2:AQ2');

        $sheet->setCellValue('I4', $guia['fecha_emision']);
        $sheet->mergeCells('I4:P4');

        $sheet->setCellValue('K5', $guia['empresa_razon_social']);
        $sheet->getStyle('K5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K5:Z5');
        $sheet->getStyle('K5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AD5', $guia['cliente_razon_social']);
        $sheet->getStyle('AD5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AD5:AU6');
        $sheet->getStyle('AD5')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('K8', $guia['empresa_nro_documento']);
        $sheet->getStyle('K8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K8:P8');


        $sheet->setCellValue('AD7', $guia['punto_llegada']);
        $sheet->getStyle('AD7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AD7:AU7');
        $sheet->getStyle('AD7')->getAlignment()->setWrapText(true);



        $sheet->setCellValue('K9', $guia['fecha_emision']);
        $sheet->getStyle('K9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K9:P9');

        $sheet->setCellValue('AA8', $guia['cliente_nro_documento']);
        $sheet->getStyle('AA8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AA8:AH8');


        $sheet->setCellValue('F6', $guia['punto_partida']);
        $sheet->getStyle('F6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('F6:Z7');
        $sheet->getStyle('F6')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('I11', 'INGRESAR NOMBRE DE TRANSPORTISTA');
        $sheet->getStyle('I11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I11:AC11');
        // $sheet->getStyle('D11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AO11', 'LICENCIA');
        $sheet->getStyle('AO11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AO11:AU11');
        // $sheet->getStyle('AJ11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('I12', 'RUC TRA');
        $sheet->getStyle('I12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I12:O12');
        // $sheet->getStyle('E13')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('Z12', 'INGRESAR MARCA VEHICULO');
        $sheet->getStyle('Z12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('Z12:AI12');
        // $sheet->getStyle('X13')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('AM12', 'PLACA TRA');
        $sheet->getStyle('AM12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AM12:AU12');
        // $sheet->getStyle('AJ13')->getAlignment()->setWrapText(true);


    }

    public static function insertarSeccionDetalle($sheet, $detalle)
    {
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 15;
        $keySerietemp = 0;
        $aux=0;
        foreach ($detalle as $key1 => $item) {
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $item['codigo']);
            // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*1)+4).$filaInicioItem);
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $item['cantidad']);
            // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*4).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*4)+4).$filaInicioItem);
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*12, $filaInicioItem, $item['abreviatura']);
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, $item['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*16).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*16)+31).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*16).$filaInicioItem)->getAlignment()->setWrapText(true);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'MARCA: '.$item['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'NÚMERO DE PARTE: '.$item['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'S/N:');
            $filaInicioItem++;

            $filaInicioItem++;
            $ColumnaInicioSerie=$ColumnaInicioItem*16;
            $aux=$ColumnaInicioItem*16;
            $i=0;
            foreach ($item['series'] as $key2 => $serie) {

                $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie+$i, $filaInicioItem, $serie['serie']);
                $i=$i+8;
                if (($key2 + 1) % 3 == 0) {
                    $filaInicioItem++;
                    $ColumnaInicioSerie = $ColumnaInicioSerie;
                    $i=0;
                }
                
            }
            
            $filaInicioItem++;

        }
        return false;
        $sheet->setCellValue('A15', '0010966800');
        $sheet->mergeCells('A15:B15');


        $sheet->setCellValue('C15', '00,000,000');
        $sheet->mergeCells('C15:G15');
        $sheet->setCellValue('H15', 'UND0000');
        $sheet->mergeCells('H15:L15');

        $sheet->setCellValue('M15', 'COMPUTADORA PORTATIL');
        $sheet->mergeCells('M15:AR15');

        $sheet->setCellValue('M16', 'MARCA: HP');
        $sheet->mergeCells('M16:AR16');

        $sheet->setCellValue('M17', 'MODELO: 250 G8 ');
        $sheet->mergeCells('M17:AR17');
        $sheet->getStyle('M17')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('M18', 'NUMERO DE PARTE: 2 P 5 M 3 LT# ABM - W');
        $sheet->mergeCells('M18:AR18');

        $sheet->setCellValue('M19', 'COMPUTADORA PORTATIL: PROCESADOR: INTEL CORE I 7 - 1065 G 7 1 . 30 GHz RAM: 8 GB DDR 4 2666 333 MHz  ALMACENAMIENTO: 1 TB HDD 5400 RPMPANTALLA: LCD CON RETROILUMINACION LED 15 . 6 " 1366 X 768 PIXELES LAN: SI  WLAN: SI BLUETOOTH: SI V GA: NO HDMI: SI SIST. OPER: WINDOWS 10 P RO 64 BITS ESPAÑOL BATERIA: LI- ION 3 CELDAS PESO: 1 . 74 kg UNIDAD OPTICA: NO CAMARA WEB: SI SUITE OFIMATICA: NO G. F: 36 MESES ON- SITE');
        $sheet->mergeCells('M19:AR19');
        $sheet->getStyle('M19')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('M20', 'S/N:');

        $ColumnaInicioSerie = 13;
        $filaInicioSerie = 21;
        $combinacionCeldaSerie = 8;
        $keySerietemp = 0;
        foreach ($newArraySeries as $key => $serie) {

            $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie, $filaInicioSerie, $serie);
            // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie).$filaInicioSerie.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie+$combinacionCeldaSerie).$filaInicioSerie);
            $ColumnaInicioSerie = $ColumnaInicioSerie + $combinacionCeldaSerie;
            if (($key + 1) % 3 == 0) {
                $filaInicioSerie++;
                $ColumnaInicioSerie = 13;
            }
            $ActualNumeroFilaRecorrida = $sheet->getHighestRow();

            if (($ActualNumeroFilaRecorrida * 13) >= ($pageMaxHeight - 400)) {
                if (($key + 1) % 3 == 0) {
                    $keySerietemp = $key;
                    break;
                }
            }
        }
        $this->seCompletoImpresionDeSerie($newArraySeries, $keySerietemp);
    }

    public function seCompletoImpresionDeSerie($newArraySeries, $keySerietemp)
    {
        if ($keySerietemp > 0) {
            $this->crearNuevaHoja($newArraySeries, $keySerietemp);
        }
    }

    // public function crearNuevaHoja($newArraySeries, $keySerietemp)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $spreadsheet->createSheet();
    //     $spreadsheet->setActiveSheetIndex(1);
    //     $sheetCount = $spreadsheet->getSheetCount(); 
    //     $spreadsheet->getActiveSheet()->setTitle('Guia '+$sheetCount);
    //     $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
    //     $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $this->insertarSeccionGuia($sheet, $data);
    //     $this->insertarSeccionDetalle($sheet, $newArraySeries);
    // }

    public static function construirExcel($data)
    {
        $data = [
            "guia" => [
                "id_guia_ven" => 51,
                "serie" => "0012",
                "numero" => "0001234",
                "fecha_emision" => "2022-03-31",
                "fecha_almacen" => "2022-03-31",
                "id_almacen" => 2,
                "usuario" => 3,
                "estado" => 1,
                "fecha_registro" => "2022-03-31 14:56:18",
                "id_sede" => 4,
                "punto_partida" => "calle Arica Mz.M Lt.145",
                "punto_llegada" => "Villas del sol Mz.T L.500",
                "transportista" => null,
                "fecha_traslado" => null,
                "tra_serie" => null,
                "tra_numero" => null,
                "placa" => null,
                "id_tp_doc_almacen" => 2,
                "id_operacion" => 1,
                "id_cliente" => 2121,
                "registrado_por" => 3,
                "id_guia_com" => null,
                "id_od" => 1409,
                "id_persona" => null,
                "id_transferencia" => null,
                "id_empresa" => 1,
                "empresa_nro_documento" => "20519865476",
                "empresa_razon_social" => "OK COMPUTER E.I.R.L.",
                "cliente_nro_documento" => "20605944061",
                "cliente_razon_social" => "HV INDUSTRIAL S.A.C - HV INDUSTRIAL"
            ],
            "detalle" => [
                [
                    "id_guia_ven_det" => 79,
                    "id_almacen" => 2,
                    "id_producto" => 10824,
                    "codigo" => "010824",
                    "part_number" => "751859552",
                    "descripcion" => "DELL LATITUDE 5500 I7 8665U 16GB 1TB W10PRO 15.6\"",
                    "marca" => "DELL",
                    "cantidad" => "5",
                    "abreviatura" => "CJA",
                    "series" => [
                        [
                            "id_prod_serie" => 1233,
                            "id_prod" => 10824,
                            "serie" => "SPF2N46KV",
                            "estado" => 1,
                            "fecha_registro" => "2021-09-13 15:37:13",
                            "id_guia_com_det" => 297,
                            "id_almacen" => 2,
                            "id_guia_ven_det" => 79
                        ], [
                            "id_prod_serie" => 1232,
                            "id_prod" => 10824,
                            "serie" => "SPF2N3V9C",
                            "estado" => 1,
                            "fecha_registro" => "2021-09-13 15:37:13",
                            "id_guia_com_det" => 297,
                            "id_almacen" => 2,
                            "id_guia_ven_det" => 79
                        ],
                        [
                            "id_prod_serie" => 1231,
                            "id_prod" => 10824,
                            "serie" => "SPF2N2LS3",
                            "estado" => 1,
                            "fecha_registro" => "2021-09-13 15:37:13",
                            "id_guia_com_det" => 297,
                            "id_almacen" => 2,
                            "id_guia_ven_det" => 79
                        ],
                        [
                            "id_prod_serie" => 1230,
                            "id_prod" => 10824,
                            "serie" => "SPF2N2K3M",
                            "estado" => 1,
                            "fecha_registro" => "2021-09-13 15:37:13",
                            "id_guia_com_det" => 297,
                            "id_almacen" => 2,
                            "id_guia_ven_det" => 79
                        ],
                        [
                            "id_prod_serie" => 1229,
                            "id_prod" => 10824,
                            "serie" => "SPF2N1WEL",
                            "estado" => 1,
                            "fecha_registro" => "2021-09-13 15:37:13",
                            "id_guia_com_det" => 297,
                            "id_almacen" => 2,
                            "id_guia_ven_det" => 79
                        ]
                    ]
                ],
                [
                    "id_guia_ven_det" => 80,
                    "id_almacen" => 2,
                    "id_producto" => 12447,
                    "codigo" => "012447",
                    "part_number" => "12471",
                    "descripcion" => "MOCHILA LENOVO B210 NEGRA",
                    "marca" => "LENOVO",
                    "cantidad" => "1",
                    "abreviatura" => "UND",
                    "series" => []
                ]
            ]
        ];
        
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();
        $productosArray = [
            'CND127H0TZ11', 'CND127H0TZ11', 'CND127H0TZ11',
            'CND127H0TZ12', 'CND127H0TZ12', 'CND127H0TZ12',
            'CND127H0TZ13', 'CND127H0TZ13', 'CND127H0TZ13',
            'CND127H0TZ14', 'CND127H0TZ14', 'CND127H0TZ14',
            'CND127H0TZ15', 'CND127H0TZ15', 'CND127H0TZ15',
            'CND127H0TZ16', 'CND127H0TZ16', 'CND127H0TZ16',
            'CND127H0TZ17', 'CND127H0TZ17', 'CND127H0TZ17',
            'CND127H0TZ18', 'CND127H0TZ18', 'CND127H0TZ18',
            'CND127H0TZ19', 'CND127H0TZ19', 'CND127H0TZ19',
            'CND127H0TZ20', 'CND127H0TZ20', 'CND127H0TZ20',
            'CND127H0TZ21', 'CND127H0TZ21', 'CND127H0TZ21',
            'CND127H0TZ22', 'CND127H0TZ22', 'CND127H0TZ22',
            'CND127H0TZ23', 'CND127H0TZ23', 'CND127H0TZ23',
            'CND127H0TZ24', 'CND127H0TZ24', 'CND127H0TZ24',
            'CND127H0TZ25', 'CND127H0TZ25', 'CND127H0TZ25',
            'CND127H0TZ26', 'CND127H0TZ26', 'CND127H0TZ26',
            'CND127H0TZ27', 'CND127H0TZ27', 'CND127H0TZ27',
            'CND127H0TZ28', 'CND127H0TZ28', 'CND127H0TZ28',
            'CND127H0TZ29', 'CND127H0TZ29', 'CND127H0TZ29',
            'CND127H0TZ30', 'CND127H0TZ30', 'CND127H0TZ30',
            'CND127H0TZ31', 'CND127H0TZ31', 'CND127H0TZ31',
            'CND127H0TZ32', 'CND127H0TZ32', 'CND127H0TZ32',
            'CND127H0TZ33', 'CND127H0TZ33', 'CND127H0TZ33',
            'CND127H0TZ34', 'CND127H0TZ34', 'CND127H0TZ34',
            'CND127H0TZ35', 'CND127H0TZ35', 'CND127H0TZ35',
            'CND127H0TZ36', 'CND127H0TZ36', 'CND127H0TZ36',
            'CND127H0TZ37', 'CND127H0TZ37', 'CND127H0TZ37',
            'CND127H0TZ38', 'CND127H0TZ38', 'CND127H0TZ38',
            'CND127H0TZ39', 'CND127H0TZ39', 'CND127H0TZ39',
            'CND127H0TZ40', 'CND127H0TZ40', 'CND127H0TZ40',
            'CND127H0TZ41', 'CND127H0TZ41', 'CND127H0TZ41',
            'CND127H0TZ42', 'CND127H0TZ42', 'CND127H0TZ42',
            'CND127H0TZ43', 'CND127H0TZ43', 'CND127H0TZ43',
            'CND127H0TZ44', 'CND127H0TZ44', 'CND127H0TZ44',
            'CND127H0TZ45', 'CND127H0TZ45', 'CND127H0TZ45',
            'CND127H0TZ46', 'CND127H0TZ46', 'CND127H0TZ46',
            'CND127H0TZ47', 'CND127H0TZ47', 'CND127H0TZ47',
            'CND127H0TZ48', 'CND127H0TZ48', 'CND127H0TZ48',
            'CND127H0TZ49', 'CND127H0TZ49', 'CND127H0TZ49',
            'CND127H0TZ50', 'CND127H0TZ50', 'CND127H0TZ50'
        ];
        GuiaSalidaExcelFormatoOKCController::insertarSeccionGuia($sheet, $data['guia']);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($sheet, $data['detalle']);


        // $headerRowsHeight = 0; // calculated height of header and images of the top
        // $pageHeight = 0; //Current used page height Header + Footer + $headerRowsHeight
        // $reset = $pageHeight; //If you will have the firstpage diffrent change reset value and/or pageheight
        // $pageMaxHeight = 1040; //Maximale page height
        // $pageClearance = 2; //Clearance of footer


        // // $LetraColumnaPartidaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(16);
        // // $LetraColumnaLlegadaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(24);
        // $ColumnaInicioSerie = 13;
        // $filaInicioSerie = 21;
        // $combinacionCeldaSerie = 8;
        // $keySerietemp = 0;
        // foreach ($productosArray as $key => $serie) {
        //     $height = 13; // row height
        //     $pageHeight = $pageHeight + $height;



        //     $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie, $filaInicioSerie, ($pageMaxHeight - $pageHeight) . $serie);
        //     // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie).$filaInicioSerie.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie+$combinacionCeldaSerie).$filaInicioSerie);
        //     $ColumnaInicioSerie = $ColumnaInicioSerie + $combinacionCeldaSerie;
        //     if (($key + 1) % 3 == 0) {
        //         $filaInicioSerie++;
        //         $ColumnaInicioSerie = 13;
        //     }

        //     //Check if the space is still in the range of page 
        //     $leftOverSpace = $pageMaxHeight - $pageHeight;
        //     if ($leftOverSpace < $pageClearance) {
        //         $spreadsheet->createSheet();
        //         $spreadsheet->setActiveSheetIndex(1);
        //         $spreadsheet->getActiveSheet()->setTitle('Guia 2');
        //         $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        //         $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        //         if (($key + 1) % 3 == 0) {
        //             $keySerietemp = $key;
        //             break;
        //         }
        //     }
        // }

        // if ($keySerietemp > 0) {

        //     $newArraySeries = [];
        //     for ($i = $keySerietemp + 1; $i < count($productosArray); $i++) {
        //         $newArraySeries[] = $productosArray[$i];
        //     }

        //     $spreadsheet->createSheet();
        //     $spreadsheet->setActiveSheetIndex(1);
        //     $spreadsheet->getActiveSheet()->setTitle('Guia 2');
        //     $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        //     $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        //     $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        //     $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //     $sheet = $spreadsheet->getActiveSheet();
        //     $this->insertarSeccionGuia($sheet);
        //     $this->insertarSeccionDetalle($sheet, $newArraySeries);
        // }



        $spreadsheet->setActiveSheetIndex(0);

        $fileName = "guia-salida-okc";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
    }
}
