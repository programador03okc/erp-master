<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoPYCController extends Controller
{
    public static function insertarSeccionGuia($spreadsheet,$data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $guia=$data['guia'];
        $sheet->getDefaultColumnDimension()->setWidth(8, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(70, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(25, 'pt');
        $sheet->getRowDimension(9)->setRowHeight(2.5, 'pt');
   

        $sheet->setCellValue('BH1', '');

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


        $sheet->setCellValue('K7', $guia->empresa_nro_documento);
        $sheet->getStyle('K7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K7:P7');


        $sheet->setCellValue('AD7', $guia->punto_llegada);
        $sheet->getStyle('AD7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AD7:AU7');
        $sheet->getStyle('AD7')->getAlignment()->setWrapText(true);



        $sheet->setCellValue('K8', $guia->fecha_emision);
        $sheet->getStyle('K8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('K8:P8');

        $sheet->setCellValue('AA8', $guia->cliente_nro_documento);
        $sheet->getStyle('AA8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AA8:AH8');


        $sheet->setCellValue('F6', $guia->punto_partida);
        $sheet->getStyle('F6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('F6:Z6');
        $sheet->getStyle('F6')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('I10', 'INGRESAR NOMBRE DE TRANSPORTISTA');
        $sheet->getStyle('I10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I10:AC10');
        // $sheet->getStyle('D11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AO10', 'LICENCIA');
        $sheet->getStyle('AO10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AO10:AU10');
        // $sheet->getStyle('AJ11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('I11', 'RUC TRA');
        $sheet->getStyle('I11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('I11:O11');
        // $sheet->getStyle('E13')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('Z11', 'INGRESAR MARCA VEHICULO');
        $sheet->getStyle('Z11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('Z11:AI11');
        // $sheet->getStyle('X13')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('AM11', 'PLACA TRA');
        $sheet->getStyle('AM11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AM11:AU11');
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

            $cantidadColumnasPorFilaSerie=3;
            $anchoDeSerie=8;
            $cantidadTotalSeries =count($detalle[$i]['series']);
            if($cantidadTotalSeries>100){
                $ColumnaInicioSerie=$ColumnaInicioItem*8;
                $cantidadColumnasPorFilaSerie=4;
                $anchoDeSerie=10;
            }else{
                $ColumnaInicioSerie=$ColumnaInicioItem*16;
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
            //     GuiaSalidaExcelFormatoPYCController::crearNuevaHoja($spreadsheet,$data,$idItemInterrumpido, $idSerieInterrumpido);
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
    //     GuiaSalidaExcelFormatoPYCController::insertarSeccionGuia($spreadsheet, $data);
    //     GuiaSalidaExcelFormatoPYCController::insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido,$idSerieInterrumpido );
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
        GuiaSalidaExcelFormatoPYCController::insertarSeccionGuia($spreadsheet, $data);
        GuiaSalidaExcelFormatoPYCController::insertarSeccionDetalle($spreadsheet, $data, 0,0);

        $fileName = 'FORMATO-PYC-GR'.$data['guia']->serie.'-'.$data['guia']->numero.'-'.json_decode($data['guia']->codigos_requerimiento)[0].'-'.$data['guia']->cliente_razon_social."-okc";
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
