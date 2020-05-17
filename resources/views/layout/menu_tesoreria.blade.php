<!-- MENU LATERAL -->
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>{{ Auth::user()->trabajador->postulante->persona->nombres }}</p>
            <a href="#"><i class="fa fa-circle"></i> {{ Auth::user()->concepto_login_rol }}</a>
        </div>
    </div>
    @yield('menu_lateral', 'seccion menu_lateral')
</section>

<!-- FIN MENU LATERAL -->
