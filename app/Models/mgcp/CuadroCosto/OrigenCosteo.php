<?php

namespace App\Models\mgcp\CuadroCosto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Resumen
 * 
 * Detallado o descripcion completa del controlador
 * 
 * @author Wilmar Garibaldi Valdez <wgaribaldi@ok>
 */
class OrigenCosteo extends Model
{
    use HasFactory;
    protected $table = 'mgcp_cuadro_costos.origenes_costeo';
    public $timestamps = false;

    /**
     * Resumen
     * 
     * @param string $parametro1 Usuario que solicita aprobación
     * @param string $parametro2 Usuario que aprobará
     * 
     * @return void
     */
    public function metodoPrueba($parametro1, $parametro2)
    {

    }
}
