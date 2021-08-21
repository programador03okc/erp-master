@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Servicios CAS</span></a></li>

    @if(Auth::user()->tieneSubModulo(41))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-code-branch"></i> <span>Transformaciones</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(87))
            <li><a href="{{route('cas.customizacion.gestion-customizaciones.index')}}"> Gestión de Transformaciones </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(88))
            <li><a href="{{route('cas.customizacion.hoja-transformacion.index')}}"> Hoja de Transformación </a></li>
            @endif
        </ul>
    </li>
    @endif

</ul>
@endsection