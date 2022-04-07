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

    public static function insertarSeccionDetalle($spreadsheet, $data, $idItemInterrumpido, $idSerieInterrumpido)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $detalle = $data['detalle'];
        $pageMaxHeight = 1008;
        $ColumnaInicioItem = 1;
        $filaInicioItem = 15;
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
            $ColumnaInicioSerie=$ColumnaInicioItem*16;
            $ii=0;
            // foreach ($detalle[$i]['series'] as $key2 => $serie) {
            for ($j=$idSerieInterrumpido; $j < count($detalle[$i]['series']) ; $j++) { 
                

                $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie+$ii, $filaInicioItem, $detalle[$i]['series'][$j]->serie);
                $ii=$ii+8;
                if (($j + 1) % 3 == 0) {
                    $filaInicioItem++;
                    $ColumnaInicioSerie = $ColumnaInicioSerie;
                    $ii=0;
                }

                // inica evaluar altura de pagina actual, si series excede la pagina
                // $ActualNumeroFilaRecorrida = $sheet->getHighestRow();
                // if (($ActualNumeroFilaRecorrida * 13) >= ($pageMaxHeight - 400)) {
                //     if (($j + 1) % 3 == 0) {

                //         $idItemInterrumpido = $i;
                //         $idSerieInterrumpido = $j;
                //         break;
                //     }
                // }
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

        $fileName = 'FORMATO-OKC-GR'.$data['guia']->serie.'-'.$data['guia']->numero.'-'.json_decode($data['guia']->codigos_requerimiento)[0].'-'.$data['guia']->cliente_razon_social."-okc";
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        // header('Content-Transfer-Encoding: binary');
        // header('Cache-Control: must-revalidate');
        // // header('Cache-Control: max-age=0');
        // $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // $writer->save('php://output');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'.xlsx"');
        $writer->save('php://output');
    }
}
