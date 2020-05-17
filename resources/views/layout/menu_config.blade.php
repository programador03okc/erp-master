
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Programador</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Módulo del Sistema</label><p>SI</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Configuración</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="modulo"> Módulos </a></li>
                <li><a href="aplicaciones"> Aplicaciones</a></li>
                <li><a href="usuarios"> Usuarios</a></li>
                <li><a href="accesos"> Accesos </a></li>
                <li><a href="notas_lanzamiento"> Notas de Lanzamiento</a></li>
                <li><a href="correo_coorporativo"> Correo Corporativo</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-bezier-curve"></i> <span>Flujo de Aprobación</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="gestionar-flujos">Gestionar Flujos</a></li>
                <li><a href="documentos">Documentos</a></li>
                <li><a href="historial-aprobaciones">Historial de Aprobaciones</a></li>
            </ul>
        </li>
    </ul>
</section>