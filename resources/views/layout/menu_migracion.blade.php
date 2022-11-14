@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{route('migracion.index')}}"><i class="fa fa-upload"></i> <span>Migración Almacen</span></a></li>
    <li><a href="{{route('migracion.softlink.index')}}"><i class="fa fa-upload"></i> <span>Migración Series</span></a></li>
</ul>
@endsection