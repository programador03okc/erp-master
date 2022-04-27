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
        $sheet->getDefaultColumnDimension()->setWidth(8, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(67, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(28, 'pt');
        $sheet->getRowDimension(14)->setRowHeight(1, 'pt');
   

        $sheet->setCellValue('BH1', '');

        $sheet->setCellValue('AR2', 'GR'.($guia->serie.'-'.$guia->numero));
        $sheet->mergeCells('AR2:BA2');

        $sheet->setCellValue('I4', $guia->fecha_emision);
        $sheet->mergeCells('I4:P4');

        $sheet->setCellValue('M5', $guia->empresa_razon_social);
        $sheet->getStyle('M5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('M5:Z5');
        $sheet->getStyle('M5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AN5', $guia->cliente_razon_social);
        $sheet->getStyle('AN5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AN5:BH6');
        $sheet->getStyle('AN5')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('K7', $guia->empresa_nro_documento);
        $sheet->getStyle('K7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K7:P7');


        $sheet->setCellValue('AM7', $guia->punto_llegada);
        $sheet->getStyle('AM7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AM7:AU7');
        $sheet->getStyle('AM7')->getAlignment()->setWrapText(true);



        $sheet->setCellValue('K8', $guia->fecha_emision);
        $sheet->getStyle('K8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K8:P8');

        $sheet->setCellValue('AH8', $guia->cliente_nro_documento);
        $sheet->getStyle('AH8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AH8:AP8');


        $sheet->setCellValue('F6', $guia->punto_partida);
        $sheet->getStyle('F6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('F6:Z6');
        $sheet->getStyle('F6')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('I10', 'INGRESAR NOMBRE DE TRANSPORTISTA');
        $sheet->getStyle('I10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I10:AC10');
        // $sheet->getStyle('D11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AZ10', 'LICENCIA');
        $sheet->getStyle('AZ10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AZ10:BH10');
        // $sheet->getStyle('AJ11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('F11', 'RUC TRA');
        $sheet->getStyle('F11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('F11:O11');
        // $sheet->getStyle('E13')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AE11', 'INGRESAR MARCA VEHICULO');
        $sheet->getStyle('AE11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AE11:AQ11');
        // $sheet->getStyle('X13')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('AX11', 'PLACA TRA');
        $sheet->getStyle('AX11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AX11:BH11');
        // $sheet->getStyle('AJ13')->getAlignment()->setWrapText(true);


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
        // $idSerieInterrumpido = 0;
        // $idItemInterrumpido = 0;
        // foreach ($detalle as $key1 => $item) {
            for ($i=$idItemInterrumpido; $i < count($detalle); $i++) { 
                
            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*1, $filaInicioItem, $detalle[$i]['codigo']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*1).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*1)+6).$filaInicioItem);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*8, $filaInicioItem, $detalle[$i]['cantidad']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*8).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*8)+3).$filaInicioItem);

            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*14, $filaInicioItem, $detalle[$i]['abreviatura']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*14).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*14)+4).$filaInicioItem);

            
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, $detalle[$i]['descripcion']);
            $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*20).$filaInicioItem.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(($ColumnaInicioItem*20)+31).$filaInicioItem);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioItem*20).$filaInicioItem)->getAlignment()->setWrapText(true);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, 'CATEGORÍA: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, 'MARCA: '.$detalle[$i]['marca']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, 'MODELO: ');
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, 'NÚMERO DE PARTE: '.$detalle[$i]['part_number']);
            $filaInicioItem++;
            $sheet->setCellValueByColumnAndRow($ColumnaInicioItem*20, $filaInicioItem, 'S/N:');
            $filaInicioItem++;

            $filaInicioItem++;

            $cantidadColumnasPorFilaSerie=3;
            $anchoDeSerie=8;
            $cantidadTotalSeries =count($detalle[$i]['series']);
            if($cantidadTotalSeries>100){
                $ColumnaInicioSerie=$ColumnaInicioItem*8;
                $cantidadColumnasPorFilaSerie=4;
                $anchoDeSerie=10;
            }else{
                $ColumnaInicioSerie=$ColumnaInicioItem*20;
            }
            $ii=0;
            // foreach ($detalle[$i]['series'] as $key2 => $serie) {
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

            // inica evaluar altura de pagina actual, considerando itme y si series excede la pagina
            // if($idSerieInterrumpido>0 ){
            //     $nuevoDetalle=[];
            //     foreach ($data['detalle'] as $keyi => $det) {
                    
            //         if($keyi==$idItemInterrumpido){
            //             $tempDetalle=$det;
            //             $serieRestantesArray=[];
            //             foreach ($det['series'] as $keys => $serie) {
            //                 // $serieRestantesArray[]=$serie;
            //                 if($keys>$idSerieInterrumpido){
            //                     $serieRestantesArray[]=$serie;
            //                 }
            //             }
            //             $tempDetalle['series']=$serieRestantesArray;
            //             $nuevoDetalle[]=$tempDetalle;
            //         }
            //     }
            //     $data=['guia'=>$data['guia'],'detalle'=>$nuevoDetalle];
            //     // dd($data);   
            //     GuiaSalidaExcelFormatoOKCController::crearNuevaHoja($spreadsheet,$data,$idItemInterrumpido, $idSerieInterrumpido);
            //     return false;

            // }
            // fin evaluar altura de pagina actual, considerando itme y si series excede la pagina
            
            $filaInicioItem++;
        }
        
        if($filaLimiteParaImprimir>0){
            $sheet->getCell('BH'.$filaLimiteParaImprimir)->setValue($filaLimiteParaImprimir.'Hasta aquí se sugiere imprimir');
        }
        
    }

    // public static function crearNuevaHoja($spreadsheet,$data, $idItemInterrumpido,$idSerieInterrumpido)
    // {

    //     $spreadsheet->createSheet();
    //     $spreadsheet->setActiveSheetIndex(1);

    //     $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
    //     $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheetCount = $spreadsheet->getSheetCount(); 
    //     $spreadsheet->getActiveSheet()->setTitle('Guia '.$sheetCount);
    //     GuiaSalidaExcelFormatoOKCController::insertarSeccionGuia($spreadsheet, $data);
    //     GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido,$idSerieInterrumpido );
    // }

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
        GuiaSalidaExcelFormatoOKCController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-OKC-GR'.($data['guia']->serie??'').'-'.($data['guia']->numero??'').'-'.($data['guia']->codigos_requerimiento!=null ?json_decode($data['guia']->codigos_requerimiento)[0]:'').'-'.($data['guia']->cliente_razon_social??'');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        // header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');

        // $writer = new Xlsx($spreadsheet);
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment; filename="'. urlencode($fileName).'.xlsx"');
        // $writer->save('php://output');
    }
}
