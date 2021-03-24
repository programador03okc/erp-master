
@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('contabilidad.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Contabilidad</span></a></li>
    
    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-store"></i> <span>Ventas</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('contabilidad.ventas.vista-listar-ventas')}}"><i class="far fa-circle fa-xs"></i> Listado</a></li>
            <li><a href="{{route('contabilidad.ventas.vista-registro-ventas')}}"><i class="far fa-circle fa-xs"></i> Registro </a></li>
        </ul>
    </li>
<!-- 
    <li class="treeview">
        <a href="#">
            <i class="fas fa-file-invoice-dollar"></i> <span> Cuentas</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href=""><i class="far fa-circle fa-xs"></i> Plan de cuentas Contable </a></li>
            <li><a href=""><i class="far fa-circle fa-xs"></i> Cuentas de Detracción </a></li>
            <li><a href=""><i class="far fa-circle fa-xs"></i> Impuestos </a></li>
        </ul>
    </li> -->

</ul>
@endsection