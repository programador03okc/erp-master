<?php

namespace App\Helpers\mgcp;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;

class ProductoHelper {

    public static function procesarDescripcion(&$producto, $descripcion) {
        $producto->descripcion = trim(str_replace("'", "", html_entity_decode($descripcion)));
        $producto->part_no = trim(substr($producto->descripcion, strrpos($producto->descripcion, ' ')));
        $unidad = strrpos($producto->descripcion, 'UNIDADES ');
        $longitud = 9;
        if ($unidad === FALSE) {
            $unidad = strrpos($producto->descripcion, 'UNIDAD ');
            $longitud = 7;
        }
        if ($unidad !== FALSE) {
            $detalles = substr($producto->descripcion, $unidad + $longitud);
            $producto->marca = trim(substr($detalles, 0, strpos($detalles, ' ')));
            $producto->modelo = trim(substr($detalles, strpos($detalles, ' '), strrpos($detalles, ' ') - strpos($detalles, ' ')));
        } else {
            $producto->marca = 'S/M';
            $producto->modelo = 'S/M';
        }
    }

    public static function obtenerIdPorDescripcion(&$acuerdoMarco, $descripcion,$ficha) {
        $ficha='https://saeusceprod01.blob.core.windows.net/contproveedor/Documentos/Productos/'.$ficha;
        $descategoria=trim(strstr($descripcion, ':', true));
        if (in_array($descategoria,['KIT DE MANTENIMIENTO','UNIDAD DE FUSOR','UNIDAD DE RECOLECCION','KIT DE TRANSFERENCIA']))
        {
            $descategoria=str_replace('DE ','D/',$descategoria);
        }
        $categoria = Categoria::join('mgcp_acuerdo_marco.catalogos', 'categorias.id_catalogo', '=', 'catalogos.id')
                    ->where('id_acuerdo_marco', $acuerdoMarco->id)->where('categorias.descripcion', $descategoria)
                    ->orderBy('categorias.id','desc')->select(['categorias.id'])->first();
        $producto = Producto::where('id_categoria', $categoria->id)->where('ficha_tecnica',$ficha)->orderBy('id', 'desc')->first();
        if (is_null($producto)) {
            $producto = new Producto();
            ProductoHelper::procesarDescripcion($producto,$descripcion);
            $producto->ficha_tecnica=$ficha;
            $producto->id_categoria =  $categoria->id;
        }
        $producto->descripcion=$descripcion;
        $producto->save();
        return $producto->id;
    }

    public static function obtenerMMNdePortal()
    {

    }

}
