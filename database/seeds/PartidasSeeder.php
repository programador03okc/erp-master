<?php

use Illuminate\Database\Seeder;
use App\Models\Presupuestos\Partida;

use Illuminate\Support\Facades\DB;

class PartidasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partidas = Partida::all();
        
        foreach($partidas as $par){
            $pardet = DB::table('finanzas.presup_pardet')->where('id_pardet',$par->id_pardet)->first();
            
            if ($pardet!==null){
                $par->update(['descripcion' => $pardet->descripcion]);
            }
        }
    }
}
