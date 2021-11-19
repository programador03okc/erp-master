@extends('layout.base')

	@section('body')
	<div class="wrapper">
		@include('layout.header')
		<aside class="main-sidebar">
		<section class="sidebar">
			<div class="user-panel">
			<div class="pull-left image">
				<img src="{{asset('images/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p>Bienvenido(a)<br><br>{{ Auth::user()->nombre_corto }}</p>
			</div>
			</div>
			@yield('sidebar')
		</section>
		</aside>
		<!-- contenido -->
		<div class="content-wrapper" id="wrapper-okc" style="min-height: 100vh;">
			@yield('option')
		<!-- Vistas -->
		<section class="content-header">
			<h1>@yield('cabecera')</h1>

			@yield('breadcrumb')

		</section>
		<section class="content">
			@yield('content')
		</section>
		</div>
		</div>
		<script>
			// document.addEventListener('DOMContentLoaded', function(){ 
			// 	get_notificaciones_sin_leer_interval();
			// }, false);
		</script>
	@endsection
	