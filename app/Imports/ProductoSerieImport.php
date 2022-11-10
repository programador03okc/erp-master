<?php

namespace App\Imports;

use App\Models\almacen\softlink\ProductoSerie;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class ProductoSerieImport implements ToCollection, WithHeadingRow
{
    private $numRows = 0;
    private $almacen;

    public function __construct($almacen)
    {
        $this->almacen = $almacen;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $producto01 = str_replace("'", "", str_replace("", "", $row['producto']));
                $producto = htmlspecialchars(trim($producto01));
            $serie01 = str_replace("'", "", str_replace("", "", $row['serie']));
                $serie = htmlspecialchars(trim($serie01));
            $tipo01 = str_replace("'", "", $row['tipo']);
                $tipo = trim($tipo01);
            $documento01 = str_replace("'", "", $row['documento']);
                $documento = trim($documento01);

            $queryProducto = DB::table('almacen.alm_prod')->where('descripcion', 'LIKE', '%'.$producto.'%')->first();
                $id_producto = ($queryProducto) ? $queryProducto->id_producto : null;
                $codigo = ($queryProducto) ? $queryProducto->cod_softlink : null;

            $fecha01 = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha']));
            $fecha = $fecha01->format('Y-m-d');

            if ($producto != '') {
                $registro = new ProductoSerie();
                    $registro->id_almacen = $this->almacen;
                    $registro->id_producto = $id_producto;
                    $registro->codigo = $codigo;
                    $registro->nombre = $producto;
                    $registro->serie = $serie;
                    $registro->fecha = $fecha;
                    $registro->tipo = $tipo;
                    $registro->documento = $documento;
                    $registro->auxiliar = $row['producto'];
                $registro->save();
                $this->numRows++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->numRows;
    }
}
