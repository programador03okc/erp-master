<?php

namespace App\Http\Controllers;

use App\Models\Administracion\Documento;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use App\Models\Rrhh\Trabajador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EcommerceController extends Controller
{
    //
    public function index()
    {
        return view('necesidades.ecommerce.index');
    }
    public function crear()
    {
        $monedas = Moneda::mostrar();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $empresas = Empresa::mostrar();
        $unidades_medida = UnidadMedida::mostrar();
        return view('necesidades.ecommerce.crear', compact("monedas","periodos","prioridades","empresas","unidades_medida"));
    }
    public function guardar(Request $request)
    {

        // return response()->json([
        //     "success"=>true,
        //     "status"=>200,
        //     "data"=>sizeof($request->item)
        // ]);exit;
        $requerimiento = new Requerimiento();
        $requerimiento->id_tipo_requerimiento = 2;
        $requerimiento->id_usuario = Auth::user()->id_usuario;
        $requerimiento->id_rol = null;
        $requerimiento->fecha_requerimiento = new Carbon();
        $requerimiento->id_periodo = $request->periodo;
        $requerimiento->concepto = strtoupper($request->concepto);
        $requerimiento->id_moneda = $request->moneda > 0 ? $request->moneda : null;
        $requerimiento->id_proyecto = null;
        $requerimiento->observacion = $request->observacion;
        $requerimiento->id_grupo = 2;
        $requerimiento->id_area = null;
        $requerimiento->id_prioridad = $request->prioridad;
        $requerimiento->fecha_registro = new Carbon();
        $requerimiento->estado = 1;
        // $requerimiento->id_empresa = $request->empresa ? $request->empresa : null;
        $requerimiento->id_empresa = 1;
        $requerimiento->id_sede = 1;
        $requerimiento->tipo_cliente = 1;
        $requerimiento->id_cliente = null;
        $requerimiento->id_persona = null;
        $requerimiento->direccion_entrega = null; //libre para usar en la factura
        $requerimiento->id_cuenta = null;  //libre para usar en la factura
        $requerimiento->nro_cuenta = null; //libre para usar en la factura
        $requerimiento->nro_cuenta_interbancaria = null; // libre para usar en la factura
        $requerimiento->telefono = null; // libre para usar en la factura
        $requerimiento->email = null; // libre para usar en la factura
        $requerimiento->id_ubigeo_entrega = null; //libre para usar en la factura
        $requerimiento->id_almacen = 1;
        $requerimiento->confirmacion_pago =null; // libre para usar en la factura
        $requerimiento->monto_subtotal = $request->monto_subtotal;
        $requerimiento->monto_igv = $request->monto_igv;
        $requerimiento->monto_total = $request->monto_total;
        $requerimiento->fecha_entrega = $request->fecha_entrega;
        $requerimiento->id_cc = null; // buscador para el cuadro de presupuesto de costos solo para un usuario comercial
        $requerimiento->tipo_cuadro = null;
        $requerimiento->tiene_transformacion = false; // solo puede cambiar si el usuario es comercial
        $requerimiento->fuente_id = null; // libre para usar
        $requerimiento->fuente_det_id = null; // libre para usar
        $requerimiento->division_id = 3;
        $requerimiento->trabajador_id = $request->nombre_trabajador;
        $requerimiento->id_incidencia = null;
        $requerimiento->save();

        $requerimiento = Requerimiento::find($requerimiento->id_requerimiento);
        $requerimiento->codigo= Requerimiento::crearCodigo(2, 2, $requerimiento->id_requerimiento);
        $requerimiento->save();

        if (sizeof($request->item)>0) {

            foreach ($request->item as $key => $value) {

                $detalle = new DetalleRequerimiento();
                $detalle->id_requerimiento = $requerimiento->id_requerimiento;
                $detalle->id_tipo_item = 1;
                $detalle->partida = null; //consultar
                $detalle->centro_costo_id = null; //consultar
                $detalle->part_number = $value['part_number'];
                $detalle->descripcion = $value['descripcion'];
                $detalle->id_unidad_medida = $value['unidad'];
                $detalle->cantidad = $value['cantidad'];
                $detalle->precio_unitario = floatval($value['precioUnitario']);
                $detalle->subtotal = floatval($request->subtotal);
                $detalle->motivo = $value['motivo'];
                $detalle->tiene_transformacion = false;
                $detalle->fecha_registro = new Carbon();
                $detalle->estado = $requerimiento->id_tipo_requerimiento == 2 ? 19 : 1;
                $detalle->save();

            }
        }
        $documento = new Documento();
        $documento->id_tp_documento = 1;
        $documento->codigo_doc = $requerimiento->codigo;
        $documento->id_doc = $requerimiento->id_requerimiento;
        $documento->save();

        $trazabilidad = new Trazabilidad();
        $trazabilidad->id_requerimiento = $requerimiento->id_requerimiento;
        $trazabilidad->id_usuario = Auth::user()->id_usuario;
        $trazabilidad->accion = 'ELABORADO';
        $trazabilidad->descripcion = 'Requerimiento elaborado.' . (isset($request->justificacion_generar_requerimiento) ? ('Con CC Pendiente Aprobación. ' . $request->justificacion_generar_requerimiento) : '');
        $trazabilidad->fecha_registro = new Carbon();
        $trazabilidad->save();


        return response()->json([
            "success"=>true,
            "status"=>200,
        ]);
    }
    public function buscarTrabajador(Request $request)
    {
        // $page = $request->page;
        // $resultCount = 25;
        // $offset = ($page - 1) * $resultCount;

        $data = DB::table('rrhh.rrhh_trab')
                ->select('rrhh_trab.*', 'rrhh_perso.nro_documento',
                // 'rrhh_trab.id_trabajador as id',
                'rrhh_perso.nombres',
                'rrhh_perso.apellido_paterno',
                'rrhh_perso.apellido_materno',
                    DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS nombre_trabajador ")
                    // DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS text ")
                )
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where('rrhh_trab.estado', '=', 1)
                ->where('rrhh_perso.nombres','like','%'.$request->searchTerm.'%')
                ->orWhere('rrhh_perso.nombres','like','%'.strtoupper($request->searchTerm).'%')
                ->orWhere('rrhh_perso.apellido_paterno','like','%'.$request->searchTerm.'%')
                ->orWhere('rrhh_perso.apellido_materno','like','%'.$request->searchTerm.'%')
                ->orWhere('rrhh_perso.apellido_paterno','like','%'.strtoupper($request->searchTerm).'%')
                ->orWhere('rrhh_perso.apellido_materno','like','%'.strtoupper($request->searchTerm).'%')

                // ->skip($offset)
                ->take(20)

                ->get();


        // $count = DB::table('rrhh.rrhh_trab')
        // ->select('rrhh_trab.*', 'rrhh_perso.nro_documento',
        // 'rrhh_perso.nombres',
        // 'rrhh_perso.apellido_paterno',
        // 'rrhh_perso.apellido_materno',
        //     DB::raw("concat(rrhh_perso.nombres, ' ' ,rrhh_perso.apellido_paterno, ' ' ,rrhh_perso.apellido_materno) AS nombre_trabajador ")
        // )
        // ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
        // ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')->count();
        // $endCount = $offset + $resultCount;
        // $morePages = $endCount > $count;


        // $data = json_encode($data);
        // $data = json_decode($data);
        // return response()->json($count);

        // $results = array(
        //     "results" => $data,
        //     "pagination" => array(
        //       "more" => $morePages
        //     )
        // );

        // return response()->json($results);
        if (sizeof($data)>0) {
            return $data;
        }
        return [];
    }
}
