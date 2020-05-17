@extends('tesoreria.layout.tesoreria')

@php($responsableSeccion = [7,22])

@php($pagina = ['titulo' => 'Tesoreria > Solicitudes', 'tiene_menu' => true, 'slug' => 'solicitudes'])

@php($modals['modalSolicitud'] = ['id' => 'modalSolicitud', 'titulo' => 'Solicitud', 'class' => '', 'style' => 'width: 60%'])
@php($modals['modalPartidas'] = ['id' => 'modalPartidas', 'titulo' => 'Partidas Presupuestales', 'class' => 'modal-dialog-centered '])

@section($modals['modalSolicitud']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto">
			<form id="frmAccion" type="register" form="formulario">
				<input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
				<input type="hidden" name="reg_code" id="reg_code">
				<div class="form-group row">
					<div class="col-sm-6">
						<label for="reg_subtipo">Tipo</label>
						<select name="reg_subtipo" id="reg_subtipo" class="form-control activation" required>
							<option value="">Elija una opción</option>
							@foreach ($solicitud_subtipos as $subtipo)
								<option value="{{$subtipo->id}}">{{$subtipo->descripcion}}</option>
							@endforeach
						</select>
					</div>


					<div class="col-sm-6 div_cond_subtipo" data-id="6" style="display: none;">
						<label for="reg_usuario_final">Cargar Excel</label>
						<input type="file" id="archivo" name="archivo" class="filestyle" data-buttonText="Seleccionar archivo" data-size="sm">
					</div>
					<div class="col-sm-6 div_cond_subtipo opcion_defa" data-id="a">
						<label for="reg_usuario_final">Asignado a:</label>
						<select name="reg_usuario_final" id="reg_usuario_final" class="form-control activation">
							<option value="">Elija una opción</option>
							@foreach ($trabajadores as $trabajador)
								<option value="{{ $trabajador->id_trabajador }}">{{ $trabajador->postulante->persona->nombre_completo }}</option>
							@endforeach
						</select>
					</div>

				</div>
				<div class="form-group row">
					<div class="col-sm-12">
						<label for="reg_detalle">Detalle General</label>
						<input type="text" name="reg_detalle" id="reg_detalle" class="form-control activation"
							   placeholder="..." required>
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
				<div class="form-group row">
					<div class="col-sm-4">
						<label for="reg_moneda">Moneda</label>
						<select name="reg_moneda" id="reg_moneda" class="form-control activation"
								onchange="cambioMoneda();" required>
							@foreach ($monedas as $moneda)
								<option data-simbolo="{{ $moneda->simbolo }}"
										value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_importe">Importe</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda"></span>
							<input name="reg_importe" id="reg_importe" type="number"
								   class="form-control text-right activation" min="0" value="0" step="0.01" required readonly>
						</div>
					</div>
					<div class="col-sm-4">
						<label for="reg_prioridad">Prioridad</label>
						<select name="reg_prioridad" id="reg_prioridad" class="form-control activation" required>
							@foreach ($prioridades as $prioridad)
								<option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</form>

		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h4>Detalle de Solicitud</h4>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-5">
			<input name="reg_det_descripcion" id="reg_det_descripcion" type="text" class="form-control  input-sm" placeholder="Descripcion detallada">
		</div>
		<div class="col-sm-3">

			<div class="input-group" id="ctrlPartidas" style="cursor: pointer;">
				<input type="hidden" id="reg_det_partida_id" name="reg_det_partida_id" value="">
				<input name="reg_det_partida" id="reg_det_partida" type="text" class="form-control" placeholder="Partida Pres." readonly="">
				<span class="input-group-addon" title="Buscar Partidas">
							<i class="fas fa-search"></i>
						</span>
			</div>

		</div>
		<div class="col-sm-3">
			<div class="input-group">
				<span class="input-group-addon esMoneda"></span>
				<input name="reg_det_estimado" id="reg_det_estimado" type="number"
					   class="form-control text-right activation" min="0" step="0.01" placeholder="Cost. Est.">
			</div>
		</div>
		<div class="col-sm-1">
			<button type="button" class="btn btn-success" id="btnAddDet">
				<i class="fas fa-plus"></i>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-striped table-bordered" id="listaSolicitudDetalles" width="100%">
				<thead>
				<tr>
					<th></th>
					<th>Descripcion</th>
					<th>Partida</th>
					<th>Cost. Est.</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
@stop

@section($modals['modalPartidas']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto" id="listaPartidas">



		</div>
	</div>
@stop



@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Estado de solicitudes</h2>
		<ol class="breadcrumb">
			<li>Solicitud</li>
			<li>Estado</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped table-bordered" id="listaSolicitudes">
				<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Codigo</th>
					<th>Tipo</th>
					<th>Detalle</th>
					<th>Importe</th>
					<th>Fecha</th>
					<th>Empresa / Sede / Area</th>
					<th>Observacion</th>
					<th>Estado</th>

				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
@stop

@section('styles_modulo')
    <style type="text/css">
		.dataTables_wrapper .dt-buttons .text-success{
			color: green;
		}
		.dataTables_wrapper .dt-buttons .text-success:hover{
			background-color: green;
			color: #fff3a1;
		}
		.dataTables_wrapper .dt-buttons .text-danger{
			color: red;
		}
		.dataTables_wrapper .dt-buttons .text-danger:hover{
			background-color: red;
			color: #fff3a1;
		}
		.dataTables_wrapper .dt-buttons .text-warning{
		}
		.dataTables_wrapper .dt-buttons .text-warning:hover{
			background-color: #f0ad4e;
		}
		.dataTables_wrapper .dt-buttons .text-primary{
			color: blue;
		}
		.dataTables_wrapper .dt-buttons .text-primary:hover{
			background-color: blue;
			color: #fff3a1;
		}
		/*
		.dataTables_wrapper .dt-buttons button{
			color: #fff;
		}
		*/
	</style>

@stop


@section('scripts_seccion')
	<script type="text/javascript">


		$('#reg_subtipo').on('change', function () {
		    //console.log([this.value, this.text]);
		    //const valor = $(this).val();
			habilitarSubTipo(this.value);


        });


		function habilitarSubTipo(id){
		    const ctrl_subtipo = $('.div_cond_subtipo');
		    let encontrado = false;
		    $.each(ctrl_subtipo, function () {
		        const valId = $(this).data('id');
		        // console.log(this.data('id'));
				if (valId == id) {
				    encontrado = true;
                    $(this).show();
				}
				else{
                    $(this).hide();
				}
            });

		    if (!encontrado){
                $('.opcion_defa').show();
			}


		}



























		$('#reg_usuario_final').select2({
            placeholder: "Seleccionar ...",
            allowClear: true,
            dropdownAutoWidth: true,
            dropdownParent: $("#modalSolicitud")
        });


        let vardataTables = funcDatatables();

        let newBtns = $.merge([
				@if( ( Auth::user()->hasAnyRole( array_merge($roles['programador'] ,$roles['gerente']) )) )
            {
                text: '<i class="fas fa-check"></i> Aprobar',
                className: 'buttons-html5 text-success',
                action: function ( e, dt, node, config ) {
                    cambiarEstadosIdx(2, 'Solicitud Aprobada');
                }
            },
            {
                text: '<i class="fas fa-times"></i> Denegar',
                className: 'buttons-html5 text-danger',
                action: function ( e, dt, node, config ) {
                    cambiarEstadosIdx(4, 'Solicitud Denegada');
                }
            },
            {
                text: '<i class="fas fa-eye"></i> Observar',
                className: 'buttons-html5 text-warning',
                action: function ( e, dt, node, config ) {
                    Swal.fire({
						title: 'Observacion:',
                        input: 'textarea',
                        inputPlaceholder: 'Observacion...',
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        preConfirm: (observacion) => {
                            cambiarEstadosIdx(3, observacion);
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });

                }
            },/*
            {
                text: '<i class="fas fa-question"></i>',
                className: 'buttons-html5 text-primary',
                action: function ( e, dt, node, config ) {
                    cambiarEstadosIdx(4, ' Pago con cuenta por rendir');
                }
            },*/
				@endif
        ], vardataTables[2]);

        let dataSolicitudes = $('#listaSolicitudes').DataTable({
            'dom': vardataTables[1],
            'buttons': newBtns	,
            'language': vardataTables[0],
            select: true,
            iDisplayLength: 50,
            order: [],
            // 'processing': true,
            'ajax': {
                url: '{{ route('ajax.solicitudes') }}',
				@if($tipo_solicitud !== null)
					data: {
					    filtro: JSON.stringify([
                            {campo: 'estado_id', condicion: '', valor: '{{ $tipo_solicitud }}'}
                        ])
					}
				@endif
			},
            'columns': [
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
                {'data': 'id'},
                {'data': 'codigo'},
                {
                    'data': 'subtipo', 'render': function (subtipo) {
                        htmlRet = '<strong>' + subtipo.tipo.descripcion + '</strong><br>';
                        htmlRet += '<em>' + subtipo.descripcion + '</em>';
                        return htmlRet;
                    }
                },
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
                {'data': 'estado', render: function (data) {
						return '<span class="label label-' + data.bootstrap_color + '">' + data.estado_doc + '</span>';
                    }},
            ],
        "rowCallback": function( row, data, index ) {
            $('td:eq(1)',row).html(index + 1);
        }
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
			console.log(suma);
        } );

        $('#listaSolicitudes tbody').on('click', 'tr', function () {

            let controlCheck = $(this).find('input[type=checkbox]');
            var dtRow = dataSolicitudes.rows($(this));
            // dtRow.data()[0].isChecked = isChecked;
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
                controlCheck.prop("checked", false);
                dtRow.data()[0].isChecked = false;
            } else {
                $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
                controlCheck.prop("checked", true);
                dtRow.data()[0].isChecked = true;
            }


            let tolSel = $('.eventClick');
            if (tolSel.length == 0) {
                changeStateButton('inicio');
            } else {
                changeStateButton('historial');
            }

        });

        $('#listaSolicitudes tbody').on('dblclick', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            $('#btnHistorial').click();


        });

        // This is the event handler for the check all checkbox
        $("th input[type=checkbox]").on("click", function () {
            var isChecked = this.checked;
            var ld = $('#listaSolicitudes').DataTable().rows().data();
            $.each(ld, function (i, item) {
                item.isChecked = isChecked;
            });
            $(".cbcell input").prop("checked", isChecked);
            //dtapi.data().sum();

            //console.dir(dataPlanillaPagos.data()[0].fecha)
        });

        // event handler for individual rows
        $("#listaSolicitudes").on("click", "td input[type=checkbox]", function () {
            var isChecked = this.checked;

            // set the data item associated with the row to match the checkbox
            var dtRow = dataSolicitudes.rows($(this).closest("tr"));
            dtRow.data()[0].isChecked = isChecked;

            // determine if the over all checkbox should be checked or unchecked
            if (!isChecked) {
                // if one is unchecked, then checkall cannot be checked
                $("th input[type=checkbox]").prop("checked", false);
            }
            else {
                $("th input[type=checkbox]").prop("checked", true);
                $.each(dataSolicitudes.data(), function (i, item) {
                    if (!item.isChecked) {
                        $("th input[type=checkbox]").prop("checked", false);
                        return false;
                    }
                });
            }

            //dtapi.data().sum();
        });


        // ########## ACCIONES BOTONES MODAL #####
        $('#btnNuevo').on('click', function () {

			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", true);
			@endif
            $('#modalSolicitud').modal('show')
        });
        $('#btnEditar').on('click', function () {
            let data = dataSolicitudes.row('.eventClick').data();
            llenarFormulario(data);
            console.log(data);
			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", true);
			@endif
            $('#modalSolicitud').modal('show');
            let found = [1,3].includes(data.estado_id);
            if(!found){
                Swal.fire({
                    title: 'Info!',
                    html: '<span>No se permite editar esta solicitud porque fue <strong>'+data.estado.estado_doc + '</strong></span>',
                    type: 'info',
                    confirmButtonText: 'OK'
                });
                $('#modalSolicitud').find("*").prop("disabled", true);
                $('#modalSolicitud').find("#btnCancelar_modalSolicitud").prop("disabled", false);
                throw new Error('No se Puede Editar');
            }
        });
        $('#btnHistorial').on('click', function () {
            let data = dataSolicitudes.row('.eventClick').data();
            llenarFormulario(data);
            //console.log(data);
            $('#reg_id').val(data.id);
            $('#modalSolicitud').find("*").prop("disabled", true);
            $('#modalSolicitud').find("#btnCancelar_modalSolicitud").prop("disabled", false);
			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", false);
			@endif
            $('#modalSolicitud').modal('show');

            throw new Error('Visualizar Contenido');
        });

        $('#btnGuardar_modalSolicitud').on('click', function () {
            //let data = new FormData($('#frmAccion')[0]);

            //data = $('#frmAccion').serialize();

            //console.log(data);

            $('#btnGuardar').click();
        });


        $('#modalSolicitud').on('hide.bs.modal', function (e) {
            // do something...
            limpiarFormulario();

            dataSolicitudDetalle.clear().draw();

            $('#modalSolicitud').find("*").prop("disabled", false);
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
            //console.log($(this).parent());
            dataSolicitudDetalle.row($(this).parents('tr')).remove().draw(false);
        });

		@if($adm)
        $('#btnAdminProcesarPago').on('click', function () {

        });

        function cambiarEstadosIdx(estado_f, detalle){

            let idxSolicitudes = [];
            $.each(dataSolicitudes.data(), function (i, item) {
                console.log(item);
                if (item.isChecked) {
                    idxSolicitudes.push({
                        id: item.id,
                        estado: estado_f,
                        observacion: detalle
                    });
                }
            });

            if (idxSolicitudes.length > 0){
                let objData = {
                    'idxs': idxSolicitudes
                };

                baseUrl = '{{ route('tesoreria.solicitud.update.state') }}';
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
                                dataSolicitudes.ajax.reload();
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
                                    dataSolicitudes.ajax.reload();
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


        }

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
                                dataSolicitudes.ajax.reload();

                                $('#modalSolicitud').modal('hide');
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
		    //console.log($(this).val());
			//console.log(value);

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

            console.log(codigo);

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
                dataSolicitudes.clear().draw();
                dataSolicitudes.rows.add(data.data); // Add new data
                dataSolicitudes.columns.adjust().draw(); // Redraw the DataTable
			});

        }

        // ############# ACCIONES DE BOTONES ###########

        function guardarSolicitud(data, action) {
            let error = false;
            let htmlE = '';
            let jsonDetalle = [];

            data = new FormData($('#frmAccion')[0]);

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
                data.append('detalle_solicitud', jsonDetalle);

                //data += '&detalle_solicitud=' + jsonDetalle;
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
                    contentType: false,
                    processData: false,
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
                                    dataSolicitudes.ajax.reload();

                                    $('#modalSolicitud').modal('hide');
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
            id = dataSolicitudes.row('.eventClick').data().id;
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

                                    dataSolicitudes.ajax.reload();

                                    $('#modalSolicitud').modal('hide');
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
            console.log(data);
            $('#modalSolicitudLabel').text(data.codigo);
            $('#reg_id').val(data.id);
            $('#reg_code').val(data.codigo);

            $('#reg_tipo').val(data.subtipo.solicitudes_tipo_id).change();
            $('#reg_subtipo').val(data.subtipo.id).change();

            $('#reg_detalle').val(data.detalle).change();

            $('#reg_prioridad').val(data.prioridad_id).change();

            $('#reg_empresa').val(data.area.grupo.sede.id_empresa).change();
            $('#reg_sede').val(data.area.grupo.id_sede).change();
            $('#reg_area').val(data.area_id).change();

            $('#reg_moneda').val(data.moneda_id).change();
            $('#reg_importe').val(data.importe).change();

            //$('#reg_usuario_final').select2('val','');
            $('#reg_usuario_final').val(data.trabajador_id);
            $('#reg_usuario_final').select2().trigger('change');


            $.each(data.detalles, function (idx, val) {
                dataSolicitudDetalle.row.add(
                    {partida_id: val.partida_id, descripcion: val.descripcion, partida: val.partida.codigo, estimado: val.estimado}
                ).draw( false );
            });
			@if(Auth::user()->hasAnyRole( $responsableSeccion ) && ($tipo_solicitud !== null))
			if(data.estado_id === ('{{ $tipo_solicitud }}' *1 )){
                $('#btnAdminProcesarPago').parent().show();
                $('[name=idSolicitud]').val(data.id);
			}
			console.log([data.estado_id, '{{ $tipo_solicitud }}']);
			@endif
        }

        function limpiarFormulario() {
            $('#modalSolicitudLabel').text('Solicitud: ');
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

            //$('#reg_usuario_final').select2('val','');

			@if(Auth::user()->hasAnyRole( $responsableSeccion ))
            $('#btnAdminProcesarPago').parent().hide();
            $('[name=idSolicitud]').val('');
			@endif
        }

	</script>

@stop



