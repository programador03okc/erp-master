@section('sidebar')
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Proyectos</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Proyectos</label><p>PY</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-stream"></i> <span>Variables de Entorno</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="tipo_insumo"> Tipos de Insumo </a></li>
                <li><a href="sis_contrato"> Sistemas de Contrato </a></li>
                <li><a href="iu"> Indices Unificados </a></li>
                <li><a href="cat_insumo"> Categoría de Insumos </a></li>
                <li><a href="cat_acu"> Categoría de A.C.U. </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-layer-group"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="insumo"> Insumos </a></li>
                <li><a href="cu"> Nombres de A.C.U. </a></li>
                <li><a href="acu"> Detalle de A.C.U. </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fab fa-opera"></i> <span>Opcion Comercial</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="opcion"> Opción Comercial </a></li>
                <li><a href="presint"> Presupuesto Interno </a></li>
                <li><a href="cronoint"> Cronograma Interno </a></li>
                <li><a href="cronovalint"> Cronograma Val. Interno </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-copyright"></i> <span>Propuesta Cliente</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="propuesta"> Propuesta Cliente </a></li>
                <li><a href="cronopro"> Cronograma de Propuesta </a></li>
                <li><a href="cronovalpro"> Cronograma Val. Propuesta </a></li>
                <li><a href="valorizacion"> Valorización </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-file-powerpoint"></i> <span>Proyecto de Ejecución</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="proyecto"> Proyectos </a></li>
                <li><a href="residentes"> Residentes </a></li>
                <li><a href="preseje"> Presupuesto de Ejecución </a></li>
                <li><a href="cronoeje"> Cronograma de Ejecución </a></li>
                <li><a href="cronovaleje"> Cronograma Val. Ejecución </a></li>
                {{-- <li><a href="#"> Portafolio de Proyectos </a></li> --}}
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="curvas"> Informe Gral. del Proyecto </a></li>
                <li><a href="saldos_pres"> Saldos por Presupuesto </a></li>
                <li><a href="opciones_todo"> Gestión de Todas las Opciones </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-cog"></i> <span>Configuraciones</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="presEstructura"> Estructura Presupuesto </a></li>
            </ul>
        </li>
    </ul>
</section>
@endsection