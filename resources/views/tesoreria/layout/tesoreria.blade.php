@extends('tesoreria.layout.principal')

@php($modals = $modals ?? [])

@php( $mostrarTCambio = ((isset($arrayDatosDolar)) && (count($arrayDatosDolar) > 0)) )

@if ($mostrarTCambio)
	@php($modals['modalTipoCambio'] = ['id' => 'modalTipoCambio', 'titulo' => 'Registro de Tipo de Cambio', 'class' => 'modal-sm modal-dialog-centered '])

@section($modals['modalTipoCambio']['id'])

	<div class="row">
		<div class="col-md-12 mx-auto">
			<form id="frmTipoCambio" method="POST" action="{{route('tesoreria.tcambio.store')}}">
				@csrf
				<input type="hidden" name="id" value="{{$arrayDatosDolar['id']}}">
				<div class="form-group m-form__group row has-danger">
					<div class="col-sm-12">
						<label for="fecha" class="col col-form-label">Fecha:</label>
						<div class="col-8">
							<input name="fecha" id="fecha" class="form-control m-input" type="text" value="{{$arrayDatosDolar['fecha']}}" readonly>
						</div>
					</div>

				</div>
				<div class="form-group m-form__group row has-warning">
					<div class="col-sm-12">
						<label for="compra" class="col col-form-label">Cambio Compra:</label>
						<div class="col-8">
							<input name="compra" id="compra" class="form-control m-input" type="text" value="{{$arrayDatosDolar['sunat']['compra']}}">
						</div>
					</div>

				</div>
				<div class="form-group m-form__group row has-warning">
					<div class="col-sm-12">
						<label for="venta" class="col col-form-label">Cambio Venta:</label>
						<div class="col-8">
							<input name="venta" id="venta" class="form-control m-input" type="text" value="{{$arrayDatosDolar['sunat']['venta']}}">
						</div>
					</div>

				</div>
			</form>
			<em>Los datos son extraidos de la web de
				<a href="https://e-consulta.sunat.gob.pe/cl-at-ittipcam/tcS01Alias" target="_blank">SUNAT</a></em>
		</div>
	</div>

@stop

@endif




@section('styles_modulo')
	<link rel="stylesheet" href="{{ asset('addons/bootstrap-toggle/css/bootstrap-toggle.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('addons/file-icon-vectors/file-icon-vectors.css') }}" />

	<style type="text/css">
		/** estilos propios **/
		.sidebar-menu .activado {
			background-color: #c92424;
		}

		.has-error .select2-selection {
			border-color: #a94442 !important;
		}

		.has-success .select2-selection {
			border-color: #3c763d !important;
		}

		td.details-control {
			text-align:center;
			color:forestgreen;
			cursor: pointer;
		}
		tr.shown td.details-control {
			text-align:center;
			color:red;
		}

	</style>
@stop

