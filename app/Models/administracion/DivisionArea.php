<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionArea extends Model
{
    protected $table = 'administracion.division';
    protected $primaryKey = 'id_division';
    public $timestamps = false;
}