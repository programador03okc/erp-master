@include('layout.head')
@include('layout.menu_tesoreria')
<!-- BARRA OPCIONES CRUD -->
@if ((isset($pagina['tiene_menu'])) && ($pagina['tiene_menu']))
	{{--@include('tesoreria.partials.crud_btn')--}}
	@include('layout.body')
@else
	@include('layout.body_sin_option')
@endif
<!-- FIN BARRA OPCIONES CRUD -->
@yield('contenido', 'seccion contenido')

@include('layout.footer')
@include('layout.scripts')
<!-- SCRIPTS -->
@yield('scripts_modulo')
@yield('scripts_seccion')
<!-- FIN SCRIPTS -->
@include('layout.fin_html')