@section('scripts_modulo')
	<script src="{{ asset('addons/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>

	<script type="text/javascript">
        /**
         * Cargar respuesta JSON a Combo
         *
         * @param {string} idComboN IdContenedor Nuevo
         * @param {string} method Metodo de Llamada AJAX
         * @param {string} route Ruta de Laravel donde consultar
         * @param {Object} dataEnviar Si existe algun cambio, en la ruta {v: xxxx}
         * @param {Object|2} dataMostar Campos a mostrar en el combo, puede ser array de 2 obj, el primero es para grupos, si solo se declara uno, se considera como select simple
         * @param {string} contenedor_json si existe un contenedor de respuesta json se debe especificar. ejm (result['data'], se escribe 'data')
         */
        function llenarOtroComboAjax(idComboN, method, route, dataEnviar, dataMostar, contenedor_json = null) {
            console.log('Llena Combo Ajax ' + idComboN);

            $.ajax({
                type: method,
                url: route,
                data: dataEnviar,
                async: false,
                dataType: 'JSON',
                success: function (result_emp) {
                    console.log(result_emp);
                    if (contenedor_json != null) {
                        result_emp = result_emp[contenedor_json];
                    }
                    let htmls = '<option value="">Elija una opción</option>';

                    if (dataMostar.length > 1) {
                        Object.keys(result_emp).forEach(function (key) {
                            htmls += '<optgroup label="' + result_emp[key][dataMostar[0].text] + '">';
                            Object.keys(result_emp[key][dataMostar[0].campo]).forEach(function (key2) {
                                htmls += '<option value="' + result_emp[key][dataMostar[0].campo][key2][dataMostar[1].value] + '">' + result_emp[key][dataMostar[0].campo][key2][dataMostar[1].text] + '</option>';
                            });
                            htmls += '</optgroup>';
                        });
                    } else {
                        Object.keys(result_emp).forEach(function (key) {
                            htmls += '<option value="' + result_emp[key][dataMostar[0].value] + '">' + result_emp[key][dataMostar[0].text] + '</option>';
                        });
                    }

                    $('#' + idComboN).html(htmls).change();
                    //$('#' + idComboN).change();
                    //$('#' + idComboN).val($('#' + idComboN + " option:first").val());

                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 403) {
                    /*
					Swal.fire({
						title: 'Error!',
						text: response.msg,
						type: 'error',
						confirmButtonText: 'Revisar'
					});
					*/
                    Swal.fire({
                        title: 'No Autorizado!',
                        text: jqXHR.responseJSON.message,
                        imageUrl: '{{ asset('images/guard_man.png') }}',
                        imageWidth: 100,
                        imageHeight: 100,
                        backdrop: 'rgba(255,0,13,0.4)'


                    })
                }
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        }


        /**
         * Cargar respuesta JSON a Combo
         *
         * @param {string} idComboN IdContenedor Nuevo
         * @param {string} route Ruta de Laravel donde consultar
         * @param {Object} valChange Si existe algun cambio, en la ruta {v: xxxx}
         * @param {Object|2} dataMostar Campos a mostrar en el combo, puede ser array de 2 obj, el primero es para grupos, si solo se declara uno, se considera como select simple
         * @param {string} contenedor_json si existe un contenedor de respuesta json se debe especificar. ejm (result['data'], se escribe 'data')
         */
        function llenarOtroCombo(idComboN, route, valChange, dataMostar, contenedor_json = null) {
            console.log('Llena Combo ' + idComboN);
            url = route;
            for (var prop in valChange) {
                url = url.replace('::' + prop, valChange[prop])
            }
            console.log(url);

            $.ajax({
                type: 'GET',
                url: url,
                async: false,
                dataType: 'JSON',
                success: function (result_emp) {
                    console.log(result_emp);
                    if (contenedor_json != null) {
                        result_emp = result_emp[contenedor_json];
                    }
                    let htmls = '<option value="">Elija una opción</option>';

                    if (dataMostar.length > 1) {
                        Object.keys(result_emp).forEach(function (key) {
                            htmls += '<optgroup label="' + result_emp[key][dataMostar[0].text] + '">';
                            Object.keys(result_emp[key][dataMostar[0].campo]).forEach(function (key2) {
                                htmls += '<option value="' + result_emp[key][dataMostar[0].campo][key2][dataMostar[1].value] + '">' + result_emp[key][dataMostar[0].campo][key2][dataMostar[1].text] + '</option>';
                            });
                            htmls += '</optgroup>';
                        });
                    } else {
                        Object.keys(result_emp).forEach(function (key) {
                            htmls += '<option value="' + result_emp[key][dataMostar[0].value] + '">' + result_emp[key][dataMostar[0].text] + '</option>';
                        });
                    }

                    $('#' + idComboN).html(htmls).change();
                    //$('#' + idComboN).change();
                    //$('#' + idComboN).val($('#' + idComboN + " option:first").val());

                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 403) {
                    /*
					Swal.fire({
						title: 'Error!',
						text: response.msg,
						type: 'error',
						confirmButtonText: 'Revisar'
					});
					*/
                    Swal.fire({
                        title: 'No Autorizado!',
                        text: jqXHR.responseJSON.message,
                        imageUrl: '{{ asset('images/guard_man.png') }}',
                        imageWidth: 100,
                        imageHeight: 100,
                        backdrop: 'rgba(255,0,13,0.4)'


                    })
                }
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        }

        /**
         * Cambiar entre monedas. agregado clase a esMoneda
         */
        function cambioMoneda() {
            let monedaSel = $('#reg_moneda').find(':selected').data('simbolo');
            $('.esMoneda').html('<b>' + monedaSel + '</b>');
        }


		@if ($mostrarTCambio)
        $('#modalTipoCambio').modal('show');

        $('#btnGuardar_modalTipoCambio').on('click', function () {

            let form = $('#frmTipoCambio');
            let data = form.serialize();
            let url = form.attr('action');

            $.ajax({
                type: 'POST',
                url: url,
				data: data,
                async: false,
                dataType: 'JSON',
                success: function (response) {

                    if (response.error) {
                        console.log(response.msg)
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        //changeStateButton('guardar');
                    } else {
                        let timerInterval;
                        Swal.fire({
                            type: 'success',
                            title: 'Completado!',
                            footer: 'Cerrando en <strong></strong> segundos',
                            html: 'Datos registrados exitosamente.',
                            timer: 3000,
                            onBeforeOpen: () => {
                                //let tFaltante = Math.round(1.56) // Swal.getTimeLeft();
                                Swal.showLoading();
                                timerInterval = setInterval(() => {
                                    Swal.getFooter().querySelector('strong')
                                        .textContent = Math.ceil((Swal.getTimerLeft() / 1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
                                }, 100)
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            if (
                                // Read more about handling dismissals
                                result.dismiss === Swal.DismissReason.timer
                            ) {
                                console.log('Cerrado Automaticamente - Swal');
                                $('#modalTipoCambio').modal('hide');
                            }
                        })
                    }

                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 403) {
                    /*
					Swal.fire({
						title: 'Error!',
						text: response.msg,
						type: 'error',
						confirmButtonText: 'Revisar'
					});
					*/
                    Swal.fire({
                        title: 'No Autorizado!',
                        text: jqXHR.responseJSON.message,
                        imageUrl: '{{ asset('images/guard_man.png') }}',
                        imageWidth: 100,
                        imageHeight: 100,
                        backdrop: 'rgba(255,0,13,0.4)'


                    })
                }
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });


        });

		@endif












		@if(isset($ruta))


		// FUNCIONES GENERALES

        function validar(form) {
            let htmlE = '';
            form.find("[required]").each(function () {
                if ($(this).val() === '') {
                    $(this).closest('div:not(.input-group)').removeClass('has-success').addClass('has-error');
                    htmlE += '<li class="list-group-item">' + $(this).closest('div:not(.input-group)').find('label').text() + '</li>\n';
                    error = true;
                } else {
                    $(this).closest('div:not(.input-group)').removeClass('has-error').addClass('has-success');
                }
            });

            if (htmlE !== '') {

                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '<ul class="list-group">\n' + htmlE + '</ul>',
                    confirmButtonText: 'Revisar'
                });
                throw new Error('Datos faltantes en el formulario');
            }
            return true;
        }


        // ##################### FUNNCIONES DE INTERACION CON EL CONTROLADOR
        function guardar(form, fnSuccess = null) {

            let validacion = validar(form);

            //throw new Error('Error en el procesamiento de datos');

            if (validacion) {


                let tipo = form.attr('type');
                let formData = form.serialize();
                console.log(formData);
                let url = null;
                let metodo = 'GET';
                switch (tipo) {
                    case 'register':
                        url = '{{ route($ruta.'.store') }}';
                        metodo = 'POST';
                        break;
                    case 'edition':
                        let idReg = $('[name=reg_id]').val();
                        url = '{{ route($ruta.'.update', '::v') }}';
                        url = url.replace('::v', idReg);
                        metodo = 'PATCH';
                        break;
                }


                $.ajax({
                    type: metodo,
                    url: url,
                    data: formData,
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.error) {
                            console.log(response.msg)
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                type: 'error',
                                confirmButtonText: 'Revisar'
                            });
                            throw new Error('Error en el procesamiento de datos');
                            //changeStateButton('guardar');
                        } else {
                            let timerInterval;
                            Swal.fire({
                                type: 'success',
                                title: 'Completado!',
                                footer: 'Cerrando en <strong></strong> segundos',
                                html: 'Datos registrados exitosamente.',
                                timer: 3000,
                                onBeforeOpen: () => {
                                    //let tFaltante = Math.round(1.56) // Swal.getTimeLeft();
                                    Swal.showLoading();
                                    timerInterval = setInterval(() => {
                                        Swal.getFooter().querySelector('strong')
                                            .textContent = Math.ceil((Swal.getTimerLeft() / 1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
                                    }, 100)
                                },
                                onClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                if (
                                    // Read more about handling dismissals
                                    result.dismiss === Swal.DismissReason.timer
                                ) {
                                    console.log('Cerrado Automaticamente - Swal');
                                    if (fnSuccess !== null) {
                                        fnSuccess(response.data);
                                    }
                                    throw new Error('Guardado Exitosamente');
                                }
                            })
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 403) {
                        /*
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        */
                        Swal.fire({
                            title: 'No Autorizado!',
                            text: jqXHR.responseJSON.message,
                            imageUrl: '{{ asset('images/guard_man.png') }}',
                            imageWidth: 100,
                            imageHeight: 100,
                            backdrop: 'rgba(255,0,13,0.4)'


                        })
                    }
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
        @endif






	</script>
@stop

@section('menu_lateral')
	<ul class="sidebar-menu tree">
		<li class="okc-menu-title"><label>Tesoreria</label>
			<p>FIN</p></li>
{{--		<li class="treeview {{ (strpos(request()->route()->getName(), 'solicitud') !== false) ? 'active' : '' }}">
			<a href="#">
				<i class="fas fa-coins"></i> <span>Solicitud</span> <i class="fa fa-angle-left pull-right"></i>
			</a>
			<ul class="treeview-menu">
				<li class="{{ (strpos(request()->route()->getName(), 'solicitud.index') !== false) ? 'activado' : '' }}">
					<a href="{{ route('tesoreria.solicitud.index') }}">Estado de Solicitudes</a></li>
				<li class=""><a href="#" onclick="mostrarData(7); return false;">Solicitudes Anuladas</a></li>
			</ul>
		</li>--}}
		@php($menuItems = [
		['permisos' => $entrar['solicitud'], 'parent_route' => 'solicitud.', 'txt_seccion' => 'Solicitud', 'icon_seccion' => 'fas fa-coins', 'submenu' => [
			[ 'permisos' => $roles['req_sol'], 'route' => route('tesoreria.solicitud.index'), 'txt' => 'Administrar Solicitudes' ],
			[ 'permisos' => array_merge($roles['gerente'],$roles['programador']), 'route' => route('tesoreria.solicitud.tipo', 1), 'txt' => 'Pendientes de Aprobacion' ],
			[ 'permisos' => $entrar['solicitud'], 'route' => route('tesoreria.solicitud.tipo', 2), 'txt' => 'Solicitudes Aprobadas' ],
			//[ 'route' => route('tesoreria.planillapagos.extraordinario'), 'txt' => 'Extraordinario' ],
		] ],
		['permisos' => $entrar['pagos'], 'parent_route' => 'planillapagos.', 'txt_seccion' => 'Planilla de Pagos', 'icon_seccion' => 'fas fa-coins', 'submenu' => [
			[ 'route' => route('tesoreria.planillapagos.ordinario'), 'txt' => 'Ordinario' ],
			[ 'route' => route('tesoreria.planillapagos.extraordinario'), 'txt' => 'Extraordinario' ],
		] ],
		['permisos' => [1,2,3,7], 'parent_route' => 'cajachica_movimientos.', 'txt_seccion' => 'Caja Chica', 'icon_seccion' => 'fas fa-coins', 'submenu' => [
			[ 'route' => route('tesoreria.cajachica_movimientos.index'), 'txt' => 'Flujo' ],
		] ],
		['permisos' => [1,2,3,7], 'parent_route' => 'cajachica.', 'txt_seccion' => 'Administracion', 'icon_seccion' => 'fas fa-users-cog', 'submenu' => [
			[ 'route' => route('tesoreria.cajachica.index'), 'txt' => 'Cajas Chicas' ],
			[ 'permisos' => $roles['programador'], 'route' => route('tesoreria.administracion.solicitudes_tipos.index'), 'txt' => 'Tipos Solicitud' ],
		] ],
		['permisos' => $roles['programador'], 'parent_route' => 'configuraciones.', 'txt_seccion' => 'Configuraciones', 'icon_seccion' => 'fas fa-coins', 'submenu' => [
			[ 'route' => route('tesoreria.configuraciones.index'), 'txt' => 'Configuraciones' ],
		] ],
		])

		@foreach($menuItems as $item)
			@if(Auth::user()->hasAnyRole( $item['permisos'] ))
				<li class="treeview {{ (strpos(request()->route()->getName(), $item['parent_route']) !== false) ? 'active' : '' }}">
					<a href="#">
						<i class="{{ $item['icon_seccion'] }}"></i> <span>{{ $item['txt_seccion'] }}</span> <i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						@foreach($item['submenu'] as $submenu)
							@php($urlRel = str_replace(url('/'). '/', '', $submenu['route']))
							@if(isset($submenu['permisos']))
							@if(Auth::user()->hasAnyRole( $submenu['permisos'] ))
							<li class="{{ ($urlRel == request()->path()) ? 'activado' : '' }}">
								<a href="{{ $submenu['route'] }}"> {{ $submenu['txt'] }} </a>
							</li>
							@endif
							@else
							<li class="{{ ($urlRel == request()->path()) ? 'activado' : '' }}">
								<a href="{{ $submenu['route'] }}"> {{ $submenu['txt'] }} </a>
							</li>
							@endif
						@endforeach
					</ul>
				</li>
			@endif
		@endforeach
	</ul>
@stop

@section('contenido')
	<div class="page-main" type="tesoreria_{{ ($pagina['slug'] ?? '') }}">
		@yield('cuerpo_seccion')

		@foreach($modals as $modal)
			@include('tesoreria.partials.modal', [
			'modal' => $modal
			])
		@endforeach
	</div>

@stop
