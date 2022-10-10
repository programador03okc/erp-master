<?php


namespace App\Models\Almacen;

 
use Illuminate\Database\Eloquent\Model;
 

class StockSeriesView extends Model
{

    protected $table = 'almacen.stock_series_view';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;


}

