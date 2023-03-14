<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>@yield('cabecera') - {{config('global.nombreSistema')}} {{config('global.version')}}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="Cache-control" content="no-cache">
	<meta name="csrf-token" content="{{csrf_token()}}">
	<link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
	<link rel="stylesheet" href="{{ asset('template/fontawesome/css/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/adminlte/css/AdminLTE.css') }}">
	<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/app.css')}}">
	<link rel="stylesheet" href="{{ asset('css/skin-okc.css')}}">
	<link rel="stylesheet" href="{{ asset('template/plugins/lobibox/dist/css/lobibox.min.css')}}">
	<link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2/sweetalert2.min.css')}}">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	<link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/fontawesome/css/fontawesome.min.css') }}">
	@yield('estilos')
    <style>
        .modal-style{
            width:30%;
        }
        .modal-style-recuperar{
            width: 17%;
        }
        @media screen and (max-width: 775px) {
            .modal-style{
                width:90%;
            }
            .modal-style-recuperar{
                width:90%;
            }
        }
    </style>
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
		<div class="modal-dialog modal-style-recuperar">
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

    <div class="modal fade" tabindex="-1" role="dialog" id="atualizar-contraseña" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
        <div class="modal-dialog modal-style">
            <div class="modal-content">
                <form id="form-clave" data-form="actualizar-contraseña" accept="{{ route('modificarClave') }}" method="POST">
                    <div class="modal-header">
                        <h3 class="modal-title" id="titulo">Actualizar contraseña</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Ingrese su nueva contraseña</label>
                                    <input class="form-control contraseña-validar" type="password" id="clave" name="clave" minlength="8"  required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Repita su contraseña</label>
                                    <input class="form-control contraseña-validar" type="password" name="repita_clave" minlength="8" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning" role="alert">
                                    <p>Su nueva contraseña debe tener al menos 8 caracteres alfanuméricos.</p>
                                    <p>Mínimo una Mayúscula</p>
									<p>Mínimo una Minúscula</p>
									<p>Mínimo un número</p>
									<p>Mínimo un caracter especial (@#_%)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	
	<script src="{{ asset('template/plugins/jQuery/jquery.min.js') }}"></script>
	<script src="{{ asset('template/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('template/adminlte/js/adminlte.min.js') }}"></script>
	<script src="{{ asset('template/plugins/bootstrap_filestyle/bootstrap-filestyle.min.js') }}"></script>
	<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
	<script src="{{ asset('template/plugins/lobibox/dist/js/lobibox.min.js') }}"></script>
	<script src="{{ asset('template/plugins/jquery-number/jquery.number.min.js') }}"></script>

	<script src="{{ asset('js/ini.js?')}}?v={{filemtime(public_path('js/ini.js'))}}"></script>
	<script src="{{ asset('js/function.js?')}}?v={{filemtime(public_path('js/function.js'))}}"></script>
	<script src="{{ asset('/js/publico/animation.js')}}"></script>

	<script src="{{ asset('template/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
	@yield('scripts')
</body>
</html>
