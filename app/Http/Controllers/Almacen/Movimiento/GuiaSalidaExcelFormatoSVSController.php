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
        $sheet->getDefaultColumnDimension()->setWidth(8, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(58, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(43, 'pt');
        $sheet->getRowDimension(12)->setRowHeight(1.8, 'pt');
        // $sheet->getColumnDimension('A')->setWidth(9);

        $sheet->setCellValue('BH1', '');

        $sheet->setCellValue('AW2', 'GR'.($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AW2:BF2');

        $sheet->setCellValue('N4', $guia->fecha_emision);
        $sheet->mergeCells('N4:T4');

        $sheet->setCellValue('I6', $guia->cliente_razon_social);
        $sheet->getStyle('I6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I6:AB7');
        $sheet->getStyle('I6')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AM5', $guia->punto_partida);
        $sheet->getStyle('AM5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AM5:BF6');
        $sheet->getStyle('AM5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('K8', $guia->punto_llegada);
        $sheet->getStyle('K8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K8:AB10');
        $sheet->getStyle('K8')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AU9', 'INGRESAR MARCA VEHICU');
        $sheet->mergeCells('AU9:BF9');

        $sheet->setCellValue('AU10', 'PLACA TRA');
        $sheet->mergeCells('AU10:BF10');

        $sheet->setCellValue('G11', $guia->cliente_nro_documento);
        $sheet->mergeCells('G11:P11');
        $sheet->getStyle('G11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('AC11', 'NRO DNI');
        $sheet->mergeCells('AC11:AH11');

        $sheet->setCellValue('AZ11', 'LICENCIA');
        $sheet->mergeCells('AZ11:BH11');


    }

    public static function insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido, $idSerieInterrumpido)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $detalle = $data['detalle'];
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 15;
        $filaLimiteParaImprimir = 0;
        $filaLimiteMarcada = false;

            for ($i=$idItemInterrumpido; $i < count($detalle); $i++) { 
                
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $detalle[$i]['codigo']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*1)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*6, $filaInicioItem, $detalle[$i]['cantidad']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*6).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*6)+3).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*6).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*11, $filaInicioItem, $detalle[$i]['abreviatura']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*11).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*11)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*11).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, $detalle[$i]['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*18).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*18)+31).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*18).$filaInicioItem)->getAlignment()->setWrapText(true);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*18).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, 'MARCA: '.$detalle[$i]['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, 'NÚMERO DE PARTE: '.$detalle[$i]['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*18, $filaInicioItem, 'S/N:');
            $filaInicioItem++;

            $filaInicioItem++;

            $cantidadColumnasPorFilaSerie=3;
            $anchoDeSerie=8;
            $ColumnaInicioSerie=$ColumnaInicioItem*18;
            $ii=0;
            for ($j=$idSerieInterrumpido; $j < count($detalle[$i]['series']) ; $j++) { 
                
                $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie+$ii, $filaInicioItem, $detalle[$i]['series'][$j]->serie);
                $ii=$ii+$anchoDeSerie;
                if (($j + 1) % $cantidadColumnasPorFilaSerie == 0) {
                    $filaInicioItem++;
                    $ColumnaInicioSerie = $ColumnaInicioSerie;
                    $ii=0;
                }
            
            // inica evaluar altura de pagina actual, si series excede la pagina
            if($filaLimiteMarcada==false){
                $ActualNumeroFilaRecorrida = $sheet->getHighestRow();
                if (($ActualNumeroFilaRecorrida * 13) >= ($pageMaxHeight - 400)) {
                    $filaLimiteParaImprimir= $ActualNumeroFilaRecorrida;
                    $filaLimiteMarcada=true;
                }
            }
            // fin evaluar altura de pagina actual, si series excede la pagina
                
                                
        }
        $filaInicioItem++;
        
    }
    $sheet->getComment('AR'.$filaLimiteParaImprimir)->getText()->createTextRun('Hasta esta fila se sugiere imprimir');
    }

    public static function construirExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.35);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        GuiaSalidaExcelFormatoSVSController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoSVSController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-SVS-GR'.($data['guia']->serie??'').'-'.($data['guia']->numero??'').'-'.( $data['guia']->codigos_requerimiento !=null? json_decode($data['guia']->codigos_requerimiento)[0]:'').'-'.($data['guia']->cliente_razon_social??'');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: must-revalidate');
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');

        // $writer = new Xlsx($spreadsheet);
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment; filename="'. urlencode($fileName).'.xlsx"');
        // $writer->save('php://output');
    }
}
