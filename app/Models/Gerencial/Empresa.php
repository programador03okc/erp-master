<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    //
    protected $table = 'gerencial.empresa';
    protected $primaryKey = 'id_empresa';
    protected $fillable = [
        'ruc',
        'nombre' ,
        'codigo',
        'estado' ,
    ];
    public $timestamps = false;

    public function empresaCobranza()
    {
        return $this->belongsTo(RegistroCobranza::class,'id_empresa', 'id_empresa');
    }
}
