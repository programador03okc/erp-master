<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mail;
use Storage;
use File;


use Illuminate\Support\Facades\DB;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Swift_Transport;
use Swift_Message;
use Swift_Mailer;
use Swift_Attachment;
use Swift_IoException;
use Swift_Preferences;

class CorreoController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        return view("correo.form_mail");
    }

	public function getAttachReq($id_cotizacion,$items=null){
		if($items==null){
			$items =(new LogisticaController)->get_cotizacion($id_cotizacion)[0]['items'];
		}
		$adjuntosReq=[];
		$folderRequerimiento = "files/logistica/detalle_requerimiento/";

		foreach($items as $item){
			if(count($item['adjuntos']) >0 ){
				foreach($item['adjuntos'] as $adjunto){
					
					$adjuntosReq[]=['id'=>$adjunto->id_adjunto, 'ruta'=>$folderRequerimiento.$adjunto->archivo, 'active'=>true];
				}
			}
		}
		return $adjuntosReq;

    }
    
    public function getAttachCoti($id_cotizacion){

        $cotiArchivos = DB::table('logistica.cotizacion_archivos')
            ->select(
                'cotizacion_archivos.*'
            )
            ->where([
                ['cotizacion_archivos.id_cotizacion', '=', $id_cotizacion],
                ['cotizacion_archivos.estado', '=', 1]
            ])
            ->orderBy('cotizacion_archivos.id_archivo', 'asc')
            ->get();

         
		$adjuntosCoti=[];
		$folderRequerimiento = "files/logistica/cotizacion/";

		foreach($cotiArchivos as $cotiArchivo){					
					$adjuntosCoti[]=['id'=>$cotiArchivo->id_archivo, 'ruta'=>$folderRequerimiento.$cotiArchivo->archivo, 'active'=>true];
		}
		return $adjuntosCoti;

	}

    public function getAttachFileStatus ($id_cotizacion){
		$cotizacionArray =(new LogisticaController)->get_cotizacion($id_cotizacion);
        $folderCotizacion = "files/logistica/cotizacion/";
		$StatusGenerarCotiInServer=$this->generarCotizacionInServer($id_cotizacion,$cotizacionArray); //genera un archivo xlsx en files/logistica/cotizacion/co{id_cotizacion}.xlsx
		$todoAdjuntos=[];
		$status=0;

		if($StatusGenerarCotiInServer['status']==1){
			$status =1;
			$adjuntoCotizacion = $folderCotizacion. "co".$id_cotizacion.".xlsx";
            $adjuntoRequerimientos = $this->getAttachReq($id_cotizacion,$cotizacionArray[0]['items']);
			$todoAdjuntos=$adjuntoRequerimientos;
            $todoAdjuntos[]=['id'=> intval($id_cotizacion),'ruta'=>$adjuntoCotizacion, 'active'=>true];
            
            $adjuntoCotizacion = $this->getAttachCoti($id_cotizacion);
            if(count($adjuntoCotizacion)>0){
                foreach($adjuntoCotizacion as $ac){
                    $todoAdjuntos[]=$ac;
                }
            }

			return ['data'=>$todoAdjuntos,'status'=>$status];

		}else{
			$status=-1;
			return ['data'=>$todoAdjuntos,'status'=>$status];
		}

    }


    public function guardar_archivos_adjuntos_cotizacion(Request $request)
    {
        $infoFile = json_decode($request->info_adjuntos);
        // return response()->json($infoFile[0]->id);
        $id_cotizacion   = $infoFile[0]->id_cotizacion;
        $name_file = 'undefined';
        // if (is_array($adjuntos)) {}
        foreach ($request->only_adjuntos_coti as $clave => $valor) {
            $file = $request->file('only_adjuntos_coti')[$clave];

            if (isset($file)) {
                $name_file = "co".$id_cotizacion. time() . $file->getClientOriginalName();
                if ($infoFile[0]->active == true) {

                    $alm_det_req_adjuntos = DB::table('logistica.cotizacion_archivos')->insertGetId(
                        [
                            'id_cotizacion'             => $id_cotizacion,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/cotizacion/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }

        return response()->json($alm_det_req_adjuntos);
    }


    // public function discardFileInServer($id, $typeDoc){
    //     $exists='';
    //     switch($typeDoc){
    //         case 'DETALLE_REQUERIMIENTO':
    //             $data = DB::table('almacen.alm_det_req_adjuntos')
    //             ->select(
    //                 'alm_det_req_adjuntos.*'
    //             )
    //             ->where('alm_det_req_adjuntos.id_adjunto',$id)
    //             ->orderBy('alm_det_req_adjuntos.id_adjunto', 'desc')
    //             ->get();

    //             if($data){
    //                 $exists = Storage::disk('archivos')->exists('logistica/detalle_requerimiento/'.$data->first()->archivo);
    //             }
    //             return response()->json(['existsFile'=>$exists]);
    //         break;
    //         case 'COTIZACION':
    //             return "22";
    //         break;
    //     }

    //     return $id;
    // }
    public function is_true($val, $return_null=false){
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null && !$return_null ? false : $boolval );
    }

    public function get_empresa($id_cotizacion){
        $id_empresa='';
        $statusOption=['success','fail'];
        $status='';
        $data = DB::table('logistica.log_cotizacion')
        ->select('log_cotizacion.id_empresa')
        ->where([
                ['log_cotizacion.estado', '=', 1],
                ['log_cotizacion.id_cotizacion','=',$id_cotizacion]
                ])
        ->get();
        
        if($data->count() > 0){
            $id_empresa = $data->first()->id_empresa;
            $status=$statusOption[0];
        }else{
            $status=$statusOption[1];

        }
        return ['id_empresa'=>$id_empresa,'status'=>$status];
    }

    public static function get_smtp_authentication($id_empresa){
        $smtp_server='';
        $port='';
        $emial='';
        $password='';
        $encryption='';

        $statusOption=['success','fail'];
        $status='';
        $data = DB::table('configuracion.smtp_authentication')
        ->select('smtp_authentication.*')
        ->where([
                ['smtp_authentication.estado', '=', 1],
                ['smtp_authentication.id_empresa','=',$id_empresa]
                ])
        ->get();
        
        if($data->count() > 0){
            $smtp_server = $data->first()->smtp_server;
            $port = $data->first()->port;
            $email = $data->first()->email;
            $password = $data->first()->password;
            $encryption = $data->first()->encryption;

            $status=$statusOption[0];
        }else{
            $status=$statusOption[1];
        }

        return [
            'smtp_server'=>$smtp_server,
            'port'=>$port,
            'email'=>$email,
            'password'=>$password,
            'encryption'=>$encryption,
            'status'=>$status
        ];
    }

    public static function enviar_correo($id_empresa, $destinatario, $asunto, $contenido){
        $attachments=[];
        $cantidadAdjuntos=0;
        $smpt_setting=[];
        $smpt_setting = CorreoController::get_smtp_authentication($id_empresa);

        if ($smpt_setting['status'] =='success'){
            $smtpAddress = $smpt_setting['smtp_server'];
            $port = $smpt_setting['port'];
            $encryption = $smpt_setting['encryption'];
            $yourEmail = $smpt_setting['email'];
            $yourPassword = $smpt_setting['password'];
        } else { 
            return 'Error, no existe configuración de correo para la empresa seleccionada';
        }
		
        Swift_Preferences::getInstance()->setCacheType('null');

        $transport = (new \Swift_SmtpTransport($smtpAddress, $port, $encryption))
                ->setUsername($yourEmail)
                ->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message($asunto))
        ->setFrom([$yourEmail])
        ->setTo([$destinatario])
        // ->setBody($contenido)
        ->addPart($contenido,'text/html')
        // ->attach($attachment)
        ;
        
        foreach ($attachments as $attachment) {
            $message->attach(\Swift_Attachment::fromPath($attachment));
        }

        if ($mailer->send($message)){
            return "Mensaje Enviado.";
        }
        return "Algo salió mal :(";
    }

    public function enviar(Request $request)
    {
		$attachments=[];
        $cantidadAdjuntos=0;
        $smpt_setting=[];
        // $pathToFile = "";
        // $containfile = false;
        $id_cotizacion = $request->input("id_cotizacion");
        $remitente = $request->input("remitente");
        $destinatario = $request->input("destinatario");
        $asunto = $request->input("asunto");
        $contenido = $request->input("contenido_mail");
        $adjunto_server = json_decode($request->adjunto_server);


        $empresa =$this->get_empresa($id_cotizacion);
        if($empresa['status'] == 'success'){
           $smpt_setting = CorreoController::get_smtp_authentication($empresa['id_empresa']);
           
        }

        if($smpt_setting['status'] =='success'){
            $smtpAddress = $smpt_setting['smtp_server'];
            $port = $smpt_setting['port'];
            $encryption = $smpt_setting['encryption'];
            $yourEmail = $smpt_setting['email'];
            $yourPassword = $smpt_setting['password'];
        }else{ 
            return 'Error, no existe configuración de correo para la empresa seleccionada';
        }
		
        // // $getAttachFileStatus=$this->getAttachFileStatus($id_cotizacion)['data'];
        foreach($adjunto_server as $attach){
            //deescartar si existe en adjunto_server archivo active = false
            if($this->is_true($attach->active) == true){
                $attachments[]=public_path('files').substr($attach->ruta,5); //todo adjuntos del servidor
            }
        }
        // return response()->json($adjunto_server);


        if ($request->hasFile('file')) {
            // $containfile = true;
            // $file = $request->file('file');
            $file = $request->file('file');
            $nombre = $file->getClientOriginalName();
            $pathToFile = public_path('files') . "/" . $nombre;
            $attachments[]=$pathToFile;
        } 
        // $containfile = true;
        // $attachments[] = public_path('files') ."/logistica/cotizacion/". "co".$id_cotizacion.".xlsx";
        // $attachments[] = public_path('files') ."/logistica/detalle_requerimiento/". "PR02377.pdf";
        $cantidadAdjuntos=count($attachments);
        
		    // Configuration
            // $smtpAddress = 'smtp.privateemail.com';
            // $port = 587;
            // $encryption = 'tls';
            // $yourEmail = 'hello@raulsalinas.me';
            // $yourPassword = 'Sharapova1';

            // $smtpAddress = 'smtp.gmail.com';
            // $port = 587;
            // $encryption = 'tls';
            // $yourEmail = 'raulsalinas.cultural@gmail.com';
            // $yourPassword = 'superduper8919';

            // return $attachments;
        // Prepare transport
        // $allFiles= implode(", ", $attachments);
        // return strval($allFiles);
        Swift_Preferences::getInstance()->setCacheType('null');


        $transport = (new \Swift_SmtpTransport($smtpAddress, $port, $encryption))
                ->setUsername($yourEmail)
                ->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        // foreach($attachments as $attachment){
        //     $attachment = Swift_Attachment::fromPath($attachment);
        // }

                 // Create a message
                $message = (new \Swift_Message($asunto))
                ->setFrom([$yourEmail => $remitente])
                ->setTo([$destinatario])
                ->setBody($contenido)
                // ->attach($attachment)
                ;
                
                foreach ($attachments as $attachment) {
                    $message->attach(\Swift_Attachment::fromPath($attachment));
                }

        if($mailer->send($message)){

                $estado_enviado =(new LogisticaController)->get_estado_doc('Enviado');

                $update = DB::table('logistica.log_cotizacion')->where('id_cotizacion', $id_cotizacion)
                ->update([
                    'estado_envio'          => $estado_enviado
                ]);
                
            return "Mensaje Enviado, se enviaron ".$cantidadAdjuntos." adjunto(s)";
        }
        return "Algo salió mal :(";



            // $data = array('remitente'=>$remitente,'contenido' => $contenido);
            // $r = Mail::send('correo.plantilla_correo', $data, function ($message) use ($asunto, $destinatario,  $containfile, $attachments) {
            //     $message->from('logistica@okcomputer.com.pe', 'Logistica OKC');
            //     $message->to($destinatario)->subject($asunto);
            //     if ($containfile) {
            // 		// $message->attach($pathToFile);
            // 		foreach($attachments as $filePath){
            // 			$message->attach(public_path() . "/" .$filePath);
            // 		}
            //     }
            // });

            // if( count(Mail::failures()) > 0 ) {
            //     // echo "There was one or more failures. They were: <br />";     
            //     // return view("mensaje.msj_rechazado")->with("msj", "hubo un error vuelva a intentarlo");
            //     $msj = "hubo un error vuelva a intentarlo";
            //     // return view("correo.form_mail",compact('msj'));
            //     return response()->json($msj);

            // } else {
            //     //  echo "No errors, all sent successfully!";
            //     // return view("mensaje.msj_correcto")->with("msj", "Correo Enviado correctamente");
            //     $msj ="Correo Enviado correctamente";
            //     $estado_enviado =(new LogisticaController)->get_estado_doc('Enviado');

            //     $coti = DB::table('logistica.log_cotizacion')
            //     ->where([
            //         ['id_cotizacion', $id_cotizacion]
            //     ])
            //     ->update(
            //         [
            //             'estado_envio' => $estado_enviado
            //         ],
            //         'id_cotizacion'
            //     );

            //     // return view("correo.form_mail",compact('msj'));
            //     return response()->json($msj);

            //     // if ($containfile) {
            //     //     Storage::disk('archivos')->delete($nombre);
            //     // }
            // }

    }

    public function enviar_correo_a_usuario($payload){
        $status=0;
        $msg='';
        $ouput=[];

        $smpt_setting = $this->get_smtp_authentication($payload['id_empresa']);
        if($smpt_setting['status'] =='success'){
            $smtpAddress = $smpt_setting['smtp_server'];
            $port = $smpt_setting['port'];
            $encryption = $smpt_setting['encryption'];
            $yourEmail = $smpt_setting['email'];
            $yourPassword = $smpt_setting['password'];
            
            Swift_Preferences::getInstance()->setCacheType('null');
            $transport = (new \Swift_SmtpTransport($smtpAddress, $port, $encryption))
                    ->setUsername($yourEmail)
                    ->setPassword($yourPassword);
            $mailer = new Swift_Mailer($transport);
            $message = (new \Swift_Message($payload['titulo']))
            ->setFrom([$yourEmail => 'ERP'])
            ->setTo($payload['email_destinatario'])
            ->addPart($payload['mensaje'],'text/html');
            if($mailer->send($message)){            
                $msg = "Se envio un correo de notificación";
                $status = 200;
                $ouput=['mensaje'=>$msg,'status'=>$status];
                return $ouput;
            }else{
                $msg= "Algo salió mal al tratar de notificar por email";
                $ouput=['mensaje'=>$msg,'status'=>$status];
                return $ouput;
    
            }
        }else{ 
            $msg= 'Error, no existe configuración de correo para la empresa seleccionada';
        }
      
    }

    public function generarCotizacionInServer($id_cotizacion,$cotizacionArray){
        // return $cotizacionArray;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getStyle("B6:B8")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("H6")->getFont()->setBold(true);
        $sheet = $spreadsheet->getActiveSheet();
        foreach (range('B', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $spreadsheet->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            $spreadsheet->getActiveSheet()
                        ->getStyle($col)
                        ->getNumberFormat()
                        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                        ->setFormatCode('#');

                        
        } 


        $styleArrayTabelTitle = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>true
            ),
         
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );

        $styleArrayTabelBody = array(
            'font' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'format' => array(
                'color' => array('rgb' => '111112'),
                'bold' =>false
            ),
            'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'rotation'   => 0,
                    'wrap'       => true
            ),
            'borders' => array(
                'allBorders' => array(
                      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR
                    'color' => array('rgb' => '808296')
                )
            )
        );
        $sheet->getStyle("B3")->getFont()->setSize(24);
        $sheet->getStyle("B3")->getFont()->setBold(true);
        $sheet->getStyle("B3")->getAlignment()->setHorizontal('center');
        $title= 'COTIZACIÓN '.$cotizacionArray[0]['codigo_cotizacion'];
        $sheet->setCellValue('B3', $title);
        $sheet->mergeCells('B3:N3');
        // $sheet->setCellValueByColumnAndRow(3, 3, 'COTIZACIÓN');
        

        switch ($cotizacionArray[0]['empresa']['id_empresa']){
            case 1:
                $logo_empresa= 'logo_okc.png';
            break;
            case 2:
                $logo_empresa= 'logo_proyectec.png';
            break;
            case 3:
                $logo_empresa= 'logo_smart.png';
            break;
            default:
                $logo_empresa='img-default.jpg';
        }

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath('images/'.$logo_empresa); // put your path and image here
        $drawing->setWidthAndHeight(220,80);
        $drawing->setResizeProportional(true);
        $drawing->setCoordinates('B2');
        // $drawing->setOffsetX(30);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('B10:O10')->applyFromArray($styleArrayTabelTitle);

        $sheet->setCellValueByColumnAndRow(2, 6, 'Nro COTIZACIÓN');
        $sheet->setCellValueByColumnAndRow(3, 6, $cotizacionArray[0]['codigo_cotizacion']);
        $sheet->setCellValueByColumnAndRow(8, 6, 'FECHA');
        $sheet->setCellValueByColumnAndRow(9, 6, $cotizacionArray[0]['fecha_registro']);
        $sheet->setCellValueByColumnAndRow(2, 7, 'CLIENTE');
        $sheet->setCellValueByColumnAndRow(3, 7, $cotizacionArray[0]['empresa']['razon_social']);
        $sheet->setCellValueByColumnAndRow(2, 8, 'PROVEEDOR');
        $sheet->setCellValueByColumnAndRow(3, 8, $cotizacionArray[0]['proveedor']['razon_social']);
        
        $sheet->setCellValueByColumnAndRow(2, 10, 'Req.');
        $sheet->setCellValueByColumnAndRow(3, 10, 'Item');
        $sheet->setCellValueByColumnAndRow(4, 10, 'Descripción');
        $sheet->setCellValueByColumnAndRow(5, 10, 'Und. Medida');
        $sheet->setCellValueByColumnAndRow(6, 10, 'Cantidad Solicitada');
        $sheet->setCellValueByColumnAndRow(7, 10, 'Und. de Medida');
        $sheet->setCellValueByColumnAndRow(8, 10, 'Cantidad');
        $sheet->setCellValueByColumnAndRow(9, 10, 'Precio');
        $sheet->setCellValueByColumnAndRow(10, 10, 'Lugar de Despacho');
        $sheet->setCellValueByColumnAndRow(11, 10, 'Sub-Total');
        $sheet->setCellValueByColumnAndRow(12, 10, 'Incluye IGV');
        $sheet->setCellValueByColumnAndRow(13, 10, 'Plazo Entrega');
        $sheet->setCellValueByColumnAndRow(14, 10, 'Garantía');
        $sheet->setCellValueByColumnAndRow(15, 10, 'Observación');

            $dataRequerimiento = ['requerimiento'=>'req01',
            'descripcion'=>'posit amarillos',
            'unidad'=>'unidad',
            'cantidad'=>'12'];

        $inicioDataReqX=11;
        $inicioDataReqY=2;
                
        foreach ($cotizacionArray as $row) {
            foreach ($row['items'] as $item) {
                $id_cotizacion = $item['id_cotizacion'];
                $id_detalle_requerimiento = $item['id_detalle_requerimiento'];
                $codigo_requerimiento = $item['codigo_requerimiento'];
                $codigo = $item['codigo'];
                $unidad_medida = $item['unidad_medida_descripcion'];
                $cantidad = $item['cantidad'];
                $stock_comprometido = $item['stock_comprometido'];
                $descripcion = $item['descripcion'];
                $cantidad_item= intval($cantidad) - intval($stock_comprometido);

                $sheet->setCellValueByColumnAndRow($inicioDataReqY+0,$inicioDataReqX , $codigo_requerimiento);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+1,$inicioDataReqX , $codigo);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+2,$inicioDataReqX , $descripcion);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+3,$inicioDataReqX , $unidad_medida);
                $sheet->setCellValueByColumnAndRow($inicioDataReqY+4,$inicioDataReqX , $cantidad);
                $spreadsheet->getActiveSheet()->getStyle('B'.$inicioDataReqX.':O'.$inicioDataReqX)->applyFromArray($styleArrayTabelBody);

                $inicioDataReqX+=1;

            }
        }
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+1 , 'Tipo Comprobante');
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+2 , 'Condicion Compra');
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+3 , 'N° Cuenta Banco Principal');
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+4 , 'N° Cuenta Banco Alternativa');
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+5 , 'N° Cuenta Detracción');
        $sheet->setCellValueByColumnAndRow(2,$inicioDataReqX+7 , '* Adjuntar fichas técnicas');
        $spreadsheet->getActiveSheet()->getStyle('B'.intval($inicioDataReqX+1).':C'.intval($inicioDataReqX+1))->applyFromArray($styleArrayTabelBody);
        $spreadsheet->getActiveSheet()->getStyle('B'.intval($inicioDataReqX+2).':C'.intval($inicioDataReqX+2))->applyFromArray($styleArrayTabelBody);
        $spreadsheet->getActiveSheet()->getStyle('B'.intval($inicioDataReqX+3).':C'.intval($inicioDataReqX+3))->applyFromArray($styleArrayTabelBody);
        $spreadsheet->getActiveSheet()->getStyle('B'.intval($inicioDataReqX+4).':C'.intval($inicioDataReqX+4))->applyFromArray($styleArrayTabelBody);
        $spreadsheet->getActiveSheet()->getStyle('B'.intval($inicioDataReqX+5).':C'.intval($inicioDataReqX+5))->applyFromArray($styleArrayTabelBody);


        

		$writer = new Xlsx($spreadsheet);
        try {
            $writer->save('./files/logistica/cotizacion/co'.$id_cotizacion.'.xlsx');
			$message = 'File Created';
			$ouput=['status'=>1,'message'=>$message];
            return $ouput;
        }
        catch (Exception $e) {
			$message = 'Unable to save file. Please close any other applications(s) that are using it: [",  $e->getMessage(), "]\n"';
			$ouput=['status'=>-1,'message'=> $message];

            return $ouput;
        }
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $nombre = $file->getClientOriginalName();
            $r = Storage::disk('archivos')->put($nombre,  \File::get($file));
        } else {

            return "no";
        }

        if ($r) {
            return $nombre;
        } else {
            return "error vuelva a intentarlo";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function loadFilesAttach(){
        $file = storage_path('app/public/files/logistica/cotizacion/solicitud_de_cotizacion.xls');
        
        return $this->view('emails.confirm')
        ->from('me@stackoverflow.com', 'From')->subject('New mail')
        ->with([
            'name' => $this->data['name'],
        ])->attach($file, [
            'as' => 'File name',
            'mime' => 'application/pdf',
        ]);

    }
}
