<?php

namespace App\models\Gerencial;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //
    protected $table = 'gerencial.cliente';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
    // protected $fillable = [
    //     'ruc',
    //     'nombre',
    //     'estado',
    // ];
    public function cliente()
    {
        return $this->belongsTo(Cobranza::class, 'id_cliente', 'id_cliente');
    }
}
