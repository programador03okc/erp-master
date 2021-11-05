<?php

namespace App\Helpers\mgcp;

use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Estado;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class OrdenCompraDirectaHelper
{
    public static function generarCodigo()
    {
        $fecha=Carbon::now('America/Lima');
        $codigo="DIRECTA-".$fecha->year.'-'.$fecha->format('m').'-';
        $total=OrdenCompraDirecta::where('nro_orden','like',$codigo.'%')->count();
        $codigo.=str_pad(($total+1), 3, "0", STR_PAD_LEFT);
        return $codigo;
    }

    public static function copiarArchivos($idOrden)
    {
        $archivos=[];
        $carpetaDestino=storage_path('app/mgcp/ordenes-compra/temporal/');
        $carpetaOrigen = storage_path('app/mgcp/ordenes-compra/directas/' . $idOrden);
        if (is_dir($carpetaOrigen)) {
            $files = File::files($carpetaOrigen);
            foreach ($files as $file) {
                copy($carpetaOrigen.'/'.$file->getFilename(),$carpetaDestino.$file->getFilename());
                $archivos[]=$carpetaDestino.$file->getFilename();
                //$archivos .= '<li style="margin-bottom: 5px"><a target="_blank" href="' . route('mgcp.ordenes-compra.propias.directas.descargar-archivo', ['id' => $this->attributes['id'], 'archivo' => $file->getFilename()]) . '">' . $file->getFilename() . '</a></li>';
            }
        }
        return $archivos;
    }
}
