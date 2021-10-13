<?php

namespace App\Http\Controllers\Logistica;

use App\Helpers\NotificacionHelper;
use App\Http\Controllers\Almacen\Reporte\SaldosController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ConfiguracionController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Aprobacion;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Administracion\DivisionArea;
use App\Models\Administracion\Documento;
use App\Models\Administracion\Flujo;
use App\Models\Administracion\Operacion;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Administracion\VoBo;
use App\Models\Almacen\CategoriaAdjunto;
use App\Models\Almacen\DetalleRequerimiento;
use App\Models\Almacen\Fuente;
use App\Models\Almacen\Requerimiento;
use App\Models\Almacen\TipoRequerimiento;
use App\Models\Almacen\Trazabilidad;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Administracion\Empresa;
use App\Models\Administracion\Estado;
use App\Models\administracion\Sede;
use App\Models\Almacen\AdjuntoDetalleRequerimiento;
use App\Models\Almacen\AdjuntoRequerimiento;
use App\Models\Almacen\Transferencia;
use App\Models\Configuracion\Grupo;
use App\Models\Presupuestos\Presupuesto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Debugbar;
use PhpParser\Node\Stmt\TryCatch;

class RequerimientoController extends Controller
{
    public function index()
    {

        $grupos = Auth::user()->getAllGrupo();
        $idTrabajador = Auth::user()->id_trabajador;
        $idUsuario = Auth::user()->id_usuario;
        $nombreUsuario = Auth::user()->trabajador->postulante->persona->nombre_completo;
        $monedas = Moneda::mostrar();
        $prioridades = Prioridad::mostrar();
        $tipo_requerimiento = TipoRequerimiento::mostrar();
        $empresas = Empresa::mostrar();
        $areas = Area::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $periodos = Periodo::mostrar();
        $roles = Auth::user()->getAllRol(); //Usuario::getAllRol(Auth::user()->id_usuario);
        $sis_identidad = Identidad::mostrar();
        $bancos = Banco::mostrar();
        $tipos_cuenta = TipoCuenta::mostrar();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();
        $fuentes = Fuente::mostrar();
        $divisiones = Division::mostrar();
        $categoria_adjunto = CategoriaAdjunto::mostrar();
        $tipo_cambio = (new SaldosController)->tipo_cambio_compra(new Carbon());

        return view('logistica/requerimientos/gestionar_requerimiento', compact('tipo_cambio','idTrabajador', 'nombreUsuario', 'categoria_adjunto', 'grupos', 'sis_identidad', 'tipo_requerimiento', 'monedas', 'prioridades', 'empresas', 'unidadesMedida', 'roles', 'periodos', 'bancos', 'tipos_cuenta', 'clasificaciones', 'subcategorias', 'categorias', 'unidades', 'proyectos_activos', 'fuentes', 'divisiones'));
    }

    
    public function mostrar($idRequerimiento){
        return redirect()->route('logistica.gestion-logistica.requerimiento.elaboracion.index', ['id' => $idRequerimiento]);

    }


    public function listaDivisiones()
    {
        return DivisionArea::all();
    }
    public function mostrarPartidas($idGrupo, $idProyecto = null)
    {
        return Presupuesto::mostrarPartidas($idGrupo, $idProyecto);
    }

    public function mostrarCategoriaAdjunto()
    {
        return CategoriaAdjunto::mostrar();
    }

    public function requerimientoAPago($id)
    {
        $req = DB::table('almacen.alm_req')
            ->where('id_requerimiento', $id)
            ->update(['estado' => 8]);

        return response()->json($req);
    }
    
