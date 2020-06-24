@section('sidebar')
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Almacén</a>
        </div>
    </div>

    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title okc-box-title">Almacén<p>GC</p></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
    <div class="box-body" style="display: block;">        
        <ul class="sidebar-menu">
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-book"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="tipo"> Tipo de Producto </a></li>
                    <li><a href="categoria"> Categoría</a></li>
                    <li><a href="subcategoria"> SubCategoría</a></li>
                    <li><a href="clasificacion"> Clasificación</a></li>
                    <li><a href="producto"> Producto</a></li>
                    <li><a href="prod_catalogo"> Catálogo de Productos</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-map-marker-alt"></i> <span>Ubicación de Productos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="tipo_almacen"> Tipo Almacén </a></li>
                    <li><a href="almacenes"> Almacenes </a></li>
                    <li><a href="ubicacion"> Ubicaciones </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                <i class="fas fa-hand-holding-usd"></i> <span>Pagos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="requerimientoPagos"> Confirmación de Pagos </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-truck"></i> <span>Distribución</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="ordenesDespacho"> Gestión de Despachos </a></li>
                    <!-- <li><a href="grupoDespachos"> Despachos </a></li> -->
                    <!-- <li><a href="#"> Despachos Pendientes </a></li> -->
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fab fa-medium-m"></i> <span>Movimientos de Almacén</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="ordenesPendientes"> Pendientes de Ingreso </a></li>
                    <li><a href="despachosPendientes"> Pendientes de Salida </a></li>
                    <li><a href="guia_compra"> Compras / Ingresos </a></li>
                    <li><a href="guia_venta"> Ventas / Salidas </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                <i class="fas fa-exchange-alt"></i> <span>Transferencias</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="listar_transferencias"> Gestión de Transferencias </a></li>
                    <!-- <li><a href="#"> Despachos Pendientes </a></li> -->
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                <i class="fas fa-code-branch"></i> <span>Customización</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="listar_transformaciones"> Gestión de Customizaciones </a></li>
                    <li><a href="transformacion"> Hoja de Transformación </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="saldos"> Saldos Físicos Valorizados </a></li>
                    <li><a href="lista_ingresos"> Reporte de Ingresos </a></li>
                    <li><a href="lista_salidas"> Reporte de Salidas </a></li>
                    <li><a href="busqueda_ingresos"> Búsq. Avan. de Ingresos </a></li>
                    <li><a href="busqueda_salidas"> Búsq. Avan. de Salidas </a></li>
                    <li><a href="kardex_general"> Kardex General </a></li>
                    <li><a href="kardex_detallado"> Kardex por Producto </a></li>
                    <li><a href="kardex_series"> Kardex de Series </a></li>
                    <li><a href="docs_prorrateo"> Documentos de Prorrateo </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-stream"></i> <span>Variables</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="serie_numero"> Series-Números </a></li>
                    <li><a href="tipo_movimiento"> Tipos de Operación </a></li>
                    <li><a href="tipo_doc_almacen"> Tipos de Documentos </a></li>
                    <li><a href="unid_med"> Unidades de Medida </a></li>
                </ul>
            </li>
        </ul>
    </div>
    </div>

</section>
@endsection
