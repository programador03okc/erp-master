<header class="main-header">
    <a href="{{ route('modulos') }}" class="logo">
        <span class="logo-mini"><b>OKC</b></span>
        <span class="logo-lg"><b>OK COMPUTER EIRL</b></span>
    </a>

    <nav class="navbar navbar-static-top">
        <!--<a href="#" class="sidebar-okc" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i></a>-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <i class="fas fa-bars"></i>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- <li class="okc-li-mod"><a href="#" class="btn" id="like" data-name="Espejito espejito...quien es el más bonito">Test Socket</a></li> -->
                <li><a href="/modulos">Módulos</a></li>
                <li><a href="/config">Configuración</a></li>
                <li class="info-docs"><a href="#" data-toggle="modal" data-target="#modal-sobre-erp">Sobre el ERP</a></li>
                <!-- <li><span onclick="modalSobreERP();" style="cursor:pointer;"></span></li> -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ asset('images/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Auth::user()->nombre_corto }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="{{ asset('images/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                            <p>{{ Auth::user()->trabajador->postulante->persona->nombre_completo }}
                                <!-- <small>{{ Auth::user()->cargo }}</small> -->
                                <small>{{ Auth::user()->getRol() }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left"><a href="javascript: void(0)" onclick="changePassword();" class="btn btn-default btn-flat">Perfil</a></div>
                            <div class="pull-right">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-default btn-flat">Salir</button>
                                </form>

                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
