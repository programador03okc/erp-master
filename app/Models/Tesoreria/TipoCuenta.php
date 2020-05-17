<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class TipoCuenta extends Model
{
    //
    protected $table = 'contabilidad.adm_tp_cta';
    //primary key
    protected $primaryKey = 'id_tipo_cuenta';
    //  public $incrementing = false;
    //Timesptamps
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado'

    ];

    protected $guarded = ['id_tipo_cuenta'];

}
