@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Logística</span></a></li>
    {{-- @if(Auth::user()->tieneSubModuloPadre(48)) --}}
    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-truck-loading"></i> <span>Compras</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            {{-- @if(Auth::user()->tieneSubModulo(25))
            @if(Auth::user()->tieneAplicacion(108)) --}}
            <li><a href="{{route('logistica.gestion-logistica.compras.pendientes.index')}}"><i class="far fa-circle fa-xs"></i> Req. Pendientes</a></li>
            {{-- @endif
            @endif --}}

            {{-- @if(Auth::user()->tieneSubModulo(25))
                @if(Auth::user()->tieneAplicacion(108)) --}}
                <li><a href="{{route('logistica.gestion-logistica.compras.ordenes.elaborar.index')}}"><i class="far fa-circle fa-xs"></i> Orden de compra / servicio</a></li>

                {{-- @endif
                @if(Auth::user()->tieneAplicacion(109)) --}}
                    <li><a href="{{route('logistica.gestion-logistica.compras.ordenes.listado.index')}}"><i class="far fa-circle fa-xs"></i> Gestión de ordenes</a></li>
                {{-- @endif
            @endif --}}
            <!-- <li><a href="{{route('logistica.distribucion.ordenes-transformacion.index')}}"><i class="far fa-circle fa-xs"></i> Envío de transformaciones </a></li> -->

            {{-- @if(Auth::user()->tieneSubModulo(28)) --}}
            <li class="treeview">
                <a href="#"><i class="fas fa-people-carry"></i> Servicios
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    {{-- @if(Auth::user()->tieneAplicacion(118)) --}}
                    <li><a href="tipoServ"><i class="far fa-circle fa-xs"></i> Tipo de Servicio</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(119)) --}}
                    <li><a href="servicio"><i class="far fa-circle fa-xs"></i> Servicio</a></li>
                    {{-- @endif --}}
                </ul>
            </li>
            {{-- @endif
            @if(Auth::user()->tieneSubModulo(26)) --}}
            <li class="treeview">
                <a href="#"><i class="fas fa-chart-bar"></i> Reportes
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    {{-- @if(Auth::user()->tieneAplicacion(110)) --}}
                    <li><a href="/logistica/reportes/productos_comprados"><i class="far fa-circle fa-xs"></i> Productos Comprados</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(111)) --}}
                    <li><a href="/logistica/reportes/compras_por_proveedor"><i class="far fa-circle fa-xs"></i> Compras por Proveedor</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(112)) --}}
                    <li><a href="/logistica/reportes/compras_por_producto"><i class="far fa-circle fa-xs"></i> Compras por Producto</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(113)) --}}
                    <li><a href="/logistica/reportes/proveedores_producto_determinado"><i class="far fa-circle fa-xs"></i> Proveedores con Producto Determinado</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(114)) --}}
                    <li><a href="/logistica/reportes/mejores_proveedores"><i class="far fa-circle fa-xs"></i> Mejores Proveedores</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(115)) --}}
                    <li><a href="/logistica/reportes/frecuencia_compras"><i class="far fa-circle fa-xs"></i> Frecuencia de Compra por Producto</a></li>
                    {{-- @endif
                    @if(Auth::user()->tieneAplicacion(116)) --}}
                    <li><a href="/logistica/reportes/historial_precios"><i class="far fa-circle fa-xs"></i> Historial de Precios</a></li>
                    {{-- @endif --}}
                </ul>
            </li>
            {{-- @endif --}}
        </ul>
    </li>
    {{-- @endif --}}
    {{-- @if(Auth::user()->tieneSubModuloPadre(48)) --}}
    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-address-book"></i> <span>Proveedores</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('logistica.gestion-logistica.proveedores.index')}}"><i class="far fa-circle fa-xs"></i> Lista de proveedores</a></li>
        </ul>
    </li>
    {{-- @endif --}}

    {{-- @if(Auth::user()->tieneSubModulo(20)) --}}
    <li class="treeview">
        <a href="#"><i class="fas fa-truck"></i> <span> Despachos</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            {{-- @if(Auth::user()->tieneAplicacion(80)) --}}
            <li><a href="{{route('logistica.distribucion.ordenes-despacho-externo.index')}}"><i class="far fa-circle fa-xs"></i> Despachos Externos </a></li>
            <li><a href="{{route('logistica.distribucion.ordenes-despacho-interno.index')}}"><i class="far fa-circle fa-xs"></i> Despachos Internos </a></li>
            {{-- @endif --}}
