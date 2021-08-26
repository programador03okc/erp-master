<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Pais;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\TipoContribuyente;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Contabilidad\TipoDocumentoIdentidad;
use App\Models\Logistica\Proveedor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

// use Debugbar;


class ProveedoresController extends Controller
{
    public function viewLista()
    {   
        $tipoDocumentos = TipoDocumentoIdentidad::mostrar();
        $tipoContribuyentes = TipoContribuyente::mostrar();
        $paises = Pais::mostrar();
        $bancos = Banco::mostrar();
        $tipo_cuenta = TipoCuenta::mostrar();
        $monedas = Moneda::mostrar();

        
        return view('logistica/gestion_logistica/proveedores/lista_proveedores',compact('paises','tipoDocumentos','tipoContribuyentes','bancos','tipo_cuenta','monedas'));

    }

    public function listaProveedores(){
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

}