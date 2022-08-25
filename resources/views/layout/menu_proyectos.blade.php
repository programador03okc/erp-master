@section('sidebar')
    <ul class="sidebar-menu" data-widget="tree">
        <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Proyectos</span></a></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-stream"></i> <span>Variables de Entorno</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.variables-entorno.tipos-insumo.index')}}"> Tipos de Insumo </a></li>
                <li><a href="{{route('proyectos.variables-entorno.sistemas-contrato.index')}}"> Sistemas de Contrato </a></li>
                <li><a href="{{route('proyectos.variables-entorno.iu.index')}}"> Indices Unificados </a></li>
                <li><a href="{{route('proyectos.variables-entorno.categorias-insumo.index')}}"> Categoría de Insumos </a></li>
                <li><a href="{{route('proyectos.variables-entorno.categorias-acu.index')}}"> Categoría de A.C.U. </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-layer-group"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.catalogos.insumos.index')}}"> Insumos </a></li>
                <li><a href="{{route('proyectos.catalogos.nombres-cu.index')}}"> Nombres de A.C.U. </a></li>
                <li><a href="{{route('proyectos.catalogos.acus.index')}}"> Detalle de A.C.U. </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fab fa-opera"></i> <span>Opcion Comercial</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.opciones.opciones.index')}}"> Opción Comercial </a></li>
                <li><a href="{{route('proyectos.opciones.presupuestos-internos.index')}}"> Presupuesto Interno </a></li>
                <li><a href="{{route('proyectos.opciones.cronogramas-internos.index')}}"> Cronograma Interno </a></li>
                <li><a href="{{route('proyectos.opciones.cronogramas-valorizados-internos.index')}}"> Cronograma Val. Interno </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-copyright"></i> <span>Propuesta</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.propuestas.propuestas-cliente.index')}}"> Propuesta Cliente </a></li>
                <li><a href="{{route('proyectos.propuestas.cronogramas-cliente.index')}}"> Cronograma de Propuesta </a></li>
                <li><a href="{{route('proyectos.propuestas.cronogramas-valorizados-cliente.index')}}"> Cronograma Val. Propuesta </a></li>
                <li><a href="{{route('proyectos.propuestas.valorizaciones.index')}}"> Valorización </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-file-powerpoint"></i> <span>Ejecución</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.ejecucion.proyectos.index')}}"> Proyectos </a></li>
                <li><a href="{{route('proyectos.ejecucion.residentes.index')}}"> Residentes </a></li>
                <li><a href="{{route('proyectos.ejecucion.presupuestos-ejecucion.index')}}"> Presupuesto de Ejecución </a></li>
                <li><a href="{{route('proyectos.ejecucion.cronogramas-ejecucion.index')}}"> Cronograma de Ejecución </a></li>
                <li><a href="{{route('proyectos.ejecucion.cronogramas-valorizados-ejecucion.index')}}"> Cronograma Val. Ejecución </a></li>
                {{-- <li><a href="#"> Portafolio de Proyectos </a></li> --}}
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-chart-bar"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.reportes.curvas.index')}}"> Curvas S del Proyecto </a></li>
                <li><a href="{{route('proyectos.reportes.saldos.index')}}"> Saldos por Presupuesto </a></li>
                <li><a href="{{route('proyectos.reportes.opciones-relaciones.index')}}"> Opciones y sus Relaciones </a></li>
                <li><a href="{{route('logistica.gestion-logistica.reportes.compras-locales')}}"> Compras locales</a></li>

            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-cog"></i> <span>Configuraciones</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{route('proyectos.configuraciones.estructuras.index')}}"> Estructura Presupuesto </a></li>
            </ul>
        </li>
    </ul>
@endsection