</ul>
</li>
{{-- @endif
@if(Auth::user()->tieneSubModuloPadre(49)) --}}
<li class="treeview">
    <a href="#"><i class="fas fa-boxes"></i> Activos
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        {{-- @if(Auth::user()->tieneSubModulo(45)) --}}
        <li class="treeview" style="height: auto;">
            <a href="#"><i class="fas fa-id-card-alt"></i> Solicitudes / Asignaciones
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                {{-- @if(Auth::user()->tieneAplicacion(121)) --}}
                <li><a href="/equi_sol"><i class="far fa-circle fa-xs"></i> Solicitud de Movilidad y Equipo</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(122)) --}}
                <li><a href="/aprob_sol"><i class="far fa-circle fa-xs"></i> Listado de Solicitudes</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(123)) --}}
                <li><a href="/control"><i class="far fa-circle fa-xs"></i> Registro de Bitácora</a></li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}
        {{-- @if(Auth::user()->tieneSubModulo(46)) --}}
        <li class="treeview">
            <a href="#"><i class="fas fa-book"></i> Catálogos
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                {{-- @if(Auth::user()->tieneAplicacion(124)) --}}
                <li><a href="/equi_tipo"><i class="far fa-circle fa-xs"></i> Tipo de Equipos</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(125)) --}}
                <li><a href="/equi_cat"><i class="far fa-circle fa-xs"></i> Categoria de Equipos</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(126)) --}}
                <li><a href="/equi_catalogo"><i class="far fa-circle fa-xs"></i> Catálogo de Equipos</a></li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}
        {{-- @if(Auth::user()->tieneSubModulo(47)) --}}
        <li class="treeview">
            <a href="#"><i class="fas fa-wrench"></i> Mantenimientos
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                {{-- @if(Auth::user()->tieneAplicacion(127)) --}}
                <li><a href="/mtto"><i class="far fa-circle fa-xs"></i> Mantenimiento de Equipo</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(128)) --}}
                <li><a href="/mtto_realizados"><i class="far fa-circle fa-xs"></i> Mantenimientos Realizados</a></li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}
        {{-- @if(Auth::user()->tieneSubModulo(26)) --}}
        <li class="treeview">
            <a href="#"><i class="fas fa-chart-bar"></i> Reportes
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                {{-- @if(Auth::user()->tieneAplicacion(129)) --}}
                <li><a href="/sol_todas"><i class="far fa-circle fa-xs"></i> Listado Solicitudes </a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(130)) --}}
                <li><a href="/docs"><i class="far fa-circle fa-xs"></i> Documentos del Equipo</a></li>
                {{-- @endif
                @if(Auth::user()->tieneAplicacion(131)) --}}
                <li><a href="/mtto_pendientes"><i class="far fa-circle fa-xs"></i> Programación de Mttos</a></li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}
    </ul>
</li>
{{-- @endif --}}

{{-- @if(Auth::user()->tieneSubModuloPadre(48)) --}}
<li class="treeview">
    <a href="#">
        <i class="fas fa-chart-bar"></i> <span>Reportes</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{route('logistica.gestion-logistica.reportes.ordenes-compra')}}"><i class="far fa-circle fa-xs"></i> Ordenes de compras</a></li>
        <li><a href="{{route('logistica.gestion-logistica.reportes.transito-ordenes-compra')}}"><i class="far fa-circle fa-xs"></i> Transito de ordenes de compras</a></li>
        <li><a href="{{route('logistica.gestion-logistica.reportes.ordenes-servicio')}}"><i class="far fa-circle fa-xs"></i> Ordenes de servicio</a></li>
        <li><a href="{{route('logistica.gestion-logistica.reportes.compras-locales')}}"><i class="far fa-circle fa-xs"></i> Compras locales</a></li>
    </ul>
</li>
{{-- @endif --}}
</ul>
@endsection
