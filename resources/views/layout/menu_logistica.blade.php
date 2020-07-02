@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Logística</span></a></li>
    <div class="box collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Gestión de Compra</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body" style="display: none;">
            <ul class="sidebar-menu">
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-file-prescription"></i> <span>Requerimientos</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{route('logistica.gestion-logistica.requerimiento.elaboracion.index')}}"> Elaboración de Requerimiento </a></li>
                        <li><a href="{{route('logistica.gestion-logistica.requerimiento.gestionar.index')}}"> Lista de Requerimientos </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Cotizaciones</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{route('logistica.gestion-logistica.cotizacion.gestionar.index')}}"> Gestión de Cotizaciones </a></li>
                        <li><a href="/logistica/cotizacion/valorizacion"> Valorización</a></li>
                        <li><a href="/logistica/cotizacion/cuadro-comparativo"> Cuadro Comparativo</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-file-invoice"></i> <span>Ordenes</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/generar_orden"> Generar Orden </a></li>
                        <li><a href="{{route('logistica.gestion-logistica.orden.por-requerimiento.index')}}">Orden por Requerimiento</a></li>
                        <li><a href="/vista_listar_ordenes"> Listado de Ordenes </a></li>
        
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-user-tie"></i> <span>Proveedores</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/gestionar_proveedores"> Gestionar Proveedores </a></li>
        
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-people-carry"></i> <span>Servicios</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="tipoServ"> Tipo de Servicio </a></li>
                        <li><a href="servicio"> Servicio </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-receipt"></i> <span>Comprobantes</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/doc_compra"> Comprobantes de Compra </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                            <li><a href="/logistica/reportes/productos_comprados">Productos Comprados</a></li>
                            <li><a href="/logistica/reportes/compras_por_proveedor">Compras por Proveedor</a></li>
                            <li><a href="/logistica/reportes/compras_por_producto">Compras por Producto</a></li>
                            <li><a href="/logistica/reportes/proveedores_producto_determinado">Proveedores con Producto Determinado</a></li>
                            <li><a href="/logistica/reportes/mejores_proveedores">Mejores Proveedores</a></li>
                            <li><a href="/logistica/reportes/frecuencia_compras">Frecuencia de Compra por Producto</a></li>
                            <li><a href="/logistica/reportes/historial_precios">Historial de Precios</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

 

    <div class="box box-solid collapsed-box">
        <div class="box-header with-border">
            {{-- <li class="okc-menu-title"><label>Gestión de Activos</label><p>GA</p></li> --}}
            <h3 class="box-title okc-box-title">Gestión de Activos<p>GA</p></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body" style="display: none;">
            <ul class="sidebar-menu">
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-id-card-alt"></i> <span>Solicitudes / Asignaciones</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/equi_sol"> Solicitud de Movilidad y Equipo </a></li>
                        <li><a href="/aprob_sol"> Listado de Solicitudes </a></li>
                        <li><a href="/control"> Registro de Bitácora </a></li>
                    </ul>
                </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fas fa-book"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/equi_tipo"> Tipo de Equipos </a></li>
                            <li><a href="/equi_cat"> Categoria de Equipos </a></li>
                            <li><a href="/equi_catalogo"> Catálogo de Equipos </a></li>
                            {{-- <li><a href="tp_combustible"> Tipo de Combustible </a></li> --}}
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fas fa-wrench"></i> <span>Mantenimientos</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/mtto"> Mantenimiento de Equipo </a></li>
                            <li><a href="/mtto_realizados"> Mantenimientos Realizados </a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/sol_todas"> Listado Solicitudes </a></li>
                            <li><a href="/docs"> Documentos del Equipo </a></li>
                            <li><a href="/mtto_pendientes"> Programación de Mttos </a></li>
                        </ul>
                    </li>
            </ul>
        </div><!-- /.box-body -->
    </div>
</ul>
@endsection