<?php

namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\EntidadHelper;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Helpers\mgcp\ProductoHelper;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\mgcp\AcuerdoMarco\Proforma\Proforma;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ProformaIndividualRegistroHelper {

    public static function registrar($tipoProforma, &$empresa, &$acuerdo, $fila)
    {
        if ($tipoProforma == 'GRANCOMPRA') {
            $proforma = GranCompra::find($fila->N_Proforma) ?? new GranCompra();
        } else {
            $proforma = CompraOrdinaria::find($fila->N_Proforma) ?? new CompraOrdinaria();
        }

        if ($proforma->estado == $fila->C_Estado) {
            return array('mensaje' => 'Ignorado: procesado anteriormente', 'tipo' => 'warning');
        }
        DB::beginTransaction();
        try
        {
            $proforma->id_empresa = $empresa->id;
            $proforma->requerimiento = $fila->C_Requerimento;
            $proforma->id_entidad = EntidadHelper::obtenerIdPorRuc($fila->C_Ruc, $fila->C_Entidad, $fila->N_EntidadIndicadorSemaforo);
            $proforma->fecha_emision = Carbon::createFromFormat('d/m/Y', $fila->C_FechaEmision)->toDateString();
            $proforma->fecha_limite = Carbon::createFromFormat('d/m/Y', $fila->C_FechLimCoti)->toDateString();
            $proforma->estado =  $fila->C_Estado;
            $proforma->nro_proforma = $fila->N_Proforma;
            $proforma->nro_requerimiento = $fila->N_Requerimento;
            $proforma->proforma = $fila->C_Proforma;
            self::procesarDetalles($empresa, $proforma, $acuerdo);
            if ($tipoProforma == 'GRANCOMPRA' && $proforma->estado == 'PENDIENTE' && $proforma->id_ultimo_usuario == null) {
                $proforma->precio_publicar = null;
            }
            $proforma->save();
            DB::commit();
            return array('mensaje' => 'Procesado', 'tipo' => 'success');
        } catch (Exception $ex) {
            DB::rollBack();
            return array('mensaje' => 'Error: ' . $ex->getMessage(), 'tipo' => 'danger');
        }
        return true;
    }

    private static function procesarDetalles(&$empresa, &$proforma, &$acuerdo)
    {
        $fila=ProformaPortalHelper::obtenerDetalles($empresa,$proforma, 0);
        $proforma->inicio_entrega = Carbon::createFromFormat('d/m/Y', $fila->pObjecto->entregas[0]->C_FInicioEntrega)->toDateString();
        $proforma->fin_entrega = Carbon::createFromFormat('d/m/Y', $fila->pObjecto->entregas[0]->C_FFinEntrega)->toDateString();

        $proforma->software_educativo = $fila->pObjecto->productos[0]->N_AplicaSoftEduc == 'SI';
        $proforma->moneda_ofertada = $fila->pObjecto->productos[0]->C_Moneda;
        $proforma->precio_unitario_base = $fila->pObjecto->productos[0]->N_PrecioUnitarioBase;
        $proforma->precio_publicar = $fila->pObjecto->productos[0]->N_PrecioOfertado;
        $proforma->cantidad = $fila->pObjecto->productos[0]->N_Cantidad;
        $proforma->lugar_entrega = $fila->pObjecto->entregas[0]->C_Direccion;
        $proforma->plazo_publicar = $fila->pObjecto->entregas[0]->N_Plazo;
        $proforma->requiere_flete = $fila->pObjecto->entregas[0]->m_RProductoEntrega[0]->N_RequiereFlete == 1;

        $proforma->puede_restringir = ($fila->pObjecto->N_MontoMinimoAtencion==1 && $fila->pObjecto->N_PermiteRestringir);
        $proforma->tipo_cambio = $fila->pObjecto->N_TipoCambio;

        if ($proforma->estado == 'PENDIENTE' && $proforma->requiere_flete) {
            $proforma->costo_envio_publicar = null;
        } else {
            $proforma->costo_envio_publicar = $fila->pObjecto->entregas[0]->m_RProductoEntrega[0]->N_CostoEnvio;
        }
        $proforma->id_departamento = Departamento::obtenerPorDireccion($proforma->lugar_entrega)->id;
        $proforma->aplica_igv = $fila->pObjecto->entregas[0]->N_AplicaIGV == 1;
        $proforma->pcompra_detalle_entrega = $fila->pObjecto->entregas[0]->m_RProductoEntrega[0]->N_PCompraDetalle_Entrega;
        $proforma->id_producto = ProductoHelper::obtenerIdPorDescripcion($acuerdo, $fila->pObjecto->productos[0]->C_Ficha, $fila->pObjecto->productos[0]->C_ArchivoDescriptivo);
    }
}