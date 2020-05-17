@extends('tesoreria.layout.tesoreria')

@php($pagina = ['titulo' => 'Tesoreria > Caja Chica', 'tiene_menu' => true, 'slug' => 'cajachica'])


@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Administracion de Caja Chica</h2>
		<ol class="breadcrumb">
			<li>Tesoreria</li>
			<li>Administracion</li>
			<li>Caja Chica</li>
		</ol>
	</legend>




	<div class="row">
		<div class="col-md-12">

			<div class="panel panel-success">
				<div class="panel-heading">Solicitudes de Caja Chica Abonadas</div>
				<div class="panel-body">

					<table class="table table-striped table-bordered " id="listaSolicitudes">
						<thead>
						<tr>
							<th></th>
							<th>Codigo</th>
							<th>Detalle</th>
							<th>Importe</th>
							<th>Fecha de Aprobacion</th>
							<th>Empresa / Sede / Area</th>
							<th>Observacion</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>


	<div class="row">
		<div class="col-md-6">
			<fieldset class="group-table">
				<table class="mytable table table-striped table-condensed table-bordered"
					   id="listaCajasChicas">
					<thead>
					<tr>
						<th>Id</th>
						<th>Descripción</th>
						<th>Monto Apertura</th>
						<th>Saldo</th>
						<th>Area</th>
						<th>Responsable</th>
					</tr>
					</thead>
					<tbody></tbody>
				</table>
			</fieldset>
		</div>
		<div class="col-md-6">
			<form id="frmCajaChica" type="register" form="formulario">
				<input type="hidden" class="oculto" name="reg_id" primary="ids">
				<div class="row">
					<div class="col-sm-4">
						<input name="reg_activacion" type="checkbox" class="activation" data-toggle="toggle" data-on="Habilitado" data-off="Deshabilitado" data-onstyle="success">
					</div>
					<div class="col-sm-8">
						<span>Creacion: <em id="f_creacion"></em></span>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label for="reg_descripcion">Descripcion</label>
						<input type="text" class="form-control activation" name="reg_descripcion" required>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-4">
						<label for="reg_empresa">Empresa</label>
						<select name="reg_empresa" id="reg_empresa" class="form-control activation"
								onChange="llenarOtroCombo('reg_sede', '{{ route('ajax.sedes',['::v']) }}', {v: this.value}, [ {value: 'id_sede', text: 'descripcion'}] );"
								required>
							<option value="">Elija una opción</option>
							@foreach ($empresas as $empresa)
								<option value="{{$empresa->id_empresa}}">{{$empresa->contribuyente->razon_social}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_sede">Sede</label>
						<select name="reg_sede" id="reg_sede" class="form-control activation"
								onChange="llenarOtroCombo('reg_area', '{{ route('ajax.areas',['::v']) }}', {v: this.value}, [{text: 'descripcion', campo: 'areas'}, {value: 'id_area', text: 'descripcion'}] );"
								required>
							<option value="">Elija una opción</option>
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_area">Area</label>
						<select name="reg_area" id="reg_area" class="form-control activation" required>
							<option value="">Elija una opción</option>
						</select>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-4">
						<label for="reg_moneda">Moneda</label>
						<select name="reg_moneda" id="reg_moneda" class="form-control activation"
								onchange="cambioMoneda();" required>
							@foreach ($monedas as $moneda)
								<option data-simbolo="{{ $moneda->simbolo }}"
										value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<label for="reg_responsable">Responsable</label>
						<select name="reg_responsable" id="reg_responsable" class="form-control activation"
								required>
							<option></option>
							@foreach ($usuarios as $usuario)
								<option value="{{$usuario->id_usuario}}">
									{{ $usuario->trabajador->postulante->persona->nombre_completo }}
									({{ $usuario->trabajador->postulante->persona->nro_documento }})
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<label for="reg_solicitud">Solicitud</label>
						<input type="hidden" name="reg_solicitud_id">
						<input name="reg_solicitud" id="reg_solicitud" type="text" class="form-control" placeholder="Solicitud.." readonly="">
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-4">
						<label for="reg_monto_apertura">Apertura</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda">-</span>
							<input name="reg_monto_apertura" type="number"
								   class="form-control text-right activation" min="0" step="0.01" required>
						</div>
					</div>
					<div class="col-md-4">
						<label for="reg_monto_maximo_movimiento">Max Movimiento</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda">-</span>
							<input name="reg_monto_maximo_movimiento" type="number"
								   class="form-control text-right activation" min="0" step="0.01" value="37.00" required>
						</div>
					</div>
					<div class="col-md-4">
						<label for="reg_monto_minimo">Monto Minimo</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda">-</span>
							<input name="reg_monto_minimo" type="number"
								   class="form-control text-right activation" min="0" step="0.01" value="" required>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@stop


@section('scripts_seccion')
	<script type="text/javascript">

        $("#reg_responsable").select2({
            placeholder: "Seleccionar Responsable",
            allowClear: true,
            dropdownAutoWidth: true
        });

        $('[name=reg_monto_apertura]').on('input', function () {
            //$('[name=reg_monto_maximo_movimiento]').val(37);
            $('[name=reg_monto_minimo]').val($( this ).val()  * 0.3);
        });


        let vardataTables = funcDatatables();

        let filtro = [
            {campo: 'estado_id', condicion: '', valor: 8},
            {campo: 'solicitud_subtipo_id', condicion: '', valor: 1},
        ];
        let dataSolicitudes = $('#listaSolicitudes').DataTable({
            language: vardataTables[0],
            select: true,
            paging: false,
            info: false,
            searching: false,
            ajax: {
                url: '{{ route('ajax.solicitudes') }}',
                data: {
                    filtro: JSON.stringify(filtro)
                }
            },
            columns: [

                {'data': 'id'},
                {'data': 'codigo'},
                {'data': 'detalle'},
                {
                    'data': null, className: 'text-right', render: function (data) {
                        return data.moneda.simbolo + ' ' + data.importe;
                    }
                },
                {'data': 'fecha_humanos'},
                {
                    'data': 'area', render: function (area) {
                        htmlRet = '<strong>' + area.grupo.sede.empresa.contribuyente.razon_social + '</strong>' +
                            ' <small class="text-muted">' + area.grupo.sede.descripcion + '</small><br>';
                        htmlRet += '<em class="text-uppercase">' + area.grupo.descripcion + '</em> <small class="text-muted text-capitalize">' + area.descripcion + '</small>';
                        return htmlRet;
                    }
                },
                {'data': 'observacion'},
                {
                    sortable: false, render: function () {
                        html = '<button class="btnProcesarSolicitud btn btn-block btn-sm btn-success"><i class="fas fa-forward"></i></button>'
                        return html;
                    }
                },
            ],
        });
        $('#listaSolicitudes').on("click", ".btnProcesarSolicitud", function () {
            let data = dataSolicitudes.row($(this).parents('tr')).data();
            $('#btnNuevo').click();
            llenarFormulario(data, true);
            console.log(data);
        });

        $("#listaSolicitudes").on('DOMNodeInserted DOMNodeRemoved', function () {
            if ($(this).find('tbody tr td').first().attr('colspan')) {
                //$(this).parent().hide();
                $(this).closest('.panel').hide();
            } else {
                //$(this).parent().show();
                $(this).closest('.panel').show();
            }
        });

        function llenarFormulario(data, nuevo = false) {

            if (!nuevo) {
                $('[name=reg_id]').val(data.id);
                $('#f_creacion').html(data.fecha_humanos);
                $('[name=reg_descripcion]').val(data.descripcion);
                $('[name=reg_responsable]').val(data.responsable_id).change();
                $('[name=reg_monto_apertura]').val(data.monto_apertura).change();
                $('[name=reg_monto_maximo_movimiento]').val(data.monto_maximo_movimiento).change();
                $('[name=reg_monto_minimo]').val(data.monto_minimo).change();

                $('[name=reg_solicitud_id]').val(data.solicitud_id);
                $('[name=reg_solicitud]').val(data.solicitud.codigo);

                if (data.estado_id === 11) {
                    $('[name=reg_activacion]').attr('disabled', false).bootstrapToggle('on');
                } else {
                    $('[name=reg_activacion]').attr('disabled', false).bootstrapToggle('off');
                }
            } else {

                $('[name=reg_activacion]').attr('disabled', false).bootstrapToggle('on');
                $('[name=reg_solicitud_id]').val(data.id);
                $('[name=reg_solicitud]').val(data.codigo);
                $('[name=reg_monto_apertura]').val(data.importe);
                $('[name=reg_monto_maximo_movimiento]').val(37).change();
                $('[name=reg_monto_minimo]').val(data.importe * 0.3).change();
            }
            $('[name=reg_empresa]').val(data.area.grupo.sede.id_empresa).change();
            $('[name=reg_sede]').val(data.area.grupo.id_sede).change();
            $('[name=reg_area]').val(data.area_id).change();
            $('[name=reg_moneda]').val(data.moneda_id).change();
        }

        limpiarFormulario();

        function limpiarFormulario() {
            $('[name=reg_activacion]').attr('disabled', false).bootstrapToggle('off');
            $('[name=id]').val('');
            $('#f_creacion').html('');
            $('[name=reg_descripcion]').val('');
            $('[name=reg_responsable]').val('');
            $('[name=reg_monto_maximo_movimiento]').val('');
            $('[name=reg_monto_minimo]').val('');
            $('[name=reg_activacion]').bootstrapToggle('off');
            $('[name=reg_solicitud_id]').val('');
            $('[name=reg_solicitud]').val('');
            $('[name=reg_monto_apertura]').val('');
            $('[name=reg_empresa]').val('');
            $('[name=reg_sede]').val('');
            $('[name=reg_area]').val('');
            $('[name=reg_moneda]').val('');
        }


        let dataCajaChica = $('#listaCajasChicas').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            // 'processing': true,
            'ajax': '{{ route('ajax.cajaschicas') }}',
            'columns': [

                {'data': 'id'},
                {'data': 'descripcion'},
                {
                    'data': null, className: 'text-right', render: function (data) {
                        return data.moneda.simbolo + ' ' + data.monto_apertura;
                    }
                },
                {
                    'data': null, className: 'text-right', render: function (data) {
                        return data.moneda.simbolo + ' ' + data.saldo;
                    }
                },
                {
                    'data': 'area', render: function (area) {
                        htmlRet = '<strong>' + area.grupo.sede.empresa.contribuyente.razon_social + '</strong>' +
                            ' <small class="text-muted">' + area.grupo.sede.descripcion + '</small><br>';
                        htmlRet += '<em class="text-uppercase">' + area.grupo.descripcion + '</em> <small class="text-muted text-capitalize">' + area.descripcion + '</small>';
                        return htmlRet;
                    }
                },
                {
                    'data': null, render: function (data) {
                        htmlRet = '<strong>' + data.responsable.trabajador.postulante.persona.nombre_completo + '</strong><br>' +
                            ' <small class="text-muted">' + data.usuario.trabajador.postulante.persona.nro_documento + '</small>';
                        return htmlRet;
                    }
                }
            ]
        });


        $('#listaCajasChicas tbody').on('click', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }


            let tolSel = $('.eventClick');
            if (tolSel.length == 0) {
                changeStateButton('nuevo');
            } else {
                changeStateButton('historial');
            }

        });

        // ########## ACCIONES BOTONES MODAL #####
        $('#btnNuevo').on('click', function () {
            $('#modalRegistro').modal('show')
        });
        $('#btnEditar').on('click', function () {
            let data = dataCajaChica.row('.eventClick').data();
            //llenarFormulario(data);
            //console.log(data);
            $('#modalSolicitud').modal('show')
        });
        $('#btnGuardar_modalSolicitud').on('click', function () {
            $('#btnGuardar').click();
        });

        $('#modalSolicitud').on('hide.bs.modal', function (e) {
            // do something...
            //limpiarFormulario();
            $('#btnCancelar').click();
        });


        // ####################### Partes de Formulario Manipulacion #############################

        $('#reg_num_docu').on('change', function () {
            if ($(this).val() !== '') {
                $('#reg_proveedor').attr('disabled', false);
            } else {
                $('#reg_proveedor').val(0).trigger("change");
                $('#reg_proveedor').attr('disabled', true);

            }
        });
        $('#reg_tipo').on('change', function () {
            var valor = $(this).val();
            var clase = null;

            switch (valor) {
                case 'I':
                    clase = 'fas fa-sign-in-alt text-success';
                    break;
                case 'E':
                    clase = 'fas fa-sign-out-alt text-danger';
                    break;
                default:
                    break;
            }

            $(this).parent().find('i').attr('class', clase);
        });


        // #########################################        ACCIONES DE BOTONES SUPERIORES      ########################

        $('#btnNuevo').on('click', function () {
            console.log('Funciona');

            $('#modalRegistro').modal('show')
            //return false;
        });

        $('#btnGuardar').on('click', function () {
            guardar($('#frmCajaChica'), postAjaxRegistrar);
            //return false;
        });

        $('#btnEditar').on('click', function () {
            let data = dataCajaChica.row('.eventClick').data();
            llenarFormulario(data);
            console.log(data);
        });

        $('#btnCancelar').on('click', function () {
            limpiarFormulario();
        });


        // ########## FUNCIONES AUXILIARES

        function postAjaxRegistrar() {
            dataSolicitudes.ajax.reload();
            dataCajaChica.ajax.reload();

            $('.has-success').removeClass('has-success');

            $('#btnCancelar').click();
        }


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
                        url = '{{ route('tesoreria.cajachica.store') }}';
                        metodo = 'POST';
                        break;
                    case 'edition':
                        let idReg = $('[name=reg_id]').val();
                        url = '{{ route('tesoreria.cajachica.update', '::v') }}';
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
                        console.log(response);
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
                                        fnSuccess();
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

	</script>

@stop



