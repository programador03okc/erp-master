<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoSVSController extends Controller
{
    public static function insertarSeccionGuia($spreadsheet,$data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $guia=$data['guia'];
        $sheet->getDefaultColumnDimension()->setWidth(2, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(55, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(45, 'pt');
        $sheet->getRowDimension(12)->setRowHeight(1.8, 'pt');
        $sheet->getColumnDimension('A')->setWidth(9);

        $sheet->setCellValue('AR1', '');

        $sheet->setCellValue('AG2', ($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AG2:AL2');

        $sheet->setCellValue('H4', $guia->fecha_emision);
        $sheet->mergeCells('H4:L4');

        $sheet->setCellValue('D6', $guia->cliente_razon_social);
        $sheet->getStyle('D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('D6:X7');
        $sheet->getStyle('D6')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AA5', $guia->punto_partida);
        $sheet->getStyle('AA5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AA5:AR6');
        $sheet->getStyle('AA5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('G8', $guia->punto_llegada);
        $sheet->getStyle('G8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('G8:X10');
        $sheet->getStyle('G8')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AH9', 'INGRESAR MARCA VEHICU');
        $sheet->mergeCells('AH9:AR9');

        $sheet->setCellValue('AH10', 'PLACA TRA');
        $sheet->mergeCells('AH10:AR10');

        $sheet->setCellValue('C11', $guia->cliente_nro_documento);
        $sheet->mergeCells('C11:H11');

        $sheet->setCellValue('T11', 'NRO DNI');
        $sheet->mergeCells('T11:X11');

        $sheet->setCellValue('AK11', 'LICENCIA');
        $sheet->mergeCells('AK11:AR11');


    }

    public static function insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido, $idSerieInterrumpido)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $detalle = $data['detalle'];
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 15;
            for ($i=$idItemInterrumpido; $i < count($detalle); $i++) { 
                
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $detalle[$i]['codigo']);
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $detalle[$i]['cantidad']);
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*12, $filaInicioItem, $detalle[$i]['abreviatura']);
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, $detalle[$i]['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*16).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*16)+31).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*16).$filaInicioItem)->getAlignment()->setWrapText(true);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'MARCA: '.$detalle[$i]['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'NÚMERO DE PARTE: '.$detalle[$i]['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*16, $filaInicioItem, 'S/N:');
            $filaInicioItem++;

            $filaInicioItem++;
            $ColumnaInicioSerie=$ColumnaInicioItem*16;
            $ii=0;
            for ($j=$idSerieInterrumpido; $j < count($detalle[$i]['series']) ; $j++) { 
                

                $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie+$ii, $filaInicioItem, $detalle[$i]['series'][$j]->serie);
                $ii=$ii+8;
                if (($j + 1) % 3 == 0) {
                    $filaInicioItem++;
                    $ColumnaInicioSerie = $ColumnaInicioSerie;
                    $ii=0;
                }
            
            $filaInicioItem++;
        }
    }
    }

    public static function construirExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-SVS-GR'.$data['guia']->serie.'-'.$data['guia']->numero.'-'.json_decode($data['guia']->codigos_requerimiento)[0].'-'.$data['guia']->cliente_razon_social."-okc";
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        // header('Cache-Control: must-revalidate');
        // $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'.xlsx"');
        $writer->save('php://output');

        $writer->save('php://output');
    }
}
