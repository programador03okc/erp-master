<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoJEDRController extends Controller
{
    public static function insertarSeccionGuia($spreadsheet,$data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $guia=$data['guia'];
        $sheet->getDefaultColumnDimension()->setWidth(8, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(48, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(25, 'pt');

        $sheet->setCellValue('BM1', '');

        $sheet->setCellValue('AW2', 'GR'.($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AW2:BF2');

        $sheet->setCellValue('F4', $guia->fecha_emision);
        $sheet->mergeCells('F4:L4');

        $sheet->setCellValue('Y4', $guia->fecha_emision);
        $sheet->mergeCells('Y4:AE4');

        $sheet->setCellValue('B7', $guia->punto_partida);
        $sheet->getStyle('B7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('B7:AB7');
        $sheet->getStyle('B7')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('D8','DISTR');
        $sheet->mergeCells('D8:J8');

        $sheet->getStyle('O8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('O8','PROV');
        $sheet->mergeCells('O8:U8');

        $sheet->getStyle('Y8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('Y8','DPETO');
        $sheet->mergeCells('Y8:AG8');


        $sheet->setCellValue('AK7', $guia->punto_llegada);
        $sheet->getStyle('AK7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AK7:BF7');
        $sheet->getStyle('AK7')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('AK8','DISTR');
        $sheet->mergeCells('AK8:AQ8');

        $sheet->getStyle('AW8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('AW8','PROV');
        $sheet->mergeCells('AW8:BB8');

        $sheet->getStyle('BH8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('BH8','DPETO');
        $sheet->mergeCells('BH8:BL8');
        

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

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $detalle[$i]['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*8)+37).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem)->getAlignment()->setWrapText(true);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*48, $filaInicioItem, $detalle[$i]['cantidad']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*48).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*48)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*48).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*53, $filaInicioItem, $detalle[$i]['abreviatura']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*53).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*53)+4).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*53).$filaInicioItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $filaInicioItem++;

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, 'MARCA: '.$detalle[$i]['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, 'NÚMERO DE PARTE: '.$detalle[$i]['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, 'S/N:');
            $filaInicioItem++;


            $cantidadColumnasPorFilaSerie=3;
            $anchoDeSerie=14;
            $ColumnaInicioSerie=$ColumnaInicioItem*8;
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
        GuiaSalidaExcelFormatoJEDRController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoJEDRController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-JEDR-GR'.($data['guia']->serie??'').'-'.($data['guia']->numero??'').'-'.( $data['guia']->codigos_requerimiento !=null? json_decode($data['guia']->codigos_requerimiento)[0]:'').'-'.($data['guia']->cliente_razon_social??'');
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
