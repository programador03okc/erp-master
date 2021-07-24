
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
            <li><a href="{{route('tesoreria.pagos.confirmacion-pagos.index')}}"><i class="far fa-circle fa-xs"></i> Confirmación de Pagos </a></li>
            <li><a href="{{route('tesoreria.pagos.procesar-pago.index')}}"><i class="far fa-circle fa-xs"></i> Procesar Pagos </a></li>
        </ul>
    </li>
    
    @if(Auth::user()->tieneSubModulo(44))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-receipt"></i> <span>Comprobantes</span><i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" >
            <li><a href="{{route('tesoreria.facturacion.index')}}"><i class="far fa-circle fa-xs"></i> Facturación</a></li>
            @if(Auth::user()->tieneAplicacion(120))
            <li><a href="{{route('almacen.comprobantes.lista_comprobante_compra')}}"><i class="far fa-circle fa-xs"></i> Reporte de comprobantes</a></li>
            @endif
        </ul>
    </li>
    @endif

</ul>
@endsection