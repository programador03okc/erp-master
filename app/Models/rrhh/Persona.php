<?php


namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    // protected $connection = 'pgsql_rrhh'; // *conexión con okcomput_rrhh  
    protected $table = 'rrhh.rrhh_perso';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;
    protected $fillable = ['nombres','apellido_paterno','apellido_materno'];

    protected $appends = [
        'nombre_completo'
    ];

    // public function getApellidoMaternoAttribute()
    // {
    //     return ($this->apellido_materno);
    // }

    public function getNombreCompletoAttribute()
    {
        return ucwords(strtolower($this->nombres) . ' ' . strtolower($this->apellido_paterno) . ' ' . strtolower($this->apellido_materno));
    }

    public function cuentaPersona()
    {
        return $this->hasMany('App\Models\Rrhh\CuentaPersona', 'id_persona', 'id_persona');
    }
    public function tipoDocumentoIdentidad()
    {
        return $this->hasOne('App\Models\Contabilidad\TipoDocumentoIdentidad', 'id_doc_identidad', 'id_documento_identidad')->withDefault([
            'id_doc_identidad' => null,
            'descripcion' => null,
            'longitud' => null,
            'estado' => null
        ]);
    }

    public function banco()
    {
        return $this->hasOne('App\Models\Contabilidad\Banco', 'id_banco', 'id_banco');
    }
    public function tipoCuenta()
    {
        return $this->hasOne('App\Models\Contabilidad\TipoCuenta', 'id_tipo_cuenta', 'id_tipo_cuenta');
    }
    // public function postulante()
    // {
    //     return $this->hasOne('App\Models\Contabilidad\Postulante', 'id_persona', 'id_persona');
    // }
}
