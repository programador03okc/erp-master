@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('finanzas.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Finanzas</span></a></li>

    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-hand-holding-usd"></i> <span>Presupuestos</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">

            <li><a href="{{ route('finanzas.lista-presupuestos.index') }}"><i class="far fa-circle fa-xs"></i> Lista de Presupuestos</a></li>
            <li><a href="{{ route('finanzas.presupuesto.index') }}"><i class="far fa-circle fa-xs"></i> Presupuesto</a></li>
            <li><a href="{{ route('finanzas.presupuesto.presupuesto-interno.lista') }}"><i class="far fa-circle fa-xs"></i> Presupuesto Interno</a></li>
            <li><a href="{{ route('finanzas.presupuesto.normalizar.presupuesto') }}"><i class="far fa-circle fa-xs"></i> Normalizar Presupuestos</a></li>
            {{-- <li><a href="{{ route('finanzas.centro-costos.index') }}"><i class="far fa-circle fa-xs"></i> Centro de Costos</a></li> --}}

        </ul>
    </li>
    <li>
        <a href="{{route('finanzas.centro-costos.index')}}"><i class="fas fa-file-invoice-dollar"></i>
            <span>Centro de Costos </span>
        </a>
    </li>

    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-chart-bar"></i> <span>Reportes</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{ route('finanzas.reportes.gastos.index-requerimiento-logistico') }}"><i class="far fa-circle fa-xs"></i> Gastos req. logístico</a></li>
            <li><a href="{{ route('finanzas.reportes.gastos.index-requerimiento-pago') }}"><i class="far fa-circle fa-xs"></i> Gastos req. pago</a></li>
            <li><a href="{{ route('finanzas.reportes.gastos.index-cdp') }}"><i class="far fa-circle fa-xs"></i> Gastos CDP</a></li>
        </ul>
    </li>

</ul>
@endsection
