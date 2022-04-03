<?php

namespace App\Http\Controllers\Almacen\Movimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class GuiaSalidaExcelFormatoOKCController extends Controller
{
    public function insertarSeccionCabecera($sheet)
    {
        $sheet->getDefaultColumnDimension()->setWidth(2, 'pt');
        $sheet->getRowDimension(1)->setRowHeight(65, 'pt');
        $sheet->getRowDimension(3)->setRowHeight(30, 'pt');
        $sheet->getRowDimension(12)->setRowHeight(1.8, 'pt');
        $sheet->getColumnDimension('A')->setWidth(9);

        $sheet->setCellValue('AR1', '');

        $sheet->setCellValue('AF2', 'GR0000-000000');
        $sheet->mergeCells('AF2:AK2');

        $sheet->setCellValue('E4', '24/03/2022');
        $sheet->mergeCells('E4:K4');

        $sheet->setCellValue('G5', '00OK COMPUTER E.I.R.L.00');
        $sheet->getStyle('G5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('G5:X5');
        $sheet->getStyle('G5')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AA5', '00MUNICIPALIDAD PROV000. CAR LOS F. FITZCARRALD 00000 00000000');
        $sheet->getStyle('AA5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AA5:AR6');
        $sheet->getStyle('AA5')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('G8', '20519865476');
        $sheet->getStyle('G8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('G8:L8');


        $sheet->setCellValue('Z7', '0000000000000JR. FITZCARRALD Nº 50400- SAN 0LUIS, 00000000000 000FITZCARRALD');
        $sheet->getStyle('Z7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('Z7:AR8');


        $sheet->setCellValue('G9', '24/03/2022');
        $sheet->getStyle('G9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('G9:K9');

        $sheet->setCellValue('X9', '20519865476');
        $sheet->getStyle('X9')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('X9:AC9');

        
        $sheet->setCellValue('D6', 'CA LAS CASTAÑITAS N° 00127, 00SANISIDRO,0000 LIMA00000000');
        $sheet->getStyle('D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('D6:X7');
        $sheet->getStyle('D6')->getAlignment()->setWrapText(true);

 
        $sheet->setCellValue('D11', 'INGRESAR NOMBRE DE TRANSPORTISTA00000000');
        $sheet->getStyle('D11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('D11:Y11');
        // $sheet->getStyle('D11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AK11', 'LICENCIA');
        $sheet->getStyle('AK11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AK11:AR11');
        // $sheet->getStyle('AJ11')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('E13', 'RUC TRANS');
        $sheet->getStyle('E13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('E13:K13');
        // $sheet->getStyle('E13')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('V13', 'INGRESAR MARCA VEHICU');
        $sheet->getStyle('V13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('V13:AE13');
        // $sheet->getStyle('X13')->getAlignment()->setWrapText(true);

 
        $sheet->setCellValue('AJ13', 'PLACA TRA');
        $sheet->getStyle('AJ13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->mergeCells('AJ13:AR13');
        // $sheet->getStyle('AJ13')->getAlignment()->setWrapText(true);

 

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

    public function insertarSeccionListaProductos($sheet, $newArraySeries)
    {
        $pageMaxHeight = 1008;
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
    }

    public function construirExcel()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.55);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $spreadsheet->getActiveSheet()->setTitle('Guia 1');
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $this->insertarSeccionCabecera($sheet);


        $seriesArray = [
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
        $headerRowsHeight = 0; // calculated height of header and images of the top
        $pageHeight = 0; //Current used page height Header + Footer + $headerRowsHeight
        $reset = $pageHeight; //If you will have the firstpage diffrent change reset value and/or pageheight
        $pageMaxHeight = 1040; //Maximale page height
        $pageClearance = 2; //Clearance of footer


        // $LetraColumnaPartidaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(16);
        // $LetraColumnaLlegadaParaSerie = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(24);
        $ColumnaInicioSerie = 13;
        $filaInicioSerie = 21;
        $combinacionCeldaSerie = 8;
        $keySerietemp = 0;
        foreach ($seriesArray as $key => $serie) {
            $height = 13; // row height
            $pageHeight = $pageHeight + $height;



            $sheet->setCellValueByColumnAndRow($ColumnaInicioSerie, $filaInicioSerie, ($pageMaxHeight - $pageHeight) . $serie);
            // $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie).$filaInicioSerie.':'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnaInicioSerie+$combinacionCeldaSerie).$filaInicioSerie);
            $ColumnaInicioSerie = $ColumnaInicioSerie + $combinacionCeldaSerie;
            if (($key + 1) % 3 == 0) {
                $filaInicioSerie++;
                $ColumnaInicioSerie = 13;
            }

            //Check if the space is still in the range of page 
            $leftOverSpace = $pageMaxHeight - $pageHeight;
            if ($leftOverSpace < $pageClearance) {
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex(1);
                $spreadsheet->getActiveSheet()->setTitle('Guia 2');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
                if (($key + 1) % 3 == 0) {
                    $keySerietemp = $key;
                    break;
                }
            }

        }

        if ($keySerietemp > 0) {

            $newArraySeries = [];
            for ($i = $keySerietemp + 1; $i < count($seriesArray); $i++) {
                $newArraySeries[] = $seriesArray[$i];
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
            $this->insertarSeccionListaProductos($sheet, $newArraySeries);
        }



        $spreadsheet->setActiveSheetIndex(0);

        $fileName = "guia-salida-okc";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
    }
}
