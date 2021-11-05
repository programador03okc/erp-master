<?php

namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Helpers\mgcp\PeruComprasHelper;
use App\Helpers\mgcp\ProductoHelper;
use App\Models\mgcp\AcuerdoMarco\Departamento;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\EnvioDetalle;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Paquete;
use App\Models\mgcp\AcuerdoMarco\Proforma\Proforma;
use Carbon\Carbon;

class ProformaPortalHelper
{
    public static function restringir(PeruComprasHelper &$portalHelper, &$proforma)
    {
        $dataEnviar['N_Proformacompradetalle'] = $proforma->nro_proforma;
        $dataEnviar['C_EstadoMotivo'] = 'POR MONTO MINIMO DE ATENCION';
        $dataEnviar['C_EstadoDescripcion'] = 'La proforma no supera el monto mínimo de atención';
        return json_decode($portalHelper->enviarData($dataEnviar, "https://www.catalogos.perucompras.gob.pe/t_proforma/restringir"));
    }

    public static function obtenerDetalles(&$empresa, &$proforma, $tipo)
    {
        $dataEnviar['N_Proforma'] = $tipo==0 ? $proforma->nro_proforma : null; //0: Individual, 1: Paquete
        $dataEnviar['N_Requerimiento'] = $proforma->nro_requerimiento;
        $portal = new PeruComprasHelper();

        $reintentar = true;
        while ($reintentar) {
            $data = json_decode($portal->enviarData($dataEnviar, "https://www.catalogos.perucompras.gob.pe/t_Proforma/cargarCotizar"));
            if ($data == null) {
                $portal->login($empresa, 2);
            } else {
                $reintentar = false;
            }
        }
        return $data;
    }

    public static function proformaPaqueteEnviarCotizacion(PeruComprasHelper &$portalHelper,Paquete &$proforma)
    {
        $numerosProforma=[];
        $dataEnviar = new \stdClass();
        $dataEnviar->pObjecto = new \stdClass();
        $dataEnviar->pObjecto->N_Requerimiento = $proforma->nro_requerimiento;
        $dataEnviar->pObjecto->productos = [];
        $productosSeleccionados=Paquete::join('mgcp_acuerdo_marco.proforma_paquete_productos','proforma_paquete_id','proformas_paquete.id')
        ->join('mgcp_acuerdo_marco.proforma_paquete_producto_detalles','proforma_paquete_producto_id','proforma_paquete_productos.id')
        ->where('proformas_paquete.id',$proforma->id)->where('seleccionado',true)->get();
        foreach($productosSeleccionados as $fila)
        {
            $dataProductos = new \stdClass();
            $dataProductos->N_Proforma = $fila->nro_proforma;
            $dataProductos->N_PrecioOfertado = $fila->precio_publicar ?? 0;
            array_push($dataEnviar->pObjecto->productos, $dataProductos);
            array_push($numerosProforma,$fila->nro_proforma);
        }
        $dataEnviar->pObjecto->entregas = [];
        $entregas=Paquete::join('mgcp_acuerdo_marco.proforma_paquete_destinos','proforma_paquete_id','proformas_paquete.id')
        ->where('proformas_paquete.id',$proforma->id)->select(['proforma_paquete_destinos.id','plazo_publicar','aplica_igv','proforma_paquete_destinos.id'])->get();
        foreach ($entregas as $fila)
        {
            $dataEntregas = new \stdClass();
            $dataEntregas->N_Plazo = $fila->plazo_publicar;
            $dataEntregas->N_AplicaIGV = ($fila->aplica_igv ? 1 : 0);
            array_push($dataEnviar->pObjecto->entregas, $dataEntregas);
            $dataEntregas->m_RProductoEntrega = [];
            $fletes=EnvioDetalle::join('mgcp_acuerdo_marco.proforma_paquete_envios','proforma_paquete_envio_id','proforma_paquete_envios.id')
            ->where('proforma_paquete_destino_id',$fila->id)->whereIn('nro_proforma',$numerosProforma)->get();
            foreach($fletes as $flete)
            {
                $dataProductoEntrega = new \stdClass();
                $dataProductoEntrega->N_PCompraDetalle_Entrega = $flete->nro_detalle_entrega;
                $dataProductoEntrega->N_CostoEnvio = $flete->costo_envio_publicar;
                array_push($dataEntregas->m_RProductoEntrega, $dataProductoEntrega);
            }
        }
        return json_decode($portalHelper->enviarData($dataEnviar, 'https://www.catalogos.perucompras.gob.pe/t_proforma/guardarCotizar'));
    }

    public static function proformaIndividualEnviarCotizacion(PeruComprasHelper &$portalHelper, &$proforma)
    {
        $dataEnviar = new \stdClass();
        $dataEnviar->pObjecto = new \stdClass();
        $dataEnviar->pObjecto->N_Requerimiento = $proforma->nro_requerimiento;
        $dataEnviar->pObjecto->productos = [];
        $dataProductos = new \stdClass();
        $dataProductos->N_Proforma = $proforma->nro_proforma;
        $dataProductos->N_PrecioOfertado = $proforma->precio_publicar;
        array_push($dataEnviar->pObjecto->productos, $dataProductos);
        $dataEnviar->pObjecto->entregas = [];
        $dataEntregas = new \stdClass();
        $dataEntregas->N_Plazo = $proforma->plazo_publicar;
        $dataEntregas->N_AplicaIGV = ($proforma->aplica_igv ? 1 : 0);
        $dataEntregas->m_RProductoEntrega = [];
        $dataProductoEntrega = new \stdClass();
        $dataProductoEntrega->N_PCompraDetalle_Entrega = $proforma->pcompra_detalle_entrega;
        $dataProductoEntrega->N_CostoEnvio = $proforma->costo_envio_publicar;
        array_push($dataEntregas->m_RProductoEntrega, $dataProductoEntrega);
        array_push($dataEnviar->pObjecto->entregas, $dataEntregas);
        return json_decode($portalHelper->enviarData($dataEnviar, 'https://www.catalogos.perucompras.gob.pe/t_proforma/guardarCotizar'));
    }

    public static function obtenerListado(&$portal, $idAcuerdo, $idCatalogo, $tipoProforma, $tipoContratacion, $diasAntiguedad)
    {
        $dataEnviar['N_Acuerdo'] = $idAcuerdo;
        $dataEnviar['N_Catalogo'] = $idCatalogo;
        $dataEnviar['C_Procedimiento'] = $tipoProforma;
        $dataEnviar['N_EscompraPorPaquete'] = $tipoContratacion;
        $dataEnviar['C_FechaInicio'] = Carbon::now()->subDays($diasAntiguedad)->format('d/m/Y');
        $dataEnviar['C_FechaFin'] = '31/12/' . (date('Y') + 1);
        return json_decode($portal->enviarData($dataEnviar, "https://www.catalogos.perucompras.gob.pe/t_Proforma/buscar"));
    }
}
