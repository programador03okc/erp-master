@extends('tesoreria.layout.tesoreria')

@php($ruta = 'tesoreria.planillapagos')

@php($pagina = ['titulo' => 'Tesoreria > Planilla de Pagos', 'tiene_menu' => true, 'slug' => 'planillapagos'])

@php($modals['modalPlanilla'] = ['id' => 'modalPlanilla', 'titulo' => 'Planilla de Pagos', 'class' => '', 'style' => 'width: 90%'])

@php($modals['modalExcel'] = ['id' => 'modalExcel', 'titulo' => 'Archivo Adjunto', 'class' => '', 'style' => ''])

@section($modals['modalPlanilla']['id'])
	<div class="row">
		<form id="frmPlanilla" type="register" form="formulario">
		<div class="col-md-4 mx-auto">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#t_origen"data-toggle="tab" aria-expanded="true">Origen de pago</a> </li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="t_origen">
						<div class="panel panel-info">
							<div class="panel-heading"> Cuentas </div>
							<div class="panel-body">

								<input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
								<div class="row">
									<div class="col-sm-12">
										<label for="reg_cta_origen">Numero</label>
										<select name="reg_cta_origen" id="reg_cta_origen" class="form-control activation"
												required>
											@foreach ($dataCuentas as $dCta)
												<option data-razon_social="{{ $dCta->contribuyente->razon_social??'NOVSA' }}" data-tipo_cta="{{ $dCta->tipo_cuenta->descripcion??'NOVA' }}" value="{{$dCta->id_cuenta_contribuyente}}">{{ $dCta->nro_cuenta }}</option>
											@endforeach
										</select>
									</div>

									<div class="col-sm-12">
										<label for="reg_moneda">Moneda</label>
										<select name="reg_moneda" id="reg_moneda" class="form-control activation"
												onchange="cambioMoneda();" required>
											@foreach ($monedas as $moneda)
												<option data-simbolo="{{ $moneda->simbolo }}"
														value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
											@endforeach
										</select>
									</div>

									<div class="col-sm-12">
										<label for="reg_prioridad">Prioridad</label>
										<select name="reg_prioridad" id="reg_prioridad" class="form-control activation" required>
											@foreach ($prioridades as $prioridad)
												<option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group col-sm-12">
									<label for="reg_observaciones">Observaciones</label>
									<textarea name="reg_observaciones" id="reg_observaciones" style="height:80px" placeholder="Observaciones..." class="form-control"></textarea>
								</div>

							</div>
						</div>
					</div>

				</div>
			</div>







		</div>

		<div class="col-md-8 mx-auto">

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					@foreach($dataSolicitudes as $idx => $dataSolicitud)
						<li class="{{ ($idx==0)?'active':'' }}"><a href="#tab_{{ $idx }}" data-toggle="tab" aria-expanded="true">{{ $dataSolicitud->codigo }}</a></li>
					@endforeach
				</ul>
				<div class="tab-content">
					@foreach($dataSolicitudes as $idx => $dataSolicitud)

						<input type="hidden" name="reg_solicitud_id[]" id="reg_solicitud_id" value="{{ ($dataSolicitud->id??'') }}">
						<div class="tab-pane {{ ($idx==0)?'active':'' }}" id="tab_{{ $idx }}">
							<div class="panel panel-info">
								<div class="panel-heading"> Detalles de Solicitud </div>
								<div class="panel-body">

									<table id="" class="table table-condensed table-hover">
										<tbody>
										<tr>
											<th>Importe</th>
											<td>{{ $dataSolicitud->moneda->simbolo??'' }} {{ $dataSolicitud->importe??'' }}</td>
											<td>&nbsp;</td>
											<th>Prioridad:</th>
											<td>{{ $dataSolicitud->prioridad->descripcion??'' }}</td>
										</tr>
										<tr>
											<th>Tipo</th>
											<td>{{ $dataSolicitud->subtipo->tipo->descripcion??'' }} / <em>{{ $dataSolicitud->subtipo->descripcion??'' }}</em></td>
											<td>&nbsp;</td>
											<th>Creado:</th>
											<td>{{ $dataSolicitud->fecha_humanos??'' }}</td>
										</tr>
										<tr>
											<th>Detalle</th>
											<td colspan="4">{{ $dataSolicitud->detalle??'' }}</td>
										</tr>
										<tr>
											<th>Trabajador:</th>
											<td>{{ $dataSolicitud->trabajador->postulante->persona->nombre_completo??'' }}</td>
											<td>&nbsp;</td>
											<th>Archivos:</th>
											<td>
												@if($dataSolicitud->adjuntos)
													<a href="{{ route('tesoreria.solicitud.descargar.adjunto') . '?archivo=' . $dataSolicitud->adjuntos }}" target="_blank">
														<span class="fiv-sqo fiv-size-lg fiv-icon-{{ explode('.', $dataSolicitud->adjuntos)[1] }}"></span>
													</a>
													<a id="btnMostrarExcel" href="#" onclick="return false;" data-href="{{ route('tesoreria.solicitud.ver.excel') . '?archivo=' . $dataSolicitud->adjuntos }}" target="_blank">
														<span class="fiv-sqo fiv-size-lg fiv-icon-{{ explode('.', $dataSolicitud->adjuntos)[1] }}"></span>
													</a>
												@endif
											</td>
										</tr>
										<tr>
											<td colspan="5">
												<div class="col-sm-4">
													<label for="reg_tipo_cta_destino">Tipo</label>
													<select name="reg_tipo_cta_destino[]" id="reg_tipo_cta_destino" class="form-control activation"
															required>
														@foreach ($tipoCuentas as $tipo)
															<option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
														@endforeach
													</select>
												</div>
												<div class="col-sm-8">
													<label for="reg_cta_destino">Numero</label>
													<input type="text" name="reg_cta_destino[]" id="reg_cta_destino" class="form-control activation"
														   placeholder="..." required>
												</div>
												<div class="col-sm-12">
													<label for="reg_persona_proveedor">Proveedor o Persona</label>
													<select name="reg_persona_proveedor[]" id="reg_persona_proveedor" class="form-control activation"
													>
														<option value="">Elija una opción</option>
														@foreach ($persona_proveedor as $key => $ppG)
															<optgroup label="{{ strtoupper($key) }}">
																@foreach ($ppG as $pp)
																	<option value="{{ $pp['id'] }}">{{ $pp['txt'] }}</option>
																@endforeach
															</optgroup>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										</tbody>
									</table>

								</div>
							</div>
						</div>
					@endforeach

				</div>
				<!-- /.tab-content -->
			</div>


			{{--<table id="" class="table table-hover">
				<head>
					<tr>
						<th>ID</th>
						<th>User</th>
						<th>Date</th>
						<th>Status</th>
						<th>Reason</th>
					</tr>
				</head>
				<tbody>
				<tr>
					<td>183</td>
					<td>John Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-success">Approved</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>219</td>
					<td>Alexander Pierce</td>
					<td>11-7-2014</td>
					<td><span class="label label-warning">Pending</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>657</td>
					<td>Bob Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-primary">Approved</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>175</td>
					<td>Mike Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-danger">Denied</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				</tbody>
			</table>--}}


		</div>

		</form>
	</div>
