@extends('tesoreria.layout.tesoreria')

@php($responsableSeccion = [7,22])

@php($pagina = ['titulo' => 'Tesoreria > Solicitudes', 'tiene_menu' => false, 'slug' => 'solicitudes'])

@php($modals['modalSolicitud'] = ['id' => 'modalSolicitud', 'titulo' => 'Solicitud', 'class' => '', 'style' => 'width: 60%', 'botones' => ['guardar' => false, 'cancelar' => true]])
@php($modals['modalPartidas'] = ['id' => 'modalPartidas', 'titulo' => 'Partidas Presupuestales', 'class' => 'modal-dialog-centered '])

@section($modals['modalSolicitud']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto">
			<form id="frmAccion" type="register" form="formulario">
				<input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
				<input type="hidden" name="reg_code" id="reg_code">
				<div class="form-group row">
					<div class="col-sm-6">
					</div>
					<div class="col-sm-6">
						<label for="reg_subtipo">Sub Tipo</label>
						<select name="reg_subtipo" id="reg_subtipo" class="form-control activation" required>
							<option value="">Elija una opción</option>
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
				<div class="row">
					<div class="col-sm-12">
						<h4>Detalle de Solicitud</h4>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table class="table table-striped table-bordered" id="listaSolicitudDetalles" width="100%">
							<thead>
							<tr>
								<th>Descripcion</th>
								<th>Partida</th>
								<th>Cost. Est.</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
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
		<h2>Solicitudes</h2>
		<ol class="breadcrumb">
			<li>Solicitud</li>
			<li>Estado</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">
			<button id="btnProcesarSolicitudes" type="submit" class="btn btn-log btn-info pull-right" title="Crear Planilla de Pago">
				<i class="fas fa-file fa-sm"></i> Generar Planilla con Seleccionados
			</button>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped table-hover text-sm">
				<thead>
				<tr>
					<th></th>
					<th>Codigo</th>
					<th>Tipo</th>
					<th>Detalle</th>
					<th>Importe</th>
					<th>Fecha</th>
					<th>Observacion</th>
					<th>Estado</th>
					<th>Accion</th>
				</tr>
				</thead>
				<tbody>
				@foreach($solicitudes as $solicitud)
					<tr data-solicitud='{{$solicitud}}'>
						<td><input type="checkbox" data-solicitud_id="{{ $solicitud->id }}"  name="sol_selec[]" class="chkSol"></td>
						<td>{{ $solicitud->codigo }}</td>
						<td>
							<strong>{{ $solicitud->subtipo->tipo->descripcion }}</strong><br>
							<em>{{ $solicitud->subtipo->descripcion }}</em>
						</td>
						<td>{{ $solicitud->detalle }}</td>
						<td class="text-right">{{ $solicitud->moneda->simbolo }} {{ $solicitud->importe }}</td>
						<td>{{ $solicitud->fecha_humanos }}</td>
						<td>{{ $solicitud->observacion }}</td>
						<td>
							<span class="label label-{{ $solicitud->estado->bootstrap_color }}">
								{{ $solicitud->estado->estado_doc }}
							</span>
						</td>
						<td>
							{{--<div class="row" style="margin-bottom: 5px;">
								<div class="col-sm-12">
									<div class="btn-group" id="group" role="group" aria-label="...">
										<button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onclick="editarListaReq(67);"><i class="fas fa-edit fa-sm"></i>
										</button>
										<button id="btnVer" type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle"><i class="fas fa-eye fa-sm"></i></button>
										<button type="button" class="btn btn-sm btn-log btn-info" title="Crear solicitud de cotización"><i class="fas fa-file fa-sm"></i></button>
									</div>
								</div>
							</div>--}}

							<div class="row">
								<div class="col-sm-12">
									<div class="btn-group" role="group">
										<button id="btnVer" type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle"><i class="fas fa-eye fa-sm"></i></button>
										@if( ( /*(Auth::user()->id_usuario == $solicitud->area->gerente->usuario->id_usuario) || */(Auth::user()->hasAnyRole($roles['pagos'])) || (Auth::user()->hasAnyRole($roles['programador'])) ) && (request()->route()->parameter('id_tipo') == 2) )
											<button type="button" data-solicitud_id="{{ $solicitud->id }}" class="btn btn-sm btn-log btn-info btnGenerarPlanilla" title="Crear Planilla de Pago">
												<i class="fas fa-file fa-sm"></i>
											</button>
										@endif

										@if( ( /*(Auth::user()->id_usuario == $solicitud->area->gerente->usuario->id_usuario) || */(Auth::user()->hasAnyRole($roles['programador'])) ) && (request()->route()->parameter('id_tipo') == 1) )
											<button id="btnAprobar" type="button" class="btn btn-sm btn-log bg-green" title="Aprobar"><i class="fas fa-check fa-sm"></i></button>
											<button id="btnObservar" type="button" class="btn btn-sm btn-log bg-yellow" title="Observar"><i class="fas fa-exclamation-triangle fa-sm"></i>{{--<span class="badge badge-light">0</span>--}}</button>
											<button id="btnDenegar" type="button" class="btn btn-sm btn-log bg-red" title="Denegar"><i class="fas fa-ban fa-sm"></i></button>
										@endif
									</div>
								</div>
							</div>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<form id="frmGeneralPlanilla" method="post" action="{{ route('tesoreria.planillapagos.ordinario') }}">
				@csrf
				<input type="hidden" value="" name="idSolicitud">
				<input type="hidden" value="" name="idPrioridad">
			</form>
		</div>
	</div>

	<div class="div">
		<div class="col-sm-12">
		</div>
	</div>

@stop


@section('scripts_seccion')
	<script type="text/javascript">

		function dataSolicitudTab(selector){
            let tr = $(selector).closest('tr');
            let dataSolicitud = tr.data('solicitud');
            return dataSolicitud;
		}

		@if($adm)

        function cambiarEstado(estado_id, id_solicitud, comentario='') {
            baseUrl = '{{ route('tesoreria.solicitud.update.state') }}';
            baseMethod = 'POST';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                data: {
                    idxs: [{
                        id: id_solicitud,
                        estado: estado_id,
                        observacion: comentario
                    }],
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
                                location.reload();
                            }
                        })
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: 'No Autorizado!',
                    text: jqXHR.responseJSON.message,
                    imageUrl: '{{ asset('images/guard_man.png') }}',
                    imageWidth: 100,
                    imageHeight: 100,
                    backdrop: 'rgba(255,0,13,0.4)'


                });
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
		@endif



		$('#btnVer').on('click', function () {
			let dataSolicitud = dataSolicitudTab(this);
			llenarFormulario(dataSolicitud);
            $('#modalSolicitud').modal('show');
        });

        $('#btnAprobar').on('click', function () {
            let dataSolicitud = dataSolicitudTab(this);
            Swal.fire({
                title: 'Estas Seguro?',
                text: "Se hara efectiva la Aprobacion!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Aprobar'
            }).then((result) => {
                if (result.value) {
                    cambiarEstado(2, dataSolicitud.id)
                }
            });
        });

        $('#btnObservar').on('click', function () {
            let dataSolicitud = dataSolicitudTab(this);
            Swal.fire({
                title: 'Observar',
                text: "Ingrese su Observacion!",
                type: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Razones de Observacion...',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continuar'
            }).then((result) => {
                if (result.value) {
                    //console.log(result);
                    cambiarEstado(3, dataSolicitud.id, result.value)
                }
            });
        });


        $('#btnDenegar').on('click', function () {
            let dataSolicitud = dataSolicitudTab(this);

            Swal.fire({
                title: 'Estas Seguro?',
                text: "Se hara efectiva la Denegacion!",
                type: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Razones de Observacion...',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continuar'
            }).then((result) => {
                if (result.value) {
                    //console.log(result);
                    cambiarEstado(4, dataSolicitud.id, result.value)
                }
            });
        });

        $('#btnProcesarSolicitudes').on('click', function () {
            $('#frmGeneralPlanilla').submit();
        });

        $('table .btnGenerarPlanilla').on('click', function () {

            $(this).closest('tbody').find('.chkSol').prop('checked', false).trigger('change');
            $(this).closest('tr').find('.chkSol').prop('checked', true).trigger('change');

            $('#frmGeneralPlanilla').submit();

        });

        $('#frmGeneralPlanilla').on('submit',function (e) {
            if ($(this).find('[name=idSolicitud]').val() == ''){
                Swal.fire({
                    title: 'Error!',
                    text: 'No se seleccionaron solicitudes',
                    type: 'error',
                    confirmButtonText: 'Revisar'
                });
                e.preventDefault();
			}
            /* else{
                let dataBotones = {};
				@foreach ($prioridades as $prioridad)
					dataBotones['{{$prioridad->id_prioridad}}'] = '{{$prioridad->descripcion}}';
				@endforeach

                Swal.fire({
                    title: 'Asignar prioridad:',
					text: 'Es requerido asignar una prioridad a la planilla',
                    input: 'select',
                    inputOptions: dataBotones,
                    inputPlaceholder: 'Seleccionar',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        return new Promise((resolve) => {
                            if (value === 'oranges') {
                                resolve()
                            } else {
                                resolve('You need to select oranges :)')
                            }
                        })
                    }
                })
			} */
            // e.preventDefault();

        });

        $('.chkSol').change(function() {
            let dataFrmGenerarPlanilla = $('[name=idSolicitud]').val();

            let solicitud_id = $(this).data('solicitud_id');

            if (dataFrmGenerarPlanilla != ''){
                dataFrmGenerarPlanilla = dataFrmGenerarPlanilla.split(',');
			}
            else{
                dataFrmGenerarPlanilla = [];
			}

            if(this.checked) {
                dataFrmGenerarPlanilla.push(solicitud_id);
            }
            else{
                var index = dataFrmGenerarPlanilla.indexOf(solicitud_id.toString());
                if (index > -1) {
                    dataFrmGenerarPlanilla.splice(index, 1);
                }
			}
            $('[name=idSolicitud]').val(dataFrmGenerarPlanilla.join());
        });



        // ######## Funciones HELP #####

        function llenarFormulario(data) {
            $('#modalSolicitudLabel').text(data.codigo);
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

            htmDetalle = '';
            $.each(data.detalles, function (idx, val) {
                htmDetalle += '<tr>' +
					'<td>' + val.descripcion + '</td>' +
					'<td>' + val.partida.codigo + '</td>' +
					'<td>' + val.estimado + '</td>' +
					'</tr>';

            });
            $('#listaSolicitudDetalles tbody').html(htmDetalle);
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
        }

	</script>

@stop



