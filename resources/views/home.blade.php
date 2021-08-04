@extends('layout.base')

@section('cabecera')
Módulos
@endsection

@section('body')

		@include('layout.header')

        <div class="okc-content">
            <section class="content">
            <div class="container">
                <div class="row">{!! $modulos !!}</div>
            </div>
            </section>
        </div>
    </div>
    <script>
		// document.addEventListener('DOMContentLoaded', function(){ 
		// 	get_notificaciones_sin_leer_interval();
		// }, false);

		</script>
@endsection