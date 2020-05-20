<?php

namespace App\Models\Tesoreria\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanillaPagoImport implements ToCollection, WithHeadingRow
{
	public function model(array $row)
	{
		//dd($row);
		return [
			'dni'  => $row['dni'],
			'apellidos_nombres' => $row['apellidos_y_nombres'],
			'monto' => $row['monto'],
			'empresa' => $row['empresa'],
			'sede' => $row['sede'],
			'grupo' => $row['grupo'],
			'cargo' => $row['cargo'],
		];
	}

	/**
	 * @param Collection $collection
	 */
	public function collection(Collection $collection) {
		// TODO: Implement collection() method.


	}
}
