<?php

namespace App\Models\Almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $table = 'almacen.trans';
    protected $primaryKey = 'id_transferencia';
    public $timestamps = false;

}