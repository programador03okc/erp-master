<?php


namespace App\Models\Configuracion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LogActividad extends Model {

    protected $table = 'configuracion.log_actividades';
    protected $primaryKey = 'id';
    protected $fillable = ['fecha', 'usuario_id', 'log_tipo_accion_id', 'formulario', 'tabla', 'valor_anterior', 'nuevo_valor', 'comentarios'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function log_tipo_accion()
    {
        return $this->belongsTo(LogTipoAccion::class);
    }

    public static function registrar(Usuario $usuario, $formulario, $idAccion, $tabla = null, $valorAnterior = null, $nuevoValor = null, $comentarios = null)
    {
        $log = new LogActividad();
            $log->fecha = new Carbon();
            $log->usuario_id = $usuario->id_usuario;
            $log->log_tipo_accion_id = $idAccion;
            $log->formulario = $formulario;
            $log->tabla = $tabla;
            if ($valorAnterior != null) { $log->valor_anterior = json_encode($valorAnterior, JSON_PRETTY_PRINT); }
            if ($nuevoValor != null) { $log->nuevo_valor = json_encode($nuevoValor, JSON_PRETTY_PRINT); }
            $log->comentarios = $comentarios;
        $log->save();
    }

}
