<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PresupuestoPartida extends Model
{
    //
    protected $table = 'finanzas.presup_par';

    protected $primaryKey = 'id_partida';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_partida'];

    public function presupuesto(){
    	return $this->belongsTo(Presupuesto::class, 'id_presup');
	}






}
