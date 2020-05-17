@extends('tesoreria.layout.tesoreria')

@php($ruta = 'tesoreria.cajachica_movimientos')

@php($pagina = ['titulo' => 'Tesoreria > Caja Chica', 'tiene_menu' => true, 'slug' => 'cajachica'])

@php($modals['modalRegistro'] = ['id' => 'modalRegistro', 'titulo' => 'Registro de Caja Chica', 'class' => 'modal-dialog-centered '])
@php($modals['modalSaldos'] = ['id' => 'modalSaldos', 'titulo' => 'Saldos de Caja Chica', 'class' => 'modal-dialog-centered modal-sm '])
@php($modals['modalHistorial'] = ['id' => 'modalHistorial', 'titulo' => 'Historial de Caja Chica', 'class' => 'modal-dialog-centered ', 'style' => 'width: 80%', 'botones' => ['guardar' => false, 'cancelar' => true], 'botones_derecha' => [ [ 'class' => 'btn-primary', 'id' => 'btnExportarPDF', 'txt' => 'Exportar a PDF'] ] ])

@section($modals['modalRegistro']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto">
			<form id="frmCajaChicaMovimientos" type="register" form="formulario">
				<input type="hidden" name="reg_vale" id="reg_vale" value="">
				<input type="hidden" name="reg_receptor_id" value="">
				<input type="hidden" name="reg_cajachica_id">
				<input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
				<div class="form-group row">
					<div class="col-sm-6">
						<label for="reg_tipo">Tipo</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fas fa-sign-in-alt text-success"></i></span>
							<select id="reg_tipo" name="reg_tipo" class="form-control">
								<option value="I">Ingreso</option>
								<option value="E">Egreso</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<label for="reg_orig_operacion">Orig Operacion</label>
						<select id="reg_orig_operacion" name="reg_orig_operacion" class="form-control" required>
							<option value="0">Elija una opción</option>
							@foreach ($doc_operacion as $dOp)
								<option value="{{$dOp->id}}">{{$dOp->descripcion}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group row">
					<fieldset class="col-sm-12">
						<legend>Documentos de Sustento</legend>
						<table id="tablaDocSustento" class="table table-hover table-borderless">
							<thead>
							<tr>
								<th class="col-sm-4">N° Documento</th>
								<th class="col-sm-3">Monto</th>
								<th class="col-sm-4">
									Proveedor
								</th>
								<th class="col-sm-1">
									<button type="button" class="btn btn-success" id="addNewProveedor"><i class="fas fa-plus"></i> Prov.</button>
								</th>
							</tr>
							<tr>
								<td>
									<input type="text" class="form-control input-sm" id="reg_num_docu" name="reg_num_docu">
								</td>
								<td>
{{--									<input type="text" class="form-control input-sm" id="reg_sust_monto" name="reg_sust_monto">--}}
									<div class="input-group">
										<span class="input-group-addon esMoneda"></span>
										<input id="reg_sust_monto" type="number" class="form-control text-right" name="reg_sust_monto" min="0" max="" value="0" step="0.01">
									</div>
								</td>
								<td>
									<select class="form-control activation js-example-basic-single" id="reg_proveedor" name="reg_proveedor" disabled="true">
										<option value="">Elija una opción</option>
										@foreach ($proveedores as $prov)
											<option value="{{$prov->id_proveedor}}">{{$prov->contribuyente->nro_documento}}
												- {{$prov->contribuyente->razon_social}}</option>
										@endforeach
									</select>
								</td>
								<td class="col-sm-1">
									<button type="button" class="btn btn-success btn-sm" id="addDocSustento"><i class="fas fa-plus"></i></button>
								</td>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</fieldset>
				</div>
				<div class="form-group row">
					<div class="col-sm-4">
						<label for="reg_moneda">Moneda</label>
						<select id="reg_moneda" name="reg_moneda" class="form-control" onchange="cambioMoneda();" required>
							@foreach ($monedas as $moneda)
								<option data-simbolo="{{ $moneda->simbolo }}" value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_t_cambio">T.Cambio</label>
						<div class="input-group">
							<span class="input-group-addon disabled esMoneda">S/</span>
							<input id="reg_t_cambio" type="number" class="form-control text-right" name="reg_t_cambio" min="0" value="0" step="0.01" disabled>
						</div>
					</div>
					<div class="col-sm-4">
						<label for="reg_importe">Importe</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda"></span>
							<input id="reg_importe" type="number" class="form-control text-right" name="reg_importe" min="0" max="" value="0" step="0.01" required>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-12">
						<label for="reg_observacion">Concepto:</label>
						<input type="text" id="reg_observacion" name="reg_observacion" class="form-control" rows="1" required>
					</div>
				</div>
				<div class="form-group row" id="docsData">
				</div>
			</form>
		</div>
	</div>
@stop

@section($modals['modalSaldos']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto">
			<div class="box box-info">

				<div class="box-body">
					<div class="form-group row">
						<label for="showInicial" class="col-sm-4 control-label">Inicial</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-lg text-right" id="showInicial" readonly="">
						</div>
					</div>
					<div class="form-group row">
						<label for="showIngresos" class="col-sm-4 control-label">Ingresos</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-lg text-right" id="showIngresos" readonly="">
						</div>
					</div>
					<div class="form-group row">
						<label for="showEgresos" class="col-sm-4 control-label">Egresos</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-lg text-right" id="showEgresos" readonly="">
						</div>
					</div>
					<div class="form-group row">
						<label for="showSaldo" class="col-sm-4 control-label text-lg-left">Saldo</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-lg text-right" id="showSaldo" readonly="">
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
@stop

@section($modals['modalHistorial']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto">

			<div class="form-group row">
				<div class="col-sm-4">
					<label for="txtFechaInicial">Fecha Inicial</label>
					<input type="date" id="txtFechaInicial" name="txtFechaInicial" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
				</div>
				<div class="col-sm-4">
					<label for="txtFechaFinal">Fecha Final</label>
					<input type="date" id="txtFechaFinal" name="txtFechaFinal" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
				</div>
				<div class="col-sm-4">
					<button type="button" onclick="cargarDatatableResumen()" class="btn btn-primary" id="btnGenerarResumen"> Generar Resumen </button>
				</div>
			</div>

			<div class="col-sm-5">
				<div class="box box-success">
					<h4 class="box-title">Ingresos</h4>
					<div id="dataIngresos" class="box-body">

						<table class="table table-hover table-borderless">
							<thead>
							<tr>
								<th>Fecha</th>
								<th>Detalle</th>
								<th>Total</th>
								<th>Documentos</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							<tr>
								<td class="" colspan="2">
									Total
								</td>
								<td class="text-right total"></td>
								<td></td>
							</tr>
							</tfoot>
						</table>

					</div>

				</div>
			</div>

			<div class="col-sm-6 col-sm-offset-1">
				<div class="box box-warning">
					<h4 class="box-title">Egresos</h4>
					<div id="dataEgresos" class="box-body">

						<table class="table table-hover table-borderless">
							<thead>
							<tr>
								<th>Fecha</th>
								<th>Detalle</th>
								<th>Total</th>
								<th>Documentos</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							<tr class="text-bold">
								<td class="" colspan="2">
									Total
								</td>
								<td class="text-right total"></td>
								<td></td>
							</tr>
							</tfoot>
						</table>

					</div>

				</div>
			</div>
			<form id="frmHistorialCaja" target="_blank" method="POST" style="display: none">
				@csrf
				<input type="hidden" name="txt_id_cajachica">
				<input type="hidden" name="f_ini">
				<input type="hidden" name="f_fin">
			</form>
		</div>
	</div>
@stop



@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Flujo de Caja Chica
			<button id="btnSaldos" class="btn btn-success" style="display: none;">Saldos</button>
			<button id="btnHistorialCaja" class="btn btn-success" style="display: none;">Historial</button>
		</h2>
		<ol class="breadcrumb">
			<li>Tesoreria</li>
			<li>Caja Chica</li>
			<li>Flujo</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">

		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<select id="sel_empresa" class="form-control" onChange="llenarOtroComboAjax('sel_cajachica', 'POST', '{{ route('ajax.cajaschicas') }}', {empresa: this.value}, [ {value: 'id', text: 'descripcion'}], 'data' );">
				<option value="0">Selecciones Empresa...</option>
				@foreach ($empresas as $empresa)
					<option value="{{$empresa->id_empresa}}">{{$empresa->contribuyente->razon_social}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-md-6">
			<select id="sel_cajachica" class="form-control" onchange="cargarDatatable();">
				<option value="0">Seleccione Caja Chica...</option>
			</select>
		</div>
		<div class="col-md-3">
			<input type="date" class="form-control activation" value="{{ date('Y-m-d') }}" id="sel_fecha" onchange="cargarDatatable();">
		</div>
		<div class="col-md-12">
			<p class="text-warning"> Por favor verifique los datos antes de antes de hacer un ingreso.</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="mytable table table-striped table-condensed table-bordered" id="listaFlujoCajaChica">
				<thead>
				<tr>
					<th>#</th>
					<th>Tipo</th>
					<th>Mov/Sede</th>
					<th>Nº Doc</th>
					<th>Importe</th>
					<th>Vale</th>
					<th>Detalle</th>
				</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
@stop


@section('scripts_seccion')
	<script type="text/javascript">

		$('#btnExportarPDF_modalHistorial').on('click', function () {
            var cajachica = $('#sel_cajachica').val();
            var fechaIni = $('#txtFechaInicial').val();
            var fechaFin = $('#txtFechaFinal').val();
            let urlMostrar = '{{ route('tesoreria.pdf.historial.cajachica', '::v') }}';
            urlMostrar = urlMostrar.replace('::v', cajachica);

		    $('[name=txt_id_cajachica]').val( cajachica );
		    $('[name=f_ini]').val( fechaIni );
		    $('[name=f_fin]').val( fechaFin );
            $('#frmHistorialCaja').attr('action', urlMostrar);
			$('#frmHistorialCaja').submit()
        });



		// AGREGAR FILA PARA DOCUMENTOS DE SUSTENTO
        $("#addDocSustento").click(function() {
            const trInput = $('#tablaDocSustento thead>tr:last');
            const tBody = $('#tablaDocSustento tbody');

            const docNum = $('#reg_num_docu').val();
            const docMonto = $('#reg_sust_monto').val();
            const docProv = $('#reg_proveedor').select2('data');

			if( (docNum != '') && (docProv != '') ){
			    htmInputHiden = '';
			    htmInputHiden += '<input type="hidden" name="sust_doc[]" value="' + docNum + '">';
			    htmInputHiden += '<input type="hidden" name="sust_prov[]" value="' + docProv[0].id + '">';
			    htmInputHiden += '<input type="hidden" name="sust_monto[]" value="' + docMonto + '">';
                htmlResp = '<tr>';
                htmlResp += '<td>' + docNum + '</td>';
                htmlResp += '<td>' + docMonto + '</td>';
                htmlResp += '<td>' + docProv[0].text + '</td>';
                htmlResp += '<td>' + htmInputHiden + '<button class="btnEliminarFila btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>';
                htmlResp += '</tr>';

                tBody.append(htmlResp);
                //return false;
                $('#reg_num_docu').val('');
                $('#reg_sust_monto').val('');
                $('#reg_proveedor').select2('val','');
			}
			else{
                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '',
                    confirmButtonText: 'Revisar'
                });
			}

        });

        $('#tablaDocSustento').on("click", ".btnEliminarFila", function(){
            //console.log($(this).parent());
            $(this).closest("tr").remove();
        });
		// FIN AGREGAR FILA


		// CREAR PROVEEDOR
		async function getDataProveedor(){


            /*Swal.fire({
                title: 'Nuevo Proveedor',
                type: 'question',
                input: 'text',
                inputPlaceholder: 'Ruc',
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'OK',
                inputValidator: (value) => {
                    $('[name=reg_receptor_id]').val(value);
                },
                preConfirm: (value) => {
                    console.log(value);
                    guardar($('#frmCajaChicaMovimientos'), postAjaxRegistrar)
                    return true;
                },
                allowOutsideClick: () => !Swal.isLoading()
            });*/



            const {value: dataRemoto} = await Swal.fire({
                //title: 'Ruc',
                title: 'Busqueda de RUC',
                type: 'question',
                input: 'text',
                inputPlaceholder: 'Ingrese RUC',
                showCancelButton: true,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                inputValidator: (value) => {
                    if (!value) {
                        return 'Es requerido un numero RUC para iniciar la busqueda!'
                    }
                },
                preConfirm: (ruc) => {
                    let url = '{{ route('ajax.data.persona_contribuyente',['tipo' => 'contribuyente', 'identificador' => '::a']) }}';
                    url = url.replace('::a', ruc);

                    return fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        });

                },
            });

            if (dataRemoto) {

                let dataVerif = dataRemoto.tipo_contribuyente;

                const {value: responseGuardar} = await Swal.fire({
                    title: 'Nuevo Proveedor',
                    html:
						'<div class="form-group row">' +
						'<div class="col-sm-12">' +
						'<label>Tipo Contribuyente</label>' +
                        '<select class="form-control" id="reg_prov_tipo_id" name="reg_prov_tipo_id">' +
							@foreach ($tipo_contribuyente as $tipo)
                                '<option value="{{$tipo->id_tipo_contribuyente}}">{{$tipo->descripcion}}</option>'+
							@endforeach
                                '</select>' +
						'</div>' +
						'<div class="col-sm-12">' +
						'<label>Numero Ruc</label>' +
						'<input id="reg_prov_ruc" type="text" class="form-control" name="reg_prov_ruc" value="' + dataRemoto.nro_documento + '">' +
						'</div>' +
						'<div class="col-sm-12">' +
                        '<label>Razon Social</label>' +
                        '<input id="reg_prov_razon_social" type="text" class="form-control" name="reg_prov_razon_social" value="' + dataRemoto.razon_social + '">' +
						'</div>' +
						'<div class="col-sm-12">' +
                        '<label>Direccion Fiscal</label>' +
                        '<input id="reg_prov_direccion" type="text" class="form-control" name="reg_prov_direccion" value="' + dataRemoto.direccion_fiscal + '">' +
						'</div>' +
						'<div class="col-sm-6">' +
                        '<label>Telefono</label>' +
                        '<input id="reg_prov_telefono" type="text" class="form-control" name="reg_prov_telefono">' +
						'</div>' +
						'<div class="col-sm-6">' +
                        '<label>Celular</label>' +
                        '<input id="reg_prov_celular" type="text" class="form-control" name="reg_prov_celular">' +
						'</div>' +
						'</div>',
                    focusConfirm: false,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    onOpen: function () {
                        $('#reg_prov_tipo_id').val(dataRemoto.id_tipo_contribuyente);
                    },
                    preConfirm: () => {
						let dataSave = {
						    existe: dataVerif,
							id_contribuyente: dataRemoto.id_contribuyente,
						    id_tipo: $('#reg_prov_tipo_id').val(),
						    nro_documento: $('#reg_prov_ruc').val(),
						    razon_social: $('#reg_prov_razon_social').val(),
						    direccion: $('#reg_prov_direccion').val(),
						    telefono: $('#reg_prov_telefono').val(),
						    celular: $('#reg_prov_celular').val(),
						};

                        let url = '{{ route('tesoreria.proveedor.store') }}';

                        let respuesta = $.ajax({
                            type: 'POST',
                            url: url,
                            data: dataSave,
                            dataType: 'JSON',
                            success: function (response) {
                                if (response.error) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.msg,
                                        type: 'error',
                                        confirmButtonText: 'Revisar'
                                    });
                                    throw new Error('Error en el procesamiento de datos');
                                    //changeStateButton('guardar');
                                } else {
                                    return response;
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

                        return respuesta;

                        /*

                        return fetch(url, {
                            method: 'POST', // or 'PUT'
                            body: JSON.stringify(dataSave), // data can be `string` or {object}!
                            headers:{
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                )
                            });
                        */

                        //return dataSave;
                    }
                });

                if (responseGuardar) {
                    //Swal.fire(JSON.stringify(formValues));

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

                            let urlMostrar = '{{ route('ajax.proveedores') }}';
                            $.get(urlMostrar).done(function (data) {
                                let dataSelect = [];
                                $.each(data, function(idx, item){
                                    dataSelect.push({
										text: item.contribuyente.nro_documento + ' - ' + item.contribuyente.razon_social,
										id: item.id_proveedor
									})
								});
                                $('#reg_proveedor').select2({
                                    data: dataSelect
                                });
                                $('#reg_proveedor').val(responseGuardar.data.id_proveedor).trigger('change');
                            });
                            throw new Error('Guardado Exitosamente');
                        }
                    })
                }
            }




            return false;
		}
		$('#addNewProveedor').on('click', function () {



            getDataProveedor();



/*


            const {value: email} = await Swal.fire({
                title: 'Input email address',
                input: 'email',
                inputPlaceholder: 'Enter your email address'
            })

            if (email) {
                Swal.fire('Entered email: ' + email)
            }


            Swal.fire({
                title: 'Nuevo Proveedor',
                type: 'question',
                input: 'select',
                inputOptions: {
					@foreach ($usuarios as $usuario)
                    '{{$usuario->id_usuario}}': '{{ $usuario->trabajador->postulante->persona->nombre_completo }} ({{ $usuario->trabajador->postulante->persona->nro_documento }})',
					@endforeach
                },
                inputPlaceholder: 'Seleccionar Responsable',
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'OK',
                onOpen: function () {
                    $(".swal2-select").select2({
                        theme: "bootstrap",
                        placeholder: "Seleccionar Responsable",
                        allowClear: true,
                        dropdownAutoWidth: true
                    });
                },
                inputValidator: (value) => {
                    $('[name=reg_receptor_id]').val(value);
                },
                preConfirm: (value) => {
                    console.log(value);
                    guardar($('#frmCajaChicaMovimientos'), postAjaxRegistrar)
                    return true;
                },
                allowOutsideClick: () => !Swal.isLoading()
            });

            */

        });
		// FIN Crear Proveedor

        $(window).on("load", function () {
            $('#sel_empresa').val(0);
            $('#reg_tipo').change();
        });


        let vardataTables = funcDatatables();

        let dataFlujoCajaChica = $('#listaFlujoCajaChica').DataTable({
            language: vardataTables[0],
            select: true,
            paging: false,
            info: false,
            searching: false,

            'columns': [

                {'data': null},
                {
                    'data': 'tipo_movimiento', 'render': function (data) {
                        //console.log(data);
                        if (data == 'I') {
                            return '<i class="fas fa-sign-in-alt fa-2x text-success"></i>';
                        } else if (data == 'E') {
                            return '<i class="fas fa-sign-out-alt fa-2x text-danger"></i>';
                        }
                    }
                },
                {'data': 'doc_operacion.descripcion'},
                {'data': 'data_pago', render: function (data) {
						let objPagos = JSON.parse(data);
						let htmlPagos = '<ul>';

                        $.each(objPagos, function() {
                            //alert('this is ' + this);
							htmlPagos += '<li>' + this.num_docu + '</li>';
                        });
                        htmlPagos += '<ul>';

                        return htmlPagos;
                    }
				},
                //{'data': 'proveedor_id'},
                //{'data': 'importe'},
                {
                    'data': null, render: function (data) {
                        //return data.moneda.simbolo + ' ' + data.importe;
                        return (
                            data.moneda.simbolo + ' ' +
                            Number(data.importe)
                                .toFixed(2) // always two decimal digits
                                .replace('.', ',') // replace decimal point character with ,
                                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
                        ) // use . as a separator
                    }
                },
                {
                    'data': null, render: function (data) {
                        //console.log(data.vale_numero);
                        if (data.vale_numero !== null) {
                            let urlVale = '{{ route('tesoreria.pdf.vale_salida',['::v']) }}';
                            urlVale = urlVale.replace('::v', data.vale.id);

                            htm = '<a href="' + urlVale + '" target="_blank"><i class="far fa-file-pdf fa-2x"></i></a>';
                            return htm;
                        } else {
                            return '';
                        }
                    }
                },
                {'data': 'observaciones'}
            ],
            "rowCallback": function (row, data, index) {
                //console.log(data);
                $('td:eq(0)', row).html(index + 1);

                //[{"doc_tipo":null,"num_docu":"F 004-0010290","id_proveedor":"3"},{"doc_tipo":null,"num_docu":"BV 001-0000564","id_proveedor":"20"}]
                //[{"doc_tipo":null,"num_docu":"Req OKC-S190612-001","id_proveedor":null},{"doc_tipo":null,"num_docu":"BV 001-0000564","id_proveedor":"20"}]


                if (data.tipo_movimiento === 'E') {
                    if ( (data.vale_numero !== null) ) {
                        let dataPago = JSON.parse(data.data_pago);
                        if ( (dataPago != null) && (dataPago.length <= 0) ) {
                            $('td:eq(0)', row).parent().addClass('text-warning');
                            let htmNum = '<i class="fas fa-exclamation-triangle"></i><small>Falta Comprobante</small>';
                            $('td:eq(3)', row).html(htmNum);
						}
                    } else if (data.doc_pago === null) {
                        $('td:eq(0)', row).parent().addClass('text-danger');

                        let htmNum = '<i class="fas fa-exclamation-triangle"></i><small>Falta Sustento</small>';
                        $('td:eq(3)', row).html(htmNum);
                    }
                }
            }
        });


        $('#listaFlujoCajaChica tbody').on('click', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaFlujoCajaChica').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }

            let tolSel = $('.eventClick').length;
            if (tolSel === 0) {
                changeStateButton('nuevo');
            } else {
                changeStateButton('historial');
                //$('#btnCancelar').click();
            }
            //console.log(tolSel);

        });
        $('#listaFlujoCajaChica tbody').on('dblclick', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaFlujoCajaChica').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            $('#btnHistorial').click();


        });


        function cargarDatatable() {

            var cajachica = $('#sel_cajachica').val();
            var fecha = $('#sel_fecha').val();

            if (cajachica !== '') {
                let urlMostrar = '{{ route('ajax.cajachica.movimientos', '::v') }}';
                urlMostrar = urlMostrar.replace('::v', cajachica);
                //console.log(urlMostrar);

                let dataEnviar = {
                    cajachica_id: cajachica,
                    f_ini: fecha
                };

                $.get(urlMostrar, dataEnviar).done(function (data) {
                    dataFlujoCajaChica.clear().draw();
                    dataFlujoCajaChica.rows.add(data.data); // Add new data
                    dataFlujoCajaChica.columns.adjust().draw(); // Redraw the DataTable
                });

                $('[name=reg_cajachica_id]').val(cajachica);
                $('#btnSaldos').show();
                $('#btnHistorialCaja').show();
            } else {
                dataFlujoCajaChica.clear().draw();
                $('#btnSaldos').hide();
                $('#btnHistorialCaja').hide();
            }
        }


        function cargarDatatableResumen() {

            var cajachica = $('#sel_cajachica').val();
            var fechaIni = $('#txtFechaInicial').val();
            var fechaFin = $('#txtFechaFinal').val();

            if (cajachica !== '') {
                let urlMostrar = '{{ route('ajax.cajachica.movimientos', '::v') }}';
                urlMostrar = urlMostrar.replace('::v', cajachica);
                //console.log(urlMostrar);

                let dataEnviar = {
                    cajachica_id: cajachica,
                    f_ini: fechaIni,
					f_fin: fechaFin
                };



                $.get(urlMostrar, dataEnviar).done(function (data) {
                    console.dir(data.data);
                    let htmlIngresos = '';
                    let htmlEgresos = '';
                    let totalIngresos = 0;
                    let totalEgresos = 0;
                    $.each(data.data, function (idx, dMov) {

						var lstDocs = '';
                        $.each(JSON.parse(dMov.data_pago), function (idx2, dDoc) {
                            lstDocs += dDoc.num_docu + ', ';
                        });
                        let htmlComun = '<tr>' +
                            '<td>' + dMov.fecha_j_s + '</td>' +
                            '<td>' + dMov.observaciones + '</td>' +
                            '<td>' + dMov.moneda.simbolo + ' ' + dMov.importe + '</td>' +
                            '<td>' + lstDocs + '</td>' +
                            '</tr>' ;

						if (dMov.tipo_movimiento == 'I'){
						    totalIngresos +=  dMov.importe *1;
                            htmlIngresos += htmlComun;
						}
						else{
                            totalEgresos += dMov.importe *1;
						    htmlEgresos += htmlComun;
						}
                    });

                    $('#dataIngresos tbody').html(htmlIngresos);
                    $('#dataEgresos tbody').html(htmlEgresos);
                    $('#dataIngresos .total').html(totalIngresos.toFixed(2));
                    $('#dataEgresos .total').html(totalEgresos.toFixed(2));
                });

            }
        }

        // #### ACCIONES DEL MODAL modalRegistro
        $('#modalRegistro').on('hide.bs.modal', function (e) {
            // do something...
            limpiarFormulario();
            $('#btnCancelar').click();
        });
        $('#modalRegistro').on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
        });

        function limpiarFormulario() {

            $('#reg_id').val('');

            $('#reg_orig_operacion').val('');
            $('#reg_num_docu').val('');

            $('#reg_proveedor').val('');

            $('#reg_moneda').val('');
            $('#reg_t_cambio').val('');
            $('#reg_importe').val('');

            $('#reg_observacion').val('');

            $('#reg_vale').val('');
            $('#reg_receptor_id').val('');

            $('#docsData').html();
            $('#tablaDocSustento tbody').html();
        }

        function llenarFormulario(data) {


            $('#reg_id').val(data.id);
            $('#reg_tipo').val(data.tipo_movimiento).change();
            $('#reg_orig_operacion').val(data.doc_operacion_id).change();
            $('#reg_num_docu').val(data.doc_pago);

            $('#reg_proveedor').val(data.proveedor_id).change();

            $('#reg_moneda').val(data.moneda_id).change();
            $('#reg_t_cambio').val(data.tipo_cambio);
            $('#reg_importe').val(data.importe);

            $('#reg_observacion').val(data.observaciones);

            ///////////


            let objPagos = JSON.parse(data.data_pago);
            let htmlResp = '';

            $.each(objPagos, function() {
                console.log(objPagos);

                let htmInputHiden = '';
                htmInputHiden += '<input type="hidden" name="sust_doc[]" value="' + this.num_docu + '">';
                htmInputHiden += '<input type="hidden" name="sust_prov[]" value="' + this.id_proveedor + '">';
                htmInputHiden += '<input type="hidden" name="sust_monto[]" value="' + this.monto + '">';

                let txtData = $("#reg_proveedor option[value='" + this.id_proveedor + "']").text()
                htmlResp += '<tr>';
				htmlResp += '<td>' + this.num_docu + '</td>';
				htmlResp += '<td>' + this.monto + '</td>';
                htmlResp += '<td>' + txtData + '</td>';
                htmlResp += '<td>' + htmInputHiden + '<button class="btnEliminarFila btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>';
                htmlResp += '</tr>';
            });

            $('#tablaDocSustento tbody').html(htmlResp);

            ////////


            if (data.vale !== null) {
                let htmDocs = '';

                let urlVale = '{{ route('tesoreria.pdf.vale_salida',['::v']) }}';
                urlVale = urlVale.replace('::v', data.vale.id);
                htmDocs += '<div class="col-sm-2">' +
                    '<a href="' + urlVale + '" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Vale N°' + data.vale.codigo + '" >' +
                    '<img src="{{ asset('images/icons/pdf.png')}}" style="border: none !important;" />' +
                    '</a>' +
                    '</div>';

                $('#docsData').html(htmDocs);

                $('#reg_vale').val(data.vale.id);
            }
        }


        // ############# 	ACCIONES BUTTON SUPERIOR

        $('#btnNuevo').on('click', function () {
            let varEmpresa = $('#sel_empresa').val();
            let varCajaChina = $('#sel_cajachica').val();

            if ((varEmpresa === '0') || (varCajaChina === '0')) {
                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '<em>Por favor seleccione <strong>Caja Chica</strong></em>',
                    confirmButtonText: 'Revisar'
                });
                throw new Error('Datos faltantes en el formulario');
            }

            $('#modalRegistro').modal('show');
            $('#reg_moneda').val(1).change();
        });


        $('#btnEditar').on('click', function () {
            let data = dataFlujoCajaChica.row('.eventClick').data();
            llenarFormulario(data);
            $('#modalRegistro').modal('show');
            let found = [1, 3].includes(1);
            if (!found) {
                Swal.fire({
                    title: 'Info!',
                    html: '<span>No se permite editar esta solicitud porque fue <strong>' + data.estado.estado_doc + '</strong></span>',
                    type: 'info',
                    confirmButtonText: 'OK'
                });
                $('#modalRegistro').find("*").prop("disabled", true);
                $('#modalRegistro').find("#btnCancelar_modalRegistro").prop("disabled", false);
                throw new Error('No se Puede Editar');
            }
        });
        $('#btnHistorial').on('click', function () {
            let data = dataFlujoCajaChica.row('.eventClick').data();
            llenarFormulario(data);
            $('#reg_id').val(data.id);
            $('#modalRegistro').find("*").prop("disabled", true);
            $('#modalRegistro').find("#btnCancelar_modalRegistro").prop("disabled", false);
            $('#modalRegistro').modal('show');

            throw new Error('Visualizar Contenido');
        });


        $('#modalRegistro').on('hide.bs.modal', function (e) {
            // do something...
            limpiarFormulario();

            $('#modalRegistro').find("*").prop("disabled", false);
            $('#btnCancelar').click();
        });


        $('#btnGuardar_modalRegistro').on('click', function () {

            let valDoc = $('#reg_num_docu').val();
            let tipoReg = $('#reg_tipo').val();
            let regVale = $('#reg_vale').val();



            if ((tipoReg === 'E') && (valDoc === '') && (regVale === '')) {


                Swal.fire({
                    title: '¿Generar Vale?',
                    text: "para sustentacion de caja chica",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        $('[name=reg_vale]').val(1);

                        Swal.fire({
                            title: 'Receptor de Efectivo',
                            type: 'question',
                            input: 'select',
                            inputOptions: {
								@foreach ($usuarios as $usuario)
                                '{{$usuario->id_usuario}}': '{{ $usuario->trabajador->postulante->persona->nombre_completo }} ({{ $usuario->trabajador->postulante->persona->nro_documento }})',
								@endforeach
                            },
                            inputPlaceholder: 'Seleccionar Responsable',
                            showCloseButton: true,
                            showCancelButton: false,
                            focusConfirm: false,
                            confirmButtonText: 'OK',
                            onOpen: function () {
                                $(".swal2-select").select2({
                                    theme: "bootstrap",
                                    placeholder: "Seleccionar Responsable",
                                    allowClear: true,
                                    dropdownAutoWidth: true
                                });
                            },
                            inputValidator: (value) => {
                                $('[name=reg_receptor_id]').val(value);
                            },
                            preConfirm: (value) => {
                                guardar($('#frmCajaChicaMovimientos'), postAjaxRegistrar)
                                return true;
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        });
                    } else {
                        $('[name=reg_vale]').val();
                    }


                });

            } else {

                guardar($('#frmCajaChicaMovimientos'), postAjaxRegistrar)
            }


        });


        // #########3 	ACCIONES DE CAMBIO EN COMBO
        $('#reg_moneda').on('change', function () {
            let valor = $(this).val();

            if (valor !== '1') {
                let urlMostrar = '{{ route('ajax.t_cambio', '::v') }}';
                urlMostrar = urlMostrar.replace('::v', valor);
                $.get(urlMostrar).done(function (data) {
                    $('#reg_t_cambio').val(data[0].venta);
                });
            } else {
                $('#reg_t_cambio').val(0);
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

        //############ BTN SALDOS
        $('#btnSaldos').on('click', function () {

            var cajachica = $('#sel_cajachica').val();
            var fecha = $('#sel_fecha').val();

            if (cajachica !== '') {
                let urlMostrar = '{{ route('ajax.cajachica.saldos', '::v') }}';
                urlMostrar = urlMostrar.replace('::v', cajachica);

                let dataEnviar = {
                    cajachica_id: cajachica,
                    f_ini: fecha
                };

                $.get(urlMostrar, dataEnviar).done(function (data) {
                    $('#showInicial').val(data.inicial);
                    $('#showIngresos').val(data.ingresos);
                    $('#showEgresos').val(data.egresos);
                    $('#showSaldo').val(data.saldo);
                });
                $('#modalSaldos').modal('show');
            }

        });
        //############ BTN Historial
        $('#btnHistorialCaja').on('click', function () {

            $('#modalHistorial').modal('show');

        });


        //ADDEDD

        function postAjaxRegistrar(data) {
            //dataPlanillaPagos.ajax.reload();
            //dataFlujoCajaChica.ajax.reload();
            cargarDatatable();
            $('.has-success').removeClass('has-success');
            $('#modalRegistro').modal('hide');

            if (data.vale_id !== undefined) {
                let urlVale = '{{ route('tesoreria.pdf.vale_salida',['::v']) }}';
                urlVale = urlVale.replace('::v', data.vale_id);

                Swal.fire({
                    title: 'Vale Generado!',
                    html: '<p>imprimir y almacenar</p>' +
                        '<a class="btn btn-success" target="_blank" href="' + urlVale + '" >Abrir Vale</a>',
                    imageUrl: '{{ asset('images/printTickets.png') }}',
                    imageWidth: 100,
                    imageHeight: 100,
                    //backdrop: 'rgba(255,0,13,0.4)',


                })
            }


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

	</script>

@stop



