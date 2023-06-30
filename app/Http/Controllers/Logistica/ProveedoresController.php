<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\models\Configuracion\AccesosUsuarios;
use App\Models\Configuracion\Distrito;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Pais;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\ContactoContribuyente;
use App\Models\Contabilidad\Contribuyente;
use App\Models\Contabilidad\CuentaContribuyente;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoContribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Contabilidad\TipoDocumentoIdentidad;
use App\Models\Logistica\EstablecimientoProveedor;
use App\Models\Logistica\EstadoProveedor;
use App\Models\Logistica\Proveedor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Debugbar;


class ProveedoresController extends Controller
{
    public function index()
    {
        $tipoDocumentos = TipoDocumentoIdentidad::mostrar();
        $tipoContribuyentes = TipoContribuyente::mostrar();
        $paises = Pais::mostrar();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $monedas = Moneda::mostrar();

        $array_accesos=[];
        $accesos_usuario = AccesosUsuarios::where('estado',1)->where('id_usuario',Auth::user()->id_usuario)->get();
        foreach ($accesos_usuario as $key => $value) {
            array_push($array_accesos,$value->id_acceso);
        }
        return view('logistica/gestion_logistica/proveedores/lista_proveedores', compact('paises', 'tipoDocumentos', 'tipoContribuyentes', 'bancos', 'tipo_cuenta', 'monedas','array_accesos'));
    }

    public function obtenerDataListado()
    {
        return datatables(Proveedor::listado())
            // ->filterColumn('ubigeo_completo', function ($query, $keyword) {
            //     try {
            //         $keywords = trim(strtoupper($keyword));
            //         $query->whereRaw("UPPER(CONCAT((ubi_dis.descripcion,' - ',ubi_prov.descripcion,' - ',ubi_dpto.descripcion))) LIKE ?", ["%{$keywords}%"]);
            //     } catch (\Throwable $th) {
            //     }
            // })

            ->rawColumns(['ubigeo_completo'])->toJson();
    }

