<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>@yield('cabecera') - System Agile</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta http-equiv="Cache-control" content="no-cache">
		<meta name="csrf-token" content="{{csrf_token()}}">
		<link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
		<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('template/fontawesome/css/all.min.css') }}">
		<link rel="stylesheet" href="{{ asset('template/adminlte/css/AdminLTE.min.css') }}">
		<link rel="stylesheet" href="{{ asset('css/app.css')}}">
		<link rel="stylesheet" href="{{ asset('css/skin-okc.css')}}">
		<link rel="stylesheet" href="{{ asset('Toasty.js-master/dist/toasty.min.css')}}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
		@yield('estilos')
	</head>
	<body class="hold-transition skin-okc sidebar-mini">

    
    @yield('body')
    


    <div class="modal fade" role="dialog" id="modal-sobre-erp">
		<div class="modal-dialog" style="width: 40%;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title"><strong>Sobre Agile</strong></h3>
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

	<script type="text/javascript">
	var auth_user = <?php echo $auth_user ?>; 
	var optionsToasty = {
            // STRING: main class name used to styling each toast message with CSS:
            // .... IMPORTANT NOTE:
            // .... if you change this, the configuration consider that you´re
            // .... re-stylized the plug-in and default toast styles, including CSS3 transitions are lost.
            classname: "toast",
            // STRING: name of the CSS transition that will be used to show and hide all toast by default:
            transition: "pinItUp",
            // BOOLEAN: specifies the way in which the toasts will be inserted in the HTML code:
            // .... Set to BOOLEAN TRUE and the toast messages will be inserted before those already generated toasts.
            // .... Set to BOOLEAN FALSE otherwise.
            insertBefore: true,
            // INTEGER: duration that the toast will be displayed in milliseconds:
            // .... Default value is set to 4000 (4 seconds).
            // .... If it set to 0, the duration for each toast is calculated by text-message length.
            duration: 4000,
            // BOOLEAN: enable or disable toast sounds:
            // .... Set to BOOLEAN TRUE  - to enable toast sounds.
            // .... Set to BOOLEAN FALSE - otherwise.
            // NOTE: this is not supported by mobile devices.
            enableSounds: false,
            // BOOLEAN: enable or disable auto hiding on toast messages:
            // .... Set to BOOLEAN TRUE  - to enable auto hiding.
            // .... Set to BOOLEAN FALSE - disable auto hiding. Instead the user must click on toast message to close it.
            autoClose: true,
            // BOOLEAN: enable or disable the progressbar:
            // .... Set to BOOLEAN TRUE  - enable the progressbar only if the autoClose option value is set to BOOLEAN TRUE.
            // .... Set to BOOLEAN FALSE - disable the progressbar.
            progressBar: false,
            // IMPORTANT: mobile browsers does not support this feature!
            // Yep, support custom sounds for each toast message when are shown if the
            // enableSounds option value is set to BOOLEAN TRUE:
            // NOTE: the paths must point from the project's root folder.
            sounds: {
                // path to sound for informational message:
                info: "{{ asset('/') }}/Toasty.js-master/dist/sounds/info/1.mp3",
                // path to sound for successfull message:
                success: "{{ asset('/') }}/Toasty.js-master/dist/sounds/success/1.mp3",
                // path to sound for warn message:
                warning: "{{ asset('/') }}/Toasty.js-master/dist/sounds/warning/1.mp3",
                // path to sound for error message:
                error: "{{ asset('/') }}/Toasty.js-master/dist/sounds/error/1.mp3",
            },
        
            // callback:
            // onShow function will be fired when a toast message appears.
            onShow: function (type) {},
        
            // callback:
            // onHide function will be fired when a toast message disappears.
            onHide: function (type) {},
        
            // the placement where prepend the toast container:
            prependTo: document.body.childNodes[0]
        };
	</script>

	<script src="{{ asset('Toasty.js-master/dist/toasty.min.js') }}"></script>
	<script src="{{ asset('template/plugins/jQuery/jquery.min.js') }}"></script>
	<script src="{{ asset('template/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('template/adminlte/js/adminlte.min.js') }}"></script>
	<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

	<script src="{{ asset('js/ini.js')}}"></script>
	<script src="{{ asset('js/function.js')}}"></script>
	<script src="{{ asset('/js/publico/animation.js')}}"></script>

	<script src="{{ asset('template/plugins/sweetalert/sweetalert2@8.js') }}"></script>
	<script src="{{ asset('template/plugins/bootstrap_filestyle/bootstrap-filestyle.min.js') }}"></script>
	<script src="{{ asset('template/plugins/pace/pace.js') }}"></script>
	<script src="{{asset('js/publico/notificaciones_sin_leer.js')}}"></script>

	<script>


 		function seleccionarMenu(url)
		{
            $('ul.sidebar-menu a').filter(function () {
                return this.href == url;
                
            }).parent().addClass('active');

            $('ul.treeview-menu a').filter(function () {
                return this.href == url;
            }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
            
            // sidebar with box collapsed-box
            $('ul.treeview-menu a').filter(function () {
                return this.href == url;
            }).parents("div.box.collapsed-box.active").find('div.box-body.active').removeAttr('style');;
            
            $('ul.treeview-menu a').filter(function () {
                return this.href == url;
            }).parents("div.box.collapsed-box.active").removeClass('collapsed-box');
            
            $('ul.treeview-menu a').filter(function () {
                return this.href == url;
            }).parents('div.box.active').find("button.btn.btn-box-tool i").attr("class","fa fa-minus");		
		}

		
	</script>

	@yield('scripts')
	</body>
</html>