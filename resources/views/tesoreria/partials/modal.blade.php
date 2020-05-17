<div class="modal fade" id="{{ $modal['id'] }}" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="{{ $modal['id'] }}Label">
	<div class="modal-dialog {{ ($modal['class'] ?? '') }}" style="{{ ($modal['style'] ?? '') }}" role="document">
		<div class="modal-content" style="overflow: auto;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="{{ $modal['id'] }}Label">{{ $modal['titulo'] }}</h4>
			</div>
			<div class="modal-body py-5">
				@yield($modal['id'], 'Default Content')

			</div>
			<div class="modal-footer">
				@if (isset($modal['botones_izquierda']))
					@foreach($modal['botones_izquierda'] as $boton)
						<button type="button" class="btn {{ ($boton['class']??'btn-default') }} pull-left" id="{{ $boton['id'] }}_{{ $modal['id'] }}">{{ $boton['txt'] }}</button>
					@endforeach
				@endif
				@if (isset($modal['botones']))
					@if (isset($modal['botones']['cancelar']) && ($modal['botones']['cancelar']))
							<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelar_{{ $modal['id'] }}">Cancelar</button>
					@endif
					@if (isset($modal['botones']['guardar']) && ($modal['botones']['guardar']))
							<button type="button" class="btn btn-primary" id="btnGuardar_{{ $modal['id'] }}">Guardar</button>
					@endif
				@else
						<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelar_{{ $modal['id'] }}">Cancelar</button>
						<button type="button" class="btn btn-primary" id="btnGuardar_{{ $modal['id'] }}">Guardar</button>
				@endif
{{--				<button type="button" class="btn btn-primary pull-left">Save changes</button>--}}


				@if (isset($modal['botones_derecha']))
					@foreach($modal['botones_derecha'] as $boton)
						<button type="button" class="btn {{ ($boton['class']??'btn-default') }}" id="{{ $boton['id'] }}_{{ $modal['id'] }}">{{ $boton['txt'] }}</button>
					@endforeach
				@endif
			</div>
		</div>
	</div>
</div>
