<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CierreAperturaController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index()
	{
        $empresas = DB::table('administracion.adm_empresa')
        ->select('adm_empresa.id_empresa','adm_contri.razon_social')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','adm_empresa.id_contribuyente')
        ->where('adm_empresa.estado',1)
        ->get();

        $almacenes = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.id_almacen','alm_almacen.descripcion','alm_almacen.codigo')
        ->where('alm_almacen.estado',1)
        ->orderBy('alm_almacen.codigo')
        ->get();

        $anios = DB::table('contabilidad.periodo')
        ->select('periodo.anio')
        ->distinct()->get();

        $acciones = DB::table('contabilidad.periodo_estado')
        ->where('estado',1)
        ->get();

		return view('tesoreria/cierre_apertura/lista', compact('empresas','almacenes','anios','acciones'));
	}

    public function cargarMeses($anio)
    {
        $meses = DB::table('contabilidad.periodo')
        ->select('periodo.mes')
        ->where('periodo.anio',$anio)
        ->distinct()->get();
        return response()->json($meses);
    }

	public function listar()
    {
        $data = DB::table('contabilidad.periodo')
        ->select('periodo.*','alm_almacen.descripcion as almacen','sis_sede.codigo as sede',
        'adm_contri.razon_social as empresa','periodo_estado.nombre as estado_nombre')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','periodo.id_almacen')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('contabilidad.periodo_estado','periodo_estado.id_estado','=','periodo.estado')
        ;

        return DataTables::of($data)
        ->addColumn('accion', function ($data) { 
			return 
            '<div class="btn-group" role="group">'.
            ($data->estado == 2 
            ? '<button type="button" class="btn btn-xs btn-danger abrir" data-id="'.$data->id_periodo.'" data-toggle="tooltip" data-placement="bottom" title="Abrir Periodo"><span class="fas fa-lock-open"></span></button>'
            :'<button type="button" class="btn btn-xs btn-success cerrar" data-id="'.$data->id_periodo.'" data-toggle="tooltip" data-placement="bottom" title="Cerrar Periodo"><span class="fas fa-lock"></span></button>').'
                <button type="button" class="btn btn-xs btn-warning historial" data-id="'.$data->id_periodo.'" data-toggle="tooltip" data-placement="bottom" title="Ver el Historial"><span class="fas fa-list"></span></button>
            </div>';
        })->rawColumns(['accion'])->make(true);
    }

    public function mostrarSedesPorEmpresa($id_empresa)
    {
        $sedes = DB::table('administracion.sis_sede')
        ->select('sis_sede.id_sede','sis_sede.descripcion')
        ->where('sis_sede.id_empresa',$id_empresa)
        ->where('sis_sede.estado',1)
        ->get();

        $almacenes = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.id_almacen','alm_almacen.descripcion','alm_almacen.codigo')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->where('sis_sede.id_empresa',$id_empresa)
        ->where('alm_almacen.estado',1)
        ->orderBy('alm_almacen.codigo')
        ->get();

        return response()->json(['sedes'=>$sedes,'almacenes'=>$almacenes]);
    }

    public function mostrarAlmacenesPorSede($id_sede)
    {
        $almacenes = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.id_almacen','alm_almacen.descripcion','alm_almacen.codigo')
        ->where('alm_almacen.id_sede',$id_sede)
        ->where('alm_almacen.estado',1)
        ->orderBy('alm_almacen.codigo')
        ->get();
        return response()->json($almacenes);
    }

    public function guardarAccion(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $id_historial = DB::table('contabilidad.periodo_historial')->insertGetId(
                [
                    'id_periodo' => $request->ca_id_periodo,
                    'accion' => $request->ca_estado,
                    'id_estado' => $request->ca_id_estado,
                    // 'f_inicio' => $codigo,
                    // 'f_fin' => $request->fecha_almacen,
                    'comentario' => $request->ca_comentario,
                    'id_usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => new Carbon(),
                ],
                'id_historial'
            );

            DB::table('contabilidad.periodo')
            ->where('id_periodo',$request->ca_id_periodo)
            ->update(['estado'=>$request->ca_id_estado]);

            DB::commit();
            return response()->json([
                'tipo' => 'success',
                'mensaje' => 'Se proceso correctamente.', 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la acción. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    
    public function guardarVarios(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;
            $id_almacen = json_decode($request->id_almacen);

            $periodos = DB::table('contabilidad.periodo')
            ->where('anio',$request->anio)
            ->where('mes',$request->mes)
            ->whereIn('id_almacen',$id_almacen)
            ->get();

            foreach($periodos as $p){

                $id_historial = DB::table('contabilidad.periodo_historial')->insertGetId(
                    [
                        'id_periodo' => $p->id_periodo,
                        'accion' => ($request->id_estado==1?'Abierto':'Cerrado'),
                        'id_estado' => $request->id_estado,
                        'comentario' => $request->comentario,
                        'id_usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_historial'
                );
                DB::table('contabilidad.periodo')
                ->where('id_periodo',$p->id_periodo)
                ->update(['estado'=>$request->id_estado]);
            }

            DB::commit();
            return response()->json([
                'tipo' => 'success',
                'id_almacen' => $request->id_almacen,
                'mensaje' => 'Se proceso correctamente.', 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la acción. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    
    public function guardarCierreAnual(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_usuario = Auth::user()->id_usuario;

            $periodos = DB::table('contabilidad.periodo')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','periodo.id_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->where('periodo.estado',1)
            ->where('periodo.anio',$request->anio)
            ->where('adm_empresa.id_empresa',$request->id_empresa)
            ->get();

            foreach($periodos as $p){

                $id_historial = DB::table('contabilidad.periodo_historial')->insertGetId(
                    [
                        'id_periodo' => $p->id_periodo,
                        'accion' => 'Cerrado',
                        'id_estado' => 2,
                        'comentario' => 'Cierre Anual periodo '.$request->anio,
                        'id_usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => new Carbon(),
                    ],
                    'id_historial'
                );
                DB::table('contabilidad.periodo')
                ->where('id_periodo',$p->id_periodo)
                ->update(['estado'=>2]);
            }

            DB::commit();
            return response()->json([
                'tipo' => 'success',
                'mensaje' => 'Se proceso correctamente.', 200
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['tipo' => 'error', 'mensaje' => 'Hubo un problema al guardar la acción. Por favor intente de nuevo', 'error' => $e->getMessage()], 200);
        }
    }

    public function listaHistorialAcciones($id_periodo)
    {
        $historial = DB::table('contabilidad.periodo_historial')
        ->select('periodo_historial.*','sis_usua.nombre_corto','alm_almacen.descripcion as almacen',
        'adm_contri.razon_social as empresa','periodo_estado.nombre as estado_nombre',
        'periodo.anio','periodo.mes')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','periodo_historial.id_usuario')
        ->join('contabilidad.periodo','periodo.id_periodo','=','periodo_historial.id_periodo')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','periodo.id_almacen')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('contabilidad.periodo_estado','periodo_estado.id_estado','=','periodo.estado')
        ->where('periodo_historial.id_periodo',$id_periodo)
        ->where('periodo_historial.estado',1)
        ->orderBy('periodo_historial.fecha_registro','desc')
        ->get();

        return response()->json($historial);
    }

    static function consultarPeriodo($fecha, $id_almacen)
    {
        $yyyy = date('Y', strtotime($fecha));
        $m = date('n', strtotime($fecha));

        $periodo=DB::table('contabilidad.periodo')
        ->select('periodo.estado','periodo_estado.nombre')
        ->join('contabilidad.periodo_estado','periodo_estado.id_estado','=','periodo.estado')
        ->where('periodo.anio',$yyyy)
        ->where('periodo.nro_mes',$m)
        ->where('periodo.id_almacen',$id_almacen)
        ->first();

        $rspta = ($periodo == null ? 1 : $periodo->estado);

        return $rspta;
    }

    static function consultarPeriodoOperativo($fecha, $id_almacen)
    {
        $yyyy = date('Y', strtotime($fecha));
        $m = date('n', strtotime($fecha));

        $periodo=DB::table('contabilidad.periodo')
        ->select('periodo.estado_operativo','periodo_estado.nombre')
        ->join('contabilidad.periodo_estado','periodo_estado.id_estado','=','periodo.estado_operativo')
        ->where('periodo.anio',$yyyy)
        ->where('periodo.nro_mes',$m)
        ->where('periodo.id_almacen',$id_almacen)
        ->first();

        $rspta = ($periodo == null ? 1 : $periodo->estado_operativo);

        return $rspta;
    }

    public function autogenerarPeriodos($anio)
    {
        $id_usuario=Auth::user()->id_usuario;
        $almacenes=DB::table('almacen.alm_almacen')
        ->where('estado',1)
        ->get();

        foreach ($almacenes as $alm) {
            for ($i=1; $i<=12; $i++) {
                switch ($i) {
                    case 1: $mes='Enero';break;
                    case 2: $mes='Febrero';break;
                    case 3: $mes='Marzo';break;
                    case 4: $mes='Abril';break;
                    case 5: $mes='Mayo';break;
                    case 6: $mes='Junio';break;
                    case 7: $mes='Julio';break;
                    case 8: $mes='Agosto';break;
                    case 9: $mes='Setiembre';break;
                    case 10: $mes='Octubre';break;
                    case 11: $mes='Noviembre';break;
                    case 12: $mes='Diciembre';break;
                    default:break;
                }
                
                $periodo = DB::table('contabilidad.periodo')
                ->where('anio',$anio)
                ->where('nro_mes',$i)
                ->where('id_almacen',$alm->id_almacen)
                ->first();

                if ($periodo == null){
                    DB::table('contabilidad.periodo')->insert(
                        [
                            'anio' => $anio,
                            'mes' => $mes,
                            'nro_mes' => $i,
                            'id_usuario' => $id_usuario,
                            'id_almacen' => $alm->id_almacen,
                            'estado' => 1, //Abierto
                            'estado_operativo' => 1, //Abierto
                        ]);
                }
            }
        }
        return response()->json('Se ha completado la generación de periodos para el año '.$anio);
    } 
}
