<?php

//$gruposBtn['generales'] = ['deshabilitar', 'eliminar'];

$defaults = [
	[
		'class' => 'pull-right',
		'title' => 'CRUD',
		'size' => 'sm',
		'botones' => [
			['title' => 'Ver', 'class' => 'bg-blue', 'icon' => 'fas fa-eye'],
			['title' => 'Editar', 'class' => 'bg-yellow', 'icon' => 'fas fa-edit'],
			['title' => 'Eliminar', 'class' => 'bg-red', 'icon' => 'fas fa-trash'],
		]
	]
];

if (isset($gruposBtn['generales'])){
	$detallesBtn = [
		'class' => 'pull-right',
		'title' => 'CRUD',
		'size' => 'sm'
		];
	foreach ($gruposBtn['generales'] as $boton){
		switch ($boton){
			case 'nuevo':
				$botones[] = [
					'title' => 'Nuevo',
					'class' => 'bg-blue',
					'icon' => 'fas fa-file'
				];
				break;
			case 'editar':
				$botones[] = [
					'title' => 'Editar',
					'class' => 'bg-green',
					'icon' => 'fas fa-edit'
				];
				break;
			/*case 'nuevo':
				$botones[] = [
					'title' => 'Nuevo',
					'class' => 'bg-blue',
					'icon' => 'fas fa-file'
				];
				break;*/
			case 'ver':
				$botones[] = [
					'title' => 'Ver',
					'class' => 'bg-blue',
					'icon' => 'fas fa-eye'
				];
				break;
			case 'deshabilitar':
				$botones[] = [
					'title' => 'Deshabilitar',
					'class' => 'bg-yellow',
					'icon' => 'fas fa-ban'
				];
				break;
			case 'eliminar':
				$botones[] = [
					'title' => 'Eliminar',
					'class' => 'bg-red',
					'icon' => 'fas fa-trash'
				];
				break;
		}
	}
	$detallesBtn['botones'] = $botones;

	$gruposBtnFinal[] = $detallesBtn;
}
else{
	$gruposBtnFinal = $defaults;
}

//dd($gruposBtnFinal);
?>
@foreach($gruposBtnFinal as $grupo) <div class="btn-group {{ $grupo['class'] }}" role="group"> @foreach($grupo['botones'] as $boton) <button type="button" class="btn btn-sm btn-log {{ $boton['class'] }} btn{{ preg_replace('/\s/', '', (ucwords($boton['title'])) ) }}Fila" title="{{ $boton['title'] }}"><i class="fas {{ $boton['icon'] }} fa-sm"></i></button> @endforeach </div> @endforeach
