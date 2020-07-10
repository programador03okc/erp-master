@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li class=" treeview ">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
    </li>
    <li class=" treeview menu-open ">
        <a href="#">
            <i class="fas fa-truck-loading"></i> <span>Logística</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu active">
            <li class="treeview menu-open active">
                <a href="#"><i class="fas fa-pallet"></i> Gestión Logística
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneSubModulo(23))
                    <li class="treeview" style="height: auto;">
                    <a href="#"><i class="fas fa-file-prescription"></i> Requerimientos
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                    @if(Auth::user()->tieneAplicacion(102))
                        <li><a href="{{route('logistica.gestion-logistica.requerimiento.elaboracion.index')}}"><i class="far fa-circle fa-xs"></i> Elaborar</a></li>
                    @endif
                    @if(Auth::user()->tieneAplicacion(103))
                        <li><a href="{{route('logistica.gestion-logistica.requerimiento.gestionar.index')}}"><i class="far fa-circle fa-xs"></i> Listado</a></li>
                    @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(24))
                    <li class="treeview" >
                    <a href="#"><i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Auth::user()->tieneAplicacion(104))
                        <li><a href="{{route('logistica.gestion-logistica.cotizacion.gestionar.index')}}"><i class="far fa-circle fa-xs"></i> Solicitud de cotización</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(105))
                        <li><a href="/logistica/cotizacion/valorizacion"><i class="far fa-circle fa-xs"></i> Valorizar</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(106))
                        <li><a href="/logistica/cotizacion/cuadro-comparativo"><i class="far fa-circle fa-xs"></i> Cuadro Comparativo</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(25))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-file-invoice"></i> Ordenes
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(107))
                        <li><a href="/generar_orden"><i class="far fa-circle fa-xs"></i> Por Cotización</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(108))
                        <li><a href="{{route('logistica.gestion-logistica.orden.por-requerimiento.index')}}"><i class="far fa-circle fa-xs"></i> Por Requerimiento</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(109))
                        <li><a href="{{route('logistica.gestion-logistica.orden.lista-ordenes.index')}}"><i class="far fa-circle fa-xs"></i> Listado</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(27))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-user-tie"></i> Proveedores
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(117))
                        <li><a href="/gestionar_proveedores"><i class="far fa-circle fa-xs"></i> Gestionar Proveedores</a></li>
                        @endif
                    </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tieneSubModulo(28))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-people-carry"></i> Servicios
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(118))
                        <li><a href="tipoServ"><i class="far fa-circle fa-xs"></i> Tipo de Servicio</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(119))
                        <li><a href="servicio"><i class="far fa-circle fa-xs"></i> Servicio</a></li>
                        @endif
                    </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tieneSubModulo(44))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-receipt"></i> Comprobantes
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(120))
                        <li><a href="/doc_compra"><i class="far fa-circle fa-xs"></i> Comprobante de compra</a></li>
                        @endif
                    </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tieneSubModulo(26))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-chart-bar"></i> Reportes
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(110))
                        <li><a href="/logistica/reportes/productos_comprados"><i class="far fa-circle fa-xs"></i> Productos Comprados</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(111))
                        <li><a href="/logistica/reportes/compras_por_proveedor"><i class="far fa-circle fa-xs"></i> Compras por Proveedor</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(112))
                        <li><a href="/logistica/reportes/compras_por_producto"><i class="far fa-circle fa-xs"></i> Compras por Producto</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(113))
                        <li><a href="/logistica/reportes/proveedores_producto_determinado"><i class="far fa-circle fa-xs"></i> Proveedores con Producto Determinado</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(114))
                        <li><a href="/logistica/reportes/mejores_proveedores"><i class="far fa-circle fa-xs"></i> Mejores Proveedores</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(115))
                        <li><a href="/logistica/reportes/frecuencia_compras"><i class="far fa-circle fa-xs"></i> Frecuencia de Compra por Producto</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(116))
                        <li><a href="/logistica/reportes/historial_precios"><i class="far fa-circle fa-xs"></i> Historial de Precios</a></li>
                        @endif
                    </ul>
                    </li>
                    @endif
                </ul>
            </li>
            <li class="treeview menu-open active">
                <a href="#"><i class="fas fa-truck"></i> Gestión Activos
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneSubModulo(45))
                    <li class="treeview" style="height: auto;">
                    <a href="#"><i class="fas fa-id-card-alt"></i> Solicitudes / Asignaciones
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Auth::user()->tieneAplicacion(121))
                        <li><a href="/equi_sol"><i class="far fa-circle fa-xs"></i> Solicitud de Movilidad y Equipo</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(122))
                        <li><a href="/aprob_sol"><i class="far fa-circle fa-xs"></i> Listado de Solicitudes</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(123))
                        <li><a href="/control"><i class="far fa-circle fa-xs"></i> Registro de Bitácora</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(46))
                    <li class="treeview" >
                    <a href="#"><i class="fas fa-book"></i> Catálogos
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Auth::user()->tieneAplicacion(124))
                        <li><a href="/equi_tipo"><i class="far fa-circle fa-xs"></i> Tipo de Equipos</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(125))
                        <li><a href="/equi_cat"><i class="far fa-circle fa-xs"></i> Categoria de Equipos</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(126))
                        <li><a href="/equi_catalogo"><i class="far fa-circle fa-xs"></i> Catálogo de Equipos</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(47))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-wrench"></i> Mantenimientos
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(127))
                        <li><a href="/mtto"><i class="far fa-circle fa-xs"></i> Mantenimiento de Equipo</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(128))
                        <li><a href="/mtto_realizados"><i class="far fa-circle fa-xs"></i> Mantenimientos Realizados</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                @if(Auth::user()->tieneSubModulo(26))
                    <li class="treeview">
                    <a href="#"><i class="fas fa-chart-bar"></i> Reportes
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        @if(Auth::user()->tieneAplicacion(129))
                        <li><a href="/sol_todas"><i class="far fa-circle fa-xs"></i> Listado Solicitudes </a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(130))
                        <li><a href="/docs"><i class="far fa-circle fa-xs"></i> Documentos del Equipo</a></li>
                        @endif
                        @if(Auth::user()->tieneAplicacion(131))
                        <li><a href="/mtto_pendientes"><i class="far fa-circle fa-xs"></i> Programación de Mttos</a></li>
                        @endif
                    </ul>
                    </li>
                @endif
                </ul>
            </li>
        </ul>


    </li>
    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-boxes"></i> <span>Almacén</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
        @if(Auth::user()->tieneSubModulo(6))
            <li class="treeview">
                <a href="#"><i class="fas fa-book"></i> Catálogos
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(70))
                    <li><a href="{{route('almacen.catalogos.tipos.index')}}"><i class="far fa-circle fa-xs"></i> Tipo de Producto </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(71))
                <li><a href="{{route('almacen.catalogos.categorias.index')}}"><i class="far fa-circle fa-xs"></i> Categoría</a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(72))
                <li><a href="{{route('almacen.catalogos.sub-categorias.index')}}"><i class="far fa-circle fa-xs"></i> SubCategoría</a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(73))
                <li><a href="{{route('almacen.catalogos.clasificaciones.index')}}"><i class="far fa-circle fa-xs"></i> Clasificación</a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(74))
                <li><a href="{{route('almacen.catalogos.productos.index')}}"><i class="far fa-circle fa-xs"></i> Producto</a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(75))
                <li><a href="{{route('almacen.catalogos.catalogo-productos.index')}}"><i class="far fa-circle fa-xs"></i> Catálogo de Productos</a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(18))
        <li class="treeview">
                <a href="#"><i class="fas fa-map-marker-alt"></i> Ubicación de Productos
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @if(Auth::user()->tieneAplicacion(76))
                    <li><a href="{{route('almacen.ubicaciones.tipos-almacen.index')}}"><i class="far fa-circle fa-xs"></i> Tipo Almacén </a></li>
                    @endif
                    @if(Auth::user()->tieneAplicacion(77))
                    <li><a href="{{route('almacen.ubicaciones.almacenes.index')}}"><i class="far fa-circle fa-xs"></i> Almacenes </a></li>
                    @endif
                    @if(Auth::user()->tieneAplicacion(78))
                    <li><a href="{{route('almacen.ubicaciones.posiciones.index')}}"><i class="far fa-circle fa-xs"></i> Posiciones </a></li>
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(19))
        <li class="treeview">
                <a href="#"><i class="fas fa-hand-holding-usd"></i> Pagos
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @if(Auth::user()->tieneAplicacion(79))
                    <li><a href="{{route('almacen.pagos.confirmacion-pagos.index')}}"><i class="far fa-circle fa-xs"></i> Confirmación de Pagos </a></li>
                    @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(20))
            <li class="treeview">
                <a href="#"><i class="fas fa-truck"></i> Distribución
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(80))
                <li><a href="{{route('almacen.distribucion.despachos.index')}}"><i class="far fa-circle fa-xs"></i> Gestión de Despachos </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(81))
                <li><a href="{{route('almacen.distribucion.trazabilidad-requerimientos.index')}}"><i class="far fa-circle fa-xs"></i> Trazabilidad </a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(21))
            <li class="treeview">
                <a href="#"><i class="fab fa-medium-m"></i> Movimientos de Almacén
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(82))
                <li><a href="{{route('almacen.movimientos.pendientes-ingreso.index')}}"><i class="far fa-circle fa-xs"></i> Pendientes de Ingreso </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(83))
                <li><a href="{{route('almacen.movimientos.pendientes-salida.index')}}"><i class="far fa-circle fa-xs"></i> Pendientes de Salida </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(84))
                <li><a href="{{route('almacen.movimientos.guias-compra.index')}}"><i class="far fa-circle fa-xs"></i> Compras - Ingresos </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(85))
                <li><a href="{{route('almacen.movimientos.guias-venta.index')}}"><i class="far fa-circle fa-xs"></i> Ventas - Salidas </a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(40))
            <li class="treeview">
                <a href="#"><i class="fas fa-exchange-alt"></i> Transferencias
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(86))
                <li><a href="{{route('almacen.transferencias.gestion-transferencias.index')}}"><i class="far fa-circle fa-xs"></i> Gestión de Transferencias </a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(41))
            <li class="treeview">
                <a href="#"><i class="fas fa-code-branch"></i> Customización
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(87))
                <li><a href="{{route('almacen.customizacion.gestion-customizaciones.index')}}"><i class="far fa-circle fa-xs"></i> Gestión de Customizaciones </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(88))
                <li><a href="{{route('almacen.customizacion.hoja-transformacion.index')}}"><i class="far fa-circle fa-xs"></i> Hoja de Transformación </a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(42))
            <li class="treeview">
                <a href="#"><i class="fas fa-chart-bar"></i> Reportes
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(89))
                <li><a href="{{route('almacen.reportes.saldos.index')}}"><i class="far fa-circle fa-xs"></i> Saldos Actuales </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(90))
                <li><a href="{{route('almacen.reportes.lista-ingresos.index')}}"><i class="far fa-circle fa-xs"></i> Reporte de Ingresos </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(91))
                <li><a href="{{route('almacen.reportes.lista-salidas.index')}}"><i class="far fa-circle fa-xs"></i> Reporte de Salidas </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(92))
                <li><a href="{{route('almacen.reportes.detalle-ingresos.index')}}"><i class="far fa-circle fa-xs"></i> Detalle Ingresos </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(93))
                <li><a href="{{route('almacen.reportes.detalle-salidas.index')}}"><i class="far fa-circle fa-xs"></i> Detalle Salidas </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(94))
                <li><a href="{{route('almacen.reportes.kardex-general.index')}}"><i class="far fa-circle fa-xs"></i> Kardex General </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(95))
                <li><a href="{{route('almacen.reportes.kardex-productos.index')}}"><i class="far fa-circle fa-xs"></i> Kardex por Producto </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(96))
                <li><a href="{{route('almacen.reportes.kardex-series.index')}}"><i class="far fa-circle fa-xs"></i> Kardex de Series </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(97))
                <li><a href="{{route('almacen.reportes.documentos-prorrateo.index')}}"><i class="far fa-circle fa-xs"></i> Documentos de Prorrateo </a></li>
                @endif
                </ul>
            </li>
        @endif
        @if(Auth::user()->tieneSubModulo(42))
            <li class="treeview">
                <a href="#"><i class="fas fa-pallet"></i> Reportes
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(98))
                <li><a href="{{route('almacen.variables.series-numeros.index')}}"><i class="far fa-circle fa-xs"></i> Series-Números </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(99))
                <li><a href="{{route('almacen.variables.tipos-movimiento.index')}}"><i class="far fa-circle fa-xs"></i> Tipos de Operación </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(100))
                <li><a href="{{route('almacen.variables.tipos-documento.index')}}"><i class="far fa-circle fa-xs"></i> Tipos de Documentos </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(101))
                <li><a href="{{route('almacen.variables.unidades-medida.index')}}"><i class="far fa-circle fa-xs"></i> Unidades de Medida </a></li>
                @endif
                </ul>
            </li>
        @endif
        </ul>

    </li>
</ul>
@endsection