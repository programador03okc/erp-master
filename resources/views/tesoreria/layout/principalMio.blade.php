<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ config('app.name', 'Sistema ERP') }} | {{ ($pagina['titulo'] ?? '') }}</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('datatables/DataTables/css/dataTables.bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('fonts/awesome/css/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.css') }}">
	<link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-treeview/bootstrap-treeview.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2-bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/gantt/dhtmlxgantt.css') }}">
	<link rel="stylesheet" href="{{ asset('css/app_new_erp.css') }}">
	<!-- ESTILOS -->
@yield('styles_modulo')
@yield('styles_seccion')
	<style type="text/css">
		.select2-dropdown {
			z-index: 999999;
		}
	</style>
<!-- FIN ESTILOS -->

</head>
<body class="hold-transition skin-okc fixed sidebar-mini sidebar-mini-expand-feature">
<div class="wrapper">
	<header class="main-header">

		<!-- NAVBAR -->
		<a href="modulos" class="logo">
			<span class="logo-mini"><b>OKC</b></span>
			<span class="logo-lg"><b>Ok Computer</b></span>
		</a>
		<nav class="navbar navbar-static-top" role="navigation">
			<a href="#" class="sidebar-okc" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i></a>
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<li class="okc-li-mod"><a href="/">Módulos</a></li>
					<li class="okc-li-mod">Configuración</li>
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="{{ asset('img/avatar5.png') }}" class="user-image" alt="User Image">
							<span class="hidden-xs">{{ Auth::user()->trabajador->postulante->persona->nombres }}</span>
						</a>
						<ul class="dropdown-menu">
							<li class="user-header">
								<img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
								<p>{{ Auth::user()->trabajador->postulante->persona->nombre_completo }}
									<small>Registrado
										desde {{ Auth::user()->trabajador->postulante->persona->fecha_registro }}</small>
								</p>
							</li>
							<li class="user-footer">
								<div class="pull-left"><a href="#" class="btn btn-default btn-flat">Perfil</a></div>
								<div class="pull-right">
									<a href="{{ route('logout') }}" class="btn btn-default btn-flat">Salir</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>

		<!-- FIN NAVBAR -->

	</header>
	<aside class="main-sidebar">
		<!-- MENU LATERAL -->
		<section class="sidebar">
			<div class="user-panel">
				<div class="pull-left image">
					<img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
				</div>
				<div class="pull-left info">
					<p>{{ Auth::user()->trabajador->postulante->persona->nombres }}</p>
					<a href="#"><i class="fa fa-circle"></i> {{ Auth::user()->concepto_login_rol }}</a>
				</div>
			</div>
			@yield('menu_lateral', 'seccion menu_lateral')
		</section>

		<!-- FIN MENU LATERAL -->

	</aside>
	<!-- contenido -->
	<div class="content-wrapper">

		<!-- BARRA OPCIONES CRUD -->
	@if ((isset($pagina['tiene_menu'])) && ($pagina['tiene_menu']))
		@include('tesoreria.partials.crud_btn')
	@endif
	<!-- FIN BARRA OPCIONES CRUD -->

		<!-- Vistas -->
		<section class="content">

			<!-- CONTENIDO -->
		@yield('contenido', 'seccion contenido')
		<!-- FIN CONTENIDO -->

		</section>
	</div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('template/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('template/bootstrap_filestyle/bootstrap-filestyle.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
<script src="{{ asset('js/sorttable.js') }}"></script>
<script src="{{ asset('template/dist/js/app.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-treeview/bootstrap-treeview.min.js') }}"></script>
<script src="{{ asset('template/plugins/gantt/dhtmlxgantt.js') }}"></script>

<script src="{{ asset('template/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>

<script src="{{ asset('js/ini.js') }}"></script>
<script src="{{ asset('js/function.js') }}"></script>

<!-- SCRIPTS -->
@yield('scripts_modulo')
@yield('scripts_seccion')
<!-- FIN SCRIPTS -->

</body>
</html>
