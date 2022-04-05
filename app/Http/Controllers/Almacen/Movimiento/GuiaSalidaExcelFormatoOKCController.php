<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoOKCController extends Controller
{
    public static function insertarSeccionGuia($spreadsheet,$data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $guia=$data['guia'];
        $sheet->getDefaultColumnDimension()->setWidth(2, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(65, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(25, 'pt');
        $sheet->getRowDimension(10)->setRowHeight(1.8, 'pt');
   

        $sheet->setCellValue('AR1', '');

        $sheet->setCellValue('AJ2', 'GR'.($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AJ2:AQ2');

        $sheet->setCellValue('I4', $guia->fecha_emision);
        $sheet->mergeCells('I4:P4');

        $sheet->setCellValue('K5', $guia->empresa_razon_social);
        $sheet->getStyle('K5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K5:Z5');
        $sheet->getStyle('K5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AD5', $guia->cliente_razon_social);
        $sheet->getStyle('AD5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AD5:AU6');
        $sheet->getStyle('AD5')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('K8', $guia->empresa_nro_documento);
        $sheet->getStyle('K8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K8:P8');


        $sheet->setCellValue('AD7', $guia->punto_llegada);
        $sheet->getStyle('AD7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AD7:AU7');
        $sheet->getStyle('AD7')->getAlignment()->setWrapText(true);



        $sheet->setCellValue('K9', $guia->fecha_emision);
        $sheet->getStyle('K9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K9:P9');

        $sheet->setCellValue('AA8', $guia->cliente_nro_documento);
        $sheet->getStyle('AA8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AA8:AH8');


        $sheet->setCellValue('F6', $guia->punto_partida);
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

    public static function insertarSeccionDetalle($spreadsheet, $data, $idSerieInterrumpido)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $detalle = $data['detalle'];
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 15;
        $idItemInterrumpido = 0;
        // $idSerieInterrumpido = 0;
        foreach ($detalle as $key1 => $item) {
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $item['codigo']);
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $item['cantidad']);
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
            $i=0;
            foreach ($item['series'] as $key2 => $serie) {

                $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie+$i, $filaInicioItem, $serie->serie);
                $i=$i+8;
                if (($key2 + 1) % 3 == 0) {
                    $filaInicioItem++;
                    $ColumnaInicioSerie = $ColumnaInicioSerie;
                    $i=0;
                }

                // inica evaluar altura de pagina actual, si series excede la pagina
                $ActualNumeroFilaRecorrida = $sheet->getHighestRow();
                if (($ActualNumeroFilaRecorrida * 13) >= ($pageMaxHeight - 400)) {
                    if (($key2 + 1) % 3 == 0) {
                        $idSerieInterrumpido = $key2;
                        break;
                    }
                }
                // fin evaluar altura de pagina actual, si series excede la pagina
                
            }

            // inica evaluar altura de pagina actual, considerando itme y si series excede la pagina
            if($idSerieInterrumpido>0){
                GuiaSalidaExcelFormatoOKCController::crearNuevaHoja($spreadsheet,$data, $idSerieInterrumpido);
                break;

            }
            // fin evaluar altura de pagina actual, considerando itme y si series excede la pagina
            
            $filaInicioItem++;
        }
    }

    public static function crearNuevaHoja($spreadsheet,$data, $idSerieInterrumpido=null)
    {

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);

        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionGuia($spreadsheet, $data);
        // GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($spreadsheet, $data, $idSerieInterrumpido );
    }

    public static function construirExcel($data)
    {
        
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($spreadsheet, $data, null);


        // $spreadsheet->setActiveSheetIndex(0);

        $fileName = "guia-salida-okc";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
    }
}
