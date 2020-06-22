<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('cabecera') - Sistema ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
    <link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">
    @yield('estilos')

</head>
<body class="hold-transition skin-okc sidebar-mini">
    <div class="wrapper">
        <header class="main-header">

            <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

            <a href="{{ route('modulos') }}" class="logo">
                <span class="logo-mini"><b>OKC</b></span>
                <span class="logo-lg"><b>OK COMPUTER EIRL</b></span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-okc" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i></a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- <li class="okc-li-mod"><a href="#" class="btn" id="like" data-name="Espejito espejito...quien es el más bonito">Test Socket</a></li> -->
                        <li class="okc-li-mod"><a href="/modulos">Módulos</a></li>
                        <li class="okc-li-mod"><a href="/config">Configuración</a></li>
                        <li class="okc-li-mod"><span onclick="modalSobreERP();" style="cursor:pointer;">Sobre el ERP</span></li>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ asset('img/avatar5.png') }}" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ Auth::user()->nombre_corto }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                                    <p>{{ Auth::user()->trabajador->postulante->persona->nombre_completo }}
                                        <small>{{ Auth::user()->cargo }}</small>
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

            <div class="modal fade" role="dialog" id="modal-sobre-erp" style="position:relative;">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"><strong>Sobre el ERP</strong></h3>
                        </div>
                        <div class="modal-body">
                            <p>Manuales:</p>
                            <ul>
                                <li><a href="/files/manuales/Manual de Usuario - Recursos Humanos.pdf" target="_black">Recursos Humanos </a>02/10/2019</li>
                                <li><a href="/files/manuales/Manual de Usuario - Elaboración de Requerimientos.pdf" target="_black">Logística - Elaboración de Requerimientos </a>17/09/2019</li>
                                <li><a href="/files/manuales/Manual de Usuario - Gestión Logística.pdf" target="_black">Logística - Gestión Logística </a>17/09/2019</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <aside class="main-sidebar">
            @yield('sidebar')
        </aside>
        <!-- contenido -->
        <div class="content-wrapper" id="wrapper-okc" style="min-height: 100vh;">
            @yield('option')
        <!-- Vistas -->
            <section class="content">
                @yield('content')
            </section>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-settings">
        <div class="modal-dialog" style="width: 17%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Configuración</h4>
                </div>
                <div class="modal-body">
                    <form id="formSettingsPassword">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Actual contraseña</h5>
                                <input type="password" name="pass_old" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Clave actual" required>
                            </div>
                            <div class="col-md-12">
                                <h5>Nueva contraseña</h5>
                                <input type="password" name="pass_new" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Nueva clave" required>
                            </div>
                            <div class="col-md-12">
                                <h5>Confirmar contraseña</h5>
                                <input type="password" name="pass_renew" class="form-control input-sm" aria-describedby="basic-addon" placeholder="Repita nueva clave" required>
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-success btn-block btn-sm" onclick="execSetting();"> Guardar </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">var auth_user = <?php echo $auth_user ?>; </script>
    <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('template/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/dist/js/app.min.js') }}"></script>
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('js/ini.js')}}"></script>
    <script src="{{ asset('js/function.js')}}"></script>
    <script src="{{ asset('/js/publico/animation.js')}}"></script>
    <script src="{{ asset('addons/sweetalert/sweetalert2@8.js') }}"></script>
    <script src="{{ asset('template/bootstrap_filestyle/bootstrap-filestyle.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pace/pace.js') }}"></script>
    <script>
        function get_session_actual(){
            return new Promise(function(resolve, reject) {
                const baseUrl = '/session-rol-aprob';
                $.ajax({
                    type: 'GET',
                    url:baseUrl,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then()
                    },
                    error: function(err) {
                        reject(err) // Reject the promise and go to catch()
                    }
                });
            });
        }
        $.ajax({
            type: 'GET',
            url: '/socket_setting/activado',
            success: function(response){
                if(response.status == 200){
                    if(response.data.activado == true){
                        socket_setting(response.data);
                    }
                }

            }
        });


        function socket_setting(data){
            var socket = io(data.host);
            // var socket = io('http://localhost:8008'); // modo dev
            // var socket = io('http://192.168.20.2:8008'); // modo dev
            socket.on('notification', function(response) {
                //  notifyMe(response);

                let id_area_user_session_array=[];

                get_session_actual().then(function(data) {
                    if(data.roles.length >0){
                        data.roles.forEach(element => {
                            id_area_user_session_array.push(parseInt(element.id_area));

                        });
                        // console.log(id_area_user_session_array);
                        // console.log(response.id_area);
                        // console.log(id_area_user_session_array.includes(parseInt(response.id_area)));

                        if(id_area_user_session_array.includes(parseInt(response.id_area))){
                            notifyMe(response);
                        }

                    }
                }).catch(function(err) {
                    // Run this when promise was rejected via reject()
                    console.log(err)
                })

            });
        }




        function notifyMe(data) {
            if (!window.Notification) {
                console.log('El navegador no soporta notificaciones.');
            } else {
                // check if permission is already granted
                if (Notification.permission === 'granted') {
                    // show notification here
                    var notify = new Notification( data.title, {
                        body: data.message,
                        icon: '/images/icono.ico'
                        // icon: 'http://www.okcomputer.com.pe/wp-content/uploads/2017/02/LogoSlogan-80.png'
                    });
                } else {
                    // request permission from user
                    Notification.requestPermission().then(function (p) {
                        if (p === 'granted') {
                            // show notification here
                            var notify = new Notification(data.title, {
                                body: data.message,
                                icon: '/images/icono.ico'
                            });
                        } else {
                            console.log('User blocked notifications.');
                        }
                    }).catch(function (err) {
                        console.error(err);
                    });
                }
            }
        }

        function modalSobreERP() {

            $('#modal-sobre-erp').modal({
                show: true,
                backdrop: false
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
