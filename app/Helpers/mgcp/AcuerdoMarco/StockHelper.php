<?php

namespace App\Helpers\mgcp\AcuerdoMarco;

use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Producto\NroParteIgnorado;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;

class StockHelper
{
    public static function calcularCantidad($precioUnitario, $moneda, $tipoCambio)
    {
        $precioUnitario = str_replace(['$', 'S/', ' ', ','], '', $precioUnitario);
        $dividendo = 150000; //Sugerido por JMB, monto maximo para cotizar proformas de compra ordinaria + 50,0000
        $stock = round($dividendo / (($moneda == 'USD' ? $tipoCambio : 1) * $precioUnitario))*2;
        return $stock > 300 ? 300 : $stock;
    }

    public static function publicar(Empresa $empresa, Producto $producto, $nuevoStock, $stockAnterior, $forzarActualizacion)
    {
        //$empresa = Empresa::find($idEmpresa);
        if ($nuevoStock < $stockAnterior && $forzarActualizacion == false) {
            return array('tipo' => 'warning', 'mensaje' => 'Ignorado por stock anterior');
        }

        $ignorar = NroParteIgnorado::find($producto->part_no);
        if (!is_null($ignorar)) {
            return array('tipo' => 'warning', 'mensaje' => 'Ignorado por nro. de parte');
        }

        $campo = 'id_' . $empresa->nombre_corto;
        $portal = new PeruComprasHelper();
        $portal->login($empresa, 3);
        $url = 'https://www.catalogos.perucompras.gob.pe/MejoraBasica/ModificarStock';
        $dataEnviar = array();
        $dataEnviar['ID_ProductoOfertado'] = $producto->$campo;
        $dataEnviar['N_Acuerdo'] = $producto->categoria->catalogo->acuerdoMarco->id_pc;
        $dataEnviar['N_Catalogo'] = $producto->categoria->catalogo->id_pc;
        $dataEnviar['N_Categoria'] = $producto->categoria->id_pc;
        $dataEnviar['N_StockAnt'] = $stockAnterior;
        $dataEnviar['N_Stock'] = $nuevoStock;
        $dataEnviar['__RequestVerificationToken'] = $portal->token;

        $resultado = $portal->parseHtml($portal->enviarData($dataEnviar, $url));
        $mensaje = $resultado->find('div[id=MensajeModal] div.modal-body', 0)->plaintext;
        $portal->finalizar();
        if (strpos($mensaje, 'satisf') !== false) {
            return array('tipo' => 'success', 'mensaje' => 'Stock actualizado');
        } else {
            return array('tipo' => 'danger', 'mensaje' => $mensaje);
        }
    }
}
