<?php

namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\EntidadHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Helpers\mgcp\ProductoHelper;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Destino;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Envio;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\EnvioDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Paquete;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\PaqueteProducto;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\ProductoDetalle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ProformaPaqueteRegistroHelper
{
    public static function registrar($tipoProforma, &$empresa, &$acuerdo, $fila)
    {
        $proforma = Paquete::where('nro_requerimiento', $fila->N_Requerimento)->where('id_empresa', $empresa->id)->first() ?? new Paquete();
        if ($proforma->estado == $fila->C_Estado) {
            return array('mensaje' => 'Ignorado: procesado anteriormente', 'tipo' => 'warning');
        }
        DB::beginTransaction();
        try {
            $proforma->nro_requerimiento = $fila->N_Requerimento;
            $proforma->id_empresa = $empresa->id;
            $proforma->id_entidad = EntidadHelper::obtenerIdPorRuc($fila->C_Ruc, $fila->C_Entidad, $fila->N_EntidadIndicadorSemaforo);
            $proforma->requerimiento = $fila->C_Requerimento;
            $proforma->fecha_emision = Carbon::createFromFormat('d/m/Y', $fila->C_FechaEmision)->toDateString();
            $proforma->fecha_limite = Carbon::createFromFormat('d/m/Y', $fila->C_FechLimCoti)->toDateString();
            $proforma->estado =  $fila->C_Estado;
            $proforma->tipo = ($tipoProforma == 'NORMAL' ? 1 : 2);
            $proforma->save();
            $proforma == Paquete::where('nro_requerimiento', $fila->N_Requerimento)->where('id_empresa', $empresa->id)->first();
            self::procesarDetalles($empresa, $proforma, $acuerdo);
            $proforma->save();
            DB::commit();
            return array('mensaje' => 'Procesado', 'tipo' => 'success');
        } catch (Exception $ex) {
            DB::rollBack();
            return array('mensaje' => 'Error: ' . $ex->getMessage(), 'tipo' => 'danger');
        }
    }

    private static function procesarDetalles(&$empresa, &$proforma, &$acuerdo)
    {
        $data = ProformaPortalHelper::obtenerDetalles($empresa, $proforma, 1);
        foreach ($data->pObjecto->entregas as $fila) {

            $destino = Destino::where('nro_requerimiento_entrega', $fila->N_RequerimientoEntrega)->where('proforma_paquete_id', $proforma->id)->first() ?? new Destino();
            $destino->nro_requerimiento_entrega = $fila->N_RequerimientoEntrega;
            $destino->lugar_entrega = $fila->C_Direccion;
            $destino->inicio_entrega = Carbon::createFromFormat('d/m/Y', $data->pObjecto->entregas[0]->C_FInicioEntrega)->toDateString();
            $destino->fin_entrega = Carbon::createFromFormat('d/m/Y', $data->pObjecto->entregas[0]->C_FFinEntrega)->toDateString();
            $destino->id_departamento = Departamento::obtenerPorDireccion($fila->C_Direccion)->id;
            $destino->aplica_igv = $fila->N_AplicaIGV == 1;
            //En proformas por paquete no se puede editar el plazo de entrega
            /*if ($proforma->id_ultimo_usuario == null) {
                
            }*/
            $destino->plazo_publicar = $fila->N_Plazo;
            $destino->editar_plazo = $fila->N_EditaPlazo == 1;
            $destino->proforma_paquete_id = $proforma->id;
            //$destino->nro_requerimiento = $proforma->nro_requerimiento;
            $destino->save();
            foreach ($fila->m_RProductoEntrega as $detalle) {
                $envio = Envio::where('nro_item_entrega', $detalle->N_Ritem_Entrega)->where('proforma_paquete_destino_id', $destino->id)->first() ?? new Envio();
                $envio->nro_item_entrega = $detalle->N_Ritem_Entrega;
                //$envio->nro_requerimiento_entrega = $fila->N_RequerimientoEntrega;
                $envio->requiere_flete = $detalle->N_RequiereFlete == 1;
                $envio->proforma_paquete_destino_id = $destino->id;
                $envio->save();

                foreach ($detalle->pDetalle_Entregas as $filaEntrega) {
                    $envioDetalle = EnvioDetalle::where('nro_detalle_entrega', $filaEntrega->N_PCompraDetalle_Entrega)->where('proforma_paquete_envio_id', $envio->id)->first() ?? new EnvioDetalle();
                    $envioDetalle->nro_detalle_entrega = $filaEntrega->N_PCompraDetalle_Entrega;
                    $envioDetalle->nro_proforma = $filaEntrega->N_Proforma;
                    //$envioDetalle->requiere_flete = $filaEntrega->N_RequiereFlete == 1;
                    if ($proforma->estado == 'PENDIENTE' && $envioDetalle->requiere_flete) {
                        $envioDetalle->costo_envio_publicar = null;
                    } else {
                        $envioDetalle->costo_envio_publicar = $filaEntrega->N_CostoEnvio;
                    }
                    //$envioDetalle->nro_item_entrega = $detalle->N_Ritem_Entrega;
                    $envioDetalle->proforma_paquete_envio_id = $envio->id;
                    $envioDetalle->save();
                }
            }
        }

        foreach ($data->pObjecto->productos as $fila) {
            $producto = PaqueteProducto::where('nro_requerimiento_item', $fila->N_RequerimientoItem)->where('proforma_paquete_id', $proforma->id)->first() ?? new PaqueteProducto();
            $producto->nro_requerimiento_item = $fila->N_RequerimientoItem;
            //$producto->nro_requerimiento = $proforma->nro_requerimiento;
            $producto->proforma_paquete_id = $proforma->id;
            $producto->comentario = $fila->C_Producto;
            $producto->save();
            foreach ($fila->proformas as $detalle) {
                $productoDetalle = ProductoDetalle::where('nro_proforma', $detalle->N_Proforma)->where('proforma_paquete_producto_id', $producto->id)->first() ?? new ProductoDetalle();
                if ($fila->C_Ficha == $detalle->C_Ficha) {
                    $productoDetalle->seleccionado = $fila->N_Seleccionado == 1;
                } else {
                    $productoDetalle->seleccionado = false;
                }
                $productoDetalle->nro_proforma = $detalle->N_Proforma;
                $productoDetalle->id_producto = ProductoHelper::obtenerIdPorDescripcion($acuerdo, $detalle->C_Ficha, $detalle->C_ArchivoDescriptivo);
                $productoDetalle->moneda_ofertada = $detalle->C_Moneda;
                $productoDetalle->proforma = $detalle->C_Proforma;
                $productoDetalle->software_educativo = $detalle->N_AplicaSoftEduc == 'SI';
                $productoDetalle->cantidad = $detalle->N_Cantidad;
                $productoDetalle->precio_unitario_base = $detalle->N_PrecioUnitarioBase;
                //Si está PENDIENTE y alguien cotiza, el sistema no llegará a este punto por la condición: estado_local=estado_portal -> Ignorar
                $productoDetalle->precio_publicar = $detalle->N_PrecioOfertado;

                
                //$productoDetalle->nro_requerimiento_item = $detalle->N_RequerimientoItem;
                $productoDetalle->proforma_paquete_producto_id = $producto->id;
                $productoDetalle->save();
            }
        }
    }
}
