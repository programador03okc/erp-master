<?php
namespace App\Http\Controllers\Almacen\Movimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ExportaGuiaSalidaOKCController extends Controller {
public function insertarSeccionCabecera($sheet){
    $sheet->getDefaultColumnDimension()->setWidth(2, 'pt');
    $sheet->getRowDimension(1)->setRowHeight(55, 'pt');
    $sheet->getRowDimension(3)->setRowHeight(45, 'pt');
    $sheet->getRowDimension(12)->setRowHeight(1.8, 'pt');
    $sheet->getColumnDimension('A')->setWidth(9);
    
    $sheet->setCellValue('AR1', '');
    
    $sheet->setCellValue('AG2', 'GR0000-000000');
    $sheet->mergeCells('AG2:AL2');
    
    $sheet->setCellValue('H4', '24/03/2022');
    $sheet->mergeCells('H4:L4');
    
    $sheet->setCellValue('D6', '00MUNICIPALIDAD PROV000. CAR LOS F. FITZCARRALD000000000000000000');
    $sheet->getStyle('D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
    $sheet->mergeCells('D6:X7');
    $sheet->getStyle('D6')->getAlignment()->setWrapText(true);

    $sheet->setCellValue('AA5', 'CA LAS CASTAÑITAS N° 0000000000127, 00SANISIDRO,0000LIMA0000000000000');
    $sheet->getStyle('AA5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
    $sheet->mergeCells('AA5:AR6');
    $sheet->getStyle('AA5')->getAlignment()->setWrapText(true);
    
    $sheet->setCellValue('G8', '0000000000JR. FITZCARRALD Nº 50400- SAN 0LUIS, FITZCARRALD');
    $sheet->getStyle('G8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
    $sheet->mergeCells('G8:X10');
    $sheet->getStyle('G8')->getAlignment()->setWrapText(true);
    
    $sheet->setCellValue('AH9', 'INGRESAR MARCA VEHICU');
    $sheet->mergeCells('AH9:AR9');
    
    $sheet->setCellValue('AH10', 'PLACA TRA');
    $sheet->mergeCells('AH10:AR10');
    
    $sheet->setCellValue('C11', '20519865476');
    $sheet->mergeCells('C11:H11');
    
    $sheet->setCellValue('T11', '74064499');
    $sheet->mergeCells('T11:X11');
    
    $sheet->setCellValue('AK11', 'LICENCIA');
    $sheet->mergeCells('AK11:AR11');
    
    // $sheet->insertNewRowBefore(11, 4);
    
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
}

public function insertarSeccionListaProductos($sheet,$newArraySeries){
    $pageMaxHeight = 1008 ; 
    $ColumnaInicioSerie=13;
    $filaInicioSerie=21;
    $combinacionCeldaSerie=8;
    $keySerietemp=0;
 
    foreach ($newArraySeries as $key => $serie) {
    
        $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie, $filaInicioSerie,$serie);
        // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie).$filaInicioSerie.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie+$combinacionCeldaSerie).$filaInicioSerie);
        $ColumnaInicioSerie=$ColumnaInicioSerie+$combinacionCeldaSerie;
        if(($key+1) % 3==0){
            $filaInicioSerie++;
            $ColumnaInicioSerie=13;
    
        }
        $ActualNumeroFilaRecorrida =$sheet->getHighestRow();
    
        if(($ActualNumeroFilaRecorrida*13 )>=($pageMaxHeight-400)){
            if(($key+1) % 3==0){
                $keySerietemp=$key;
                break;
            }
        } 
    
    }
}

public function insertarSeccionDatosTransportistaPagina($sheet){
    // $maximaAlturaPagina = 1008 ; //Maximale page height DIN A4 arround this
    // $maximaAlturaUsada= $sheet->getHighestRow()*13;
    // $FilasDisponibles = ($maximaAlturaPagina - $maximaAlturaUsada)/13;

    $ActualNumeroFilaRecorrida =$sheet->getHighestRow()+4;
    
    $sheet->setCellValue('G'.$ActualNumeroFilaRecorrida, 'DENOMINACIÓN, APELLIDOS Y NOMBRES');
    $sheet->mergeCells('G'.$ActualNumeroFilaRecorrida.':AF'.$ActualNumeroFilaRecorrida);
    $sheet->setCellValue('AN'.$ActualNumeroFilaRecorrida, 'R.U.C. N˚');
    $sheet->mergeCells('AN'.$ActualNumeroFilaRecorrida.':AR'.$ActualNumeroFilaRecorrida);

    // $sheet->setCellValueByColumnAndRow(7, 51,'DENOMINACIÓN, APELLIDOS Y NOMBRES');
    // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7).'51'.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(26).'51');

 
}

public function excel() {
$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.30);
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);
$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
// ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
$spreadsheet->getActiveSheet()->setTitle('Guia 1');
$spreadsheet->setActiveSheetIndex(0);

$sheet = $spreadsheet->getActiveSheet();

$this->insertarSeccionCabecera($sheet);

$headerItems = array(); //add your header items here as array
$headerRowsHeight = 0;// calculated height of header and images of the top
$rowCounter = 100; //get last written row
//add (other) modifier of page hight here
$pageHeight=25+50 + $headerRowsHeight; //Current used page height Header + Footer + $headerRowsHeight
$reset = $pageHeight; //If you will have the firstpage diffrent change reset value and/or pageheight
$pageMaxHeight = 1008 ; //Maximale page height DIN A4 arround this
$pageClearance = 15; //Clearance of footer

// $sheet->getPageSetup()->setPrintArea('A1:AM40');

// foreach (range('A', 'Z') as $letra) {            
//     $spreadsheet->getActiveSheet()->getColumnDimension($letra)->setWidth(1, 'pt');
// }



$seriesArray =[
    'CND127H0TZ11','CND127H0TZ11','CND127H0TZ11',
    'CND127H0TZ12','CND127H0TZ12','CND127H0TZ12',
    'CND127H0TZ13','CND127H0TZ13','CND127H0TZ13',
    'CND127H0TZ14','CND127H0TZ14','CND127H0TZ14',
    'CND127H0TZ15','CND127H0TZ15','CND127H0TZ15',
    'CND127H0TZ16','CND127H0TZ16','CND127H0TZ16',
    'CND127H0TZ17','CND127H0TZ17','CND127H0TZ17',
    'CND127H0TZ18','CND127H0TZ18','CND127H0TZ18',
    'CND127H0TZ19','CND127H0TZ19','CND127H0TZ19',
    'CND127H0TZ20','CND127H0TZ20','CND127H0TZ20',
    'CND127H0TZ21','CND127H0TZ21','CND127H0TZ21',
    'CND127H0TZ22','CND127H0TZ22','CND127H0TZ22',
    'CND127H0TZ23','CND127H0TZ23','CND127H0TZ23',
    'CND127H0TZ24','CND127H0TZ24','CND127H0TZ24',
    'CND127H0TZ25','CND127H0TZ25','CND127H0TZ25',
    'CND127H0TZ26','CND127H0TZ26','CND127H0TZ26',
    'CND127H0TZ27','CND127H0TZ27','CND127H0TZ27',
    'CND127H0TZ28','CND127H0TZ28','CND127H0TZ28',
    'CND127H0TZ29','CND127H0TZ29','CND127H0TZ29',
    'CND127H0TZ30','CND127H0TZ30','CND127H0TZ30',
    'CND127H0TZ31','CND127H0TZ31','CND127H0TZ31',
    'CND127H0TZ32','CND127H0TZ32','CND127H0TZ32',
    'CND127H0TZ33','CND127H0TZ33','CND127H0TZ33',
    'CND127H0TZ34','CND127H0TZ34','CND127H0TZ34',
    'CND127H0TZ35','CND127H0TZ35','CND127H0TZ35',
    'CND127H0TZ36','CND127H0TZ36','CND127H0TZ36',
    'CND127H0TZ37','CND127H0TZ37','CND127H0TZ37',
    'CND127H0TZ38','CND127H0TZ38','CND127H0TZ38',
    'CND127H0TZ39','CND127H0TZ39','CND127H0TZ39',
    'CND127H0TZ40','CND127H0TZ40','CND127H0TZ40',
    'CND127H0TZ41','CND127H0TZ41','CND127H0TZ41',
    'CND127H0TZ42','CND127H0TZ42','CND127H0TZ42',
    'CND127H0TZ43','CND127H0TZ43','CND127H0TZ43',
    'CND127H0TZ44','CND127H0TZ44','CND127H0TZ44',
    'CND127H0TZ45','CND127H0TZ45','CND127H0TZ45',
    'CND127H0TZ46','CND127H0TZ46','CND127H0TZ46',
    'CND127H0TZ47','CND127H0TZ47','CND127H0TZ47',
    'CND127H0TZ48','CND127H0TZ48','CND127H0TZ48',
    'CND127H0TZ49','CND127H0TZ49','CND127H0TZ49',
    'CND127H0TZ50','CND127H0TZ50','CND127H0TZ50'
];
// $headerRowsHeight = 0;// calculated height of header and images of the top
// $pageHeight=25+50 + $headerRowsHeight; //Current used page height Header + Footer + $headerRowsHeight
 

// $LetraColumnaPartidaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(16);
// $LetraColumnaLlegadaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(24);
$ColumnaInicioSerie=13;
$filaInicioSerie=21;
$combinacionCeldaSerie=8;
$keySerietemp=0;
foreach ($seriesArray as $key => $serie) {
	// $height=13; // row height
 	// $pageHeight = $pageHeight + $height;

	// //Check if the space is still in the range of page 
	// $leftOverSpace = $pageMaxHeight-$pageHeight;

    $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie, $filaInicioSerie,$serie);
    // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie).$filaInicioSerie.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie+$combinacionCeldaSerie).$filaInicioSerie);
    $ColumnaInicioSerie=$ColumnaInicioSerie+$combinacionCeldaSerie;
    if(($key+1) % 3==0){
        $filaInicioSerie++;
        $ColumnaInicioSerie=13;

    }
    $ActualNumeroFilaRecorrida =$spreadsheet->getActiveSheet()->getHighestRow();

    if(($ActualNumeroFilaRecorrida*13 )>=($pageMaxHeight-400)){
        if(($key+1) % 3==0){
            $keySerietemp=$key;
            break;
        }
    } 

}


$this->insertarSeccionDatosTransportistaPagina($sheet);


if($keySerietemp >0){

    $newArraySeries=[];
    for ($i=$keySerietemp+1; $i < count($seriesArray); $i++) { 
        $newArraySeries[]=$seriesArray[$i];
    }

$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$spreadsheet->getActiveSheet()->setTitle('Guia 2');
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);
$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$sheet = $spreadsheet->getActiveSheet();
$this->insertarSeccionCabecera($sheet);
$this->insertarSeccionListaProductos($sheet,$newArraySeries);
$this->insertarSeccionDatosTransportistaPagina($sheet);
}

// $highestColumm =$spreadsheet->getActiveSheet()->getHighestRow();

// $spreadsheet->getActiveSheet()->insertNewRowBefore(70, 1);


    //add height for line to pageheight = get current used space
    // $pageHeight = $pageHeight + $height;

    //Check if the space is still in the range of page 
    // $leftOverSpace = $pageMaxHeight-$pageHeight;


// $sheet->setCellValue('P40', 'OC:');



$spreadsheet->setActiveSheetIndex(0);

$fileName = "guia-salida-svs";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

$writer->save('php://output');
// $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment; filename="file.xlsx"');
// $writer->save("php://output");
}
}