@stop

@section($modals['modalExcel']['id'])
	<div class="row">
		<form id="frmExcel" type="register" form="formulario">

			<div class="col-sm-12">
				<table class="table table-striped" id="listaExcel">
					<thead>
					<tr>
						<th>#</th>
						<th>DNI</th>
						<th>Nombres y Apellidos</th>
						<th>Monto</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

		</form>
	</div>
@stop


@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Planilla de Pagos</h2>
		<ol class="breadcrumb">
			<li>Planilla de Pagos</li>
			<li>{{ $tipo_planilla }}</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">



		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="listaPlanillaPagos">
				<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Fecha</th>
					<th>Cta Origen</th>
					<th>Pagado a:</th>
					<th>Importe</th>
					<th>Estado</th>

				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
@stop


@section('scripts_seccion')
	<script type="text/javascript">

		$('#btnMostrarExcel').on('click', function () {
			const 	 url = $(this).data('href');
			$.getJSON(url, function (data) {
				console.log(data);
				let htmlTBody = '';
				let i = 1;
				$.each(data, function () {
					htmlTBody += '<tr>' +
						'<td>' + i + '</td>' +
						'<td>' + this.dni + '</td>' +
						'<td>' + this.apellidos_y_nombres + '</td>' +
						'<td>' + this.monto + '</td>' +
						'<td><input type="checkbox" name="reg_excel_"' + this.dni + '></td>' +
						'</tr>';
					i++;
                });
				$('#listaExcel tbody').html(htmlTBody);
				$('#modalExcel').modal('show');

            })
        });

		function formatearCuenta(val){
		    let razon_social = $(val.element).data('razon_social');
		    let tipo_cuenta = $(val.element).data('tipo_cta');
		    let nro_cuenta = val.text;

		    let ret = '<div class="row">' +
				'<div class="col-sm-12 text-black">' + razon_social + '</div>' +
				'<div class="col-sm-12 text-center">' + nro_cuenta + '</div>' +
				'<div class="col-sm-12 text-right text-muted">' + tipo_cuenta + '</div>' +
				'</div>';

		    return  ret;
		}

		$('#reg_cta_origen').select2({
            placeholder: "Seleccionar ...",
            allowClear: true,
            templateResult: formatearCuenta,
            escapeMarkup: function(m) { return m; },
            dropdownParent: $("#modalPlanilla")
        });
		$('#reg_persona_proveedor').select2({
            placeholder: "Seleccionar ...",
            allowClear: true,
            dropdownAutoWidth: true,
            dropdownParent: $("#modalPlanilla")
        });


        $('#btnGuardar_modalPlanilla').on('click', function () {
            guardar($('#frmPlanilla'), postAjaxRegistrar);


        });

        function postAjaxRegistrar(data) {
            //dataPlanillaPagos.ajax.reload();
            //dataFlujoCajaChica.ajax.reload();
            //cargarDatatable();
            $('.has-success').removeClass('has-success');
            $('#modalPlanilla').modal('hide');
            $('#btnGuardar').click();
        }


        let vardataTables = funcDatatables();

        //$('#tabTemp').dataTable();
        $(document).ready(function() {
            $('#tabTemp').DataTable();
			@if($dataSolicitudes)
            //$('#modalPlanilla').modal('show');
            $('#btnNuevo').click();
			@endif
        } );


        function cambiarEstadosIdx(estado_f, detalle){

            let idxPlanillasPagos = [];
            $.each(dataPlanillaPagos.data(), function (i, item) {
                if (item.isChecked) {
                    idxPlanillasPagos.push({
                        id: item.id,
                        estado: estado_f,
                        observacion: detalle
                    });
                }
            });

            let objData = {
                'idxs': idxPlanillasPagos
            };

            baseUrl = '{{ route('tesoreria.planillapagos.update.state') }}';
            baseMethod = 'POST';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                data: objData,
                dataType: 'JSON',
                success: function (response) {

                    if (response.error) {
                        console.log(response.msg)
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        }).then((result) => {
                            dataPlanillaPagos.ajax.reload();
                        });
                        //throw new Error('Error en el procesamiento de datos');
                        //changeStateButton('guardar');
                    } else {
                        let timerInterval;
                        Swal.fire({
                            type: 'success',
                            title: 'Completado!',
                            footer: 'Cerrando en <strong></strong> segundos',
                            html: 'Estados Actualizados corectamente.',
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
                                dataPlanillaPagos.ajax.reload();
                            }
                        })
                    }

                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if(jqXHR.status === 403){
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

        let newBtns = $.merge([
			@if( ( Auth::user()->hasAnyRole( array_merge($roles['programador'] ,$roles['asis_ger_general'] ,$roles['gerente_general']) )) )
            {
				text: '<i class="fas fa-check"></i> Procesar',
				className: 'btn btn-primary',
				action: function ( e, dt, node, config ) {
					cambiarEstadosIdx(2, 'Pago Procesado');
				}
			},
			@endif
			@if( ( Auth::user()->hasAnyRole( array_merge($roles['programador'] ,$roles['pagos'] ) )) )
            {
                text: '<i class="fas fa-check"></i> Abonado',
                className: 'btn btn-success',
                action: function ( e, dt, node, config ) {
                    cambiarEstadosIdx(3, 'Pago Abonado');
                }
            },
            {
                text: '<i class="fas fa-check"></i> Abonado por Rendir',
                className: 'btn btn-warning',
                action: function ( e, dt, node, config ) {
                    cambiarEstadosIdx(4, ' Pago con cuenta por rendir');
                }
            }
			@endif
		], vardataTables[2]);

        let filtro = [

            @if(in_array('ordinario', explode('.', request()->route()->getName())))
            {campo: 'planillapago_tipo_id', condicion: '', valor: 1},
			@else
            {campo: 'planillapago_tipo_id', condicion: '!=', valor: 1},
			@endif
        ];

        let dataPlanillaPagos = $('#listaPlanillaPagos').DataTable({
            'dom': vardataTables[1],
            'buttons': newBtns, //vardataTables[2],
            'language': vardataTables[0],
            select: true,
            order: [],
            // 'processing': true,
            ajax: {
                url: '{{ route('ajax.planillapagos') }}',
                data: {
                    filtro: JSON.stringify(filtro)
                }
            },
            'columns': [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": '',
                    "render": function () {
                        return '<i class="fa fa-plus" aria-hidden="true"></i>';
                    },
                    width:"15px"
                },
                {
                    "data": "isChecked",
                    // adding the class name just to make finding the checkbox cells eaiser
                    "class": "cbcell select-checkbox",
                    "orderable": false,
                    // Put the checkbox in the title bar
                    "title": "<input type='checkbox' />",
                    // puts the checkbox in each row
                    "render": function (dataItem) {
                        if (dataItem)
                            return "<input checked type='checkbox'/>";
                        else
                            return "<input type='checkbox'/>";
                    }
                },
                {'data': 'fecha_humanos'},
                {'data': 'cta_origen.nro_cuenta'},
                {'data': 'solicitudes', render: function (data) {
                    let htmlSalida = '<ul>';
                    $.each(data, function () {
                        if(this.persona_id != null){
                            //console.log(this.persona);
                            htmlSalida += '<li>' + this.persona.nombre_completo + ' (' + this.cuenta_destino + ') ' + '</li>' ;
                        }
                        if(this.proveedor_id != null){
                            htmlSalida += '<li>' + this.proveedor.contribuyente.razon_social + '</li>' ;
                            //return this.proveedor.contribuyente.razon_social;
                        }
                    });
                    //console.log(htmlSalida);
                    htmlSalida += '</ul>';


						return htmlSalida;

                    }},
                {
                    'data': null, className: 'text-right', render: function (data) {
                        return data.moneda.simbolo + ' ' + data.importe;
                    }
                },
                {'data': 'estado', render: function (data) {
                        return '<span class="label label-' + data.bootstrap_color + '">' + data.descripcion + '</span>';
                    }},
            ],/*
        "rowCallback": function( row, data, index ) {
            $('td:eq(1)',row).html(index + 1);
        }*/
        });


        // Add event listener for opening and closing details
        $('#listaPlanillaPagos tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var tdi = tr.find("i.fa");
            var row = dataPlanillaPagos.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                tdi.first().removeClass('fa-minus');
                tdi.first().addClass('fa-plus');
            }
            else {
                // Open this row
                row.child(formatDetalleSolicitud(row.data())).show();
                tr.addClass('shown');
                tdi.first().removeClass('fa-plus');
                tdi.first().addClass('fa-minus');
            }
        } );

        dataPlanillaPagos.on("user-select", function (e, dt, type, cell, originalEvent) {
            if ($(cell.node()).hasClass("details-control")) {
                e.preventDefault();
            }
        });

        /* Formatting function for row details - modify as you need */
        function formatDetalleSolicitud ( data ) {
            htmRet = '' +
				'<div class="row">' +
				'<div class="col-sm-12">' +

				'<div class="nav-tabs-custom">' +
				'<ul class="nav nav-tabs">';
            $.each(data.solicitudes, function (idx, sol) {
				htmRet += '<li class="' + (idx===0?'active':'') + '"><a href="#tab_' + this.solicitud.codigo + '" data-toggle="tab" aria-expanded="true">' + this.solicitud.codigo + '</a></li>';
            });
            htmRet += '' +
				'</ul>' +
				'<div class="tab-content">';
            $.each(data.solicitudes, function (idx, sol) {
                htmRet += '<div class="tab-pane ' + (idx===0?'active':'') + '" id="tab_' + this.solicitud.codigo + '">' +
					'<div class="row">';


                htmRet += '<div class="col-sm-6">';

                htmRet += '<table id="" class="table table-hover">' +
                    '<tbody>' +
                    '<tr>' +
                    '<th>Codigo<th>' +
                    '<td>' + this.solicitud.codigo + '<td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Detalle<th>' +
                    '<td>' + this.solicitud.detalle + '<td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Fecha de Creacion<th>' +
                    '<td>' + this.solicitud.fecha_humanos + '<td>' +
                    '</tr>' +
                    '</tbody>' +
                    '</table>';

                htmRet +='</div>';


                htmRet += '<div class="col-sm-6">';

                htmDetalleSol = '<ul>';
                $.each(this.solicitud.detalles, function () {
                    htmDetalleSol += '<li>' + this.descripcion + ' (' + this.estimado + ')</li>';

                });
                htmDetalleSol += '</ul>';


                htmRet += '<table id="" class="table table-hover">' +
                    '<tbody><tr><td>' + htmDetalleSol + '</td></tr></tbody>' +
                    '</table>';
                htmRet +='</div>';




                htmRet += '</div>' +
					'</div>';


            });

            htmRet +='</div>';

            return htmRet;

        }





        // This is the event handler for the check all checkbox
        $("th input[type=checkbox]").on("click", function () {
            var isChecked = this.checked;
            var ld = $('#listaPlanillaPagos').DataTable().rows().data();
            $.each(ld, function (i, item) {
                item.isChecked = isChecked;
            });
            $(".cbcell input").prop("checked", isChecked);
            //dtapi.data().sum();

			//console.dir(dataPlanillaPagos.data()[0].fecha)
        });

        // event handler for individual rows
        $("#listaPlanillaPagos").on("click", "td input[type=checkbox]", function () {
            var isChecked = this.checked;

            // set the data item associated with the row to match the checkbox
            var dtRow = dataPlanillaPagos.rows($(this).closest("tr"));
            dtRow.data()[0].isChecked = isChecked;

            // determine if the over all checkbox should be checked or unchecked
            if (!isChecked) {
                // if one is unchecked, then checkall cannot be checked
                $("th input[type=checkbox]").prop("checked", false);
            }
            else {
                $("th input[type=checkbox]").prop("checked", true);
                $.each(dataPlanillaPagos.data(), function (i, item) {
                    if (!item.isChecked) {
                        $("th input[type=checkbox]").prop("checked", false);
                        return false;
                    }
                });
            }

            //dtapi.data().sum();
        });
































        let dataSolicitudDetalle = $('#listaSolicitudDetalles').DataTable({
            //dom: vardataTables[1],
            //buttons: vardataTables[2],
            language: vardataTables[0],
            select: true,
            paging:   false,
            info:     false,
            searching: false,
            columns: [
				{data: 'partida_id'},
				{data: 'descripcion', width: '60%'},
				{data: 'partida', width: '15%'},
				{data: 'estimado', width: '15%'},
				{data: null, width: '10%', sortable:false, render:function () {
						return '<button class="btnEliminarFila btn btn-danger"><i class="fas fa-trash"></i></button>';
                    }},
			]

        });

        dataSolicitudDetalle.on( 'draw', function () {
            //console.log( 'Redraw occurred at: '+new Date().getTime() );
			let suma = dataSolicitudDetalle.column( 3 )
                .data()
                .reduce( function (a, b) {
                    return Number(a) + Number(b);
                }, 0 );
			$('#reg_importe').val(suma);
        } );

        $('#listaPlanillaPagos tbody').on('click', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaPlanillaPagos').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }


            let tolSel = $('.eventClick');
            if (tolSel.length == 0) {
                changeStateButton('inicio');
            } else {
                changeStateButton('historial');
            }

        });

        $('#listaPlanillaPagos tbody').on('dblclick', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaPlanillaPagos').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            $('#btnHistorial').click();


        });

        // ########## ACCIONES BOTONES MODAL #####
        $('#btnNuevo').on('click', function () {

            $('#modalPlanilla').modal('show')
        });
        $('#btnEditar').on('click', function () {
            let data = dataPlanillaPagos.row('.eventClick').data();
            llenarFormulario(data);
            $('#modalPlanilla').modal('show');
            let found = [1,3].includes(data.estado_id);
            if(!found){
                Swal.fire({
                    title: 'Info!',
                    html: '<span>No se permite editar esta solicitud porque fue <strong>'+data.estado.estado_doc + '</strong></span>',
                    type: 'info',
                    confirmButtonText: 'OK'
                });
                $('#modalPlanilla').find("*").prop("disabled", true);
                $('#modalPlanilla').find("#btnCancelar_modalPlanilla").prop("disabled", false);
                throw new Error('No se Puede Editar');
            }
        });
        $('#btnHistorial').on('click', function () {
            let data = dataPlanillaPagos.row('.eventClick').data();
            llenarFormulario(data);
            $('#reg_id').val(data.id);
            $('#modalPlanilla').find("*").prop("disabled", true);
            $('#modalPlanilla').find("#btnCancelar_modalPlanilla").prop("disabled", false);

            $('#modalPlanilla').modal('show');

            throw new Error('Visualizar Contenido');
        });




        $('#modalPlanilla').on('hide.bs.modal', function (e) {
            // do something...
            limpiarFormulario();

            dataSolicitudDetalle.clear().draw();

            $('#modalPlanilla').find("*").prop("disabled", false);
            $('#btnCancelar').click();
        });


        $('#ctrlPartidas').on('click', function () {
            //$('#modalPartidas').modal('show');
            Swal.fire({
                title: '<strong>Partidas Presupuestales</strong>',
                width: '80%',
                //type: 'error',
                html: $('#listaPartidas').html(),
                confirmButtonText: 'Revisar'
            });
        });


        $('#btnAddDet').on('click', function () {
            let descripcion = $('#reg_det_descripcion').val();
            let partida = $('#reg_det_partida').val();
            let partida_id = $('#reg_det_partida_id').val();
            let estimado = $('#reg_det_estimado').val();

            if((descripcion == '') || (partida == '') || (estimado =='')){
                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '',
                    confirmButtonText: 'Revisar'
                });
			}
            else{
                dataSolicitudDetalle.row.add(
                    {partida_id: partida_id, descripcion: descripcion, partida: partida, estimado: estimado}
                ).draw( false );

                $('#reg_det_descripcion').val();
                $('#reg_det_partida').val();
                $('#reg_det_partida_id').val();
                $('#reg_det_estimado').val();
			}


        });

        $('#listaSolicitudDetalles').on("click", ".btnEliminarFila", function(){
            dataSolicitudDetalle.row($(this).parents('tr')).remove().draw(false);
        });

		@if($adm)
        $('#btnAdminAprobado').on('click', function () {

        });

        function cambiarEstado(estado_id) {
            let codeReg = $('#reg_code').val();
            let comentario = $('#adm_comentarios').val();
            let idReg = $('#reg_id').val();
            baseUrl = '{{ route('tesoreria.solicitud.update', '::v') }}';
            baseUrl = baseUrl.replace('::v', idReg);
            baseMethod = 'PATCH';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                data: {
                    codigo: codeReg,
					estado: estado_id,
					observacion: comentario
				},
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
                            html: 'Actualizacion Exitosa.',
                            timer: 2000,
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
                                dataPlanillaPagos.ajax.reload();

                                $('#modalPlanilla').modal('hide');
                            }
                        })
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
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
		@endif

        // ### ACCIONES BOTON MODAL 2




		// ###### Acciones SELECT

		$('#reg_area').on('change', function () {

            baseUrl = '{{ route('ajax.presupuesto', '::v') }}';
            baseUrl = baseUrl.replace('::v', $(this).val() );
            baseMethod = 'GET';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                //data: data,
                dataType: 'JSON',
                success: function (response) {

                    $('#listaPartidas').html(response);

                    /*let htmlR = ''
                    $.each(response, function( index, value ) {
                        $.each(value, function( index2, value2 ) {
                            htmlR += '<div class="panel panel-default">' +
								'<div class="panel-heading" data-toggle="collapse" data-target="#collapse' + value2.codigo + '" aria-expanded="true" aria-controls="collapse' + value2.codigo + '">' +
								'<h3 class="panel-title">' + value2.descripcion + '</h3>' +
								'</div>' +
								'<div class="panel-body" id="collapse' + value2.codigo + '" class="collapse show" aria-labelledby="headingOne" data-parent="#listaPartidas">' +
								'<table class="table table-striped table-bordered" id="listaPartidas">\n' +
                                '\t\t\t\t<thead>\n' +
                                '\t\t\t\t<tr>\n' +
                                '\t\t\t\t\t<th>Codigo</th>\n' +
                                '\t\t\t\t\t<th>Descripcion</th>\n' +
                                '\t\t\t\t\t<th>Importe</th>\n' +
                                '\t\t\t\t</tr>\n' +
                                '\t\t\t\t</thead>\n' +
								'\t\t\t\t<tbody>\n' +
								'<tr><th>' + value2.codigo + '</th><td>' + value2.descripcion + '</td><td>' + value2.total + '</td></tr>';
                            htmlR += '\t\t\t\t</tbody>\n' +
                                '\t\t\t</table>' +
								'</div>' +
								'</div>';

                            console.log(value2.hijos_recursivo);

                            let varAR = flatten(value2.hijos_recursivo);

                            console.dir(varAR);


                        });
                    });
                    console.log(response.length)
					$('#listaPartidas').html(htmlR);*/
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
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

        function apertura(id_presup){
            if ($("#pres-"+id_presup+" ").attr('class') == 'oculto'){
                $("#pres-"+id_presup+" ").removeClass('oculto');
                $("#pres-"+id_presup+" ").addClass('visible');
            } else {
                $("#pres-"+id_presup+" ").removeClass('visible');
                $("#pres-"+id_presup+" ").addClass('oculto');
            }
        }

        function selectPartida(id_partida){
            var codigo = $("#par-"+id_partida+" ").find("td[name=codigo]")[0].innerHTML;
            var descripcion = $("#par-"+id_partida+" ").find("td[name=descripcion]")[0].innerHTML;


            //$('#modalPartidas').modal('hide');
            Swal.close()
            $('[name=reg_det_partida_id]').val(id_partida);
            $('[name=reg_det_partida]').val(codigo);
            //$('[name=des_partida]').val(descripcion);
        }

        // ######  ACCION MOSTRAR ANULADOS

		function mostrarData(val) {
			let urlMostrar = '{{ route('ajax.solicitudes') }}';
            urlMostrar = urlMostrar.replace('::v', val);
            console.log(urlMostrar);

            let filtro = [
				{campo: 'estado_id', condicion: '', valor: val}
			];


            $.get( urlMostrar, {filtro: JSON.stringify(filtro) } ).done(function( data ) {
                dataPlanillaPagos.clear().draw();
                dataPlanillaPagos.rows.add(data.data); // Add new data
                dataPlanillaPagos.columns.adjust().draw(); // Redraw the DataTable
			});



            //console.log(dataPlanillaPagos);

            //dataPlanillaPagos.fnClearTable();

            //dataPlanillaPagos.ajax(urlMostrar);

        }

        // ############# ACCIONES DE BOTONES ###########

        function guardarSolicitud(data, action) {
            let error = false;
            let htmlE = '';
            let jsonDetalle = [];

            $("#frmAccion [required]").each(function () {
                if ($(this).val() === '') {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    htmlE += '<li class="list-group-item">' + $(this).parent().find('label').text() + '</li>\n';
                    error = true;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                }
            });

            if ( ! dataSolicitudDetalle.data().any() ) {
                error = true;
            }
            else{
                dataSolicitudDetalle.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    var datRow = this.data();
                    //console.log(datRow);
                    jsonDetalle.push(datRow);
                    // ... do something with data(), or this.node(), etc
                } );
			}

            jsonDetalle = JSON.stringify(jsonDetalle);

            //if (dataSolicitudDetalle.items)

            if (error) {

                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '<ul class="list-group">\n' + htmlE + '</ul>',
                    confirmButtonText: 'Revisar'
                });
                throw new Error('Datos faltantes en el formulario');
            } else {

                data += '&detalle_solicitud=' + jsonDetalle;
                console.log(data);
                if (action == 'register') {
                    baseUrl = '{{ route('tesoreria.solicitud.store') }}';
                    baseMethod = 'POST';
                } else if (action == 'edition') {
                    let idReg = $('#reg_id').val();
                    baseUrl = '{{ route('tesoreria.solicitud.update', '::v') }}';
                    baseUrl = baseUrl.replace('::v', idReg);
                    baseMethod = 'PATCH';
                }
                console.log(baseUrl);
                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    data: data,
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
                                    dataPlanillaPagos.ajax.reload();

                                    $('#modalPlanilla').modal('hide');
                                }
                            })
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
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

        function anularSolicitud(id) {
            id = dataPlanillaPagos.row('.eventClick').data().id;
            if (id > 0) {
                let idReg = id;
                baseUrl = '{{ route('tesoreria.solicitud.destroy', '::v') }}';
                baseUrl = baseUrl.replace('::v', idReg);
                let baseMethod = 'DELETE';

                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    //data: data,
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
                                html: 'Documento Anulado.',
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

                                    dataPlanillaPagos.ajax.reload();

                                    $('#modalPlanilla').modal('hide');
                                }
                            })
                            //changeStateButton('guardar');
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
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

        // ######## Funciones HELP #####

        function llenarFormulario(data) {
            $('#modalPlanillaLabel').text(data.codigo);
            $('#reg_id').val(data.id);
            $('#reg_code').val(data.codigo);

            $('#reg_tipo').val(data.subtipo.solicitudes_tipos_id).change();
            $('#reg_subtipo').val(data.subtipo.id).change();

            $('#reg_detalle').val(data.detalle).change();

            $('#reg_prioridad').val(data.prioridad_id).change();

            $('#reg_empresa').val(data.area.grupo.sede.id_empresa).change();
            $('#reg_sede').val(data.area.grupo.id_sede).change();
            $('#reg_area').val(data.area_id).change();

            $('#reg_moneda').val(data.moneda_id).change();
            $('#reg_importe').val(data.importe).change();


            $.each(data.detalles, function (idx, val) {
                dataSolicitudDetalle.row.add(
                    {partida_id: val.partida_id, descripcion: val.descripcion, partida: val.partida.codigo, estimado: val.estimado}
                ).draw( false );
            });
        }

        function limpiarFormulario() {
            $('#modalPlanillaLabel').text('Solicitud: ');
            $('#reg_id').val('');
            $('#reg_code').val('');

            $('#reg_tipo').val('');
            $('#reg_subtipo').val('');

            $('#reg_detalle').val('');

            $('#reg_prioridad').val('');
            $('#reg_empresa').val('');
            $('#reg_sede').val('');
            $('#reg_area').val('');

            $('#reg_moneda').val('');
            $('#reg_importe').val('');

        }

	</script>

@stop



