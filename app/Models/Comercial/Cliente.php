<?php

namespace App\Models\Comercial;

use App\Models\Contabilidad\Contribuyente;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table='comercial.com_cliente';
    protected $fillable = ['id_contribuyente', 'codigo', 'estado', 'fecha_registro'];
    public $timestamps=false;
    protected $primaryKey='id_cliente';

    public function contribuyente(){
        return $this->hasOne(Contribuyente::class, 'id_contribuyente', 'id_contribuyente');
    }


}
