@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Almacén</span></a></li>
    
    @if(Auth::user()->tieneSubModulo(6))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-book"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(70))
            <li><a href="{{route('almacen.catalogos.tipos.index')}}"> Categoría </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(71))
            <li><a href="{{route('almacen.catalogos.categorias.index')}}"> SubCategoría</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(72))
            <li><a href="{{route('almacen.catalogos.sub-categorias.index')}}"> Marca</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(73))
            <li><a href="{{route('almacen.catalogos.clasificaciones.index')}}"> Clasificación</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(74))
            <li><a href="{{route('almacen.catalogos.productos.index')}}"> Producto</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(75))
            <li><a href="{{route('almacen.catalogos.catalogo-productos.index')}}"> Catálogo de Productos</a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(18))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-map-marker-alt"></i> <span>Ubicación de Productos</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(76))
            <li><a href="{{route('almacen.ubicaciones.tipos-almacen.index')}}"> Tipo Almacén </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(77))
            <li><a href="{{route('almacen.ubicaciones.almacenes.index')}}"> Almacenes </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(78))
            <li><a href="{{route('almacen.ubicaciones.posiciones.index')}}"> Posiciones </a></li>
            @endif
        </ul>
    </li>
    @endif
    <!-- @if(Auth::user()->tieneSubModulo(20)) -->
            <!-- <li class="treeview">
                <a href="#"><i class="fab fa-stack-overflow"></i> Control de Stock
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @if(Auth::user()->tieneAplicacion(80))
                <li><a href="{{route('almacen.control-stock.importar.index')}}"><i class="far fa-circle fa-xs"></i> Inicial / Importar </a></li>
                @endif
                @if(Auth::user()->tieneAplicacion(81))
                <li><a href="{{route('almacen.control-stock.toma-inventario.index')}}"><i class="far fa-circle fa-xs"></i> Toma de Inventario </a></li>
                @endif
                </ul>
            </li> -->
            <!-- @endif -->
    @if(Auth::user()->tieneSubModulo(21))
    <li class="treeview">
        <a href="#">
            <i class="fab fa-medium-m"></i> <span>Movimientos de Almacén</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(82))
            <li><a href="{{route('almacen.movimientos.pendientes-ingreso.index')}}"> Pendientes de Ingreso </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(83))
            <li><a href="{{route('almacen.movimientos.pendientes-salida.index')}}"> Pendientes de Salida </a></li>
            @endif
            <!-- @if(Auth::user()->tieneAplicacion(84))
            <li><a href="{{route('almacen.movimientos.guias-compra.index')}}"> Compras - Ingresos </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(85))
            <li><a href="{{route('almacen.movimientos.guias-venta.index')}}"> Ventas - Salidas </a></li>
            @endif -->
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(44))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-receipt"></i> <span>Comprobantes</span><i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" >
            @if(Auth::user()->tieneAplicacion(120))
            <!-- <li><a href="{{route('almacen.comprobantes.generar_comprobante')}}"><i class="far fa-circle fa-xs"></i> Documento de compra</a></li> -->
            <li><a href="{{route('almacen.comprobantes.lista_comprobante_compra')}}"><i class="far fa-circle fa-xs"></i> Reporte de comprobantes</a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(40))
    <li class="treeview">
        <a href="#">
        <i class="fas fa-exchange-alt"></i> <span>Transferencias</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(86))
            <li><a href="{{route('almacen.transferencias.gestion-transferencias.index')}}"> Gestión de Transferencias </a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(41))
    <li class="treeview">
        <a href="#">
        <i class="fas fa-code-branch"></i> <span>Transformación</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(87))
            <li><a href="{{route('almacen.customizacion.gestion-customizaciones.index')}}"> Transformaciones </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(88))
            <li><a href="{{route('almacen.customizacion.hoja-transformacion.index')}}"> Hoja de Transformación </a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(42))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(89))
            <li><a href="{{route('almacen.reportes.saldos.index')}}"> Saldos Actuales </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(90))
            <li><a href="{{route('almacen.reportes.lista-ingresos.index')}}"> Reporte de Ingresos </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(91))
            <li><a href="{{route('almacen.reportes.lista-salidas.index')}}"> Reporte de Salidas </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(92))
            <li><a href="{{route('almacen.reportes.detalle-ingresos.index')}}"> Detalle Ingresos </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(93))
            <li><a href="{{route('almacen.reportes.detalle-salidas.index')}}"> Detalle Salidas </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(94))
            <li><a href="{{route('almacen.reportes.kardex-general.index')}}"> Kardex General </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(95))
            <li><a href="{{route('almacen.reportes.kardex-productos.index')}}"> Kardex por Producto </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(96))
            <li><a href="{{route('almacen.reportes.kardex-series.index')}}"> Kardex de Series </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(97))
            <li><a href="{{route('almacen.reportes.documentos-prorrateo.index')}}"> Documentos de Prorrateo </a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(43))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-stream"></i> <span>Variables</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(98))
            <li><a href="{{route('almacen.variables.series-numeros.index')}}"> Series-Números </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(99))
            <li><a href="{{route('almacen.variables.tipos-movimiento.index')}}"> Tipos de Operación </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(100))
            <li><a href="{{route('almacen.variables.tipos-documento.index')}}"> Tipos de Documentos </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(101))
            <li><a href="{{route('almacen.variables.unidades-medida.index')}}"> Unidades de Medida </a></li>
            @endif
        </ul>
    </li>
    @endif
</ul>
@endsection