    public function requerimiento($id_requerimiento)
    {
        $requerimiento = Requerimiento::with([
            'tipo','moneda','division','creadoPor','empresa','sede',
            'detalle'=> function($q){
                $q->where([['alm_det_req.estado', '!=', 7]]);
            },'detalle.producto','detalle.unidadMedida','detalle.estado','detalle.reserva'])
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento]
            ])
            ->first();

        return response()->json($requerimiento);
    }

    public function detalleRequerimiento($id_requerimiento)
    {
        $detalles = DetalleRequerimiento::with('reserva')->select(
            'alm_req.codigo as codigo_requerimiento',
            'alm_det_req.*',
            'sis_moneda.simbolo as moneda_simbolo',
            'sis_moneda.descripcion as moneda_descripcion',
            'adm_estado_doc.estado_doc',
            'adm_estado_doc.bootstrap_color',
            'alm_prod.descripcion as producto_descripcion',
            'alm_prod.codigo as producto_codigo',
            'alm_prod.part_number as producto_part_number',
            'alm_und_medida.abreviatura'
        )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'alm_req.id_moneda')
            ->where([
                ['alm_det_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_det_req.estado', '!=', 7]
            ])
            ->get();

        return response()->json($detalles);
    }

    public function mostrarRequerimiento($id, $codigo)
    {
        if ($id > 0) {
            $theWhere = ['alm_req.id_requerimiento', '=', $id];
        } else {

            $theWhere = ['alm_req.codigo', '=', $codigo];
        }
        $alm_req = Requerimiento::leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('contabilidad.adm_cta_contri', 'adm_cta_contri.id_cuenta_contribuyente', '=', 'alm_req.id_cuenta')
            ->leftJoin('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('rrhh.rrhh_perso as perso_natural', 'alm_req.id_persona', '=', 'perso_natural.id_persona')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri as contri_cliente', 'com_cliente.id_contribuyente', '=', 'contri_cliente.id_contribuyente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')
            ->leftJoin('rrhh.rrhh_trab as trab_asignado', 'alm_req.trabajador_id', '=', 'trab_asignado.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as postu_asignado', 'postu_asignado.id_postulante', '=', 'trab_asignado.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as perso_asignado', 'perso_asignado.id_persona', '=', 'postu_asignado.id_persona')
            ->leftJoin('administracion.adm_prioridad', 'adm_prioridad.id_prioridad', '=', 'alm_req.id_prioridad')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')


            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'sis_moneda.simbolo as simbolo_moneda',
                'alm_req.id_proyecto',
                'proy_proyecto.codigo as codigo_proyecto',
                'proy_proyecto.descripcion as descripcion_proyecto',
                'alm_req.id_periodo',
                'adm_periodo.descripcion as periodo',
                'alm_req.id_prioridad',
                'adm_prioridad.descripcion as prioridad',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'adm_grupo.descripcion as grupo_descripcion',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("concat(rrhh_perso.nombres, ' ', rrhh_perso.apellido_paterno, ' ', rrhh_perso.apellido_materno)  AS persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_presupuesto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                'alm_req.id_sede',
                'alm_req.id_persona',
                'perso_natural.nro_documento as dni_persona',
                DB::raw("concat(perso_natural.nombres, ' ' ,perso_natural.apellido_paterno, ' ' ,perso_natural.apellido_materno)  AS nombre_persona"),
                'alm_req.tipo_cliente',
                'alm_req.id_cliente',
                'contri_cliente.nro_documento as cliente_ruc',
                'contri_cliente.razon_social as cliente_razon_social',
                'alm_req.id_ubigeo_entrega',
                DB::raw("concat(ubi_dis.descripcion, ' ' ,ubi_prov.descripcion, ' ' ,ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.direccion_entrega',
                'alm_req.telefono',
                'alm_req.email',
                'alm_req.id_almacen',
                'alm_req.monto',
                'alm_req.fecha_entrega',
                'alm_req.id_cuenta',
                'adm_cta_contri.id_tipo_cuenta',
                'cont_banco.id_banco',
                'alm_req.nro_cuenta',
                'alm_req.nro_cuenta_interbancaria',
                'alm_req.tiene_transformacion',
                'alm_req.fuente_id',
                'alm_req.fuente_det_id',
                'alm_req.para_stock_almacen',
                'alm_req.division_id',
                'division.descripcion as division',
                'alm_req.trabajador_id',
                DB::raw("concat(perso_asignado.nombres, ' ' ,perso_asignado.apellido_paterno, ' ' ,perso_asignado.apellido_materno)  AS nombre_trabajador"),
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
                DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario) 
                FROM almacen.alm_det_req 
                WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
                alm_det_req.estado != 7) AS monto_total")
            )
            ->where([
                $theWhere
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

        if (sizeof($alm_req) <= 0) {
            $alm_req = [];
            return response()->json($alm_req);
        } else {

            foreach ($alm_req as $data) {

                $id_requerimiento = $data->id_requerimiento;

                $requerimiento[] = [
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo' => $data->codigo,
                    'concepto' => $data->concepto,
                    'id_moneda' => $data->id_moneda,
                    'simbolo_moneda' => $data->simbolo_moneda,
                    'id_proyecto' => $data->id_proyecto,
                    'codigo_proyecto' => $data->codigo_proyecto,
                    'descripcion_proyecto' => $data->descripcion_proyecto,
                    'id_periodo' => $data->id_periodo,
                    'periodo' => $data->periodo,
                    'estado_doc' => $data->estado_doc,
                    'bootstrap_color' => $data->bootstrap_color,
                    'id_prioridad' => $data->id_prioridad,
                    'prioridad' => $data->prioridad,
                    'id_empresa' => $data->id_empresa,
                    'id_grupo' => $data->id_grupo,
                    'grupo_descripcion' => $data->grupo_descripcion,
                    'id_sede' => $data->id_sede,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'codigo_sede_empresa' => $data->codigo_sede_empresa,
                    'logo_empresa' => $data->logo_empresa,
                    'fecha_requerimiento' => $data->fecha_requerimiento,
                    'fecha_entrega' => $data->fecha_entrega,
                    'id_periodo' => $data->id_periodo,
                    'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                    'tipo_requerimiento' => $data->tp_req_descripcion,
                    'id_usuario' => $data->id_usuario,
                    'persona' => $data->persona,
                    'usuario' => $data->usuario,
                    'id_rol' => $data->id_rol,
                    'id_area' => $data->id_area,
                    'area_descripcion' => $data->area_descripcion,
                    'id_presupuesto' => $data->id_presupuesto,
                    'observacion' => $data->observacion,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado,
                    'estado_desc' => $data->estado_desc,
                    'id_persona' => $data->id_persona,
                    'dni_persona' => $data->dni_persona,
                    'nombre_persona' => $data->nombre_persona,
                    'tipo_cliente' => $data->tipo_cliente,
                    'id_cliente' => $data->id_cliente,
                    'cliente_ruc' => $data->cliente_ruc,
                    'cliente_razon_social' => $data->cliente_razon_social,
                    'id_ubigeo_entrega' => $data->id_ubigeo_entrega,
                    'name_ubigeo' => $data->name_ubigeo,
                    'direccion_entrega' => $data->direccion_entrega,
                    'id_cuenta' => $data->id_cuenta,
                    'id_tipo_cuenta' => $data->id_tipo_cuenta,
                    'id_banco' => $data->id_banco,
                    'nro_cuenta' => $data->nro_cuenta,
                    'nro_cuenta_interbancaria' => $data->nro_cuenta_interbancaria,
                    'telefono' => $data->telefono,
                    'email' => $data->email,
                    'id_almacen' => $data->id_almacen,
                    'monto' => $data->monto,
                    'monto_total' => number_format($data->monto_total, 2),
                    'fuente_id' => $data->fuente_id,
                    'fuente_det_id' => $data->fuente_det_id,
                    'tiene_transformacion' => $data->tiene_transformacion,
                    'para_stock_almacen' => $data->para_stock_almacen,
                    'division_id' => $data->division_id,
                    'trabajador_id' => $data->trabajador_id,
                    'division' => $data->division,
                    'nombre_trabajador' => $data->nombre_trabajador,
                    'adjuntos' => []

                ];
            };

            $adjuntosCabecera = DB::table('almacen.alm_req')
                ->select('alm_req_adjuntos.*', 'categoria_adjunto.descripcion as categoria_adjunto')
                ->join('almacen.alm_req_adjuntos', 'alm_req_adjuntos.id_requerimiento', '=', 'alm_req.id_requerimiento')
                ->join('almacen.categoria_adjunto', 'categoria_adjunto.id_categoria_adjunto', '=', 'alm_req_adjuntos.categoria_adjunto_id')
                ->where([
                    ['alm_req.id_requerimiento', '=', $id_requerimiento],
                    ['alm_req_adjuntos.estado', '=', 1]
                ])
                ->orderBy('alm_req_adjuntos.id_adjunto', 'asc')
                ->get();

            foreach ($adjuntosCabecera as $key => $value) {
                $requerimiento[0]['adjuntos'][] = $value;
            }


            $alm_det_req = DetalleRequerimiento::leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_prod', 'alm_det_req.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('almacen.alm_item', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_prod.id_categoria')
                ->leftJoin('almacen.alm_subcat', 'alm_subcat.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                // ->leftJoin('almacen.alm_almacen', 'alm_det_req.id_almacen_reserva', '=', 'alm_almacen.id_almacen')
                ->leftJoin('almacen.alm_und_medida', 'alm_det_req.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                ->leftJoin('logistica.equipo', 'alm_item.id_equipo', '=', 'equipo.id_equipo')
                // ->leftJoin('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
                ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
                ->leftJoin('finanzas.presup_titu', 'presup_titu.id_presup', '=', 'presup_par.id_presup')
                ->leftJoin('finanzas.presup_pardet', 'presup_pardet.id_pardet', '=', 'presup_par.id_pardet')
                ->leftJoin('administracion.adm_estado_doc', 'alm_det_req.estado', '=', 'adm_estado_doc.id_estado_doc')
                ->leftJoin('configuracion.sis_moneda', 'alm_det_req.id_moneda', '=', 'sis_moneda.id_moneda')
                ->leftJoin('mgcp_cuadro_costos.cc_am_filas', 'cc_am_filas.id', '=', 'alm_det_req.id_cc_am_filas')
                ->leftJoin('mgcp_cuadro_costos.cc_am_proveedores', 'cc_am_proveedores.id', '=', 'cc_am_filas.proveedor_seleccionado')
                ->leftJoin('mgcp_cuadro_costos.proveedores as proveedores_am', 'proveedores_am.id', '=', 'cc_am_proveedores.id_proveedor')
                ->leftJoin('mgcp_cuadro_costos.cc_venta_filas', 'cc_venta_filas.id', '=', 'alm_det_req.id_cc_venta_filas')
                ->leftJoin('mgcp_cuadro_costos.cc_venta_proveedor', 'cc_venta_proveedor.id', '=', 'cc_venta_filas.proveedor_seleccionado')
                ->leftJoin('mgcp_cuadro_costos.proveedores as proveedores_venta', 'proveedores_venta.id', '=', 'cc_venta_filas.proveedor_seleccionado')
                ->leftJoin('finanzas.centro_costo', 'centro_costo.id_centro_costo', '=', 'alm_det_req.centro_costo_id')
                ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'alm_det_req.proveedor_id')
                ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_req.id_sede',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_unitario',
                    'alm_det_req.subtotal',
                    'alm_det_req.cantidad',
                    'alm_det_req.id_unidad_medida',
                    'und_medida_det_req.descripcion AS unidad_medida',
                    'alm_det_req.observacion',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.lugar_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.descripcion',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.id_moneda as id_tipo_moneda',
                    'sis_moneda.descripcion as tipo_moneda',
                    'sis_moneda.simbolo as simbolo_moneda',
                    'alm_det_req.estado',
                    'adm_estado_doc.estado_doc',
                    'adm_estado_doc.bootstrap_color',
                    'alm_det_req.partida',
                    'presup_par.codigo AS codigo_partida',
                    'presup_pardet.descripcion AS descripcion_partida',
                    'presup_par.importe_total AS presupuesto_total_partida',
                    'alm_det_req.centro_costo_id as id_centro_costo',
                    'centro_costo.codigo as codigo_centro_costo',
                    'centro_costo.descripcion as descripcion_centro_costo',
                    'alm_item.id_item',
                    'alm_det_req.id_producto',
                    'alm_cat_prod.descripcion as categoria',
                    'alm_subcat.descripcion as subcategoria',
                    'alm_item.codigo AS codigo_item',
                    'alm_item.fecha_registro AS alm_item_fecha_registro',
                    'alm_prod.codigo AS alm_prod_codigo',
                    'alm_det_req.part_number',
                    'alm_prod.descripcion AS alm_prod_descripcion',
                    'alm_prod.part_number AS alm_prod_part_number',
                    'alm_det_req.tiene_transformacion',
                    'alm_det_req.proveedor_id',
                    'adm_contri.razon_social as proveedor_razon_social',
                    'alm_det_req.id_cc_am_filas',
                    'alm_det_req.id_cc_venta_filas',
                    'alm_det_req.motivo',
                    'proveedores_am.razon_social as razon_social_proveedor_seleccionado_am',
                    'cc_am_filas.proveedor_seleccionado',
                    'proveedores_venta.razon_social as razon_social_proveedor_seleccionado_venta',
                    'alm_item.id_equipo',
                    'equipo.descripcion as equipo_descripcion',
                    DB::raw("(SELECT SUM(trans_detalle.cantidad) 
                    FROM almacen.trans_detalle 
                    WHERE   trans_detalle.id_requerimiento_detalle = alm_det_req.id_detalle_requerimiento AND
                            trans_detalle.estado != 7) AS suma_transferencias")
                )
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']]

                ])
                ->orderBy('alm_item.id_item', 'asc')
                ->get();


            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                $idDetalleRequerimientoLis = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $idDetalleRequerimientoLis[] = $data->id_detalle_requerimiento;
                        $detalle_requerimiento[] = [
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'tiene_transformacion'      => $data->tiene_transformacion,
                            'proveedor_id'              => $data->proveedor_id,
                            'proveedor_razon_social'    => $data->proveedor_razon_social,
                            'id_cc_am_filas'            => $data->id_cc_am_filas,
                            'id_cc_venta_filas'         => $data->id_cc_venta_filas ? $data->id_cc_venta_filas : null,
                            'razon_social_proveedor_seleccionado' => $data->razon_social_proveedor_seleccionado_am ? $data->razon_social_proveedor_seleccionado_am : $data->razon_social_proveedor_seleccionado_venta,
                            'proveedor_seleccionado'              => $data->proveedor_seleccionado,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_sede'                   => $data->id_sede,
                            'id_item'                   => $data->id_item_alm_det_req,
                            'categoria'                 => $data->categoria,
                            'subcategoria'              => $data->subcategoria,
                            'cantidad'                  => $data->cantidad,
                            'suma_transferencias'       => $data->suma_transferencias,
                            'id_unidad_medida'          => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_unitario'           => $data->precio_unitario,
                            'subtotal'                  => $data->subtotal,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'id_tipo_moneda'            => $data->id_tipo_moneda,
                            'simbolo_moneda'            => $data->simbolo_moneda,
                            'tipo_moneda'               => $data->tipo_moneda,
                            'observacion'               => $data->observacion,
                            'estado'                    => $data->estado,
                            'estado_doc'                => $data->estado_doc,
                            'bootstrap_color'           => $data->bootstrap_color,
                            'codigo_item'                => $data->codigo_item,
                            'part_number'                => $data->part_number,
                            'id_tipo_item'                => $data->id_tipo_item,
                            'id_producto'               => $data->id_producto,
                            'producto_descripcion'       => $data->alm_prod_descripcion,
                            'producto_part_number'       => $data->alm_prod_part_number,
                            'codigo_producto'            => $data->alm_prod_codigo,
                            'descripcion'                   => $data->descripcion,
                            'id_partida'                    => $data->partida,
                            'codigo_partida'                => $data->codigo_partida,
                            'presupuesto_total_partida'     => $data->presupuesto_total_partida,
                            'id_centro_costo'                => $data->id_centro_costo,
                            'codigo_centro_costo'            => $data->codigo_centro_costo,
                            'descripcion_centro_costo'       => $data->descripcion_centro_costo,
                            'motivo'                        => $data->motivo,
                            'descripcion_partida'           => $data->descripcion_partida,
                            'suma_transferencias'           => $data->suma_transferencias,
                            'adjuntos'                      => []

                        ];
                        $lastId = $data->id_detalle_requerimiento;
                    }
                }

                // insertar adjuntos

                $adjuntosDetalle = DB::table('almacen.alm_det_req')
                    ->select('alm_det_req_adjuntos.*')
                    ->join('almacen.alm_det_req_adjuntos', 'alm_det_req_adjuntos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')
                    ->whereIn('alm_det_req.id_detalle_requerimiento', $idDetalleRequerimientoLis)
                    ->where([
                        ['alm_det_req_adjuntos.estado', '=', 1]
                    ])
                    ->orderBy('alm_det_req_adjuntos.id_adjunto', 'asc')
                    ->get();

                foreach ($detalle_requerimiento as $key => $detalleRequerimiento) {
                    foreach ($adjuntosDetalle as $ad) {
                        if ($detalleRequerimiento['id_detalle_requerimiento'] == $ad->id_detalle_requerimiento) {
                            $detalle_requerimiento[$key]['adjuntos'][] = $ad;
                        }
                    }
                }
                // end insertar adjuntos

            } else {

                $detalle_requerimiento = [];
            }
        }


        $estado_req = $requerimiento[0]['estado'];
        $req_observacion = [];


        if ($estado_req == 3) { // estado observado

            $num_doc = Documento::getIdDocAprob($id_requerimiento, 1);

            $req_observacion = Aprobacion::getHeaderObservacion($num_doc);
        }

        $num_doc = Documento::getIdDocAprob($id_requerimiento, 1);
        $cantidad_aprobados = Aprobacion::cantidadAprobaciones($num_doc);

        $historialAprobacionList = Aprobacion::getVoBo($num_doc)['data'];

        $data = [
            "requerimiento" => $requerimiento ? $requerimiento : [],
            "det_req" => $detalle_requerimiento ? $detalle_requerimiento : [],
            "observacion_requerimiento" => $req_observacion ? $req_observacion : [],
            "aprobaciones" => $cantidad_aprobados ? $cantidad_aprobados : 0,
            "historial_aprobacion" => $historialAprobacionList ? $historialAprobacionList : []
        ];

        return $data;
    }



    public function guardarRequerimiento(Request $request)
    {
        // dd($request->all());
        // exit();   
        DB::beginTransaction();
        try {

            $requerimiento = new Requerimiento();
            // $requerimiento->codigo =  Requerimiento::crearCodigo($request->tipo_requerimiento, $request->id_grupo);
            $requerimiento->id_tipo_requerimiento = $request->tipo_requerimiento;
            $requerimiento->id_usuario = Auth::user()->id_usuario;
            $requerimiento->id_rol = $request->id_rol > 0 ? $request->id_rol : null;
            $requerimiento->fecha_requerimiento = $request->fecha_requerimiento !=null ? $request->fecha_requerimiento : new Carbon();
            $requerimiento->id_periodo = $request->periodo;
            $requerimiento->concepto = strtoupper($request->concepto);
            $requerimiento->id_moneda = $request->moneda > 0 ? $request->moneda : null;
            $requerimiento->id_proyecto = $request->id_proyecto > 0 ? $request->id_proyecto : null;
            $requerimiento->observacion = $request->observacion;
            $requerimiento->id_grupo = $request->id_grupo > 0 ? $request->id_grupo : null;
            $requerimiento->id_area = $request->id_area > 0 ? $request->id_area : null;
            $requerimiento->id_prioridad = $request->prioridad;
            $requerimiento->fecha_registro = new Carbon();
            $requerimiento->estado = 1;
            $requerimiento->id_empresa = $request->empresa ? $request->empresa : null;
            $requerimiento->id_sede = $request->sede > 0 ? $request->sede : null;
            $requerimiento->tipo_cliente = $request->tipo_cliente > 0 ? $request->tipo_cliente : null;
            $requerimiento->id_cliente = $request->id_cliente > 0 ? $request->id_cliente : null;
            $requerimiento->id_persona = $request->id_persona > 0 ? $request->id_persona : null;
            $requerimiento->direccion_entrega = $request->direccion_entrega;
            $requerimiento->id_cuenta = $request->id_cuenta;
            $requerimiento->nro_cuenta = $request->nro_cuenta;
            $requerimiento->nro_cuenta_interbancaria = $request->nro_cuenta_interbancaria;
            $requerimiento->telefono = $request->telefono;
            $requerimiento->email = $request->email;
            $requerimiento->id_ubigeo_entrega = $request->id_ubigeo_entrega;
            $requerimiento->id_almacen = $request->id_almacen > 0 ? $request->id_almacen : null;
            $requerimiento->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
            $requerimiento->monto = $request->monto;
            $requerimiento->fecha_entrega = $request->fecha_entrega;
            $requerimiento->id_cc = $request->id_cc;
            $requerimiento->tipo_cuadro = $request->tipo_cuadro;
            $requerimiento->tiene_transformacion = $request->tiene_transformacion ? $request->tiene_transformacion : false;
            $requerimiento->fuente_id = $request->fuente;
            $requerimiento->fuente_det_id = $request->fuente_det;
            $requerimiento->division_id = $request->division;
            $requerimiento->trabajador_id = $request->id_trabajador;
            $requerimiento->save();
            $requerimiento->adjuntoOtrosAdjuntos = $request->archivoAdjuntoRequerimiento1;
            $requerimiento->adjuntoOrdenes = $request->archivoAdjuntoRequerimiento2;
            $requerimiento->adjuntoComprobanteBancario = $request->archivoAdjuntoRequerimiento3;
            $requerimiento->adjuntoComprobanteContable = $request->archivoAdjuntoRequerimiento4;

            $count = count($request->descripcion);
            $montoTotal = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($request->cantidad[$i]<=0) {
                    return response()->json(['id_requerimiento' => 0, 'codigo' => '', 'mensaje' => 'La cantidad solicitada debe ser mayor a 0']);
                }
                
                $detalle = new DetalleRequerimiento();
                $detalle->id_requerimiento = $requerimiento->id_requerimiento;
                $detalle->id_tipo_item = $request->tipoItem[$i];
                $detalle->partida = $request->idPartida[$i];
                $detalle->centro_costo_id = $request->idCentroCosto[$i];
                $detalle->part_number = $request->partNumber[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                $detalle->motivo = $request->motivo[$i];
                $detalle->tiene_transformacion = ($request->tiene_transformacion ? $request->tiene_transformacion : false);
                $detalle->fecha_registro = new Carbon();
                $detalle->estado = $requerimiento->id_tipo_requerimiento == 2 ? 19 : 1;
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;
                $montoTotal += $detalle->cantidad * $detalle->precio_unitario;
            }

            $documento = new Documento();
            $documento->id_tp_documento = 1;
            $documento->codigo_doc = $requerimiento->codigo;
            $documento->id_doc = $requerimiento->id_requerimiento;
            $documento->save();

            $trazabilidad = new Trazabilidad();
            $trazabilidad->id_requerimiento = $requerimiento->id_requerimiento;
            $trazabilidad->id_usuario = Auth::user()->id_usuario;
            if ($requerimiento->id_tipo_requerimiento == 1) { // tipo mgcp
                $trazabilidad->accion = 'VINCULADO';
                $trazabilidad->descripcion = 'Fecha de creación de Cuadro de Costos: ' . (isset($request->fecha_creacion_cc) ? $request->fecha_creacion_cc : '');
                $trazabilidad->fecha_registro = $request->fecha_creacion_cc;
            } else {
                $trazabilidad->accion = 'ELABORADO';
                $trazabilidad->descripcion = 'Requerimiento elaborado.' . (isset($request->justificacion_generar_requerimiento) ? ('Con CC Pendiente Aprobación. ' . $request->justificacion_generar_requerimiento) : '');
                $trazabilidad->fecha_registro = new Carbon();
            }
            $trazabilidad->save();

            $adjuntosRequerimiento = $this->guardarAdjuntoNivelRequerimiento($requerimiento);

            $adjuntoDetelleRequerimiento = [];
            for ($i = 0; $i < count($detalleArray); $i++) {
                $archivos = $request->{"archivoAdjuntoItem" . $detalleArray[$i]['idRegister']};
                if (isset($archivos)) {
                    foreach ($archivos as $archivo) {
                        $adjuntoDetelleRequerimiento[] = [
                            'id_detalle_requerimiento' => $detalleArray[$i]['id_detalle_requerimiento'],
                            'nombre_archivo' => $archivo->getClientOriginalName(),
                            'archivo' => $archivo
                        ];
                        // $adjuntoDetelleRequerimiento =DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                        //     [
                        //         'id_detalle_requerimiento'  => $detalleArray[$i]['id_detalle_requerimiento'],
                        //         'archivo'                   => $archivo->getClientOriginalName(),
                        //         'estado'                    => 1,
                        //         'fecha_registro'            => date('Y-m-d H:i:s')
                        //     ],
                        //     'id_adjunto'
                        // );
                        // Storage::disk('archivos')->put("logistica/requerimiento/" . $archivo->getClientOriginalName(), \File::get($archivo));
                    }
                }
            }
            if (count($adjuntoDetelleRequerimiento) > 0) {
                $this->guardarAdjuntoNivelDetalleItem($adjuntoDetelleRequerimiento);
            }

            // notificar al primer aprobante del requerimiento creado


            $operaciones = Operacion::getOperacion(1, $request->tipo_requerimiento, $request->id_grupo, $request->division, $request->prioridad);
            $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];
            $idRolPrimerAprobante = 0;
            foreach ($flujoTotal as $flujo) {
                if ($flujo->orden == 1) {
                    $idRolPrimerAprobante = $flujo->id_rol;
                }
            }
            if ($idRolPrimerAprobante > 0) {
                $idUsuariosList = Usuario::getAllIdUsuariosPorRol($idRolPrimerAprobante);

                foreach ($idUsuariosList as $idUsuario) {
                    $correoUsuario = Usuario::find($idUsuario)->trabajador->postulante->persona->email;
                    if (!empty($correoUsuario)) {
                        $this->enviarNotificacionPorCreacion($request, $correoUsuario, $requerimiento, $montoTotal);
                    }
                }
            }

            DB::commit();

            $codigo= Requerimiento::crearCodigo($request->tipo_requerimiento,$request->id_grupo, $requerimiento->id_requerimiento);
            $requerimiento = Requerimiento::find($requerimiento->id_requerimiento);
            $requerimiento->codigo =$codigo;
            $requerimiento->save();
    
            return response()->json(['id_requerimiento' => $requerimiento->id_requerimiento, 'mensaje' => 'Se guardó el requerimiento '.$codigo]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_requerimiento' => 0, 'mensaje' => 'Hubo un problema al guardar el requerimiento. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    private function enviarNotificacionPorAprobacion($requerimiento,$comentario,$nombreCompletoUsuarioCreador,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad){
        $titulo = 'El requerimiento ' . $requerimiento->codigo . ' fue '.$trazabilidad->accion;
        $mensaje = 'El requerimiento ' . $requerimiento->codigo . ' fue '.$trazabilidad->accion.'. Información adicional del requerimiento:' .
            '<ul>' .
            '<li> Concepto/Motivo: ' . $requerimiento->concepto . '</li>' .
            '<li> Tipo de requerimiento: ' . $requerimiento->tipo->descripcion . '</li>' .
            '<li> División: ' . $requerimiento->division->descripcion . '</li>' .
            '<li> Fecha limite de entrega: ' . $requerimiento->fecha_entrega . '</li>' .
            '<li> Monto Total: ' . $requerimiento->moneda->simbolo . number_format($montoTotal, 2) . '</li>' .
            '<li> Creado por: ' . ($nombreCompletoUsuarioCreador ?? '') . '</li>' .
            '<li> '.$trazabilidad->descripcion.': ' . ($nombreCompletoUsuarioRevisaAprueba ?? '') . '</li>' .
            (!empty($comentario) ? ('<li> Comentario: ' . $comentario . '</li>') : '') .
            '</ul>' .
            '<p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br> SYSTEM AGILE';

        $seNotificaraporEmail = false;
            $correoUsuarioList = [];
        $correoUsuarioList[] = Usuario::find($requerimiento->id_usuario)->trabajador->postulante->persona->email; // notificar a usuario
        $usuariosList = Usuario::getAllIdUsuariosPorRol(4); // notificar al usuario  con rol = 'logistico compras'

        // Debugbar::info($usuariosList);
        if (count($usuariosList) > 0) {
            foreach ($usuariosList as $idUsuario) {
                $correoUsuarioList[] = Usuario::find($idUsuario)->trabajador->postulante->persona->email;
            }

            if (count($correoUsuarioList) > 0) {
                // $destinatarios[]= 'programador03@okcomputer.com.pe';
                $destinatarios = $correoUsuarioList;
                $seNotificaraporEmail = true;



                $payload = [
                    'id_empresa' => $requerimiento->id_empresa,
                    'email_destinatario' => $destinatarios,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje
                ];

                // Debugbar::info($payload);

                if (count($destinatarios) > 0) {
                    NotificacionHelper::enviarEmail($payload);

                }
            }
        }    
    }

    private function enviarNotificacionPorCreacion($request, $correoUsuario, $requerimiento, $montoTotal)
    {
        $nombreCompletoUsuario = Auth::user()->trabajador->postulante->persona->nombre_completo;
        $payload = [
            'id_empresa' => $request->empresa,
            'email_destinatario' => $correoUsuario,
            'titulo' => 'El requerimiento ' . $requerimiento->codigo . ' requiere su revisión/aprobación',
            'mensaje' => 'El requerimiento ' . $requerimiento->codigo . ' requiere su revisión/aprobación. Información adicional del requerimiento:' .
                '<ul>' .
                '<li> Concepto/Motivo: ' . $requerimiento->concepto . '</li>' .
                '<li> Tipo de requerimiento: ' . $requerimiento->tipo->descripcion . '</li>' .
                '<li> División: ' . $requerimiento->division->descripcion . '</li>' .
                '<li> Fecha limite de entrega: ' . $requerimiento->fecha_entrega . '</li>' .
                '<li> Monto Total: ' . $requerimiento->moneda->simbolo . number_format($montoTotal, 2) . '</li>' .
                '<li> Creado por: ' . ($nombreCompletoUsuario ? $nombreCompletoUsuario : '') . '</li>' .
                '</ul>' .
                '<p> *Este correo es generado de manera automática, por favor no responder.</p> 
            <br> Saludos <br> Módulo de Logística <br> SYSTEM AGILE'
        ];

        if (strlen($correoUsuario) > 0) {
            $estado_envio = NotificacionHelper::enviarEmail($payload);
        }
    }

    public static function guardarAdjuntoNivelRequerimiento($requerimiento)
    {
        $adjuntoOtrosAdjuntosLength = $requerimiento->adjuntoOtrosAdjuntos != null ? count($requerimiento->adjuntoOtrosAdjuntos) : 0;
        $adjuntoOrdenesLength = $requerimiento->adjuntoOrdenes != null ? count($requerimiento->adjuntoOrdenes) : 0;
        $adjuntoComprobanteContableLength = $requerimiento->adjuntoComprobanteContable != null ? count($requerimiento->adjuntoComprobanteContable) : 0;
        $adjuntoComprobanteBancarioLength = $requerimiento->adjuntoComprobanteBancario != null ? count($requerimiento->adjuntoComprobanteBancario) : 0;


        if ($adjuntoOtrosAdjuntosLength > 0) {
            foreach ($requerimiento->adjuntoOtrosAdjuntos as $archivo) {
                if ($archivo != null) {
                    $fechaHoy = new Carbon();
                    $sufijo = $fechaHoy->format('YmdHis');
                    $file = $archivo->getClientOriginalName();
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $newNameFile = $filename . '_' . $sufijo . '.' . $extension;
                    $otrosAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                        [
                            'id_requerimiento'          => $requerimiento->id_requerimiento,
                            'archivo'                   => $newNameFile,
                            'estado'                    => 1,
                            'categoria_adjunto_id'      => 1,
                            'fecha_registro'            => $fechaHoy
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/requerimiento/" . $newNameFile, File::get($archivo));
                }
            }
        }
        $ordenesAdjuntos = 0;
        $comprobanteContableAdjuntos = 0;
        $comprobanteBancarioAdjunto = 0;
        $comprobanteBancarioAdjunto = 0;

        if ($adjuntoOrdenesLength > 0) {
            foreach ($requerimiento->adjuntoOrdenes as $archivo) {
                if ($archivo != null) {

                    $fechaHoy = new Carbon();
                    $sufijo = $fechaHoy->format('YmdHis');
                    $file = $archivo->getClientOriginalName();
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $newNameFile = $filename . '_' . $sufijo . '.' . $extension;
                    $ordenesAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                        [
                            'id_requerimiento'          => $requerimiento->id_requerimiento,
                            'archivo'                   => $newNameFile,
                            'estado'                    => 1,
                            'categoria_adjunto_id'      => 2,
                            'fecha_registro'            => $fechaHoy
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/requerimiento/" . $newNameFile, File::get($archivo));
                }
            }
        }
        if ($adjuntoComprobanteContableLength > 0) {
            foreach ($requerimiento->adjuntoComprobanteContable as $archivo) {
                if ($archivo != null) {
                    $fechaHoy = new Carbon();
                    $sufijo = $fechaHoy->format('YmdHis');
                    $file = $archivo->getClientOriginalName();
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $newNameFile = $filename . '_' . $sufijo . '.' . $extension;
                    $comprobanteContableAdjuntos = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                        [
                            'id_requerimiento'          => $requerimiento->id_requerimiento,
                            'archivo'                   => $newNameFile,
                            'estado'                    => 1,
                            'categoria_adjunto_id'      => 3,
                            'fecha_registro'            => $fechaHoy
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/requerimiento/" . $newNameFile, File::get($archivo));
                }
            }
        }
        if ($adjuntoComprobanteBancarioLength > 0) {
            foreach ($requerimiento->adjuntoComprobanteBancario as $archivo) {
                if ($archivo != null) {
                    $fechaHoy = new Carbon();
                    $sufijo = $fechaHoy->format('YmdHis');
                    $file = $archivo->getClientOriginalName();
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $newNameFile = $filename . '_' . $sufijo . '.' . $extension;
                    $comprobanteBancarioAdjunto = DB::table('almacen.alm_req_adjuntos')->insertGetId(
                        [
                            'id_requerimiento'          => $requerimiento->id_requerimiento,
                            'archivo'                   => $newNameFile,
                            'estado'                    => 1,
                            'categoria_adjunto_id'      => 4,
                            'fecha_registro'            => $fechaHoy
                        ],
                        'id_adjunto'
                    );
                    Storage::disk('archivos')->put("logistica/requerimiento/" . $newNameFile, File::get($archivo));
                }
            }
        }


        return response()->json($ordenesAdjuntos);
    }

    public static function guardarAdjuntoNivelDetalleItem($adjuntoDetelleRequerimiento)
    {
        $detalleAdjuntos = 0;
        if (count($adjuntoDetelleRequerimiento) > 0) {
            foreach ($adjuntoDetelleRequerimiento as $adjunto) {
                $fechaHoy = new Carbon();
                $sufijo = $fechaHoy->format('YmdHis');
                // $NameFile = $adjunto['nombre_archivo'];

                $file = $adjunto['archivo']->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $newNameFile = $filename . '_' . $sufijo . '.' . $extension;

                $detalleAdjuntos = DB::table('almacen.alm_det_req_adjuntos')->insertGetId(
                    [
                        'id_detalle_requerimiento'  => $adjunto['id_detalle_requerimiento'],
                        'archivo'                   => $newNameFile,
                        'estado'                    => 1,
                        'fecha_registro'            => $fechaHoy
                    ],
                    'id_adjunto'
                );
                Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $newNameFile, File::get($adjunto['archivo']));
            }
        }
        return response()->json($detalleAdjuntos);
    }

    public function actualizarRequerimiento(Request $request)
    {
        // dd($request->all());
        // exit();  
        $requerimiento = Requerimiento::where("id_requerimiento", $request->id_requerimiento)->first();
        $idEstadoActual = $requerimiento->estado;

        $requerimiento->id_tipo_requerimiento = $request->tipo_requerimiento;
        $requerimiento->id_usuario = Auth::user()->id_usuario;
        $requerimiento->id_rol = $request->id_rol;
        $requerimiento->fecha_requerimiento = $request->fecha_requerimiento;
        $requerimiento->id_periodo = $request->periodo;
        $requerimiento->concepto = strtoupper($request->concepto);
        $requerimiento->id_moneda = $request->moneda;
        $requerimiento->id_proyecto = $request->id_proyecto;
        $requerimiento->observacion = $request->observacion;
        $requerimiento->id_grupo = $request->id_grupo;
        $requerimiento->id_area = $request->id_area;
        $requerimiento->id_prioridad = $request->prioridad;
        $requerimiento->id_empresa = $request->empresa;
        $requerimiento->id_sede = $request->sede;
        $requerimiento->tipo_cliente = $request->tipo_cliente;
        $requerimiento->id_cliente = $request->id_cliente;
        $requerimiento->id_persona = $request->id_persona;
        $requerimiento->direccion_entrega = $request->direccion_entrega;
        $requerimiento->id_cuenta = $request->id_cuenta;
        $requerimiento->nro_cuenta = $request->nro_cuenta;
        $requerimiento->nro_cuenta_interbancaria = $request->nro_cuenta_interbancaria;
        $requerimiento->telefono = $request->telefono;
        $requerimiento->email = $request->email;
        $requerimiento->id_ubigeo_entrega = $request->id_ubigeo_entrega;
        $requerimiento->id_almacen = $request->id_almacen;
        $requerimiento->confirmacion_pago = ($request->tipo_requerimiento == 2 ? ($request->fuente == 2 ? true : false) : true);
        $requerimiento->monto = $request->monto;
        $requerimiento->fecha_entrega = $request->fecha_entrega;
        $requerimiento->id_cc = $request->id_cc;
        $requerimiento->tipo_cuadro = $request->tipo_cuadro;
        $requerimiento->tiene_transformacion = $request->tiene_transformacion ? $request->tiene_transformacion : false;
        $requerimiento->fuente_id = $request->fuente;
        $requerimiento->fuente_det_id = $request->fuente_det;
        // $requerimiento->para_stock_almacen = $request->para_stock_almacen;
        $requerimiento->division_id = $request->division;
        $requerimiento->trabajador_id = $request->id_trabajador;
        if ($idEstadoActual == 3) {
            $requerimiento->estado = 1;
        }
        $requerimiento->save();
        $requerimiento->adjuntoOtrosAdjuntos = $request->archivoAdjuntoRequerimiento1;
        $requerimiento->adjuntoOrdenes = $request->archivoAdjuntoRequerimiento2;
        $requerimiento->adjuntoComprobanteBancario = $request->archivoAdjuntoRequerimiento3;
        $requerimiento->adjuntoComprobanteContable = $request->archivoAdjuntoRequerimiento4;

        $adjuntosRequerimiento = $this->guardarAdjuntoNivelRequerimiento($requerimiento);


        $todoDetalleRequerimiento = DetalleRequerimiento::where("id_requerimiento", $requerimiento->id_requerimiento)->get();
        $idDetalleRequerimientoProcesado = [];
        $count = count($request->descripcion);

        for ($i = 0; $i < $count; $i++) {
            $id = $request->idRegister[$i];
            if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $id)) // es un id con numeros y letras => es nuevo, insertar
            {
                $detalle = new DetalleRequerimiento();
                $detalle->id_requerimiento = $requerimiento->id_requerimiento;
                $detalle->id_tipo_item = $request->tipoItem[$i];
                $detalle->partida = $request->idPartida[$i];
                $detalle->centro_costo_id = $request->idCentroCosto[$i];
                $detalle->part_number = $request->partNumber[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                $detalle->motivo = $request->motivo[$i];
                $detalle->tiene_transformacion = ($request->tiene_transformacion ? $request->tiene_transformacion : false);
                $detalle->fecha_registro = new Carbon();
                $detalle->estado = $requerimiento->id_tipo_requerimiento == 2 ? 19 : 1;
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;
            } else { // es un id solo de numerico => actualiza
                $detalle = DetalleRequerimiento::where("id_detalle_requerimiento", $id)->first();
                $detalle->id_tipo_item = $request->tipoItem[$i];
                $detalle->partida = $request->idPartida[$i] > 0 ? $request->idPartida[$i] : null;
                $detalle->centro_costo_id = $request->idCentroCosto[$i] > 0 ? $request->idCentroCosto[$i] : null;
                $detalle->part_number = $request->partNumber[$i];
                $detalle->descripcion = $request->descripcion[$i];
                $detalle->id_unidad_medida = $request->unidad[$i];
                $detalle->cantidad = $request->cantidad[$i];
                $detalle->precio_unitario = floatval($request->precioUnitario[$i]);
                $detalle->subtotal = floatval($request->cantidad[$i] * $request->precioUnitario[$i]);
                $detalle->motivo = $request->motivo[$i];
                $detalle->tiene_transformacion = ($request->tiene_transformacion ? $request->tiene_transformacion : false);
                // $detalle->fecha_registro = new Carbon();
                $detalle->estado = $requerimiento->id_tipo_requerimiento == 2 ? 19 : 1;
                $detalle->save();
                $detalle->idRegister = $request->idRegister[$i];
                $detalleArray[] = $detalle;

                $idDetalleRequerimientoProcesado[] = $detalle->id_detalle_requerimiento;
            }
        }

        // detalle requerimientos para anular
        foreach ($todoDetalleRequerimiento as $detalleRequerimiento) {
            if (!in_array($detalleRequerimiento->id_detalle_requerimiento, $idDetalleRequerimientoProcesado)) {
                $detalleConAnulidad = DetalleRequerimiento::where("id_detalle_requerimiento", $detalleRequerimiento->id_detalle_requerimiento)->first();
                $detalleConAnulidad->estado = 7;
                $detalleConAnulidad->save();
                // anular adjunto detalle requerimiento
                AdjuntoDetalleRequerimiento::where('id_detalle_requerimiento', '=', $detalleRequerimiento->id_detalle_requerimiento)
                    ->update(['estado' => 7]);
            }

            // anular adjuntos de detalle requerimiento
        }


        //si existe nuevos adjuntos de nuevos item

        if (isset($detalleArray) && count($detalleArray) > 0) {
            $adjuntoDetelleRequerimiento = [];
            for ($i = 0; $i < count($detalleArray); $i++) {
                $archivos = $request->{"archivoAdjuntoItem" . $detalleArray[$i]['idRegister']};
                if (isset($archivos)) {
                    foreach ($archivos as $archivo) {
                        if ($archivo != null) {
                            $adjuntoDetelleRequerimiento[] = [
                                'id_detalle_requerimiento' => $detalleArray[$i]['id_detalle_requerimiento'],
                                'nombre_archivo' => $archivo->getClientOriginalName(),
                                'archivo' => $archivo
                            ];
                        }
                    }
                }
            }
            if (count($adjuntoDetelleRequerimiento) > 0) {
                $this->guardarAdjuntoNivelDetalleItem($adjuntoDetelleRequerimiento);
            }
        }

        // si existen adjuntos por eliminar
        if (count($request->archivoAdjuntoRequerimientoToDelete) > 0) {
            foreach ($request->archivoAdjuntoRequerimientoToDelete as $id) {
                if (preg_match('/^[0-9]+$/', $id)) {
                    AdjuntoRequerimiento::where('id_adjunto', '=', $id)
                    ->update(['estado' => 7]);
                }
            }
        }
        if (count($request->archivoAdjuntoItemToDelete) > 0) {
            foreach ($request->archivoAdjuntoItemToDelete as $id) {
                if (preg_match('/^[0-9]+$/', $id)) {
                    AdjuntoDetalleRequerimiento::where('id_adjunto', '=', $id)
                    ->update(['estado' => 7]);
                }
            }
        }



        $nombreCompletoUsuario = Usuario::find(Auth::user()->id_usuario)->trabajador->postulante->persona->nombre_completo;

        if ($idEstadoActual == 3) {
            $trazabilidad = new Trazabilidad();
            $trazabilidad->id_requerimiento = $request->id_requerimiento;
            $trazabilidad->id_usuario = Auth::user()->id_usuario;
            $trazabilidad->accion = 'SUSTENTADO';
            $trazabilidad->descripcion = 'Sustentado por ' . $nombreCompletoUsuario ? $nombreCompletoUsuario : '';
            $trazabilidad->fecha_registro = new Carbon();
            $trazabilidad->save();

            $idDocumento = Documento::getIdDocAprob($request->id_requerimiento, 1);
            $ultimoVoBo = Aprobacion::getUltimoVoBo($idDocumento);
            $aprobacion = Aprobacion::where("id_aprobacion", $ultimoVoBo->id_aprobacion)->first();
            $aprobacion->tiene_sustento = true;
            $aprobacion->save();

            // TODO:  enviaar notificación al usuario aprobante, asuto => se levanto la observación 
            $idRolPrimerAprobante = 0;
            $operaciones = Operacion::getOperacion(1, $request->tipo_requerimiento, $request->id_grupo, $request->division, $request->prioridad);
            $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];
            foreach ($flujoTotal as $flujo) {
                if ($flujo->orden == 1) {
                    $idRolPrimerAprobante = $flujo->id_rol;
                }
            }
            if ($idRolPrimerAprobante > 0) {
                $usuariosList = Usuario::getAllIdUsuariosPorRol($idRolPrimerAprobante);
                foreach ($usuariosList as $idUsuario) {
                    $correoUsuario = Usuario::find($idUsuario)->trabajador->postulante->persona->email;
                    if (!empty($correoUsuario)) {
                        $this->enviarNotificacionPorActualizacion($request, $correoUsuario, $requerimiento);
                    }
                }
            }
        }


        return response()->json(['id_requerimiento' => $requerimiento->id_requerimiento, 'codigo' => $requerimiento->codigo]);
    }

    private function enviarNotificacionPorActualizacion($request, $correoUsuario, $requerimiento)
    {
        $nombreCompletoUsuario = Auth::user()->trabajador->postulante->persona->nombre_completo;
        $payload = [
            'id_empresa' => $request->empresa,
            'email_destinatario' => $correoUsuario,
            'titulo' => 'El requerimiento ' . $requerimiento->codigo . ' fue sustentado por ' . ($nombreCompletoUsuario ? $nombreCompletoUsuario : 'el usuario') . ', se requiere su revisión/aprobación',
            'mensaje' => 'El requerimiento ' . $requerimiento->codigo . ' fue sustentado por ' . ($nombreCompletoUsuario ? $nombreCompletoUsuario : 'el usuario') . ', se requiere su revisión/aprobación. Información adicional del requerimiento:' .
                '<ul>' .
                '<li> Concepto/Motivo: ' . $requerimiento->concepto . '</li>' .
                '<li> Tipo de requerimiento: ' . $requerimiento->tipo->descripcion . '</li>' .
                '<li> Fecha limite de entrega: ' . $requerimiento->fecha_entrega . '</li>' .
                '<li> Creado por: ' . ($nombreCompletoUsuario ? $nombreCompletoUsuario : '') . '</li>' .
                '</ul>' .
                '<p> *Este correo es generado de manera automática, por favor no responder.</p> 
                            <br> Saludos <br> Módulo de Logística <br> SYSTEM AGILE'
        ];

        if (strlen($correoUsuario) > 0) {
            $estado_envio = NotificacionHelper::enviarEmail($payload);
        }
    }

    public function anularRequerimiento($idRequerimiento)
    {
        DB::beginTransaction();
        try {
            $requerimiento = Requerimiento::find($idRequerimiento);
            $todoDetalleRequerimiento = DetalleRequerimiento::where("id_requerimiento", $requerimiento->id_requerimiento)->get();
            $tipoMensaje= 'info';

            $transferencia = Transferencia::where("id_requerimiento", $idRequerimiento)->first();

            if (isset($transferencia)) {
                if ($transferencia->estado == 1) { // habilitado para ser anulada la transferencia y el requerimiento
                    // anular trasferencia
                    $transferencia->estado = 7;
                    $transferencia->save();
                    // anular requerimiento
                    $requerimiento->estado = 7;
                    $requerimiento->save();
                    // anular detalle requerimiento
                    foreach ($todoDetalleRequerimiento as $detalleRequerimiento) {
                        $detalle = DetalleRequerimiento::where("id_detalle_requerimiento", $detalleRequerimiento->id_detalle_requerimiento)->first();
                        $detalle->estado = 7;
                        $detalle->save();
                    }

                    $mensaje = 'Se anulo el requerimiento ' . $requerimiento->codigo . ' y su transferencia fue anulada';
                    $tipoMensaje= 'success';
                } else { // no se puede anular un requerimiento con transferencia procesada
                    $mensaje = 'No es posible anulr el requerimiento ' . $requerimiento->codigo . ' tiene una transferencia procesada';
                    $tipoMensaje= 'warning';

                }
            } else {
                // anular requerimiento
                $requerimiento->estado = 7;
                $requerimiento->save();
                // anular detalle requerimiento
                foreach ($todoDetalleRequerimiento as $detalleRequerimiento) {
                    $detalle = DetalleRequerimiento::where("id_detalle_requerimiento", $detalleRequerimiento->id_detalle_requerimiento)->first();
                    $detalle->estado = 7;
                    $detalle->save();
                }
                $mensaje = 'Se anulo el requerimiento ' . $requerimiento->codigo;
                $tipoMensaje= 'success';

            }

            // DB::commit();
            return response()->json(['estado' => $requerimiento->estado, 'mensaje' => $mensaje,'tipo_mensaje'=>$tipoMensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => 0, 'mensaje' => 'Hubo un problema al anular el requerimiento. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage(),'tipo_mensaje'=>$tipoMensaje]);
        }
    }

    public function listarRequerimientosElaborados(Request $request)
    {
        $mostrar = $request->meOrAll;
        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $division = $request->idDivision;
        $fechaRegistroDesde = $request->fechaRegistroDesde;
        $fechaRegistroHasta = $request->fechaRegistroHasta;
        $idEstado = $request->idEstado;
        // Debugbar::info($division);

        $requerimientos = Requerimiento::with('detalle')->leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
            ->leftJoin('administracion.adm_empresa', 'alm_req.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'alm_req.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')
            // ->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob')

            ->select(
                'alm_req.id_requerimiento',
                'adm_documentos_aprob.id_doc_aprob',
                'alm_req.codigo',
                'alm_req.id_tipo_requerimiento',
                'alm_req.id_usuario',
                'alm_req.id_rol',
                'alm_req.fecha_requerimiento',
                'alm_req.fecha_entrega',
                'alm_req.id_periodo',
                'adm_periodo.descripcion as descripcion_periodo',
                'alm_req.concepto',
                'alm_req.id_grupo',
                'alm_req.id_empresa',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as tipo_documento_identidad',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.estado',
                'alm_req.id_area',
                'alm_req.id_prioridad',
                'alm_req.id_presupuesto',
                'alm_req.id_moneda',
                'alm_req.*',
                'adm_estado_doc.estado_doc',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'adm_prioridad.descripcion AS priori',
                'sis_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                'sis_moneda.simbolo AS simbolo_moneda',
                'alm_req.fecha_registro',
                'alm_req.division_id',
                'division.descripcion as division',
                DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario"),
                DB::raw("(SELECT COUNT(adm_aprobacion.id_aprobacion) 
                FROM administracion.adm_aprobacion 
                WHERE   adm_aprobacion.id_vobo = 3 AND
                adm_aprobacion.tiene_sustento = true AND adm_aprobacion.id_doc_aprob = adm_documentos_aprob.id_doc_aprob) AS cantidad_sustentos"),
                DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario) 
                FROM almacen.alm_det_req 
                WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
                alm_det_req.estado != 7) AS monto_total")

            )

            ->when(($mostrar === 'ME'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                return $query->whereRaw('alm_req.id_usuario = ' . $idUsuario);
            })
            ->when(($mostrar === 'ALL'), function ($query) {
                return $query->whereRaw('alm_req.id_usuario > 0');
            })
            ->when(($mostrar === 'REVISADO_APROBADO'), function ($query) {
                $idUsuario = Auth::user()->id_usuario;
                $query->leftJoin('administracion.adm_aprobacion', 'adm_aprobacion.id_doc_aprob', '=', 'adm_documentos_aprob.id_doc_aprob');
                return $query->whereRaw('adm_aprobacion.id_usuario = ' . $idUsuario . ' and adm_aprobacion.id_vobo = 1 ');
            })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('alm_req.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('alm_req.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($division) >0), function ($query)  use ($division) {
                return $query->whereRaw('alm_req.division_id = ' . $division);
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta == 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde) {
                return $query->where('alm_req.fecha_requerimiento' ,'>=',$fechaRegistroDesde); 
            })
            ->when((($fechaRegistroDesde == 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroHasta) {
                return $query->where('alm_req.fecha_requerimiento' ,'<=',$fechaRegistroHasta); 
            })
            ->when((($fechaRegistroDesde != 'SIN_FILTRO') and ($fechaRegistroHasta != 'SIN_FILTRO')), function ($query) use($fechaRegistroDesde,$fechaRegistroHasta) {
                return $query->whereBetween('alm_req.fecha_requerimiento' ,[$fechaRegistroDesde,$fechaRegistroHasta]); 
            })

            ->when((intval($idEstado) > 0), function ($query)  use ($idEstado) {
                return $query->whereRaw('alm_req.estado = ' . $idEstado);
            });

        return datatables($requerimientos)
            ->filterColumn('nombre_usuario', function ($query, $keyword) {
                $keywords = trim(strtoupper($keyword));
                $query->whereRaw("UPPER(CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno)) LIKE ?", ["%{$keywords}%"]);
            })
            ->filterColumn('alm_req.fecha_entrega', function ($query, $keyword) {
                try {
                    $keywords = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->where('alm_req.fecha_entrega', $keywords);
                } catch (\Throwable $th) {
                }
            })
            ->filterColumn('alm_req.fecha_registro', function ($query, $keyword) {
                try {
                    $desde = Carbon::createFromFormat('d-m-Y', trim($keyword))->hour(0)->minute(0)->second(0);
                    $hasta = Carbon::createFromFormat('d-m-Y', trim($keyword));
                    $query->whereBetween('alm_req.fecha_registro', [$desde, $hasta->addDay()->addSeconds(-1)]);
                } catch (\Throwable $th) {
                }
            })
            
            // ->filterColumn('monto_total', function ($query, $keyword) {
            //     $query->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento');
            //     $query->whereRaw('SUM(almace.alm_det_req.cantidad * alm_det_req.precio_unitario) = '.$keyword);
            // })
            // ->filterColumn('monto_total', function ($query, $keyword) {
            //     $query->whereRaw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario) 
            // FROM almacen.alm_det_req 
            // WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
            // alm_det_req.estado != 7)");
            // })
            ->rawColumns(['termometro'])->toJson();
    }

    function viewLista()
    {
        $gruposUsuario = Auth::user()->getAllGrupo();
        $grupos = Grupo::mostrar();
        $roles = Auth::user()->getAllRol(); //$this->userSession()['roles'];
        $empresas = Empresa::mostrar();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();
        $estados = Estado::mostrar();


        return view('logistica/requerimientos/lista_requerimientos', compact('periodos', 'gruposUsuario', 'grupos', 'roles', 'empresas', 'prioridades','estados'));
    }


    public function viewAprobar(Request $request)
    {
        $gruposUsuario = Auth::user()->getAllGrupo();
        $grupos = Grupo::mostrar();
        $roles = Auth::user()->getAllRol();
        $empresas = Empresa::mostrar();
        $periodos = Periodo::mostrar();
        $prioridades = Prioridad::mostrar();


        return view('logistica/requerimientos/aprobar_requerimiento', compact('periodos', 'gruposUsuario', 'grupos', 'roles', 'empresas', 'prioridades'));
    }


    public function listadoAprobacion(Request $request)
    {

        $idUsuarioAprobante = Auth::user()->id_usuario;
        $allGrupo = Auth::user()->getAllGrupo();
        $idGrupoList = [];
        foreach ($allGrupo as $grupo) {
            $idGrupoList[] = $grupo->id_grupo; // lista de id_rol del usuario en sesion
        }

        $allRol = Auth::user()->getAllRol();
        $idRolUsuarioList = [];
        foreach ($allRol as  $rol) {
            $idRolUsuarioList[] = $rol->id_rol;
        }

        $divisiones = Division::mostrar();
        $idDivisionList = [];
        foreach ($divisiones as $value) {
            $idDivisionList[] = $value->id_division; //lista de id del total de divisiones 
        }

        $divisionUsuarioNroOrdenUno = Division::mostrarDivisionUsuarioNroOrdenUno();
        $idDivisionUsuarioList = [];
        foreach ($divisionUsuarioNroOrdenUno as $value) {
            $idDivisionUsuarioList[] = $value->id_division; //lista de id_division al que pertenece el usuario 
        }


        $idEmpresa = $request->idEmpresa;
        $idSede = $request->idSede;
        $idGrupo = $request->idGrupo;
        $idPrioridad = $request->idPrioridad;
        $usuarioSoloSiCorrespondeAprobacion = false;
        // $compra =(new LogisticaController)->get_tipo_requerimiento('Compra');
        // $tipo_requerimiento = 3; // Bienes y Servicios
        $tipo_documento = 1; // Requerimientos

        $requerimientos = Requerimiento::join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('almacen.tipo_cliente', 'alm_req.tipo_cliente', '=', 'tipo_cliente.id_tipo_cliente')
            ->leftJoin('almacen.alm_almacen', 'alm_req.id_almacen', '=', 'alm_almacen.id_almacen')
            ->leftJoin('configuracion.sis_grupo', 'sis_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_req.id_sede')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contrib', 'adm_empresa.id_contribuyente', '=', 'contrib.id_contribuyente')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            // ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            // ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            // ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            // ->leftJoin('proyectos.proy_op_com', 'proy_op_com.id_op_com', '=', 'alm_req.id_op_com')
            // ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->leftJoin('comercial.com_cliente', 'alm_req.id_cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('configuracion.ubi_dis', 'alm_req.id_ubigeo_entrega', '=', 'ubi_dis.id_dis')
            ->leftJoin('configuracion.ubi_prov', 'ubi_dis.id_prov', '=', 'ubi_prov.id_prov')
            ->leftJoin('configuracion.ubi_dpto', 'ubi_prov.id_dpto', '=', 'ubi_dpto.id_dpto')
            ->leftJoin('configuracion.sis_moneda', 'alm_req.id_moneda', '=', 'sis_moneda.id_moneda')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_periodo', 'alm_req.id_periodo', '=', 'adm_periodo.id_periodo')
            ->leftJoin('configuracion.sis_rol', 'alm_req.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('administracion.adm_documentos_aprob', 'alm_req.id_requerimiento', '=', 'adm_documentos_aprob.id_doc')
            ->leftJoin('administracion.division', 'division.id_division', '=', 'alm_req.division_id')

            ->select(
                'alm_req.id_requerimiento',
                'adm_documentos_aprob.id_doc_aprob',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'sis_moneda.descripcion as desrcipcion_moneda',
                'sis_moneda.simbolo AS simbolo_moneda',
                'alm_req.id_periodo',
                'adm_periodo.descripcion as descripcion_periodo',
                'alm_req.id_prioridad',
                'adm_prioridad.descripcion as descripcion_prioridad',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'adm_estado_doc.bootstrap_color',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'sis_grupo.descripcion as descripcion_grupo',
                'contrib.razon_social as razon_social_empresa',
                'sis_sede.codigo as codigo_sede_empresa',
                'adm_empresa.logo_empresa',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'alm_req.id_usuario',
                DB::raw("CONCAT(rrhh_perso.nombres, ' ',rrhh_perso.apellido_paterno, ' ', rrhh_perso.apellido_materno)  AS nombre_usuario"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'sis_rol.descripcion as descripcion_rol',
                // 'rrhh_rol.id_rol_concepto',
                // 'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                // 'adm_area.descripcion AS area_descripcion',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.fecha_registro',
                'alm_req.id_sede',
                'alm_req.tipo_cliente as id_tipo_cliente',
                'tipo_cliente.descripcion as descripcion_tipo_cliente',
                'alm_req.id_ubigeo_entrega',
                DB::raw("(ubi_dis.descripcion) || ' ' || (ubi_prov.descripcion) || ' ' || (ubi_dpto.descripcion)  AS name_ubigeo"),
                'alm_req.id_almacen',
                'alm_almacen.descripcion as descripcion_almacen',
                'alm_req.monto',
                'alm_req.fecha_entrega',
                'alm_req.division_id',
                'division.descripcion as division',
                DB::raw("(SELECT SUM(alm_det_req.cantidad * alm_det_req.precio_unitario) 
                FROM almacen.alm_det_req 
                WHERE   alm_det_req.id_requerimiento = alm_req.id_requerimiento AND
                alm_det_req.estado != 7) AS monto_total")
            )
            // ->where([
            // ['alm_req.id_tipo_requerimiento', '=', $tipo_requerimiento],
            // ['alm_req.id_requerimiento', '=', '262']
            // ['alm_req.codigo', '=', 'RC-210106']
            // ['alm_req.tipo_cliente','=',$uso_administracion] // uso administracion
            // ['alm_req.estado', '!=', 2], 
            // ['alm_req.estado', '!=', 3], 
            // ['alm_req.estado', '!=', 7] 
            // ])
            ->whereNotIn('alm_req.estado', [3, 7])
            ->whereNotIn('alm_req.id_tipo_requerimiento', [1, 2, 3])
            // ->when((count($idDivisionUsuarioList) > 0), function ($query)  use ($idDivisionUsuarioList) {
            //     return $query->whereRaw('alm_req.division_id in (' . implode(",", $idDivisionUsuarioList) . ')');
            // })
            ->when((intval($idEmpresa) > 0), function ($query)  use ($idEmpresa) {
                return $query->whereRaw('alm_req.id_empresa = ' . $idEmpresa);
            })
            ->when((intval($idSede) > 0), function ($query)  use ($idSede) {
                return $query->whereRaw('alm_req.id_sede = ' . $idSede);
            })
            ->when((intval($idGrupo) > 0), function ($query)  use ($idGrupo) {
                return $query->whereRaw('sis_grupo.id_grupo = ' . $idGrupo);
            })
            ->when((intval($idPrioridad) > 0), function ($query)  use ($idPrioridad) {
                return $query->whereRaw('alm_req.id_prioridad = ' . $idPrioridad);
            })
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();

        // return $requerimientos;
        $payload = [];
        $operacion_selected = 0;
        $flujo_list_selected = [];

        $pendiente_aprobacion = [];

        $list_req = [];
        foreach ($requerimientos as $element) {

            if (in_array($element->id_grupo, $idGrupoList) == true) {
                // Debugbar::info($element->id_grupo);
                $idDocumento = $element->id_doc_aprob;
                $id_grupo_req = $element->id_grupo;
                $id_tipo_requerimiento_req = $element->id_tipo_requerimiento;
                $id_prioridad_req = $element->id_prioridad;
                $estado_req = $element->estado;
                $division_id = $element->division_id;


                $operaciones = Operacion::getOperacion(1, $id_tipo_requerimiento_req, $id_grupo_req, $division_id, $id_prioridad_req);
                // Debugbar::info($operaciones[0]->id_operacion);


                $flujoTotal = Flujo::getIdFlujo($operaciones[0]->id_operacion)['data'];

                $tamañoFlujo = $flujoTotal ? count($flujoTotal) : 0;

                $voboList = Aprobacion::getVoBo($idDocumento); // todas las vobo del documento
                $cantidadAprobacionesRealizadas = Aprobacion::getCantidadAprobacionesRealizadas($idDocumento);
                $ultimoVoBo = Aprobacion::getUltimoVoBo($idDocumento);
                // Debugbar::info($cantidadAprobacionesRealizadas);

                $nextFlujo = [];
                $nextIdRolAprobante = 0;
                $nextIdFlujo = 0;
                $nextIdOperacion = 0;
                $nextNroOrden = 0;
                $aprobacionFinalOPendiente = '';
                $cantidadConSiguienteAprobacion=false;
                $tieneRolConSiguienteAprobacion='';

                if ($cantidadAprobacionesRealizadas > 0) {

                    // si existe data => evaluar si tiene aprobacion / Rechazado / observado.
                    if (in_array($ultimoVoBo->id_vobo, [1, 5])) { // revisado o aprobado
                        // next flujo y rol aprobante
                        $ultimoIdFlujo = $ultimoVoBo->id_flujo;

                        foreach ($flujoTotal as $key => $flujo) {
                            if ($flujo->id_flujo == $ultimoIdFlujo) {
                                $nroOrdenUltimoFlujo = $flujo->orden;
                                if ($nroOrdenUltimoFlujo != $tamañoFlujo) { // get next id_flujo
                                    foreach ($flujoTotal as $key => $flujo) {
                                        if ($flujo->estado == 1) {
                                            if ($flujo->orden == $nroOrdenUltimoFlujo + 1) {
                                                $nextFlujo = $flujo;
                                                $nextIdFlujo = $flujo->id_flujo;
                                                $nextIdOperacion = $flujo->id_operacion;
                                                $nextIdRolAprobante = $flujo->id_rol;
                                                $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($ultimoVoBo->id_vobo == 3 && $ultimoVoBo->id_sustentacion != null) { //observado con sustentacion
                        foreach ($flujoTotal as $flujo) {
                            if ($flujo->orden == 1) {
                                // Debugbar::info($flujo);
                                $nextFlujo = $flujo;
                                $nextNroOrden = $flujo->orden;
                                $nextIdOperacion = $flujo->id_operacion;
                                $nextIdFlujo = $flujo->id_flujo;
                                $nextIdRolAprobante = $flujo->id_rol;
                                $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?

                            }
                        }
                    }
                } else { //  no tiene aprobaciones, entonces es la PRIMERA APROBACIÓN de este req.
                    // tiene observación?

                    //obtener rol del flujo de aprobacion con orden #1 y comprar con el rol del usuario en sesion
                    foreach ($flujoTotal as $flujo) {
                        if ($flujo->orden == 1) {
                            // Debugbar::info($flujo);
                            $nextFlujo = $flujo;
                            $nextNroOrden = $flujo->orden;
                            $nextIdOperacion = $flujo->id_operacion;
                            $nextIdFlujo = $flujo->id_flujo;
                            $nextIdRolAprobante = $flujo->id_rol;
                            $aprobacionFinalOPendiente = $flujo->orden == $tamañoFlujo ? 'APROBACION_FINAL' : 'PENDIENTE'; // NEXT NRO ORDEN == TAMAÑO FLUJO?

                        }
                    }
                }
                $numeroOrdenSiguienteAprobacion=0;
                foreach ($flujoTotal as $flujo) {
                    if ($flujo->id_operacion == $nextIdOperacion) {
                        if($flujo->orden == (intval($nextNroOrden)+1)){ // si existe una siguiente aprobacion (nro orden + 1 ) 
                            if(in_array($flujo->id_rol, $idRolUsuarioList) == true){
                                $cantidadConSiguienteAprobacion=true;
                                $numeroOrdenSiguienteAprobacion= $flujo->orden;
                            }
                            
                        }
                        
                    }
                }

                if($cantidadConSiguienteAprobacion ==true){
                    $tieneRolConSiguienteAprobacion=true;
                }else{
                    $tieneRolConSiguienteAprobacion=false;    
                }
      
                if ((in_array($nextIdRolAprobante, $idRolUsuarioList)) == true) {
                    if ($nextNroOrden == 1) {
                        // fitlar por division
                        if (in_array($element->division_id, $idDivisionUsuarioList) == true) {
                            $payload[] = [
                                'termometro' => $element->termometro,
                                'id_requerimiento' => $element->id_requerimiento,
                                'id_tipo_requerimiento' => $element->id_tipo_requerimiento,
                                'tipo_requerimiento' => $element->tipo_requerimiento,
                                'id_tipo_cliente' => $element->id_tipo_cliente,
                                'descripcion_tipo_cliente' => $element->descripcion_tipo_cliente,
                                'id_prioridad' => $element->id_prioridad,
                                'descripcion_prioridad' => $element->descripcion_prioridad,
                                'id_periodo' => $element->id_periodo,
                                'descripcion_periodo' => $element->descripcion_periodo,
                                'codigo' => $element->codigo,
                                'concepto' => $element->concepto,
                                'id_empresa' => $element->id_empresa,
                                'razon_social_empresa' => $element->razon_social_empresa,
                                'codigo_sede_empresa' => $element->codigo_sede_empresa,
                                'logo_empresa' => $element->logo_empresa,
                                'id_grupo' => $element->id_grupo,
                                'descripcion_grupo' => $element->descripcion_grupo,
                                'fecha_requerimiento' => $element->fecha_requerimiento,
                                'observacion' => $element->observacion,
                                'name_ubigeo' => $element->name_ubigeo,
                                'id_moneda' => $element->id_moneda,
                                'simbolo_moneda' => $element->simbolo_moneda,
                                'desrcipcion_moneda' => $element->desrcipcion_moneda,
                                'monto' => $element->monto,
                                'fecha_registro' => $element->fecha_registro,
                                'fecha_entrega' => $element->fecha_entrega,
                                'id_usuario' => $element->id_usuario,
                                'id_rol' => $element->id_rol,
                                'descripcion_rol' => $element->descripcion_rol,
                                'usuario' => $element->usuario,
                                'nombre_usuario' => $element->nombre_usuario,
                                'id_almacen' => $element->id_almacen,
                                'descripcion_almacen' => $element->descripcion_almacen,
                                'cantidad_aprobados_total_flujo' => ($cantidadAprobacionesRealizadas) . '/' . ($tamañoFlujo),
                                'aprobaciones' => $voboList,
                                'pendiente_aprobacion' => $pendiente_aprobacion,
                                // 'observaciones' => $observacion_list,
                                'observaciones' => [],
                                'estado' => $element->estado,
                                'estado_doc' => $element->estado_doc,
                                'division' => $element->division,
                                'id_flujo' => $nextIdFlujo,
                                'id_usuario_aprobante' => $idUsuarioAprobante,
                                'id_rol_aprobante' => $nextIdRolAprobante,
                                'aprobacion_final_o_pendiente' => $aprobacionFinalOPendiente,
                                'id_doc_aprob' => $idDocumento,
                                'monto_total' => $element->monto_total,
                                'idOperacion'=>$nextIdOperacion,
                                'tieneRolConSiguienteAprobacion'=>$tieneRolConSiguienteAprobacion

                            ];
                        }
                    } else {
                        $payload[] = [
                            'termometro' => $element->termometro,
                            'id_requerimiento' => $element->id_requerimiento,
                            'id_tipo_requerimiento' => $element->id_tipo_requerimiento,
                            'tipo_requerimiento' => $element->tipo_requerimiento,
                            'id_tipo_cliente' => $element->id_tipo_cliente,
                            'descripcion_tipo_cliente' => $element->descripcion_tipo_cliente,
                            'id_prioridad' => $element->id_prioridad,
                            'descripcion_prioridad' => $element->descripcion_prioridad,
                            'id_periodo' => $element->id_periodo,
                            'descripcion_periodo' => $element->descripcion_periodo,
                            'codigo' => $element->codigo,
                            'concepto' => $element->concepto,
                            'id_empresa' => $element->id_empresa,
                            'razon_social_empresa' => $element->razon_social_empresa,
                            'codigo_sede_empresa' => $element->codigo_sede_empresa,
                            'logo_empresa' => $element->logo_empresa,
                            'id_grupo' => $element->id_grupo,
                            'descripcion_grupo' => $element->descripcion_grupo,
                            'fecha_requerimiento' => $element->fecha_requerimiento,
                            'observacion' => $element->observacion,
                            'name_ubigeo' => $element->name_ubigeo,
                            'id_moneda' => $element->id_moneda,
                            'simbolo_moneda' => $element->simbolo_moneda,
                            'desrcipcion_moneda' => $element->desrcipcion_moneda,
                            'monto' => $element->monto,
                            'fecha_registro' => $element->fecha_registro,
                            'fecha_entrega' => $element->fecha_entrega,
                            'id_usuario' => $element->id_usuario,
                            'id_rol' => $element->id_rol,
                            'descripcion_rol' => $element->descripcion_rol,
                            'usuario' => $element->usuario,
                            'nombre_usuario' => $element->nombre_usuario,
                            'id_almacen' => $element->id_almacen,
                            'descripcion_almacen' => $element->descripcion_almacen,
                            'cantidad_aprobados_total_flujo' => ($cantidadAprobacionesRealizadas) . '/' . ($tamañoFlujo),
                            'aprobaciones' => $voboList,
                            'pendiente_aprobacion' => $pendiente_aprobacion,
                            // 'observaciones' => $observacion_list,
                            'observaciones' => [],
                            'estado' => $element->estado,
                            'estado_doc' => $element->estado_doc,
                            'division' => $element->division,
                            'id_flujo' => $nextIdFlujo,
                            'id_usuario_aprobante' => $idUsuarioAprobante,
                            'id_rol_aprobante' => $nextIdRolAprobante,
                            'aprobacion_final_o_pendiente' => $aprobacionFinalOPendiente,
                            'id_doc_aprob' => $idDocumento,
                            'monto_total' => $element->monto_total,
                            'idOperacion'=>$nextIdOperacion,
                            'tieneRolConSiguienteAprobacion'=>$tieneRolConSiguienteAprobacion
 
                        ];
                    }
                }
            }
        }


        $output = ['data' => $payload];
        return $output;
    }


    public function registrarRespuesta($accion, $idFlujo, $idDocumento, $idUsuario,$comentario, $idRolAprobante){
        $aprobacion = new Aprobacion();
        $aprobacion->id_flujo = $idFlujo;
        $aprobacion->id_doc_aprob = $idDocumento;
        $aprobacion->id_usuario = $idUsuario;
        $aprobacion->id_vobo = $accion;
        $aprobacion->fecha_vobo = new Carbon();
        $aprobacion->detalle_observacion = $comentario;
        $aprobacion->id_rol = $idRolAprobante;
        $aprobacion->tiene_sustento = false;
        $aprobacion->save();

        return $aprobacion;

    }

    public function registrarTrazabilidad($idRequerimiento,$aprobacionFinalOPendiente, $idUsuario, $nombreCompletoUsuarioRevisaAprueba, $accion){
        $trazabilidad = new Trazabilidad();
        $trazabilidad->id_requerimiento = $idRequerimiento;
        $trazabilidad->id_usuario = $idUsuario;
        switch ($accion) {
            case '1':
                if ($aprobacionFinalOPendiente == 'APROBACION_FINAL') {
                    $trazabilidad->accion = 'APROBADO';
                    $trazabilidad->descripcion = 'Aprobado por ';
                }
                break;
            case '2':
                $trazabilidad->accion = 'RECHAZADO';
                $trazabilidad->descripcion = 'Rechazado por ';
                break;
            case '3':
                $trazabilidad->accion = 'OBSERVADO';
                $trazabilidad->descripcion = 'Observado por ';

                break;
            case '5':
                $trazabilidad->accion = 'REVISADO';
                $trazabilidad->descripcion = 'Revisado por ';

                break;
        }
        $trazabilidad->descripcion .=  $nombreCompletoUsuarioRevisaAprueba ?? '';
        $trazabilidad->fecha_registro = new Carbon();
        $trazabilidad->save();

        return $trazabilidad;
    
    }

    public function actualizarEstadoRequerimiento($accion,$requerimiento,$aprobacionFinalOPendiente){
        switch ($accion) {
            case '1':
                if ($aprobacionFinalOPendiente == 'APROBACION_FINAL') {
                    $requerimiento->estado = 2;
                }
                break;
            case '2':
                $requerimiento->estado = 7;
                $detalleRequerimiento = DetalleRequerimiento::where("id_requerimiento", $requerimiento->id_requerimiento)->get();
                foreach ($detalleRequerimiento as $detalle) {
                    $detalle->estado = 7;
                    $detalle->save();
                }
                break;
            case '3':
                $requerimiento->estado = 3;
                break;
            case '5':
                $requerimiento->estado = 12;
                break;
        }
        $requerimiento->save();
    }


    public function guardarRespuesta(Request $request)
    {

        DB::beginTransaction();
        try {

            $accion = $request->accion;
            // $comentario = $request->comentario;
            // $idRequerimiento = $request->idRequerimiento;
            // $idDocumento = $request->idDocumento;
            // $idUsuario = $request->idUsuario;
            // $idRolAprobante = $request->idRolAprobante;
            // $idFlujo = $request->idFlujo;
            // $aprobacionFinalOPendiente = $request->aprobacionFinalOPendiente;
            // tieneRolConSiguienteAprobacion = $request->tieneRolConSiguienteAprobacion;
            // idOperacion = $request->idOperacion;
            $nombreCompletoUsuarioRevisaAprueba = Usuario::find($request->idUsuario)->trabajador->postulante->persona->nombre_completo;

            if ($request->aprobacionFinalOPendiente == 'PENDIENTE') {
                if ($accion == 1) {
                    $accion = 5; // Revisado
                }
            }
            // agregar vobo (1= aprobado, 2= rechazado, 3=observado, 5=Revisado)
            $aprobacion= $this->registrarRespuesta($accion, $request->idFlujo, $request->idDocumento,$request->idUsuario,$request->comentario, $request->idRolAprobante);

            // $requerimiento = Requerimiento::where("id_requerimiento", $idRequerimiento)->first();
            $requerimiento = Requerimiento::find($request->idRequerimiento);
            $detalleRequerimiento = $requerimiento->detalle;
            $montoTotal = 0;
            foreach ($detalleRequerimiento as $item) {
                $montoTotal += $item->cantidad * $item->precio_unitario;
            }

            $nombreCompletoUsuarioCreador = $requerimiento->creadoPor->trabajador->postulante->persona->nombre_completo;

            $this->actualizarEstadoRequerimiento($accion,$requerimiento,$request->aprobacionFinalOPendiente);

            $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$request->aprobacionFinalOPendiente,$request->idUsuario, $nombreCompletoUsuarioRevisaAprueba, $accion);

            $this->enviarNotificacionPorAprobacion($requerimiento,$request->comentario,$nombreCompletoUsuarioCreador,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);
            
            $accionNext=0;
            $aprobacionFinalOPendiente='';

            if($request->tieneRolConSiguienteAprobacion == true){ // si existe un siguiente flujo de aprobacion con el mismo rol
                if($accion==1 || $accion ==5){ // si accion es revisar/aprobar, buscar siguientes aprobaciones con mismo rol de usuario para auto aprobación 

                    $allRol = Auth::user()->getAllRol();
                    $idRolUsuarioList = [];
                    foreach ($allRol as  $rol) {
                        $idRolUsuarioList[] = $rol->id_rol;
                    }
    
                    $flujoTotal = Flujo::getIdFlujo($request->idOperacion)['data'];
                    $tamañoFlujo = $flujoTotal ? count($flujoTotal) : 0;
    
                    $ordenActual=0;
                    foreach ($flujoTotal as $flujo) {
                        if($flujo->id_flujo == $request->idFlujo){
                            $ordenActual=$flujo->orden;
                        }
                    }

                    if($ordenActual>0){
                        $i=1;
                        foreach ($flujoTotal as $flujo) {
                            if($i<=$tamañoFlujo){
                                if($flujo->orden == (intval($ordenActual)+$i)){
                                    if(in_array($flujo->id_rol, $idRolUsuarioList) == true){
                                        // guardar aprobación
                                        if($flujo->orden ==$tamañoFlujo ){
                                            $accionNext =1;
                                            $aprobacionFinalOPendiente='APROBACION_FINAL';
                                        }else{
                                            $accionNext =5;
                                            $aprobacionFinalOPendiente='PENDIENTE';

                                        }
                                        $aprobacion= $this->registrarRespuesta($accionNext, $flujo->id_flujo, $request->idDocumento,$request->idUsuario,$request->comentario, $flujo->id_rol);
                                        $trazabilidad= $this->registrarTrazabilidad($request->idRequerimiento,$aprobacionFinalOPendiente,$request->idUsuario, $nombreCompletoUsuarioRevisaAprueba, $accionNext);
                                        $this->actualizarEstadoRequerimiento($accionNext,$requerimiento,$aprobacionFinalOPendiente);
                                        $this->enviarNotificacionPorAprobacion($requerimiento,$request->comentario,$nombreCompletoUsuarioCreador,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$trazabilidad);

                                    }
                                    $i++;
                                }
                            }
    
                        }
                    }
                }
            }


        

            if ($accion == 1) {
                $seNotificaraporEmail = true;
                // TO-DO NOTIFICAR AL USUARIO QUE SU REQUERIMIENTO FUE APROBADO
            }

            $seNotificaraporEmail = true;
            DB::commit();
            return response()->json(['id_aprobacion' => $aprobacion->id_aprobacion, 'notificacion_por_emial' => $seNotificaraporEmail]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_aprobacion' => 0, 'notificacion_por_emial' => false, 'mensaje' => 'Hubo un problema al guardar la respuesta. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }





    public function listarSedesPorEmpresa($idEmpresa)
    {
        return Sede::listarSedesPorEmpresa($idEmpresa);
    }



    // botonera aprobar  
    function flujoAprobacion($req, $doc)
    {

        $cont = 1;
        $footer = '';

        $dataFinal = array();
        $alert = '<ul style="list-style: none; padding: 0;">';

        $dataFinal = $this->get_historial_aprobacion($req, $doc);

        foreach ($dataFinal as $value => $val) {
            $usu = $val['usuario'];
            $est = $val['estado'];
            $day = $val['fecha'];
            $obs = $val['obs'];
            $name_user = $val['nombre_usuario'];

            if (strtoupper($est) == 'ELABORADO') {
                $claseObs = 'alert-okc alert-okc-primary';
            } elseif (strtoupper($est) == 'OBSERVADO') {
                $claseObs = 'alert-okc alert-okc-warning';
            } elseif (strtoupper($est) == 'DENEGADO') {
                $claseObs = 'alert-okc alert-okc-danger';
            } elseif (strtoupper($est) == 'SUSTENTO') {
                $claseObs = 'alert-okc alert-okc-info';
            } elseif (strtoupper($est) == 'APROBADO') {
                $claseObs = 'alert-okc alert-okc-success';
            }


            $alert .=
                '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
            <strong>' . strtoupper($est) . ' - ' . $name_user . '</strong>
            <small>(' . date('d/m/Y H:i:s', strtotime($day)) . ')</small>
            <br>' . $obs . '
            </li>';

            $cont++;

            foreach ($val['detalle'] as $key => $value) {

                if (count($val['detalle'][$key]) > 0) {

                    $usu_sus = $value['usuario'];
                    $est_sus = $value['estado'];
                    $day_sus = $value['fecha'];
                    $obs_sus = $value['obs'];
                    $name_user_sus = $value['nombre_usuario'];

                    if (strtoupper($est_sus) == 'ELABORADO') {
                        $claseObs = 'alert-okc alert-okc-primary';
                    } elseif (strtoupper($est_sus) == 'OBSERVADO') {
                        $claseObs = 'alert-okc alert-okc-warning';
                    } elseif (strtoupper($est_sus) == 'DENEGADO') {
                        $claseObs = 'alert-okc alert-okc-danger';
                    } elseif (strtoupper($est_sus) == 'SUSTENTO') {
                        $claseObs = 'alert-okc alert-okc-info';
                    } elseif (strtoupper($est_sus) == 'APROBADO') {
                        $claseObs = 'alert-okc alert-okc-success';
                    }

                    $alert .=
                        '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
                    <strong>' . strtoupper($est_sus) . ' - ' . $name_user_sus . '</strong>
                    <small>(' . date('d/m/Y H:i:s', strtotime($day_sus)) . ')</small>
                    <br>' . $obs_sus . '
                </li>';
                }
            }
        }
        $estado_req = $this->consult_estado($req); // get id_estado_doc
        // $totalFlujo = $this->totalAprobOp(1);


        $id_grupo = $this->get_id_grupo($req);

        $num_doc = $this->consult_doc_aprob($req, 1);
        $total_aprob = Aprobacion::cantidadAprobaciones($num_doc);
        $total_flujo = $this->consult_tamaño_flujo($req);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc, 1); //{num doc},{tp doc} 

        $tp_doc = 1; // tipo de documento = requerimiento 
        $id_operacion = $this->get_id_operacion($id_grupo, $areaOfRolAprob['id'], $tp_doc);

        // $sgt_aprob='-';
        // $sgt_per='-';

        if ($estado_req == 12) {
            if ($total_aprob > 0) {
                if ($total_flujo > $total_aprob) {
                    $sgt_aprob = ($total_aprob + 1);
                    $sgt_per = $this->consult_sgt_aprob($sgt_aprob, $id_operacion);
                    $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
                }
            }
        } elseif ($estado_req == 3) {
            $usuario_crea = $this->consult_usuario_elab($req);
            $usu_elab = Usuario::find($usuario_crea)->trabajador->postulante->persona->nombre_completo;
            $footer .= '<strong>Por sustentar </strong>' . $usu_elab;
        } elseif ($estado_req == 13) {
            if ($total_flujo > $total_aprob) {
                $sgt_aprob = ($total_aprob + 1);
                $sgt_per = $this->consult_sgt_aprob($sgt_aprob, $id_operacion);
                $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
            }
        } elseif ($estado_req == 1) {
            $PrimeraApro = $this->consulta_req_primera_aprob($req);
            $usuPrimeraApro = $PrimeraApro['nombre'];
            $rolPrimeraApro = $PrimeraApro['id_rol'];
            $nameUserPrimeraApro = $this->consulta_nombre_usuario($rolPrimeraApro);
            $json = json_decode($nameUserPrimeraApro);
            $allnameUserPrimeraApro = implode(", ", array_map(function ($obj) {
                foreach ($obj as $p => $v) {
                    return $v;
                }
            }, $json));

            $footer = '<strong>Pendiente </strong><abbr title="' . $allnameUserPrimeraApro . '">' . $usuPrimeraApro . '</abbr>';
        }

        // if($sql3->first()->observacion != null){
        //     $footer .= ' <strong> Por Aceptar Sustento:</strong> Logistica' ;
        // }

        $reqs = $this->mostrar_requerimiento_id($req, 2);

        $data = ['flujo' => $alert, 'siguiente' => $footer, 'requerimiento' => $reqs, 'cont' => $cont];
        return response()->json($data);
    }

    public function get_historial_aprobacion($req)
    {

        $doc = $this->consult_doc_aprob($req, 1);

        $new_data = array();
        $dataFinal = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();

        $req_elaborado = DB::table('almacen.alm_req')
            ->where('id_requerimiento', '=', $req)
            ->get();
        $cant_req_elaborado = $req_elaborado->count();

        if ($cant_req_elaborado > 0) {
            foreach ($req_elaborado as $row) {
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $data1[] = array(
                    'estado' => 'ELABORADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => '',
                    'nombre_usuario' => Usuario::find($row->id_usuario)->trabajador->postulante->persona->nombre_completo,
                    'detalle' => []
                );
            }
        }



        $req_aprobado = DB::table('administracion.adm_aprobacion')
            ->join('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
            ->select('adm_aprobacion.*', 'adm_vobo.descripcion AS vobo')
            ->where('adm_aprobacion.id_doc_aprob', '=', $doc)->get();

        $cant_req_aprob = $req_aprobado->count();

        if ($cant_req_aprob > 0) {
            foreach ($req_aprobado as $key) {
                $id_usua = $key->id_usuario;
                $my_vobo = $key->vobo;
                $fechavb = $key->fecha_vobo;
                $det_obs = $key->detalle_observacion;
                $data2[] = array(
                    'estado' => $my_vobo, 'usuario' => $id_usua, 'fecha' => $fechavb,
                    'nombre_usuario' => Usuario::find($key->id_usuario)->trabajador->postulante->persona->nombre_completo,
                    'obs' => $det_obs, 'detalle' => []
                );
            }
        }

        $dataFinal = array_merge($data1, $data2, $new_data);
        $date = array();
        foreach ($dataFinal as $row) {
            $date[] = $row['fecha'];
        }
        array_multisort($date, SORT_ASC, $dataFinal);

        return $dataFinal;
    }

    function consult_estado($req)
    {
        $sql = DB::table('almacen.alm_req')->select('estado')->where('id_requerimiento', $req)->first();
        return $sql->estado;
    }

    function get_id_grupo($req)
    {
        $sql = DB::table('almacen.alm_req')
            ->where([['id_requerimiento', '=', $req]])
            ->get();
        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
        } else {
            $id_grupo = 0;
        }
        return $id_grupo;
    }

    function consult_doc_aprob($id_doc, $tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')->where([['id_tp_documento', '=', $tp_doc], ['id_doc', '=', $id_doc]])->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc_aprob;
        } else {
            $val = 0;
        }

        return $val;
    }


    public function get_id_tipo_documento($descripcion)
    {
        $adm_tp_docum = DB::table('administracion.adm_tp_docum')
            ->select('adm_tp_docum.*')
            ->where('descripcion', 'like', '%' . $descripcion)
            ->get()->first()->id_tp_documento;

        return $adm_tp_docum;
    }
    function consult_tamaño_flujo($id_req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');

        $req = DB::table('almacen.alm_req')
            ->where([
                ['id_requerimiento', '=', $id_req]
            ])
            ->first();
        // $id_prioridad = $req->id_prioridad;
        $id_prioridad = 1;
        $id_grupo = isset($req->id_grupo) ? $req->id_grupo : 0;
        $id_area = isset($req->id_area) ? $req->id_area : 0;

        $sql_operacion = DB::table('administracion.adm_operacion')
            ->where([
                ['id_grupo', '=', $id_grupo],
                ['id_tp_documento', '=', 1],
                ['estado', '=', 1]
            ])
            ->get();
        if ($sql_operacion->count() > 0) {
            $id_operacion = $sql_operacion->first()->id_operacion;
        } else {
            $id_operacion = 0;
        }

        $flujo = DB::table('administracion.adm_flujo')->where([
            ['id_operacion', '=', $id_operacion],
            ['estado', '=', 1]
        ])
            ->get();

        return $flujo->count();
    }
    public function get_id_doc($id_doc_aprob, $tp_doc)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')
            ->where([
                ['id_tp_documento', '=', $tp_doc],
                ['id_doc_aprob', '=', $id_doc_aprob]
            ])
            ->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc;
        } else {
            $val = 0;
        }
        return $val;
    }
    public function get_id_rol_req($id_req)
    {
        $sql = DB::table('almacen.alm_req')
            ->where([['id_requerimiento', '=', $id_req]])
            ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_rol;
        } else {
            $val = 0;
        }
        return $val;
    }

    public function get_id_area_rol($id_rol_req)
    {
        $sql = DB::table('administracion.rol_aprobacion')
            ->where([['id_rol_aprobacion', '=', $id_rol_req]])
            ->get();
        if ($sql->count() > 0) {
            $val = $sql->first()->id_area;
        } else {
            $val = 0;
        }
        return $val;
    }
    public function getAreaOfRolAprob($id_doc_aprob, $tp_doc)
    {

        $id_area_rol = 0;
        $msg = 'OK';

        switch ($tp_doc) {
            case '1': //requerimiento
                # code...
                $id_req = $this->get_id_doc($id_doc_aprob, 1);
                if ($id_req == 0) {
                    $msg = 'error id_req';
                } else {
                    $id_rol_req = $this->get_id_rol_req($id_req);
                    if ($id_rol_req == 0) {
                        $msg = 'error id_rol_req';
                    } else {
                        $id_area_rol = $this->get_id_area_rol($id_rol_req);
                        if ($id_area_rol == 0) {
                            $msg = 'error id_area_rol';
                        }
                    }
                }

                break;

            case '2': //orden
                # code...
                $id_orden = $this->get_id_doc($id_doc_aprob, 2);

                if ($id_orden == 0) {
                    $msg = 'error id_orden';
                } else {
                    $dataReq = $this->get_data_req_by_id_orden($id_orden);
                    if (count($dataReq) == 0) {
                        $msg = 'error dataReq';
                    } else {
                        $id_area_rol = array_unique($dataReq['data']['rol']);
                        if (count($id_area_rol) == 0) {
                            $msg = 'error id_area_rol';
                        }
                    }
                }
                break;

            default:
                # code...
                break;
        }

        $array = array('id' => $id_area_rol, 'msg' => $msg);
        return $array;
    }

    function get_id_operacion($id_grp, $id_area, $tp_doc)
    {
        $filterBy = [];
        if ($id_area == 0 && $id_grp > 0) {
            $filterBy = [['adm_operacion.id_grupo', '=', $id_grp]];
        } else if ($id_area > 0 && $id_grp > 0) {
            $filterBy = [['id_area', '=', $id_area], ['id_grupo', '=', $id_grp]];
        }

        $sql = DB::table('administracion.adm_operacion')
            ->where([
                $filterBy[0],
                ['id_tp_documento', '=', $tp_doc],
                ['estado', '=', 1]
            ])
            ->get();
        if ($sql->count() > 0) {
            $operacion = $sql->first()->id_operacion;
        } else {
            $operacion = 0;
        }
        return $operacion;
    }

    function consult_sgt_aprob($orden, $operacion)
    {
        $sql = DB::table('administracion.adm_flujo')
            ->select('adm_flujo.id_rol')
            ->where([['id_operacion', '=', $operacion], ['orden', '=', $orden], ['estado', '=', 1]])
            ->get();


        if (count($sql) > 0) {
            $trab = DB::table('configuracion.sis_usua')
                ->select('rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'sis_rol.descripcion AS rol')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('configuracion.sis_acceso', 'sis_acceso.id_usuario', '=', 'sis_usua.id_usuario')
                ->join('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'sis_acceso.id_rol')
                ->where('sis_acceso.id_rol', $sql->first()->id_rol)->first();
            $nombre = $trab->nombres . ' ' . $trab->apellido_paterno . ' - ' . $trab->rol;
        } else {
            $nombre = '';
        }
        return $nombre;
    }

    function consult_usuario_elab($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_usuario')->where('id_requerimiento', $req)->first();
        return $sql->id_usuario;
    }

    function consulta_req_primera_aprob($req)
    {
        $id_tipo_doc = $this->get_id_tipo_documento('Requerimiento');
        $message = '';
        $statusOption = ['success', 'fail'];
        $status = '';
        $output = [];

        $sql1 = DB::table('almacen.alm_req')->select('id_grupo', 'id_area', 'rol_aprobante_id')->where('id_requerimiento', $req)->get();
        if (sizeof($sql1) > 0) {
            $sql11 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo], ['id_tp_documento', $id_tipo_doc], ['estado', 1]])->get();
            if (sizeof($sql11) > 0) {
                if ($sql1->first()->rol_aprobante_id > 0) {

                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion], ['id_rol', $sql1->first()->rol_aprobante_id], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();
                } else {

                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();
                }

                $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre : '';
                $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol : '';
                $status = $statusOption[0];
                $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status' => $status, 'message' => 'Flujo Encontrado');

                return $array;
            } else {
                $message = 'No existe id operacion con id_area=' . $sql1->first()->id_area . ',id_grupo=' . $sql1->first()->id_grupo;
                $status = $statusOption[1];
                $output = ['message' => $message, 'status' => $status];

                $sql111 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo], ['id_area', null], ['id_tp_documento', 1], ['estado', 1]])->get();
                if (sizeof($sql111) > 0) {
                    $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql111->first()->id_operacion], ['estado', 1]])
                        ->orderby('orden', 'asc')
                        ->get();

                    $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre : '';
                    $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol : '';
                    $status = $statusOption[0];
                    $array = array('nombre' => $nombre, 'id_rol' => $id_rol, 'status' => $status, 'message' => 'Flujo Encontrado');

                    return $array;
                } else {
                    $message = 'No existe id operacion con id_area= null, id_grupo=' . $sql1->first()->id_grupo;
                    $status = $statusOption[1];
                    $output = ['message' => $message, 'status' => $status];
                }


                return $output;
            }
        } else {
            $message = 'No existe id requerimiento';
            $status = $statusOption[1];
            $output = ['message' => $message, 'status' => $status];
            return $output;
        }
    }


    function consulta_nombre_usuario($id_rol)
    {
        $query = DB::table('administracion.rol_aprobacion')
            ->select(
                DB::raw("concat(rrhh_perso.nombres,' ', rrhh_perso.apellido_paterno, ' ',rrhh_perso.apellido_materno)  AS nombre_completo")
            )


            ->when(($id_rol > 0), function ($query) use ($id_rol) {
                return $query->Where('rol_aprobacion.id_rol_concepto', '=', $id_rol);
            })
            ->where([
                ['rol_aprobacion.estado', '=', 1]
            ])
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rol_aprobacion.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')

            ->orderby('rol_aprobacion.id_rol_aprobacion', 'desc')
            ->get();
        return $query;
    }

    public function mostrar_nombre_grupo($id_grupo)
    {
        $sql = DB::table('administracion.adm_grupo')
            ->select('adm_grupo.id_grupo', 'adm_grupo.descripcion')
            ->where('adm_grupo.id_grupo', $id_grupo)
            ->get();


        if ($sql->count() > 0) {
            $id_grupo = $sql->first()->id_grupo;
            $descripcion = $sql->first()->descripcion;
        } else {
            $id_grupo = 0;
            $descripcion = '';
        }
        $array = array('id_grupo' => $id_grupo, 'descripcion' => $descripcion);
        return $array;
    }

    function consult_moneda($id)
    {
        $sql = DB::table('configuracion.sis_moneda')
            ->select('descripcion', 'simbolo')
            ->where('id_moneda', '=', $id)->first();

        return $sql;
    }
    function mostrar_requerimiento_id($id, $type)
    {
        $sql = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_periodo', 'adm_periodo.id_periodo', '=', 'alm_req.id_periodo')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.estado', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('configuracion.sis_grupo', 'alm_req.id_grupo', '=', 'sis_grupo.id_grupo')
            ->leftJoin('administracion.sis_sede', 'alm_req.id_sede', '=', 'sis_sede.id_sede')
            ->leftJoin('administracion.adm_empresa', 'sis_sede.id_empresa', '=', 'adm_empresa.id_empresa')
            ->leftJoin('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->select(
                'alm_req.*',
                'adm_periodo.descripcion as descripcion_periodo',
                'adm_estado_doc.estado_doc',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'adm_prioridad.descripcion AS priori',
                'sis_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.estado',
                'adm_contri.razon_social',
                'sis_sede.codigo as codigo_sede'
            )
            ->where('alm_req.id_requerimiento', '=', $id)
            ->orderBy('alm_req.fecha_registro', 'desc')
            ->get();
        $html = '';

        foreach ($sql as $row) {
            $code = $row->codigo;
            $motivo = $row->concepto;
            $empresa_sede = $row->razon_social . ' - ' . $row->codigo_sede;
            $id_usu = $row->id_usuario;
            $grupo = $row->id_grupo;
            $area_id = $row->id_area;
            $id_op_com = null;
            $date = date('d/m/Y', strtotime($row->fecha_requerimiento));
            $id_periodo = $row->id_periodo;
            $descripcion_periodo = $row->descripcion_periodo;
            $moneda = $row->id_moneda ? $row->id_moneda : 1;
            $estado_doc = $row->estado_doc;

            $infoGrupo = $this->mostrar_nombre_grupo($grupo);

            if ($infoGrupo['descripcion'] == 'Proyectos') {
                if ($id_op_com != null) {
                    $destino = null;
                } else {
                    $destino = $row->area . ' - GASTOS ADMINISTRATIVOS';
                }
            } else {
                if ($area_id != 6) {
                    $destino = $row->area;
                } else {
                    $destino = $row->area . ' - ' . $row->occ;
                }
            }

            $responsable = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $simbolMoneda = $this->consult_moneda($moneda)->simbolo;
            $descripcionMoneda = $this->consult_moneda($moneda)->descripcion;
        }

        $html =
            '<table width="100%">
            <thead>
                <tr>
                    <th width="140">Código:</th>
                    <td>' . $code . '</td>
                </tr>
                <tr>
                    <th width="140">Motivo:</th>
                    <td>' . $motivo . '</td>
                </tr>
                <tr>
                    <th width="140">Empresa:</th>
                    <td>' . $empresa_sede . '</td>
                </tr>
                <tr>
                    <th width="140">Responsable:</th>
                    <td>' . $responsable . '</td>
                </tr>
                <tr>
                    <th>Area o Servicio:</th>
                    <td>' . $destino . '</td>
                </tr>';
        if ($destino == 'COMERCIAL') {
            $html .= '<tr>
                                <th>OCC:</th>
                                <td></td>
                            </tr>';
        }

        $html .= '<tr>
                    <th>Fecha:</th>
                    <td colspan="2">' . $date . '</td>
                </tr>
                <tr>';
        if ($type == 1) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $descripcionMoneda . '</td>';
        } elseif ($type == 2) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $descripcionMoneda . '</td>
                    <td width="100" align="right"><button class="btn btn-primary" onClick="imprimirReq(' . $id . ');"><i class="fas fa-print"></i> Imprimir formato</button></td>
                    <td>&nbsp;</td>
                    <td width="100" align="right"><button class="btn btn-info" onClick="verArchivosAdjuntosRequerimiento(' . $id . ');"><i class="fas fa-folder"></i> Archivos Adjuntos</button></td>';
        }
        $html .=
            '</tr>
            <tr>
                <th>Periodo:</th>
                <td colspan="2">' . $descripcion_periodo . '</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td colspan="2">' . $estado_doc . '</td>
            </tr>
            </thead>
        </table>
        <br>
        <table class="table table-bordered table-striped table-view-okc" width="100%">';
        if ($type == 1) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th>Código</th>
                    <th>Part.No</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        } elseif ($type == 2) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th>Código</th>
                    <th>Part.No</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Fecha Entrega</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        }

        $cont = 1;
        $total = 0;

        $detail = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_prod.codigo', 'alm_prod.part_number', 'alm_prod.descripcion as descripcion_producto', 'alm_und_medida.descripcion as unidad_medida_descripcion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')

            ->where('id_requerimiento', $id)
            ->get();

        foreach ($detail as $clave => $det) {
            $id_det = $det->id_detalle_requerimiento;
            $codigo_producto = $det->codigo;
            $part_number = $det->part_number;
            $id_item = $det->id_item;
            $id_producto = $det->id_producto;
            $precio = $det->precio_unitario;
            $cant = $det->cantidad;
            $id_part = $det->partida;
            $tiene_transformacion = $det->tiene_transformacion;
            $unit = $det->unidad_medida_descripcion;
            // $simbMoneda = $this->consult_moneda($det->id_moneda)->simbolo;
            $descripcion_adicional = $det->descripcion_adicional;

            $active = '';

            if (is_numeric($id_part)) {
                $name_part = DB::table('finanzas.presup_par')->select('codigo')->where('id_partida', $id_part)->first();
                $partida = $name_part->codigo;
            } else {
                $partida = ''/*$id_part*/;
            }

            $subtotal = $precio * $cant;
            $total += $subtotal;
            $unidad = 'S/N';
            if ($id_producto == null) {
                $name = $descripcion_adicional;
                $unidad = 'Servicio';
            } else {
                $name = $det->descripcion_producto;
            }

            // if ($obs == 't' or $obs == '1' or $obs == 'true') {
            //     $active = 'checked="checked" disabled';
            // }

            if ($type == 1) {
                $html .=
                    '<tr>
                    <td> ' . ($codigo_producto ? $codigo_producto : '') . '</td>
                    <td> ' . ($part_number ? $part_number : '') . ($tiene_transformacion > 0 ? '<br><span style="display: inline-block; font-size: 8px; background:#ddd; color: #666; border-radius:8px; padding:2px 10px;">Transformado</span>' : '') . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td></td>
                    <td>' . $unit . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($precio, 2) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($subtotal, 2) . '</td>
                </tr>';
            } elseif ($type == 2) {
                $html .=
                    '<tr>
                    <td> ' . ($codigo_producto ? $codigo_producto : '') . '</td>
                    <td> ' . ($part_number ? $part_number : '') . ($tiene_transformacion > 0 ? '<br><span style="display: inline-block; font-size: 8px; background:#ddd; color: #666; border-radius:8px; padding:2px 10px;">Transformado</span>' : '') . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td></td>
                    <td>' . ($unit ? $unit : $unidad) . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($precio, 2) . '</td>
                    <td class="text-right">' . $simbolMoneda . number_format($subtotal, 2) . '</td>
                </tr>';
            }

            $cont++;
        }

        $html .=
            '<tr>
            <th colspan="7" class="text-right">Total:</th>
            <td class="text-right">' . $simbolMoneda . number_format($total, 2) . '</td>
        </tr>
        </tbody></table>';

        // if ($type == 1){
        //     return response()->json($html);
        // }elseif ($type == 2){
        //     return $html;
        // }
        return $html;
    }



    // tracking requerimiento 

    public function explorar_requerimiento($id_requerimiento)
    {
        $requerimiento = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('administracion.rol_aprobacion', 'alm_req.id_rol', '=', 'rol_aprobacion.id_rol_aprobacion')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rol_aprobacion.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_req.estado')

            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            // ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),

                'alm_req.id_area',
                'adm_area.descripcion AS area_desc',
                'rol_aprobacion.id_rol_aprobacion as id_rol',
                'rol_aprobacion.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                // 'proy_op_com.codigo as codigo_op_com',
                // 'proy_op_com.descripcion as descripcion_op_com',
                'alm_req.concepto AS alm_req_concepto',
                'alm_req.fecha_registro',
                'alm_req.id_prioridad',
                'alm_req.estado',
                'adm_estado_doc.estado_doc',
                'alm_req.estado'
            )
            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento]
            ])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();

        // $id_prioridad= $requerimiento->first()->id_prioridad;
        $id_prioridad = 1;
        $tipo_documento = 1;
        $id_grupo = $requerimiento->first()->id_grupo;
        // $id_area= $requerimiento->first()->id_area;

        $num_doc = $this->consult_doc_aprob($id_requerimiento, 1);
        // $id_operacion=$this->get_id_operacion($id_grupo,$id_area,$tipo_documento);
        $areaOfRolAprob = $this->getAreaOfRolAprob($num_doc, 1); //{num doc},{tp doc} 

        $id_operacion = $this->get_id_operacion($id_grupo, $areaOfRolAprob['id'], $tipo_documento);

        // get flujo aprobación
        $flujo_aprobacion = $this->get_flujo_aprobacion($id_operacion, $areaOfRolAprob['id']);
        // $flujo_aprobacion = $id_operacion;
        // Lista de historial aprobación
        $historial_aprobacion = $this->get_historial_aprobacion($id_requerimiento);
        // lista de Solicitud de Cotización
        // $solicitud_de_cotizaciones = $this->get_cotizacion_by_req($id_requerimiento);
        $solicitud_de_cotizaciones = [];
        // Lista de Cuadros Comparativo
        $cuadros_comparativos = [];
        // $cuadros_comparativos = $this->get_cuadro_comparativo_by_req($id_requerimiento);
        //lista de ordenes
        // $ordenes = $this->get_orden_by_req($id_requerimiento);
        $ordenes = [];

        // salida
        $output = [
            'requerimiento' => $requerimiento,
            'flujo_aprobacion' => $flujo_aprobacion,
            'historial_aprobacion' => $historial_aprobacion,
            'solicitud_cotizaciones' => $solicitud_de_cotizaciones,
            'cuadros_comparativos' => $cuadros_comparativos,
            'ordenes' => $ordenes
        ];

        return response()->json($output);
    }

    public function mostrarDivisionesDeGrupo($idGrupo)
    {
        $divisiones = DivisionArea::where("grupo_id", $idGrupo)->get();

        return $divisiones;
    }

    public function get_flujo_aprobacion($id_operacion, $id_area)
    {
        $adm_flujo_aprobacion = DB::table('administracion.adm_flujo')
            ->select(
                'adm_flujo.id_flujo',
                'adm_flujo.id_operacion',
                'adm_flujo.id_rol',
                DB::raw("(rrhh_perso.nombres) || ' ' || (rrhh_perso.apellido_paterno) || ' ' || (rrhh_perso.apellido_materno) AS nombre_responsable"),
                // 'rol_aprobacion.id_area',
                'sis_rol.descripcion as descripcion_rol',
                'adm_flujo.nombre as nombre_fase',
                'adm_flujo.orden',
                'adm_flujo.estado'
            )
            ->leftJoin('configuracion.sis_rol', 'sis_rol.id_rol', '=', 'adm_flujo.id_rol')
            ->leftJoin('configuracion.sis_acceso', 'sis_acceso.id_rol', '=', 'sis_rol.id_rol')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'sis_acceso.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([
                ['adm_flujo.estado', '=', 1],
                // ['rol_aprobacion.estado', '=', 1],
                // ['rol_aprobacion.id_area', '=', $id_area],
                ['adm_flujo.id_operacion', '=', $id_operacion]
            ])
            ->orderBy('adm_flujo.orden', 'asc')
            ->get();
        // return $adm_flujo_aprobacion;
        $flujo_aprobacion = [];
        $id_flujo_list = [];

        foreach ($adm_flujo_aprobacion as $data) {

            $id_flujo_list[] = $data->id_flujo;

            $flujo_aprobacion[] = [
                'id_flujo' => $data->id_flujo,
                'nombre_fase' => $data->nombre_fase,
                'id_operacion' => $data->id_operacion,
                'id_rol' => $data->id_rol,
                // 'id_area'=>$data->id_area,
                'nombre_responsable' => $data->nombre_responsable,
                'descripcion_rol' => $data->descripcion_rol,
                'orden' => $data->orden,
                'estado' => $data->estado,
                'criterio_monto' => [],
                'criterio_prioridad' => []
            ];
        }


        return $flujo_aprobacion;
    }



    function aprobarDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $detalle_observacion = $request->detalle_observacion;
        $id_rol = $request->id_rol;
        $id_vobo = VoBo::getIdVoBo('Aprobado');
        $id_usuario = Auth::user()->id_usuario;

        $status = '';
        $message = '';

        // ### determinar flujo , tamaño de flujo
        $flujo = Documento::getFlujoByIdDocumento($id_doc_aprob);
        $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);

        $sql_req = DB::table('almacen.alm_req')->select('rol_aprobante_id')->where('id_requerimiento', $id_req)->get();
        if (count($sql_req) > 0) {
            if ($sql_req->first()->rol_aprobante_id > 0) {
                foreach ($flujo as $value) {
                    if ($sql_req->first()->rol_aprobante_id == $value->id_rol) {
                        $numOrdenAprobante = $value->orden;
                    }
                }

                foreach ($flujo as $key => $value) {
                    if (($value->id_rol != $sql_req->first()->rol_aprobante_id) && ($value->orden == $numOrdenAprobante)) {

                        array_splice($flujo, $key, 1);
                    }
                }
            }
        }

        $tamaño_flujo = count($flujo);

        $aprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        $aprobacionList = $aprobaciones['data'];
        $cantidad_aprobaciones = count($aprobacionList);
        // ### tiene aprobaciones? cantidad de aprobaciones realizadas?
        // ### si tiene aprobaciones determinar si es la ultima aprobacion
        // return $aprobacionList;




        // obtener todo los roles del usuario
        $idRolUsuarioList = [];
        $allRol = Auth::user()->getAllRol();
        foreach ($allRol as  $rol) {
            $idRolUsuarioList[] = $rol->id_rol;
            # code...
        }

        // examinar el el flujo el rol que coiciden con el usuario
        $idFlujoUsuarioApruebaList[] = Documento::searchIdFlujoPorIdRol($flujo, $allRol);

        // aprobaciones pendientes
        $idFlujoAprobacionesHechasList = [];
        $OrdenFlujoAprobacionesHechasList = [];
        foreach ($aprobacionList as $aprobacion) {
            $idFlujoAprobacionesHechasList[] = $aprobacion->id_flujo;
            $OrdenFlujoAprobacionesHechasList[] = $aprobacion->orden;
        }

        //eliminando flujo ya aprobados
        $aprobacionPendienteList = [];
        foreach ($flujo as $value) {
            if (!in_array($value->id_flujo, $idFlujoAprobacionesHechasList) && !in_array($value->orden, $OrdenFlujoAprobacionesHechasList)) {
                $aprobacionPendienteList[] = $value;
            }
        }

        // eliminar flujo con numero de orden aprobado 
        // Debugbar::info($aprobacionPendienteList);


        // si el id_rol usuario le corresponde aprobar la primera aprobacion pendiente y evaluar si le toca la siguiente
        $i = 0;
        $FlujoAGrabarList = [];
        foreach ($aprobacionPendienteList as $ap) {
            if (in_array($ap->id_rol, $idRolUsuarioList)) {
                $FlujoAGrabarList[] = $ap;
            }
            if (++$i > 2) break; //limite 2
        }
        // guardar 
        foreach ($FlujoAGrabarList as $value) {
            $nuevaAprobacion = $this->guardar_aprobacion_documento($value->id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $value->id_rol);
        }


        // verificar aprobacionesPendientes == aprobaciones
        $newAprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        $newAprobacionList = $newAprobaciones['data'];

        // aprobaciones pendientes
        $idFlujoAprobacionesHechasList = [];
        $OrdenFlujoAprobacionesHechasList = [];
        foreach ($newAprobacionList as $aprobacion) {
            $idFlujoAprobacionesHechasList[] = $aprobacion->id_flujo;
            $OrdenFlujoAprobacionesHechasList[] = $aprobacion->orden;
        }

        //eliminando flujo ya aprobados
        $newAprobacionPendienteList = [];
        foreach ($flujo as $value) {
            if (!in_array($value->id_flujo, $idFlujoAprobacionesHechasList) && !in_array($value->orden, $OrdenFlujoAprobacionesHechasList)) {
                $newAprobacionPendienteList[] = $value;
            }
        }

        $idRequerimiento = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        if (count($newAprobacionPendienteList) == 0) {
            DB::table('almacen.alm_req')->where('id_requerimiento', $idRequerimiento)->update(['estado' => 2]); // estado aprobado
        } else {
            DB::table('almacen.alm_req')->where('id_requerimiento', $idRequerimiento)->update(['estado' => 12]); //Pendiente de Aprobación
        }

        if ($nuevaAprobacion > 0) {
            $status = 200;
            $message = 'Ok';
        } else {
            $status = 204;
            $message = 'No Content, data vacia';
        }




        // $id_flujo = Documento::searchIdFlujoByOrden($flujo, $cantidad_aprobaciones + 1);


        // if ($cantidad_aprobaciones < $tamaño_flujo) {
        //     $nuevaAprobacion = $this->guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol);
        //     if ($nuevaAprobacion > 0) {
        //         $status = 200; // No Content
        //         $message = 'Ok';
        //         $aprobaciones = Aprobacion::getVoBo($id_doc_aprob);
        //         // Debugbar::info($aprobaciones);
        //         $aprobacionList = $aprobaciones['data'];
        //         $cantidad_aprobaciones = count($aprobacionList);

        //         if ($cantidad_aprobaciones == $tamaño_flujo) {
        //             $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        //             $estado_aprobado = 2; // estado aprobado
        //             $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_aprobado]);
        //         } else {
        //             $id_req = Documento::getIdDocByIdDocAprob($id_doc_aprob);
        //             $estado_pendiente_aprobacion = 12; //Pendiente de Aprobación
        //             $update_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['estado' => $estado_pendiente_aprobacion]);
        //         }
        //     } else {
        //         $status = 204; // No Content
        //         $message = 'No Content, data vacia';
        //     }
        // }

        $output = ['status' => $status, 'message' => $message];
        return $output;
    }


    public static function guardar_aprobacion_documento($id_flujo, $id_doc_aprob, $id_vobo, $detalle_observacion, $id_usuario, $id_rol)
    {
        $hoy = date('Y-m-d H:i:s');
        $nuevaAprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $id_flujo,
                'id_doc_aprob'          => $id_doc_aprob,
                'id_vobo'               => $id_vobo,
                'id_usuario'            => $id_usuario,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $detalle_observacion,
                'id_rol'                => $id_rol
            ],
            'id_aprobacion'
        );

        return $nuevaAprobacion;
    }


    function observarDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $detalle_observacion = $request->detalle_observacion;
        $id_rol = $request->id_rol;
        $id_usuario = Auth::user()->id_usuario;
        $id_requerimiento = $this->get_id_doc($id_doc_aprob, 1);

        $status = '';
        $message = '';
        $hoy = date('Y-m-d H:i:s');
        $estado_observado = 3;

        $requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_requerimiento)
            ->update([
                'estado' => $estado_observado
            ]);
        $detalle_req = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento', '=', $id_requerimiento)
            ->update([
                'estado' => $estado_observado
            ]);
        if ($requerimiento && $detalle_req > 0) {
            $nuevaAprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'              => null,
                    'id_doc_aprob'          => $id_doc_aprob,
                    'id_vobo'               => 3,
                    'id_usuario'            => $id_usuario,
                    'fecha_vobo'            => $hoy,
                    'detalle_observacion'   => $detalle_observacion,
                    'id_rol'                => $id_rol,
                    'id_sustentacion'       => null
                ],
                'id_aprobacion'
            );

            if ($nuevaAprobacion > 0) {
                $status = 200;
                $message = 'OK';
            }
        }



        $output = ['status' => $status, 'message' => $message];
        return $output;
    }



    function anularDocumento(Request $request)
    {
        $id_doc_aprob = $request->id_doc_aprob;
        $id_requerimiento = $this->get_id_doc($id_doc_aprob, 1);
        $motivo = $request->motivo;
        $id_rol = $request->id_rol;
        $id_usuario = Auth::user()->id_usuario;
        // $estado_anulado = $this->get_estado_doc('Anulado');
        $estado_anulado = 7;
        $hoy = date('Y-m-d H:i:s');
        $status = '';
        $message = '';

        $requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id_requerimiento)
            ->update([
                'estado' => $estado_anulado
            ]);
        $detalle_req = DB::table('almacen.alm_det_req')
            ->where('id_requerimiento', '=', $id_requerimiento)
            ->update([
                'estado' => $estado_anulado
            ]);

        if ($requerimiento && $detalle_req > 0) {
            $AnularReq = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'              => null,
                    'id_doc_aprob'          => $id_doc_aprob,
                    'id_vobo'               => 2,
                    'id_usuario'            => $id_usuario,
                    'fecha_vobo'            => $hoy,
                    'detalle_observacion'   => $motivo,
                    'id_rol'                => $id_rol,
                    'id_sustentacion'       => null
                ],
                'id_aprobacion'
            );

            if ($AnularReq > 0) {
                $status = 200;
                $message = 'OK';
            }
        }

        $output = ['status' => $status, 'message' => $message];

        return response()->json($output);
    }


    public function imprimir_requerimiento_pdf($id, $codigo)
    {
        $requerimiento = $this->mostrarRequerimiento($id, $codigo);
        $now = new \DateTime();
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
                        font-size: 10px;
                        box-sizing: border-box;
                        padding:10px;
                }
                
                table{
                width:100%;
                height:auto;
                border-collapse: collapse;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#d04f46;
                    color:white;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: .5px solid #dbdbdb;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                .subtitle{
                    font-weight: bold;
                }
                .bordebox{
                    border: 1px solid #000;
                }
                .verticalTop{
                    vertical-align:top;
                }
                .texttab { 
                    
                    display:block; 
                    margin-left: 20px; 
                    margin-bottom:5px;
                }
                hr{
                    color:#cc352a;
                }
                
                .right{
                    text-align:right;
                }
                .left{
                    text-align:left;
                }
                .justify{
                    text-align: justify;
                }
                .top{
                vertical-align:top;
                }
                footer{
                    position:relative;
                }
                .pie_de_pagina{
                    position: absolute;
                    bottom:0px;
                    right:0px;
                    text-align:right;
                }
            </style>
            </head>
            <body>
            
                <img src=".' . $requerimiento['requerimiento'][0]['logo_empresa'] . '" alt="Logo" height="75px">
                <hr>

                <h1><center>REQUERIMIENTO ' . $requerimiento['requerimiento'][0]['codigo'] . '</center></h1>
                <br><br>
            <table border="0">
            <tr>
                <td class="subtitle">Req.</td>
                <td class="subtitle verticalTop">:</td>
                <td width="40%" class="verticalTop">' . $requerimiento['requerimiento'][0]['codigo'] . '</td>
                <td class="subtitle verticalTop">Fecha entrega</td>
                <td class="subtitle verticalTop">:</td>
                <td>' . $requerimiento['requerimiento'][0]['fecha_entrega'] . '</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">Solicitante</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['persona'] . '</td>
                    <td class="subtitle verticalTop">Prioridad</td>
                    <td class="subtitle verticalTop">:</td>
                    <td>' . $requerimiento['requerimiento'][0]['prioridad'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">Empresa</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['razon_social_empresa'] . ' - ' . $requerimiento['requerimiento'][0]['codigo_sede_empresa'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">Gerencia</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['grupo_descripcion'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle top">Proyecto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop justify" colspan="4" >' . $requerimiento['requerimiento'][0]['codigo_proyecto'] . ' - ' . $requerimiento['requerimiento'][0]['descripcion_proyecto'] . '</td>
                </tr>    
                <tr>
                    <td class="subtitle">Presupuesto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop"></td>
                </tr>
                <tr>
                    <td class="subtitle">Observación</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['observacion'] . '</td>
                </tr>
                </table>
                <br>';

        $html .= '</div>
                <table width="100%" class="tablePDF" border=0 style="font-size:10px">
                <thead>
                    <tr class="subtitle">
                        <td width="10%" style="text-align:center;">Centro costo</td>
                        <td width="10%" style="text-align:center;">Part.No</td>
                        <td width="30%" style="text-align:center;">Descripcion</td>
                        <td width="5%" style="text-align:center;">Und.</td>
                        <td width="5%" style="text-align:center;">Cant.</td>
                        <td width="6%" style="text-align:center;">Precio ref.</td>
                        <td width="7%" style="text-align:center;">Subtotal</td>
                    </tr>   
                </thead>';
        $total = 0;
        $simbolMonedaRequerimiento = $this->consult_moneda($requerimiento['requerimiento'][0]['id_moneda'])->simbolo;

        foreach ($requerimiento['det_req'] as $key => $data) {

            $html .= '<tr>';
            $html .= '<td >' . $data['descripcion_centro_costo'] . '</td>';
            $html .= '<td >' . ($data['id_tipo_item'] == 1 ? ($data['producto_part_number'] ? $data['producto_part_number'] : $data['part_number']) : '(Servicio)') . ($data['tiene_transformacion'] > 0 ? '<br><span style="display: inline-block; font-size: 8px; background:#ddd; color: #666; border-radius:8px; padding:2px 10px;">Transformado</span>' : '') . '</td>';
            $html .= '<td >' . ($data['producto_descripcion'] ? $data['producto_descripcion'] : ($data['descripcion'] ? $data['descripcion'] : '')) . '</td>';
            $html .= '<td style="text-align:center;">' . $data['unidad_medida'] . '</td>';
            $html .= '<td class="right" style="text-align:center;">' . $data['cantidad'] . '</td>';
            $html .= '<td class="right" style="text-align:right;">' . $simbolMonedaRequerimiento . number_format($data['precio_unitario'], 2) . '</td>';
            $html .= '<td class="right" style="text-align:right;">' . $simbolMonedaRequerimiento . number_format($data['cantidad'] * $data['precio_unitario'], 2) . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad'] * $data['precio_unitario']);
        }
        $html .= '
            <tr>
                <td  class="right" style="font-weight:bold;" colspan="6">TOTAL</td>
                <td class="right">' . $simbolMonedaRequerimiento . number_format($total, 2) . '</td>
            </tr>
            </table>
                <br/>
                <br/>
                <footer>
                    <p style="font-size:9px; " class="pie_de_pagina">Generado por: ' . ucwords(strtolower($requerimiento['requerimiento'][0]['persona'])) .  '<br>'
                    .'Fecha registro: '.$requerimiento['requerimiento'][0]['fecha_registro'].'<br>'
                    .'Versión del sistema: '.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>

                </footer>
            </html>';
        return $html;
    }

    public function generar_requerimiento_pdf($id, $codigo)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->imprimir_requerimiento_pdf($id, $codigo));
        return $pdf->stream();
        return $pdf->download('requerimiento.pdf');
    }


    public function mostrarCabeceraRequerimiento($idRequerimiento)
    {
        $requerimiento = Requerimiento::find($idRequerimiento);
        return $requerimiento;
    }

    public function mostrarHistorialAprobacion($idRequerimiento)
    {
        $historialAprobacion = Aprobacion::getHistorialAprobacion($idRequerimiento);
        return $historialAprobacion;
    }
    public function mostrarTrazabilidadDetalleRequerimiento($idRequerimiento)
    {

        $detalleRequerimiento = DetalleRequerimiento::where("id_requerimiento", $idRequerimiento)
            ->select('alm_det_req.*', 'alm_prod.codigo as codigo_producto', 'alm_prod.part_number as part_number_producto', 'alm_prod.descripcion as descripcion_producto', 'alm_und_medida.descripcion as unidad_medida', 'adm_estado_doc.estado_doc as nombre_estado')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_det_req.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->leftJoin('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'alm_det_req.estado')
            ->get();
        return datatables($detalleRequerimiento)->toJson();
    }

    public function detalleRequerimientoParaReserva($idDetalleRequerimiento)
    {
        $detalleRequerimiento = DetalleRequerimiento::where("id_detalle_requerimiento", $idDetalleRequerimiento)
            ->with(['unidadMedida', 'producto', 'reserva' => function ($q) {
                $q->where('alm_reserva.estado', '=', 1);
            }, 'reserva.almacen', 'reserva.usuario', 'reserva.usuario.trabajador.postulante.persona', 'reserva.estado', 'estado'])

            ->first();
        if ($detalleRequerimiento) {
            return ['data' => $detalleRequerimiento, 'status' => 200];
        } else {
            return ['data' => [], 'status' => 204];
        }
    }
    public function historialReservaProducto($idDetalleRequerimiento)
    {
        $detalleRequerimiento = DetalleRequerimiento::where("id_detalle_requerimiento", $idDetalleRequerimiento)
            ->with(['unidadMedida', 'producto', 'reserva', 'reserva.almacen', 'reserva.usuario', 'reserva.usuario.trabajador.postulante.persona', 'reserva.estado', 'estado'])
            ->first();
        if ($detalleRequerimiento) {
            return ['data' => $detalleRequerimiento, 'status' => 200];
        } else {
            return ['data' => [], 'status' => 204];
        }
    }
    public function todoDetalleRequerimiento($idRequerimiento, $transformadosONoTransformados)
    {
        $detalleRequerimiento = DetalleRequerimiento::where([["id_requerimiento", $idRequerimiento],["estado",'!=',7]])
            ->when(($transformadosONoTransformados === 'SIN_TRANSFORMACION'), function ($query) {
                return $query->where('tiene_transformacion', false);
            })
            ->when(($transformadosONoTransformados === 'CON_TRANSFORMACION'), function ($query) {
                return $query->where('tiene_transformacion', true);
            })
            ->with(['unidadMedida', 'producto', 'reserva' => function ($q) {
                $q->where('alm_reserva.estado', '=', 1);
            }, 'reserva.almacen', 'reserva.usuario', 'reserva.usuario.trabajador.postulante.persona', 'reserva.estado', 'estado'])
            ->get();

        if ($detalleRequerimiento) {
            return ['data' => $detalleRequerimiento, 'status' => 200];
        } else {
            return ['data' => [], 'status' => 204];
        }
    }
}
