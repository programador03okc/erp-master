<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller{
    
    public function traer_personas($id){
        $id = $this->decode5t($id);
        $trabs = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_pensi', 'rrhh_pensi.id_pension', '=', 'rrhh_trab.id_pension')
                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                ->join('rrhh.rrhh_cat_ocupac', 'rrhh_cat_ocupac.id_categoria_ocupacional', '=', 'rrhh_trab.id_categoria_ocupacional')
                ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nro_documento', 'rrhh_perso.nombres', 'rrhh_perso.apellido_paterno',
                        'rrhh_perso.apellido_materno', 'rrhh_perso.fecha_nacimiento', 'rrhh_postu.direccion', 'rrhh_trab.hijos',
                        'rrhh_pensi.descripcion AS pension', 'rrhh_tp_trab.descripcion AS tipo_empleado', 'rrhh_cat_ocupac.descripcion AS cat_ocupac')
                ->where('rrhh_trab.id_trabajador', $id)->first();
        
        $html = '
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <style type="text/css">
                    body{
                        background-color: #fff;
                        font-family: "DejaVu Sans";
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    table{
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    table tr th,
                    table tr td{
                        border: 1px solid #ccc;
                        padding: 5px;
                    }
                    .okc-header{
                        width: 100%;
                    }
                </style>
            </head>
            <body>
                <h1><center>Información del Trabajador</center></h1>
                <br><br>';

        // foreach ($trabs as $row){
        $trab = $trabs->id_trabajador;
        $docu = $trabs->nro_documento;
        $name = $trabs->nombres.' '.$trabs->apellido_paterno.' '.$trabs->apellido_materno;
        $fnac = $trabs->fecha_nacimiento;
        $dire = $trabs->direccion;
        $temp = $trabs->tipo_empleado;
        $fpen = $trabs->pension;
        $asig = $trabs->hijos;
        $cate = $trabs->cat_ocupac;

        if ($asig == '1'){
            $asig_fam = 'Si';
        }else{
            $asig_fam = 'No';
        }

        $ctas = DB::table('rrhh.rrhh_cta_banc')->where('id_trabajador', $trab)->limit(1)->orderBy('id_cuenta_bancaria', 'asc')->first();
        $nro_cuenta = $ctas->nro_cuenta;

        $html .= '
            <h2>Datos del Trabajador</h2>
            <table width="100%">
                <tr>
                    <th width="170">N° Documento</th>
                    <td>'.$docu.'</td>
                </tr>
                <tr>
                    <th>Nombres y Apellidos</th>
                    <td>'.$name.'</td>
                </tr>
                <tr>
                    <th>Fecha Nacimiento</th>
                    <td>'.date('d/m/Y', strtotime($fnac)).'</td>
                </tr>
                <tr>
                    <th>Dirección</th>
                    <td>'.$dire.'</td>
                </tr>
                <tr>
                    <th>Asignación Familiar</th>
                    <td>'.$asig_fam.'</td>
                </tr>
                <tr>
                    <th>Fondo de Pension</th>
                    <td>'.$fpen.'</td>
                </tr>
                <tr>
                    <th>Nro Cuenta</th>
                    <td>'.$nro_cuenta.'</td>
                </tr>
                <tr>
                    <th>Tipo Trabajador</th>
                    <td>'.$temp.'</td>
                </tr>
                <tr>
                    <th>Categoría Ocupacional</th>
                    <td>'.$cate.'</td>
                </tr>
            </table>
            <br><br>
            <h2>Vínculo Laboral</h2>
            <br>
            <h4>Roles</h4>
            <table width="100%">
                <tr>
                    <th style="text-align:left;">Area</th>
                    <th style="text-align:left;">Cargo</th>
                    <th style="text-align:left;">Rol</th>
                    <th style="text-align:left;" width="60">Salario</th>
                </tr>';
                $roles = DB::table('rrhh.rrhh_trab')
                    ->leftjoin('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->leftjoin('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
                    ->leftjoin('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
                    ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
                    ->select('rrhh_rol_concepto.descripcion AS rol', 'rrhh_cargo.descripcion AS cargo', 'adm_area.descripcion AS area', 'rrhh_rol.salario')
                    ->where('rrhh_trab.id_trabajador', $trab)->get();

                foreach ($roles as $key) {
                    $area = $key->area;
                    $carg = $key->cargo;
                    $role = $key->rol;
                    $sldo = $key->salario;

                    $html .= '
                    <tr>
                        <td>'.$area.'</td>
                        <td>'.$carg.'</td>
                        <td>'.$role.'</td>
                        <td style="text-align:right;">'.number_format($sldo, 2).'</td>
                    </tr>
                    ';
                }
            $html .=
            '</table>
            <br>
            <h4>Contratos</h4>
            <table width="100%">
                <tr>
                    <th style="text-align:left;">Motivo</th>
                    <th style="text-align:left;">Tipo Contra</th>
                    <th style="text-align:left;">Fecha Inicio</th>
                    <th style="text-align:left;">Fecha Fin</th>
                </tr>';
                $contra = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                    ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
                    ->select('rrhh_contra.fecha_inicio', 'rrhh_contra.fecha_fin', 'rrhh_contra.motivo', 'rrhh_tp_contra.descripcion AS tipo_contrato')
                    ->where('rrhh_trab.id_trabajador', $trab)->get();

                foreach ($contra as $val) {
                    $moti = $val->motivo;
                    $tpco = $val->tipo_contrato;
                    $fini = $val->fecha_inicio;
                    $ffin = $val->fecha_fin;

                    $html .= '
                    <tr>
                        <td>'.$moti.'</td>
                        <td>'.$tpco.'</td>
                        <td>'.date('d-m-Y', strtotime($fini)).'</td>
                        <td>'.date('d-m-Y', strtotime($ffin)).'</td>
                    </tr>
                    ';
                }
            $html .=
            '</table>';
        // }

        $html .= '
            </body>
        </html>';

        return $html;
    }
    public function traer_permisos($permiso){
        $sql = DB::table('rrhh.rrhh_permi')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_permi.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('rrhh.rrhh_tp_permi', 'rrhh_tp_permi.id_tipo_permiso', '=', 'rrhh_permi.id_tipo_permiso')
                ->select('rrhh_permi.motivo', 'rrhh_permi.fecha_inicio_permiso', 'rrhh_permi.fecha_fin_permiso', 'rrhh_permi.hora_inicio', 'rrhh_permi.hora_fin',
                        'rrhh_perso.*', 'rrhh_tp_permi.descripcion AS tipo_permiso', 'rrhh_permi.id_trabajador_autoriza')
                ->where('rrhh_permi.id_permiso', $permiso)->get();
        
        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                    background-color: #fff;
                        font-family: "DejaVu Sans";
                        font-size: 12px;
                        box-sizing: border-box;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                .tablePDF tr td label{
                    font-weight: bold;
                }
                .divFirma{
                    border: 1px solid white;
                    padding: 0px 15px;
                    width: 100%;
                    height: auto;
                }
                .divFirma .textFirma{
                    display: inline-block;
                    margin-top: 15px;
                    margin-left: 2%;
                    width: 46%;
                    height: 100px;
                    border: 1px solid white;
                    padding: 10px 0px;
                    text-align: center;
                }
                .textFirma .divFirmaSpace{
                    border-top: 1px dashed black;
                    margin-top: 60px;
                    padding: 5px 10px;
                }
            </style>
            </head>
            <body>
                <h1><center>Papeleta de Permiso</center></h1>
                <br><br>';

        foreach ($sql as $row){
            $nom = $row->nombres;
            $app = $row->apellido_paterno;
            $apm = $row->apellido_materno;

            $id_autoriza = $row->id_trabajador_autoriza;

            $dni = $row->nro_documento;
            $datos = $nom.' '.$app.' '.$apm;
            $motivo = $row->motivo;
            $fexa1 = date('d/m/Y', strtotime($row->fecha_inicio_permiso));
            $fexa2 = date('d/m/Y', strtotime($row->fecha_fin_permiso));
            $hini = $row->hora_inicio;
            $hfin = $row->hora_fin;
            $tpp = $row->tipo_permiso;

            $sqlAutoriza = DB::table('rrhh.rrhh_trab')
                        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', 'rrhh_postu.id_postulante')
                        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', 'rrhh_perso.id_persona')
                        ->select('rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno')
                        ->where('rrhh_trab.id_trabajador', $id_autoriza)->first();
            
            $autoriza = $sqlAutoriza->nombres.' '.$sqlAutoriza->apellido_paterno.' '.$sqlAutoriza->apellido_materno;

            if ($fexa1 == $fexa2) {
                $fecha = $fexa1;
            }else{
                $fecha = 'Del '.$fexa1.'  al  '.$fexa2;
            }
            $html .=
                '<table width="100%" class="tablePDF">
                    <tr>
                        <td width="120">
                            <label>N° DNI:</label>
                            <p>'.$dni.'</p>
                        </td>
                        <td colspan="3">
                            <label>Trabajador:</label>
                            <p>'.$datos.'</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Tipo:</label>
                            <p>'.$tpp.'</p>
                        </td>
                        <td>
                            <label>Fecha Permiso</label>
                            <p>'.$fecha.'</p>
                        </td>
                        <td width="80">
                            <label>Hora Inicio:</label>
                            <p>'.$hini.'</p>
                        </td>
                        <td width="80">
                            <label>Hora Fin:</label>
                            <p>'.$hfin.'</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <label>Motivo:</label>
                            <p>'.$motivo.'</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="divFirma">
                                <div class="textFirma"><p class="divFirmaSpace">Firma Trabajador</p></div>
                                <div class="textFirma"><p class="divFirmaSpace">Firma Jefe de RRHH</p></div>
                            </div>
                        </td>
                        <td colspan="2">
                            <label>Autoriza:</label>
                            <p>'.$autoriza.'</p>
                        </td>
                    </tr>
                </table>';
        }

        $html .= '
            </body>
        </html>';

        return $html;
    }

    public function generar_personas_pdf($id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->traer_personas($id));
        $pdf->setPaper('A4');
        return $pdf->stream('Trabajador.pdf');
        // return $pdf->download('reporte.pdf');
        // $dompdf = new Dompdf();
        // $dompdf->loadHTML($this->traer_personas($id));
        // $dompdf->setPaper('A4');
        // $dompdf->render();
        // $dompdf->stream('trabajador.pdf');
    }

    public function generar_permiso_pdf($perm){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->traer_permisos($perm));
        return $pdf->stream();
        return $pdf->download('permiso.pdf');
    }

    public function generar_personas_excel(){
        $sql = DB::table('rrhh.rrhh_perso')->get()->toArray();
        $perso[] = array('DNI', 'NOMBRES', 'APELLIDOS');

        foreach ($sql as $row){
            $perso[] = array(
                'DNI'       => $row->nro_documento,
                'NOMBRES'   => $row->nombres,
                'APELLIDOS' => $row->apellido_paterno.' '.$row->apellido_materno
            );
        }
        // Excel::create('Reporte de Personas', function($excel) use ($perso){
        //     $excel->sheet('Personas', function($sheet) use ($perso){
        //         $sheet->fromArray($perso, null, 'A1', false, false);
        //     });
        // })->download('xlsx');
        // return Excel::download($sql, 'users.xlsx');
        return ($sql)->download('invoices.xlsx');
    }

    public function encode5t($str){
        for($i=0; $i<5;$i++){
          $str=strrev(base64_encode($str));
        }
        return $str;
    }
    
    public function decode5t($str){
        for($i=0; $i<5;$i++){
          $str=base64_decode(strrev($str));
        }
        return $str;
    }
}
