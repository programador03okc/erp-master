<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoRBDBController extends Controller
{
    public static function insertarSeccionGuia($spreadsheet,$data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $guia=$data['guia'];
        $sheet->getDefaultColumnDimension()->setWidth(8, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(60, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(26, 'pt');

        $sheet->setCellValue('BM1', '');

        $sheet->setCellValue('AW2', 'GR'.($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AW2:BF2');

        $sheet->setCellValue('F4', $guia->fecha_emision);
        $sheet->mergeCells('F4:L4');

        $sheet->setCellValue('Y4', $guia->fecha_emision);
        $sheet->mergeCells('Y4:AE4');

        $sheet->setCellValue('B7', $guia->punto_partida);
        $sheet->getStyle('B7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('B7:AB8');
        $sheet->getStyle('B7')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('D9','DISTR');
        $sheet->mergeCells('D9:J9');

        $sheet->getStyle('O9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('O9','PROV');
        $sheet->mergeCells('O9:U9');

        $sheet->getStyle('Y9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('Y9','DPETO');
        $sheet->mergeCells('Y9:AG9');


        $sheet->setCellValue('AK7', $guia->punto_llegada);
        $sheet->getStyle('AK7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AK7:BF8');
        $sheet->getStyle('AK7')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('AK9','DISTR');
        $sheet->mergeCells('AK9:AQ9');

        $sheet->getStyle('AW9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('AW9','PROV');
        $sheet->mergeCells('AW9:BB9');

        $sheet->getStyle('BH9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('BH9','DPETO');
        $sheet->mergeCells('BH9:BL9');
        

        $sheet->setCellValue('G11', $guia->cliente_razon_social);
        $sheet->getStyle('G11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('G11:AE12');
        $sheet->getStyle('G11')->getAlignment()->setWrapText(true);


        
        $sheet->setCellValue('D13', $guia->cliente_nro_documento);
        $sheet->mergeCells('D13:P13');
        $sheet->getStyle('D13')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('V13', 'NRO DNI');
        $sheet->mergeCells('V13:AA13');

        $sheet->setCellValue('AU11', 'INGRESAR MARCA VEHICU');
        $sheet->mergeCells('AU11:BF11');

        $sheet->setCellValue('AU12', 'PLACA TRA');
        $sheet->mergeCells('AU12:BF12');




        $sheet->setCellValue('AT13', 'LICENCIA');
        $sheet->mergeCells('AT13:BH13');


    }

    public static function insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido, $idSerieInterrumpido)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $detalle = $data['detalle'];
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 16;
        $filaLimiteParaImprimir = 0;
        $filaLimiteMarcada = false;

            for ($i=$idItemInterrumpido; $i < count($detalle); $i++) { 
                
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $detalle[$i]['codigo']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*1)+5).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $detalle[$i]['cantidad']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*8)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*13, $filaInicioItem, $detalle[$i]['abreviatura']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*13).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*13)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*13).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, $detalle[$i]['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*22).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*22)+37).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*22).$filaInicioItem)->getAlignment()->setWrapText(true);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*22).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, 'MARCA: '.$detalle[$i]['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, 'NÚMERO DE PARTE: '.$detalle[$i]['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*22, $filaInicioItem, 'S/N:');
            $filaInicioItem++;

            $filaInicioItem++;

            $cantidadColumnasPorFilaSerie=3;
            $anchoDeSerie=14;
            $ColumnaInicioSerie=$ColumnaInicioItem*22;
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
                if (($sheet->getHighestRow() * 12) >= ($pageMaxHeight - 400)) {
                    $filaLimiteParaImprimir= $sheet->getHighestRow();
                    $filaLimiteMarcada=true;
                }
            }
            // fin evaluar altura de pagina actual, si series excede la pagina
                
                                
        }
        $filaInicioItem++;
        
    }
    if($filaLimiteParaImprimir>0){
        $sheet->getCell('BH'.$filaLimiteParaImprimir)->setValue($filaLimiteParaImprimir.'Hasta aquí se sugiere imprimir');
    }

    }

    public static function construirExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.35);
        $spreadsheet->getActiveSheet()->getPageMargins()->setright(0.25);

        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetCount = $spreadsheet->getSheetCount(); 
        $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
        GuiaSalidaExcelFormatoRBDBController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoRBDBController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-RBDB-GR'.($data['guia']->serie??'').'-'.($data['guia']->numero??'').'-'.( $data['guia']->codigos_requerimiento !=null? json_decode($data['guia']->codigos_requerimiento)[0]:'').'-'.($data['guia']->cliente_razon_social??'');
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
