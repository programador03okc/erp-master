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
            <li><a href="{{ route('finanzas.centro-costos.index') }}"><i class="far fa-circle fa-xs"></i> Centro de Costos</a></li>
        
        </ul>
    </li>

</ul>
@endsection