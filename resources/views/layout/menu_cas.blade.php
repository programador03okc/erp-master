@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> <span>Servicios CAS</span></a></li>

    @if(Auth::user()->tieneSubModulo(41))
    <li class="treeview">
        <a href="#">
            <i class="fas fa-code-branch"></i> <span>Transformaciones</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @if(Auth::user()->tieneAplicacion(88))
            <li><a href="{{route('cas.customizacion.tablero-transformaciones.index')}}"> Gestión de Transformaciones </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(87))
            <li><a href="{{route('cas.customizacion.gestion-customizaciones.index')}}"> Lista de Transformaciones </a></li>
            @endif
            @if(Auth::user()->tieneAplicacion(88))
            <li><a href="{{route('cas.customizacion.hoja-transformacion.index')}}"> Orden de Transformación </a></li>
            @endif
        </ul>
    </li>
    @endif
    <li class="treeview">
        <a href="#">
            <i class="fas fa-medal"></i> <span>Garantías</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{route('cas.garantias.incidencias.index')}}"> Registro de incidencia </a></li>
            {{-- @if (in_array(Auth::user()->id_usuario,[1,3,27,17,77,93,64,8])) --}}
            <li><a href="{{route('cas.garantias.devolucionCas.index')}}"> Devolución </a></li>
            {{-- @endif --}}
            <li><a href="{{route('cas.garantias.fichas.index')}}"> Gestión de incidencias </a></li>
        </ul>
    </li>
</ul>
@endsection
