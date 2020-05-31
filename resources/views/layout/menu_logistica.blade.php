<!-- <?php
    if (!is_null(Auth::user())) {
        $roles = Auth::user()->obtenerRoles();
    } else {
        $roles = array();
    }
 
?> -->
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> {{ Auth::user()->concepto_login_rol }}</a>
        </div>
    </div>

    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title okc-box-title">Gestión de Compra<p>GC</p></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body" style="display: none;">
        <ul class="sidebar-menu">
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Requerimientos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/logistica/requerimiento/gestionar"> Elaboración de Requerimiento </a></li>
                    <li><a href="/logistica/requerimiento/lista"> Gestión de Requerimientos</a></li>
                </ul>
            </li>
            <?php 
            
            $roles = Auth::user()->trabajador->roles;
            $idRolConceptoHabilitadoList=[];
            $idAreaHabilitadaList=[];

            array_push($idRolConceptoHabilitadoList,10); // JEFE DE CONTABILIDAD
            array_push($idRolConceptoHabilitadoList,2); // GERENTE ADMINISTRATIVO
            array_push($idRolConceptoHabilitadoList,39); // GERENTE DE CONTROL INTERNO
            array_push($idRolConceptoHabilitadoList,3); // GERENTE COMERCIAL
            array_push($idRolConceptoHabilitadoList,15); // GERENTE DE PROYECTOS
            array_push($idRolConceptoHabilitadoList,4); // JEFE DE PERSONAL
            array_push($idAreaHabilitadaList,62); // // AREA DE FINANZAS Y TESORERÍA 
            array_push($idAreaHabilitadaList,2); // // AREA DE contabilidad 

            ?>

            @foreach( $roles as $rol)
                @if(in_array($rol->id_rol_concepto,$idRolConceptoHabilitadoList) or in_array($rol->pivot->id_area,$idAreaHabilitadaList))
        
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Ordenes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <li><a href="/vista_listar_ordenes"> Listado de Ordenes </a></li>
    
                </ul>
            </li>
            @break
                @endif

            @endforeach

                @if(Auth::user()->id_trabajador == 4 || 
                    Auth::user()->id_trabajador == 21 || 
                    Auth::user()->id_trabajador == 15 || 
                    Auth::user()->id_trabajador == 10 || 
                    Auth::user()->id_trabajador == 30)
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i> <span>Cotizaciones</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/gestionar_cotizaciones"> Gestión de Cotizaciones </a></li>
                        <li><a href="/logistica/cotizacion/valorizacion"> Valorización</a></li>
                        <li><a href="/logistica/cotizacion/cuadro-comparativo"> Cuadro Comparativo</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i> <span>Ordenes</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/generar_orden"> Generar Orden </a></li>
                        <li><a href="/vista_listar_ordenes"> Listado de Ordenes </a></li>
        
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i> <span>Proveedores</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/gestionar_proveedores"> Gestionar Proveedores </a></li>
        
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i> <span>Servicios</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="tipoServ"> Tipo de Servicio </a></li>
                        <li><a href="servicio"> Servicio </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-truck"></i> <span>Comprobantes</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="doc_compra"> Comprobantes de Compra </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
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
            @endif
            </ul>
        </div>
    </div>

    {{-- <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Gestión de Compra</label><p>GC</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Requerimientos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="/logistica/requerimiento/lista"> Listado</a></li>
                <li><a href="/logistica/requerimiento/gestionar"> Gestionar Requerimiento </a></li>
            </ul>
        </li>
        @if(Auth::user()->id_trabajador == 4 || Auth::user()->id_trabajador == 21)
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Cotizaciones</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/gestionar_cotizaciones"> Gestión de Cotizaciones</a></li>
                    <li><a href="/logistica/cotizacion/valorizacion"> Valorización</a></li>
                    <li><a href="/logistica/cotizacion/cuadro-comparativo"> Cuadro Comparativo</a></li>

                </ul>
            </li>
 
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Ordenes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/generar_orden"> Generar Orden </a></li>
    
                </ul>
            </li>
 
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Proveedores</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/gestionar_proveedores"> Gestionar Proveedores </a></li>
    
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <!-- <ul class="treeview-menu">
                    <li><a href="Proveedores"> Proveedores </a></li>
                </ul> -->
            </li>
        @endif
    </ul> --}}

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
                        <i class="fas fa-tachometer-alt"></i> <span>Solicitudes / Asignaciones</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/equi_sol"> Solicitud de Movilidad y Equipo </a></li>
                        <li><a href="/aprob_sol"> Listado de Solicitudes </a></li>
                        <li><a href="/control"> Registro de Bitácora </a></li>
                    </ul>
                </li>
                @if(Auth::user()->id_trabajador == 4 || 
                    Auth::user()->id_trabajador == 21 || 
                    Auth::user()->id_trabajador == 15 || 
                    Auth::user()->id_trabajador == 10 || 
                    Auth::user()->id_trabajador == 30)
                    <li class="treeview">
                        <a href="#">
                            <i class="fas fa-tachometer-alt"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
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
                            <i class="fas fa-tachometer-alt"></i> <span>Mantenimientos</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/mtto"> Mantenimiento de Equipo </a></li>
                            <li><a href="/mtto_realizados"> Mantenimientos Realizados </a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/sol_todas"> Listado Solicitudes </a></li>
                            <li><a href="/docs"> Documentos del Equipo </a></li>
                            <li><a href="/mtto_pendientes"> Programación de Mttos </a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div><!-- /.box-body -->
    </div>
</section>