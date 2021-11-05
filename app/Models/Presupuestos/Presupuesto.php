<?php

namespace App\Models\Presupuestos;

use App\Models\Administracion\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Presupuesto extends Model
{
    protected $table = 'finanzas.presup';

    protected $primaryKey = 'id_presup';

    public $timestamps = false;

    protected $fillable = [
        "id_empresa",
        "id_grupo",
        "fecha_emision",
        "codigo",
        "descripcion",
        "moneda",
        "responsable",
        "unid_program",
        "cantidad",
        "estado",
        "fecha_registro",
        "tp_presup"
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function monedaSeleccionada()
    {
        return $this->belongsTo(Moneda::class, 'moneda');
    }

    public function Partidas()
    {
        return $this->hasMany(Partida::class, 'id_presup')->where('estado', 1);
    }

    public function Titulos()
    {
        return $this->hasMany(Titulo::class, 'id_presup')->where('estado', 1);
    }

    public static function mostrarPartidas($id_grupo, $id_proyecto = null)
    {
        if ($id_proyecto != null || $id_proyecto != '') {

            $presup = Presupuesto::where([
                ['id_proyecto', '=', $id_proyecto],
                ['estado', '=', 1],
                ['tp_presup', '=', 4]
            ])
                ->get();
        } else {

            $presup = Presupuesto::where([
                ['id_grupo', '=', $id_grupo],
                ['id_proyecto', '=', null],
                ['estado', '=', 1],
                ['tp_presup', '=', 2]
            ])
                ->get();
        }

        foreach ($presup as $p) {
            $titulos = DB::table('finanzas.presup_titu')
                ->where([
                    ['id_presup', '=', $p->id_presup],
                    ['estado', '=', 1]
                ])
                ->orderBy('presup_titu.codigo')
                ->get();
            $partidas = DB::table('finanzas.presup_par')
                ->select('presup_par.*', 'presup_pardet.descripcion as des_pardet')
                ->join('finanzas.presup_pardet', 'presup_pardet.id_pardet', '=', 'presup_par.id_pardet')
                ->where([
                    ['presup_par.id_presup', '=', $p->id_presup],
                    ['presup_par.estado', '=', 1]
                ])
                ->orderBy('presup_par.codigo')
                ->get();
        }

        return json_encode(['presupuesto' => $presup, 'titulos' => $titulos, 'partidas' => $partidas]);
    }
}
