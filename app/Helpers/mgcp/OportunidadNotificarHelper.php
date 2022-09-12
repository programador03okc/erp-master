<?php

namespace App\Helpers\mgcp;

use Illuminate\Support\Facades\Mail;
use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\mgcp\Oportunidad\Notificar;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Exception;
use Illuminate\Support\Facades\Auth;

class OportunidadNotificarHelper
{

    public static function nueva($oportunidad)
    {
        try
        {
            $correos = OportunidadNotificarHelper::obtenerCorreos();
            foreach ($correos as $correo) {
                // Mail::send('mgcp.oportunidad.email.nueva', ['oportunidad' => $oportunidad,
                    'autor' => Auth::user()], function ($message) use ($correo, $oportunidad) {
                    $message->from(config('mgcp.sistema.correo'), config('mgcp.sistema.nombre'));
                    $message->subject('Reg. de oportunidad - ' . $oportunidad->entidad->razon_social . ' - ' . $oportunidad->oportunidad);
                    $message->to($correo);
                });
            }
        }
        catch (\Exception $ex)
        {

        }
    }

    public static function eliminar($oportunidad)
    {
        $correos = OportunidadNotificarHelper::obtenerCorreos($oportunidad->id);
        foreach ($correos as $correo) {
            // Mail::send('mgcp.oportunidad.email.eliminada', ['oportunidad' => $oportunidad,
                'autor' => Auth::user()], function ($message) use ($correo, $oportunidad) {
                $message->from(config('mgcp.sistema.correo'), config('mgcp.sistema.nombre'));
                $message->subject('Eliminación de oportunidad - ' . $oportunidad->entidad->razon_social . ' - ' . $oportunidad->oportunidad);
                $message->to($correo);
            });
        }
    }

    public static function otro($oportunidad, $tipo, $data)
    {
        $correos = OportunidadNotificarHelper::obtenerCorreos($oportunidad->id);
        foreach ($correos as $correo) {

            // Mail::send('mgcp.oportunidad.email.otro', ['oportunidad' => $oportunidad,
                'tipo' => $tipo, 'autor' => Auth::user(), 'data' => $data], function ($message) use ($correo, $oportunidad, $tipo) {
                $message->from(config('mgcp.sistema.correo'), config('mgcp.sistema.nombre'));
                $message->subject(ucwords($tipo) . ' en oportunidad ' . $oportunidad->codigo_oportunidad . ', cliente ' . $oportunidad->entidad->razon_social);
                $message->to($correo);
            });
        }
    }

    public static function obtenerCorreos($idOportunidad = null)
    {
        $correos = [];
        $notificar = Notificar::where('id_oportunidad', $idOportunidad)->get();
        foreach ($notificar as $fila) {
            $correos[] = $fila->correo;
        }
        $roles = RolUsuario::with('usuario')->where('id_rol', 9)->get();
        foreach ($roles as $rol) {
            $correos[] = $rol->usuario->email;
        }

        if ($idOportunidad != null) {
            $oportunidad = Oportunidad::find($idOportunidad);
            $correos[] = $oportunidad->responsable->email;
        }
        return array_unique($correos);
    }

}
