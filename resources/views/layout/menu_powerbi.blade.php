@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('power-bi.ventas')}}"><i class="fa fa-file-invoice-dollar"></i> <span>Ventas</span></a></li>
    <li><a href="{{route('power-bi.cobranzas')}}"><i class="fa fa-receipt"></i> <span>Cobranzas</span></a></li>
    <li><a href="{{route('power-bi.inventario')}}"><i class="fa fa-cubes"></i> <span>Inventario</span></a></li>
</ul>
@endsection
