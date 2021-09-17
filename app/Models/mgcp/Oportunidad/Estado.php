<?php

namespace App\Models\mgcp\Oportunidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    protected $table = 'mgcp_oportunidades.estados';
    public $timestamps = false;

}
