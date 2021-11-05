<?php


namespace App\Helpers\mgcp;


use App\Models\User;
use Illuminate\Support\Facades\DB;

class OportunidadResumenHelper
{
    public static function obtenerResponsables()
    {
        return User::whereRaw('id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado=false)')->orderBy('name', 'asc')->get();
        //return User::selectRaw('*')->get();
    }

    public static function obtenerSumaImportes()
    {
        return DB::select("SELECT e.id, estado, COUNT(id_estado) AS cantidad,
SUM(CASE WHEN moneda='s' THEN importe ELSE 0 END) AS suma_soles,
SUM(CASE WHEN moneda='d' THEN importe ELSE 0 END) AS suma_dolares
FROM mgcp_oportunidades.estados AS e
LEFT JOIN mgcp_oportunidades.oportunidades AS o ON o.id_estado=e.id AND eliminado=false
GROUP BY e.id, estado
ORDER BY e.id ASC");
    }

    public static function obtenerSumaImportesResponsable($responsable)
    {
        return DB::select("SELECT e.id, estado, COUNT(estado) AS cantidad,
SUM(CASE WHEN moneda='s' THEN importe ELSE 0 END) AS suma_soles,
SUM(CASE WHEN moneda='d' THEN importe ELSE 0 END) AS suma_dolares
FROM mgcp_oportunidades.estados AS e
LEFT JOIN mgcp_oportunidades.oportunidades AS o ON o.id_estado=e.id AND eliminado=false
AND id_responsable=?
GROUP BY e.id, estado
ORDER BY e.id ASC", [$responsable->id]);
    }

    public static function obtenerSumaImporteEstadoResponsable($estado, $responsable)
    {
        return DB::select("SELECT
SUM(CASE WHEN moneda='s' THEN importe ELSE 0 END) AS suma_soles,
SUM(CASE WHEN moneda='d' THEN importe ELSE 0 END) AS suma_dolares
FROM mgcp_oportunidades.oportunidades
WHERE eliminado=false AND id_estado IN (" . $estado . ")
AND id_responsable=?", [$responsable->id]);
    }
}