    public function guardar(Request $request)
    {

        DB::beginTransaction();
        try {

            $mensaje = '';
            $status = '';
            $idProveedor = 0;
            $crearProveedor = false;

            // buscar proveedor si existe el ruc o razon social
            $contribuyenteExistente = Contribuyente::where([["estado", 1], ["id_doc_identidad", $request->tipoDocumentoIdentidad], ["nro_documento", $request->nroDocumento]])
                ->orwhere([["estado", 1], ["id_doc_identidad", $request->tipoDocumentoIdentidad], ["razon_social", 'like', $request->razonSocial . "%"]])->first();

            if (isset($contribuyenteExistente)) {
                // $mensaje='Ya se encuentra registrado un contribuyente con la misma razón social / número de documento.';
                // $status='warning';
                $proveedorExistente = Proveedor::where([['id_contribuyente', $contribuyenteExistente->id_contribuyente]])->first();
                if (isset($proveedorExistente) && ($proveedorExistente->id_proveedor > 0)) {
                    if ($proveedorExistente->estado == 1) {
                        $mensaje = 'Ya se encuentra registrado un proveedor con la misma razón social / número de documento.';
                        $status = 'warning';
                        $crearProveedor = false;
                    }
                    // elseif($proveedorExistente->estado ==7){
                    //     $mensaje='Se encuentró coincidencia con un proveedor anuado. ¿Desea visualizar para una recuperación de información?';
                    //     $status='warning';
                    //     $crearProveedor=false;

                    // }

                } else {
                    $crearProveedor = true;
                }
            } else {
                $crearProveedor = true;
            }

            if ($crearProveedor) {
                $contribuyente = new Contribuyente();
                $contribuyente->id_tipo_contribuyente = $request->tipoContribuyente;
                $contribuyente->id_doc_identidad = $request->tipoDocumentoIdentidad > 0 ? $request->tipoDocumentoIdentidad : null;
                $contribuyente->nro_documento = $request->nroDocumento;
                $contribuyente->razon_social = $request->razonSocial;
                $contribuyente->direccion_fiscal = $request->direccion;
                $contribuyente->id_pais = $request->pais > 0 ? $request->pais : null;
                $contribuyente->ubigeo = $request->ubigeoProveedor;
                $contribuyente->telefono = $request->telefono;
                $contribuyente->celular = $request->celular;
                $contribuyente->email = $request->email;
                $contribuyente->estado = 1;
                $contribuyente->fecha_registro = new Carbon();
                $contribuyente->transportista = false;
                $contribuyente->save();

                $proveedor = new Proveedor();
                $proveedor->id_contribuyente = $contribuyente->id_contribuyente;
                $proveedor->observacion = $request->observacion;
                $proveedor->estado = 1;
                $proveedor->fecha_registro = new Carbon();
                $proveedor->save();
                $idProveedor = $proveedor->id_proveedor;

                if (isset($request->idEstablecimiento)) {
                    $countEstablecimiento = count($request->idEstablecimiento);
                    for ($i = 0; $i < $countEstablecimiento; $i++) {
                        if ($request->estadoEstablecimiento[$i] == 1) {
                            $establecimientoProveedor = new EstablecimientoProveedor();
                            $establecimientoProveedor->id_proveedor = $proveedor->id_proveedor;
                            $establecimientoProveedor->direccion = $request->direccionEstablecimiento[$i];
                            $establecimientoProveedor->horario = $request->horarioEstablecimiento[$i];
                            $establecimientoProveedor->ubigeo = $request->ubigeoEstablecimiento[$i] > 0 ? $request->ubigeoEstablecimiento[$i] : null;
                            $establecimientoProveedor->estado = 1;
                            $establecimientoProveedor->fecha_registro = new Carbon();
                            $establecimientoProveedor->save();
                        }
                    }
                }

                if (isset($request->idContacto)) {
                    $countContacto = count($request->idContacto);

                    for ($i = 0; $i < $countContacto; $i++) {
                        if ($request->estadoContacto[$i] == 1) {
                            $contactoProveedor = new ContactoContribuyente();
                            $contactoProveedor->id_contribuyente = $contribuyente->id_contribuyente;
                            $contactoProveedor->nombre = $request->nombreContacto[$i];
                            $contactoProveedor->telefono = $request->telefonoContacto[$i];
                            $contactoProveedor->email = $request->emailContacto[$i];
                            $contactoProveedor->cargo = $request->cargoContacto[$i];
                            $contactoProveedor->fecha_registro = new Carbon();
                            $contactoProveedor->direccion = $request->direccionContacto[$i];
                            $contactoProveedor->estado = 1;
                            $contactoProveedor->horario = $request->horarioContacto[$i];
                            $contactoProveedor->ubigeo = $request->ubigeoContactoProveedor[$i] > 0 ? $request->ubigeoContactoProveedor[$i] : null;
                            $contactoProveedor->save();
                        }
                    }
                }
                if (isset($request->idCuenta)) {
                    $countCuenta = count($request->idCuenta);
                    for ($i = 0; $i < $countCuenta; $i++) {
                        if ($request->estadoCuenta[$i] == 1) {
                            $cuentaBancariaProveedor = new CuentaContribuyente();
                            $cuentaBancariaProveedor->id_contribuyente  = $contribuyente->id_contribuyente;
                            $cuentaBancariaProveedor->id_banco  = $request->idBanco[$i];
                            $cuentaBancariaProveedor->id_tipo_cuenta  = $request->idTipoCuenta[$i] > 0 ? $request->idTipoCuenta[$i] : null;
                            $cuentaBancariaProveedor->id_moneda  = $request->idMoneda[$i] > 0 ? $request->idMoneda[$i] : null;
                            $cuentaBancariaProveedor->nro_cuenta  =  $request->nroCuenta[$i];
                            $cuentaBancariaProveedor->nro_cuenta_interbancaria  = $request->nroCuentaInterbancaria[$i];
                            $cuentaBancariaProveedor->swift  = $request->swift[$i];
                            $cuentaBancariaProveedor->estado  = 1;
                            $cuentaBancariaProveedor->fecha_registro  = new Carbon();
                            $cuentaBancariaProveedor->save();
                        }
                    }
                }

                $status = 'success';
            }


            DB::commit();
            return response()->json(['status' => $status, 'id_proveedor' => $idProveedor, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'id_proveedor' => 0, 'mensaje' => 'Hubo un problema al guardar el proveedor. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }


    public function mostrar($idProveedor)
    {

        return Proveedor::mostrar($idProveedor);
    }


    public function actualizar(Request $request)
    {

        DB::beginTransaction();
        try {

            $mensaje = '';

            if($request->contribuyenteEncontrado==true){
                if($request->idProveedor >0){
                    // actualizar registro como proveedor
                    $proveedor = Proveedor::find($request->idProveedor);
                    $proveedor->id_contribuyente = $request->idContribuyente;
                    $proveedor->observacion = $request->observacion;
                    $proveedor->estado = 1;
                    $proveedor->save();
                }else{
                    // crear registro como proveedor de caso existir un contribuyente pero no existe registro en tabla de proveedor.
                    $proveedor = new Proveedor();
                    $proveedor->id_contribuyente = $request->idContribuyente;
                    $proveedor->observacion = $request->observacion;
                    $proveedor->estado = 1;
                    $proveedor->fecha_registro = new Carbon();
                    $proveedor->save();
                }
            }

            $contribuyente = Contribuyente::find($proveedor->id_contribuyente); //where("id_contribuyente", $proveedor->id_contribuyente)->first();
            // $contactoProveedor = ContactoContribuyente::where("id_contribuyente", $proveedor->id_contribuyente)->first();
            $cuentaBancariaProveedor = CuentaContribuyente::where("id_contribuyente", $proveedor->id_contribuyente)->first();
            // $establecimientoProveedor = EstablecimientoProveedor::where("id_proveedor", $proveedor->id_proveedor)->first();

            $contribuyente->id_tipo_contribuyente = $request->tipoContribuyente;
            $contribuyente->id_doc_identidad = $request->tipoDocumentoIdentidad > 0 ? $request->tipoDocumentoIdentidad : null;
            $contribuyente->nro_documento = $request->nroDocumento;
            $contribuyente->razon_social = $request->razonSocial;
            $contribuyente->direccion_fiscal = $request->direccion;
            $contribuyente->id_pais = $request->pais > 0 ? $request->pais : null;
            $contribuyente->ubigeo = $request->ubigeoProveedor;
            $contribuyente->telefono = $request->telefono;
            $contribuyente->celular = $request->celular;
            $contribuyente->estado = 1;
            $contribuyente->email = $request->email;
            $contribuyente->transportista = false;
            $contribuyente->save();

            if (isset($request->idEstablecimiento)) {
                $countEstablecimiento = count($request->idEstablecimiento);
                for ($i = 0; $i < $countEstablecimiento; $i++) {
                    if ($request->estadoEstablecimiento[$i] == 1 && $request->idEstablecimiento[$i] > 0) {
                        $establecimientoSeleccionado = EstablecimientoProveedor::where("id_establecimiento", $request->idEstablecimiento[$i])->first();
                        if ($establecimientoSeleccionado) {
                            $establecimientoSeleccionado->direccion = $request->direccionEstablecimiento[$i];
                            $establecimientoSeleccionado->horario = $request->horarioEstablecimiento[$i];
                            $establecimientoSeleccionado->ubigeo = $request->ubigeoEstablecimiento[$i] > 0 ? $request->ubigeoEstablecimiento[$i] : null;
                            $establecimientoSeleccionado->save();
                        }
                    } elseif ($request->estadoEstablecimiento[$i] == 7 && $request->idEstablecimiento[$i] > 0) {
                        $establecimientoSeleccionado = EstablecimientoProveedor::where("id_establecimiento", $request->idEstablecimiento[$i])->first();
                        if ($establecimientoSeleccionado) {
                            // Debugbar::info($establecimientoSeleccionado->id_establecimiento);
                            $establecimientoSeleccionado->estado = 7;
                            $establecimientoSeleccionado->save();
                        }
                    } elseif ($request->estadoEstablecimiento[$i] == 1 && ($request->idEstablecimiento[$i] == '' || $request->idEstablecimiento[$i] == null || $request->idEstablecimiento[$i] == 0)) {
                        $nuevoEstablecimientoProveedor = new EstablecimientoProveedor();
                        $nuevoEstablecimientoProveedor->id_proveedor = $proveedor->id_proveedor;
                        $nuevoEstablecimientoProveedor->fecha_registro = new Carbon();
                        $nuevoEstablecimientoProveedor->estado = 1;
                        $nuevoEstablecimientoProveedor->direccion = $request->direccionEstablecimiento[$i];
                        $nuevoEstablecimientoProveedor->horario = $request->horarioEstablecimiento[$i];
                        $nuevoEstablecimientoProveedor->ubigeo = $request->ubigeoEstablecimiento[$i] > 0 ? $request->ubigeoEstablecimiento[$i] : null;
                        $nuevoEstablecimientoProveedor->save();
                    }
                }
            }

            if (isset($request->idContacto)) {
                $countContacto = count($request->idContacto);

                for ($i = 0; $i < $countContacto; $i++) {

                    if ($request->estadoContacto[$i] == 1 && $request->idContacto[$i] > 0) {
                        $contactoSeleccionado = ContactoContribuyente::where("id_datos_contacto", $request->idContacto[$i])->first();
                        if ($contactoSeleccionado) {
                            $contactoSeleccionado->nombre = $request->nombreContacto[$i];
                            $contactoSeleccionado->telefono = $request->telefonoContacto[$i];
                            $contactoSeleccionado->email = $request->emailContacto[$i];
                            $contactoSeleccionado->cargo = $request->cargoContacto[$i];
                            $contactoSeleccionado->direccion = $request->direccionContacto[$i];
                            $contactoSeleccionado->horario = $request->horarioContacto[$i];
                            $contactoSeleccionado->ubigeo = $request->ubigeoContactoProveedor[$i] > 0 ? $request->ubigeoContactoProveedor[$i] : null;
                            $contactoSeleccionado->save();
                        }
                    } elseif ($request->estadoContacto[$i] == 7 && $request->idContacto[$i] > 0) {
                        $contactoSeleccionado = ContactoContribuyente::where("id_datos_contacto", $request->idContacto[$i])->first();
                        if ($contactoSeleccionado) {
                            $contactoSeleccionado->estado = 7;
                            $contactoSeleccionado->save();
                        }
                    } elseif ($request->estadoContacto[$i] == 1 && ($request->idContacto[$i] == '' || $request->idContacto[$i] == null || $request->idContacto[$i] == 0)) {
                        $nuevoContactoProveedor = new ContactoContribuyente();
                        $nuevoContactoProveedor->id_contribuyente = $contribuyente->id_contribuyente;
                        $nuevoContactoProveedor->nombre = $request->nombreContacto[$i];
                        $nuevoContactoProveedor->telefono = $request->telefonoContacto[$i];
                        $nuevoContactoProveedor->email = $request->emailContacto[$i];
                        $nuevoContactoProveedor->cargo = $request->cargoContacto[$i];
                        $nuevoContactoProveedor->fecha_registro = new Carbon();
                        $nuevoContactoProveedor->direccion = $request->direccionContacto[$i];
                        $nuevoContactoProveedor->horario = $request->horarioContacto[$i];
                        $nuevoContactoProveedor->ubigeo = $request->ubigeoContactoProveedor[$i] > 0 ? $request->ubigeoContactoProveedor[$i] : null;
                        $nuevoContactoProveedor->estado = 1;
                        $nuevoContactoProveedor->save();
                    }
                }
            }

            if (isset($request->idCuenta)) {
                $countCuenta = count($request->idCuenta);
                for ($i = 0; $i < $countCuenta; $i++) {
                    if ($request->estadoCuenta[$i] == 1 && $request->idCuenta[$i] > 0) {
                        $cuentaSeleccionada = CuentaContribuyente::where("id_cuenta_contribuyente", $request->idCuenta[$i])->first();
                        if ($cuentaSeleccionada) {
                            $cuentaSeleccionada->id_banco  = $request->idBanco[$i];
                            $cuentaSeleccionada->id_tipo_cuenta  = $request->idTipoCuenta[$i] > 0 ? $request->idTipoCuenta[$i] : null;
                            $cuentaSeleccionada->id_moneda  = $request->idMoneda[$i] > 0 ? $request->idMoneda[$i] : null;
                            $cuentaSeleccionada->nro_cuenta  =  $request->nroCuenta[$i];
                            $cuentaSeleccionada->nro_cuenta_interbancaria  = $request->nroCuentaInterbancaria[$i];
                            $cuentaSeleccionada->swift  = $request->swift[$i];
                            $cuentaSeleccionada->updated_at = new Carbon();
                            $cuentaSeleccionada->save();
                        }
                    } elseif ($request->estadoCuenta[$i] == 7 && $request->idCuenta[$i] > 0) {
                        $cuentaSeleccionada = CuentaContribuyente::where("id_cuenta_contribuyente", $request->idCuenta[$i])->first();
                        if ($cuentaSeleccionada) {
                            $cuentaSeleccionada->estado  = 7;
                            $cuentaSeleccionada->updated_at = new Carbon();
                            $cuentaSeleccionada->save();
                        }
                    } elseif ($request->estadoCuenta[$i] == 1 && ($request->idCuenta[$i] == '' || $request->idCuenta[$i] == null || $request->idCuenta[$i] == 0)) {
                        $cuentaBancariaProveedor = new CuentaContribuyente();
                        $cuentaBancariaProveedor->id_contribuyente  = $contribuyente->id_contribuyente;
                        $cuentaBancariaProveedor->id_banco  = $request->idBanco[$i];
                        $cuentaBancariaProveedor->id_tipo_cuenta  = $request->idTipoCuenta[$i] > 0 ? $request->idTipoCuenta[$i] : null;
                        $cuentaBancariaProveedor->id_moneda  = $request->idMoneda[$i] > 0 ? $request->idMoneda[$i] : null;
                        $cuentaBancariaProveedor->nro_cuenta  =  $request->nroCuenta[$i] ?? null;
                        $cuentaBancariaProveedor->nro_cuenta_interbancaria  = $request->nroCuentaInterbancaria[$i] ?? null;
                        $cuentaBancariaProveedor->swift  = $request->swift[$i] ?? null;
                        $cuentaBancariaProveedor->estado  = 1;
                        $cuentaBancariaProveedor->fecha_registro  = new Carbon();
                        $cuentaBancariaProveedor->save();
                    }
                }
            }

            $dataProveedor = Proveedor::mostrar($proveedor->id_proveedor);

            DB::commit();
            return response()->json(['id_proveedor' => $proveedor->id_proveedor, 'data' => $dataProveedor, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_proveedor' => 0, 'data' => [], 'mensaje' => 'Hubo un problema al actualizar el proveedor. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public function anular(Request $request)
    {

        DB::beginTransaction();
        try {

            $mensaje = '';

            $proveedor = Proveedor::where("id_proveedor", $request->idProveedor)->first();
            $contribuyente = Contribuyente::where("id_contribuyente", $proveedor->id_contribuyente)->first();
            // $contactoProveedor = ContactoContribuyente::where("id_contribuyente", $proveedor->id_contribuyente)->first();
            // $cuentaBancariaProveedor = CuentaContribuyente::where("id_contribuyente", $proveedor->id_contribuyente)->first();

            $contribuyente->estado = 7;
            $contribuyente->save();

            $proveedor->estado = 7;
            $proveedor->save();


            DB::commit();
            return response()->json(['id_proveedor' => $proveedor->id_proveedor, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['id_proveedor' => 0, 'mensaje' => 'Hubo un problema al actualizar el proveedor. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

    public function obtenerDescripcionTipoDocumentoIdentidad($idTipoDocumento)
    {
        return Identidad::find($idTipoDocumento)->descripcion;
    }

    public function obtenerDataContribuyenteSegunNroDocumento(Request $request)
    { //$request->nroDocumento, $request->tipoDocumento
        DB::beginTransaction();
        try {

            $mensaje = '';
            $tipoEstado='';


            $contribuyente = Contribuyente::with(['tipoContribuyente',
            'proveedor',
            'tipoDocumentoIdentidad',
            'cuentaContribuyente'=> function($q){
                $q->where('adm_cta_contri.estado', '=', 1);
            },
            'cuentaContribuyente.banco',
            'cuentaContribuyente.banco.contribuyente',
            'cuentaContribuyente.tipoCuenta',
            'cuentaContribuyente.moneda',
            'pais',
            'distrito',
            'contactoContribuyente' => function($q){
                $q->where('adm_ctb_contac.estado', '=', 1);
            },
            'proveedor.establecimientoProveedor' => function($q){
                $q->where('establecimiento_proveedor.estado', '=', 1);
            },
            'proveedor.establecimientoProveedor.estadoEstablecimiento',
            'proveedor.estadoProveedor'
            ])->where([["nro_documento", $request->nroDocumento], ["id_doc_identidad", $request->tipoDocumento]])->orWhere([["nro_documento", $request->nroDocumento]])->first();



            if ((!empty($contribuyente))) {
                $mensaje = 'Se encontró una coincidencia con el mismo número de documento';
                $tipoEstado='success';
            }


            DB::commit();
            return response()->json(['data' => $contribuyente, 'tipo_estado'=>$tipoEstado, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['data' => null, 'tipo_estado'=>$tipoEstado, 'mensaje' => 'Hubo un problema en el controllador para  obtener los datos del contribuyente. Por favor intentelo de nuevo. Mensaje de error: ' . $e->getMessage()]);
        }
    }

}
