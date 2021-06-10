<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\AlmacenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProyectosController;
use App\Models\Administracion\Area;
use App\Models\Administracion\Division;
use App\Models\Administracion\Periodo;
use App\Models\Administracion\Prioridad;
use App\Models\Almacen\CategoriaAdjunto;
use App\Models\Almacen\Fuente;
use App\Models\Almacen\TipoRequerimiento;
use App\Models\Almacen\UnidadMedida;
use App\Models\Configuracion\Moneda;
use App\Models\Configuracion\Usuario;
use App\Models\Contabilidad\Banco;
use App\Models\Contabilidad\Identidad;
use App\Models\Contabilidad\TipoCuenta;
use App\Models\Logistica\Empresa;
use App\Models\Presupuestos\Presupuesto;
use Illuminate\Support\Facades\Auth;

class RequerimientoController extends Controller
{
    public function index()
    {
        
        $grupos = Auth::user()->getAllGrupo();
        $monedas = Moneda::mostrar();
        $prioridades = Prioridad::mostrar();
        $tipo_requerimiento = TipoRequerimiento::mostrar();
        $empresas = Empresa::all();
        $areas = Area::mostrar();
        $unidadesMedida = UnidadMedida::mostrar();
        $periodos = Periodo::mostrar();
        $roles = Auth::user()->getAllRol();//Usuario::getAllRol(Auth::user()->id_usuario);
        //var_dump($roles);
        //die("FIN");
        $sis_identidad = Identidad::mostrar();
        $bancos = Banco::mostrar();
        $tipos_cuenta = TipoCuenta::mostrar();
        $clasificaciones = (new AlmacenController)->mostrar_clasificaciones_cbo();
        $subcategorias = (new AlmacenController)->mostrar_subcategorias_cbo();
        $categorias = (new AlmacenController)->mostrar_categorias_cbo();
        $unidades = (new AlmacenController)->mostrar_unidades_cbo();
        $proyectos_activos = (new ProyectosController)->listar_proyectos_activos();
        $fuentes = Fuente::mostrar();
        $aprobantes = Division::mostrarFlujoAprobacion();
        $categoria_adjunto = CategoriaAdjunto::mostrar();

        return view('logistica/requerimientos/gestionar_requerimiento', compact('categoria_adjunto','aprobantes','grupos','sis_identidad','tipo_requerimiento','monedas', 'prioridades', 'empresas', 'unidadesMedida','roles','periodos','bancos','tipos_cuenta','clasificaciones','subcategorias','categorias','unidades','proyectos_activos','fuentes'));
    }

    public function mostrarPartidas($idGrupo,$idProyecto=null){
        return Presupuesto::mostrarPartidas($idGrupo,$idProyecto);

    }

    public function mostrarCategoriaAdjunto(){
        return CategoriaAdjunto::mostrar();

    }
}
