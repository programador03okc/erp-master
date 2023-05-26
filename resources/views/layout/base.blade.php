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
            width: 30%;
        }
        .modal-style-recuperar{
            width: 40%;
        }
		#atualizar-contraseña .modal-content {
			width: 500px;
		}
        @media screen and (max-width: 775px) {
            .modal-style{
                width: 90%;
            }
            .modal-style-recuperar{
                width:90%;
            }
            /* #atualizar-contraseña{
                padding-left: 180px !import;
            } */
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
                                    <label for="">Nueva contraseña</label>
                                    <input class="form-control contraseña-validar" type="password" placeholder="Escriba la nueva contraseña" id="clave" name="clave" minlength="8"  required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Repita su contraseña</label>
                                    <input class="form-control contraseña-validar" type="password" placeholder="Repita la contraseña" name="repita_clave" minlength="8" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning" role="alert">
                                    <p>Su nueva contraseña debe tener al menos 8 caracteres alfanuméricos.</p>
                                    <p>- Mínimo una Mayúscula</p>
									<p>- Mínimo una Minúscula</p>
									<p>- Mínimo un número</p>
									<p>- Mínimo un caracter especial ("'@#_%")</p>
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

	<script type="text/javascript">
		var auth_user = {!! $auth_user !!};
        const csrf_token = '{{ csrf_token() }}';
	</script>
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
	<script>
		$(document).ready(function() {
			notificacionesNoLeidas();

            $.ajax({
				url: '{{ route("actualizar") }}',
				data: {_token: '{{ csrf_token() }}'},
				type: 'GET',
				dataType: 'JSON',
				success: function (data) {
					if (data.success === true) {
                        $('#atualizar-contraseña').modal('show');
                    }
				}
			});
		});

        $("#form-clave").on('submit',function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            var clave = $('.contraseña-validar[name="clave"]').val(),
                repita_clave = $('.contraseña-validar[name="repita_clave"]').val(),
                // regularExpression  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%.*?&])([A-Za-z\d$@$!%*?&]|[^ ])$/;
                // regularExpression = /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8}$/
                // regularExpression = /^(?=\w*[A-Z])(?=\w*[a-z])\S{8}$/;
                regularExpression = /^(?=^.{8,}$)((.)(?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
                success=false;

            if (clave === repita_clave) {
                if (regularExpression.test(clave)) {
                    success=true;
                } else {
                    success=false;
                    Swal.fire(
                        'Información',
                        'Su nueva contraseña debe tener al menos 8 caracteres alfanuméricos. Ejemplos: Inicio01., Inicio01.@, @"+*}-+',
                        'warning',
                    );

                }
            }else{
                Swal.fire(
                    'Información',
                    'Su clave no coincide, ingrese correctamente en ambos campos su clave',
                    'warning'
                );
            }

            if (success) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '/modificar-clave',
                    data: data,
                    dataType: 'JSON',
                    beforeSend: (data) => {

                    }
                }).done(function(response) {
                    // console.log(response);
                    if (response.success===true) {
                        $('#atualizar-contraseña').modal('hide');
                        Swal.fire(
                        'Éxito',
                        'Se actualizo con éxito',
                        'success'
                        )
                    }else{
                        Swal.fire(
                        'Información',
                        'Ingrese de nuevo su clave',
                        'warning'
                        )
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }

        });

		function seleccionarMenu(url) {
			$('ul.sidebar-menu a').filter(function() {
				return this.href == url;

			}).parent().addClass('active');

			$('ul.treeview-menu a').filter(function() {
				return this.href == url;
			}).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

			// sidebar with box collapsed-box
			$('ul.treeview-menu a').filter(function() {
				return this.href == url;
			}).parents("div.box.collapsed-box.active").find('div.box-body.active').removeAttr('style');;

			$('ul.treeview-menu a').filter(function() {
				return this.href == url;
			}).parents("div.box.collapsed-box.active").removeClass('collapsed-box');

			$('ul.treeview-menu a').filter(function() {
				return this.href == url;
			}).parents('div.box.active').find("button.btn.btn-box-tool i").attr("class", "fa fa-minus");
		}

		function notificacionesNoLeidas() {
			const $spanNotificaciones = $('#spanNotificaciones');
			$.ajax({
				url: '{{ route("notificaciones.cantidad-no-leidas") }}',
				data: {_token: '{{ csrf_token() }}'},
				type: 'POST',
				dataType: 'JSON',
				success: function (data) {
					$spanNotificaciones.html(data.mensaje);
					if (data.mensaje > 0) {
						$spanNotificaciones.removeClass('label-default');
						$spanNotificaciones.addClass('label-warning');
					} else {
						$spanNotificaciones.removeClass('label-warning');
						$spanNotificaciones.addClass('label-default');
					}
				}
			});
		}
	</script>
	@yield('scripts')
</body>
</html>
