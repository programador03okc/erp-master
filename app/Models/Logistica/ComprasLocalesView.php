<?php


namespace App\Models\Logistica;

 
use Illuminate\Database\Eloquent\Model;
 

class ComprasLocalesView extends Model
{

    protected $table = 'logistica.compras_locales_view';
    protected $primaryKey = 'id_detalle_orden';
    public $timestamps = false;


}

