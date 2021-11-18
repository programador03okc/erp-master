<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 
use Debugbar;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class CuadroCostoController extends Controller
{

    function detalles($id)
    {

        return $id;
    }
}