@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Gerencial</span></a></li>

    <li class="treeview">
        <a href="#">
            <i class="fas fa-book"></i> <span>Cobranzas</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('gerencial.cobranza.cliente')}}"> Clientes</a></li>
            <li><a href="{{route('gerencial.cobranza.index')}}"> Cobranza de Ventas</a></li>
            <li><a href="{{route('gerencial.cobranza.fondos.index')}}"> Fondos y Auspicios</a></li>
            <li><a href="{{route('gerencial.cobranza.devoluciones.index')}}"> Devol. de Penalidades</a></li>
        </ul>
    </li>
</ul>
@endsection