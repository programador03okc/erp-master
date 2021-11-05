<?php


namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Proforma\Proforma;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProformaFiltrosHelper
{
    public static function actualizar(Request $request)
    {
        if ($request->chkFechaEmision == 'on') {
            session(['proformaFechaEmisionDesde' => $request->fechaEmisionDesde]);
            session(['proformaFechaEmisionHasta' => $request->fechaEmisionHasta]);
        } else {
            $request->session()->forget('proformaFechaEmisionDesde');
            $request->session()->forget('proformaFechaEmisionHasta');
        }

        if ($request->chkEmpresa == 'on') {
            if ($request->selectEmpresa != null && count($request->selectEmpresa) > 0) {
                session(['proformaEmpresas' => $request->selectEmpresa]);
            } else {
                session(['proformaEmpresas' => [0]]);
            }
        } else {
            $request->session()->forget('proformaEmpresas');
        }

        if ($request->chkFechaLimite == 'on') {
            session(['proformaFechaLimiteDesde' => $request->fechaLimiteDesde]);
            session(['proformaFechaLimiteHasta' => $request->fechaLimiteHasta]);
        } else {
            $request->session()->forget('proformaFechaLimiteDesde');
            $request->session()->forget('proformaFechaLimiteHasta');
        }

        if ($request->chkCatalogo == 'on') {
            if ($request->selectCatalogo != null && count($request->selectCatalogo) > 0) {
                session(['proformaCatalogos' => $request->selectCatalogo]);
            } else {
                session(['proformaCatalogos' => [0]]);
            }
        } else {
            $request->session()->forget('proformaCatalogos');
        }

        if ($request->chkDepartamento == 'on') {
            if ($request->selectDepartamento != null && count($request->selectDepartamento) > 0) {
                session(['proformaDepartamentos' => $request->selectDepartamento]);
            } else {
                session(['proformaDepartamentos' => [0]]);
            }
        } else {
            $request->session()->forget('proformaDepartamentos');
        }

        if ($request->chkEstado == 'on') {
            session(['proformaEstado' => $request->selectEstado]);
        } else {
            $request->session()->forget('proformaEstado');
        }

        if ($request->chkMarca == 'on') {
            if ($request->selectMarca != null && count($request->selectMarca) > 0) {
                session(['proformaMarcas' => $request->selectMarca]);
            } else {
                session(['proformaMarcas' => [0]]);
            }
        } else {
            $request->session()->forget('proformaMarcas');
        }
    }

    

    
}
