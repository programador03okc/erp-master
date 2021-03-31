
@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Tesorería</span></a></li>
    
    <li class="treeview">
        <a href="#">
            <i class="fas fa-file-invoice-dollar"></i> <span> Pagos </span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('tesoreria.pagos.index')}}"><i class="far fa-circle fa-xs"></i> Procesar Pagos </a></li>
            <!-- <li><a href=""><i class="far fa-circle fa-xs"></i> Cuentas de Detracción </a></li>
            <li><a href=""><i class="far fa-circle fa-xs"></i> Impuestos </a></li> -->
        </ul>
    </li>

</ul>
@endsection