@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Necesidades</span></a></li>
    @if(Auth::user()->tieneSubModulo(23))
    <li class=" treeview ">
        <a href="#">
            <i class="fas fa-file-prescription"></i> <span>Requerimiento de B/S</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(102))
            <li><a href="{{route('necesidades.requerimiento.elaboracion.index')}}"><i class="far fa-circle fa-xs"></i> Crear / editar</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(103))
            <li><a href="{{route('necesidades.requerimiento.listado.index')}}"><i class="far fa-circle fa-xs"></i> Listado</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(103))
            <li><a href="{{route('necesidades.requerimiento.aprobar.index')}}"><i class="far fa-circle fa-xs"></i> Revisar / aprobar</a></li>
            @endif
        </ul>
    </li>
    @endif
    @if(Auth::user()->tieneSubModulo(23))
    <li class="treeview oculto">
        <a href="#">
            <i class="fas fa-file-invoice-dollar"></i> <span>Requerimiento de pago</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(102))
            <li><a href="{{route('necesidades.pago.listado.index')}}"><i class="far fa-circle fa-xs"></i> Listado</a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(103))
            <li><a href="{{route('necesidades.pago.revisar_aprobar.index')}}"><i class="far fa-circle fa-xs"></i> Revisar / aprobar</a></li>
            @endif
  
        </ul>
    </li>
    @endif
</ul>
@endsection