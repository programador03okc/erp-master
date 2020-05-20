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

<script>
    function modalSobreERP() {

        $('#modal-sobre-erp').modal({
            show: true,
            backdrop: false
        });
    }
</script>



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
<script src="https://code.jquery.com/jquery-1.11.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>

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
</script>
