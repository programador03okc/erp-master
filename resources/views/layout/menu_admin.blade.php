@section('sidebar')
    <ul class="sidebar-menu" data-widget="tree">
        <!--<li class="okc-menu-title"><label>Administración</label><p>RH</p></li>-->
        <li class="header">ADMINISTRACIÓN</li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Administrativos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="empresas"> Empresas</a></li>
                <li><a href="sedes"> Sedes </a></li>
                <li><a href="grupos"> Grupos</a></li>
                <li><a href="areas"> Area </a></li>
            </ul>
        </li>
    </ul>
@endsection
