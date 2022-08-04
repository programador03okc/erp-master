@section('sidebar')
<ul class="sidebar-menu" data-widget="tree">
    <li><a href="{{ route('notificaciones.index') }}"><i class="fa fa-enveloped"></i> <span>Lista de pendientes</span></a></li>
    <li><a href="{{ route('notificaciones.index') }}"><i class="fa fa-enveloped"></i> <span>Lista de revisados</span></a></li>
</ul>
@endsection