<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Model;

class PresupuestoTitulo extends Model
{
    //
    protected $table = 'finanzas.presup_titu';

    protected $primaryKey = 'id_titulo';

    public $timestamps = false;
/*
   protected $fillable = [
        'codigo',
        'descripcion',
        'direccion',
        'estado',
    ];
   */
    protected $guarded = ['id_titulo'];

    public function presupuesto(){
    	return $this->belongsTo(Presupuesto::class, 'id_presup');
	}



	// Survey model
// loads only direct children - 1 level
	public function hijos()
	{
		return $this->hasMany(self::class, 'cod_padre', 'codigo');
	}

// recursive, loads all descendants
	public function hijosRecursivo()
	{
		return $this->hijos()->with('hijosRecursivo');
		// which is equivalent to:
		// return $this->hasMany('Survey', 'parent')->with('childrenRecursive);
	}

// parent
	public function padre()
	{
		return $this->belongsTo(self::class,'cod_padre','codigo');
	}

// all ascendants
	public function padreRecursivo()
	{
		return $this->padre()->with('padreRecursivo');
	}


}
