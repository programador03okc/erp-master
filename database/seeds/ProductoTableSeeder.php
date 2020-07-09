<?php

use Illuminate\Database\Seeder;

class ProductoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productos = DB::table('almacen.alm_prod')->where('estado',1)->get();

        foreach ($productos as $prod) {
            $cat = DB::table('almacen.alm_cat_prod')->select('codigo')
                ->where('id_categoria', $prod->id_categoria)
                ->first();

            $subcat = DB::table('almacen.alm_subcat')->select('codigo')    
                ->where('id_subcategoria', $prod->id_subcategoria)
                ->first();

            $count = DB::table('almacen.alm_prod')
                ->where([['id_categoria', '=', $prod->id_categoria],
                        ['id_subcategoria', '=', $prod->id_subcategoria],
                        ['id_clasif','=',$prod->id_clasif],
                        ['id_producto','<',$prod->id_producto]])//solo cuenta los ids menores
                ->get()->count();        
    
            $clasif = $this->leftZero(2,$prod->id_clasif);
            $nro = $this->leftZero(3,$count+1);

            $nextId = $cat->codigo.$subcat->codigo.$clasif.$nro;
            $id = $prod->id_producto;

            DB::table('almacen.alm_prod')->where('id_producto',$id)
            ->update(['codigo'=>$nextId]);

            DB::table('almacen.alm_item')->insert([   
                    'id_producto' => $id,
                    'codigo' => $nextId,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ]);
        }
    }

    public function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
}
