<?php

namespace App\Helpers\Almacen;

use App\Helpers\mgcp\WebHelper;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Estado;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use Carbon\Carbon;

class TransformacionHelper
{

    public static function descargarArchivos($id)
    {
        // $orden=OrdenCompraAm::find($id);
        $carpeta=storage_path('app/public/files/almacen/temporal/');
        $archivos=array('hoja-transformacion-prueba.pdf');
        $helper = new WebHelper();
        //Descargar
        // $helper->descargarArchivo('/necesidades/requerimiento/listado/imprimir_transformacion/'.$id, $archivos[0]);
        $helper->descargarArchivo('https://apps1.perucompras.gob.pe/OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=' . $id . '&ImprimirCompleto=1', $archivos[0]);

        return $archivos;
    }
}
