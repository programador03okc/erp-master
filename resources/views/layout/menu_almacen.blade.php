@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Almacén</span></a></li>
    <li class="treeview">
        <a href="#">
            <i class="fas fa-book"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.catalogos.tipos.index')}}"> Tipo de Producto </a></li>
            <li><a href="{{route('almacen.catalogos.categorias.index')}}"> Categoría</a></li>
            <li><a href="{{route('almacen.catalogos.sub-categorias.index')}}"> SubCategoría</a></li>
            <li><a href="{{route('almacen.catalogos.clasificaciones.index')}}"> Clasificación</a></li>
            <li><a href="{{route('almacen.catalogos.productos.index')}}"> Producto</a></li>
            <li><a href="{{route('almacen.catalogos.catalogo-productos.index')}}"> Catálogo de Productos</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fas fa-map-marker-alt"></i> <span>Ubicación de Productos</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.ubicaciones.tipos-almacen.index')}}"> Tipo Almacén </a></li>
            <li><a href="{{route('almacen.ubicaciones.almacenes.index')}}"> Almacenes </a></li>
            <li><a href="{{route('almacen.ubicaciones.posiciones.index')}}"> Posiciones </a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
        <i class="fas fa-hand-holding-usd"></i> <span>Pagos</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.pagos.confirmacion-pagos.index')}}"> Confirmación de Pagos </a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fas fa-truck"></i> <span>Distribución</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.distribucion.despachos.index')}}"> Gestión de Despachos </a></li>
            <li><a href="{{route('almacen.distribucion.trazabilidad-requerimientos.index')}}"> Trazabilidad </a></li>
            <!-- <li><a href="#"> Despachos Pendientes </a></li> -->
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fab fa-medium-m"></i> <span>Movimientos de Almacén</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.movimientos.pendientes-ingreso.index')}}"> Pendientes de Ingreso </a></li>
            <li><a href="{{route('almacen.movimientos.pendientes-salida.index')}}"> Pendientes de Salida </a></li>
            <li><a href="{{route('almacen.movimientos.guias-compra.index')}}"> Compras - Ingresos </a></li>
            <li><a href="{{route('almacen.movimientos.guias-venta.index')}}"> Ventas - Salidas </a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
        <i class="fas fa-exchange-alt"></i> <span>Transferencias</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.transferencias.gestion-transferencias.index')}}"> Gestión de Transferencias </a></li>
            <!-- <li><a href="#"> Despachos Pendientes </a></li> -->
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
        <i class="fas fa-code-branch"></i> <span>Customización</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.customizacion.gestion-customizaciones.index')}}"> Gestión de Customizaciones </a></li>
            <li><a href="{{route('almacen.customizacion.hoja-transformacion.index')}}"> Hoja de Transformación </a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.reportes.saldos.index')}}"> Saldos Actuales </a></li>
            <li><a href="{{route('almacen.reportes.lista-ingresos.index')}}"> Reporte de Ingresos </a></li>
            <li><a href="{{route('almacen.reportes.lista-salidas.index')}}"> Reporte de Salidas </a></li>
            <li><a href="{{route('almacen.reportes.detalle-ingresos.index')}}"> Detalle Ingresos </a></li>
            <li><a href="{{route('almacen.reportes.detalle-salidas.index')}}"> Detalle Salidas </a></li>
            <li><a href="{{route('almacen.reportes.kardex-general.index')}}"> Kardex General </a></li>
            <li><a href="{{route('almacen.reportes.kardex-productos.index')}}"> Kardex por Producto </a></li>
            <li><a href="{{route('almacen.reportes.kardex-series.index')}}"> Kardex de Series </a></li>
            <li><a href="{{route('almacen.reportes.documentos-prorrateo.index')}}"> Documentos de Prorrateo </a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fas fa-stream"></i> <span>Variables</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('almacen.variables.series-numeros.index')}}"> Series-Números </a></li>
            <li><a href="{{route('almacen.variables.tipos-movimiento.index')}}"> Tipos de Operación </a></li>
            <li><a href="{{route('almacen.variables.tipos-documento.index')}}"> Tipos de Documentos </a></li>
            <li><a href="{{route('almacen.variables.unidades-medida.index')}}"> Unidades de Medida </a></li>
        </ul>
    </li>
</ul>
@endsection
