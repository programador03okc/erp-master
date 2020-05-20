@extends('tesoreria.layout.tesoreria')


@php($pagina = ['titulo' => 'Tesoreria > Planilla de Pagos', 'tiene_menu' => false, 'slug' => 'planillapagos'])

@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Configuraciones</h2>
		<ol class="breadcrumb">
			<li>Tesoreria</li>
			<li>Configuraciones</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">


		</div>
	</div>

	<div class="row">
		<div class="col-md-6">

			<fieldset class="row" id="tipos">
				<legend style="width: 90%; text-align: center">
					<span class="pull-left">Tipos</span>
					<button type="button" class="btn btn-success btn-sm pull-right btnAdd" data-obj="tipo">
						<i class="fas fa-plus"></i>
					</button>
				</legend>
				<div class="col-sm-12">
					<table class="table table-hover table-bordered">
						<thead>
						<tr>
							<th>Codigo</th>
							<th>Descripcion</th>
							<th>Estado</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@if($tipos->count() >0)
							@foreach($tipos as $tipo)
								<tr data-tipo='{{ $tipo }}' class="clickable-row">
									<td>{{ $tipo->codigo }}</td>
									<td>{{ $tipo->descripcion }}</td>
									<td>{{ $tipo->estado }}</td>
									<td>

										@include('tesoreria.partials.botones_crud_tabla', ['gruposBtn' => [ 'generales' => ['editar', 'deshabilitar', 'eliminar'] ] ])

{{--
										<div class="btn-group pull-right" role="group">
											<button id="btnVer" type="button" class="btn btn-sm btn-log bg-yellow" title="Deshabilitar"><i class="fas fa-ban fa-sm"></i></button>
											<button id="btnEliminar" type="button" class="btn btn-sm btn-log bg-red" title="Eliminar"><i class="fas fa-trash fa-sm"></i></button>
										</div>--}}
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4"> No Existen Registros </td>
							</tr>
						@endif
						</tbody>
					</table>
				</div>
			</fieldset>


		</div>


		<div class="col-md-6">
{{--
			<div class="row form-group">
				<div class="col-sm-3">
					<input type="text" class="form-control input-sm" name="txtCodigo" placeholder="Codigo" value="">
				</div>
				<div class="col-sm-7">
					<input type="text" class="form-control input-sm" name="txtDescripcion" placeholder="Descripcion" value="">
				</div>
				<div class="col-sm-2">

					<button type="button" class="btn btn-success btn-sm pull-right" style="margin-right: 15px;" id="btnAdd">
						<i class="fas fa-plus"></i>
					</button>
				</div>
			</div>--}}
			<fieldset class="row" id="subTipos" style="display: none;">
				<legend style="width: 90%; text-align: center">
					<span class="pull-left">Subtipos</span>
					<button type="button" class="btn btn-success btn-sm pull-right btnAdd" data-obj="subtipo">
						<i class="fas fa-plus"></i>
					</button>
				</legend>
				<div class="col-sm-12">
					<table class="table table-hover table-bordered">
						<thead>
						<tr>
							<th>Codigo</th>
							<th>Descripcion</th>
							<th>Estado</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</fieldset>

		</div>
	</div>
@stop

@section('styles_modulo')
    <style type="text/css">
		.clickable-row{
			cursor: pointer;
		}
	</style>
@stop


@section('scripts_seccion')
	<script type="text/javascript">

		$('.btnAdd').on('click', function () {
			registrarEditar(this);
        });

		$('fieldset').on('click', '.btnEditarFila', function () {
			let data = $(this).closest('tr').data('tipo');
			let strData = null;
			if (data !== undefined) {
			    strData = data;
			}
			else {
                let subdata = $(this).closest('tr').data('subtipo');
				if (subdata !== undefined){
				    strData = subdata;
				}
			}

			console.log(this, strData);

			if (strData !== undefined) {

                if($(this).closest('tr').hasClass('bg-warning')){
                    //$(this).closest('tr').click();
                    //console.log(strData);


					console.log(strData.subtipos);

					registrarEditar(strData);

					/////////////



                }
			}



			//return false;

        });


        $('table').on('click', '.clickable-row', function(event){
			//alert('YAA');
            if($(this).hasClass('bg-warning')){
                $(this).removeClass('bg-warning');
                $(this).removeClass('text-danger');
                $(this).removeClass('text-bold');

                $('#subTipos').fadeOut(500, function () {
                    $('#subTipos table tbody').html('');
                });

            } else {
                $(this).addClass('bg-warning').siblings().removeClass('bg-warning');
                $(this).addClass('text-danger').siblings().removeClass('text-danger');
                $(this).addClass('text-bold').siblings().removeClass('text-bold');
                //alert('Seleccionado');
                let dataGen = $(this).data('tipo');

                if (dataGen !== undefined){
                    cargarSubTipos(dataGen.subtipos);
                    $('#subTipos').fadeIn(500);

				}


            }
        });

        function cargarSubTipos(data) {
			let btnAccion = ' @include('tesoreria.partials.botones_crud_tabla', ['gruposBtn' => [ 'generales' => ['editar', 'deshabilitar', 'eliminar'] ] ]) ';

            if (data.length > 0){
                let html = '';
                $.each(data, function () {
                    html += "<tr data-subtipo='" + JSON.stringify(this) + "' class='clickable-row'>";
					html += '<td>' + this.codigo + '</td>';
					html += '<td>' + this.descripcion + '</td>';
					html += '<td>' + this.estado + '</td>';
					html += '<td>' + btnAccion + '</td>';
                    html += '</tr>';
                });
				$('#subTipos table tbody').html(html);
			}

        }

        function registrarEditar(obj = null, strData = null){

            let preLegend = null;
            let idTp = null;

            if (strData === null){
                chk_tipo = $(obj).data('obj');
                strData = {
                    id: '',
                    descripcion: '',
                    codigo: '',
                };

                if (chk_tipo == 'subtipo'){
                    idTp = $('#tipos .bg-warning').data('tipo');
					idTp = idTp.id;
				}

                preLegend = 'Nuevo ';

			}
            else{
                if (strData.subtipos != undefined){
                    chk_tipo = 'tipo';
                    idTp = null;
                }
                else{
                    chk_tipo = 'subtipo';

                    idTp = $('#tipos .bg-warning').data('tipo');
					idTp = idTp.id;
                }
                preLegend = 'Actualizar ';
			}

            let strLegend = $(obj).closest('fieldset').find('legend>span').text();




            Swal.fire({
                //title: 'Ruc',
                title: preLegend + strLegend,
                type: 'question',
                html:
                    '<form>' +
                    '<div class="form-group row">' +
                    '<div class="col-sm-12">' +
                    '<input type="hidden" name="hidden_id" value="' + strData.id + '" > ' +
                    '<input type="hidden" name="hidden_idpadre" value="' + idTp + '" > ' +
                    '<input type="hidden" name="hidden_chk_tipo" value="' + chk_tipo + '" > ' +
                    '<label>Descripcion</label>' +
                    '<input name="txt_descripcion" type="text" class="form-control" value="' + strData.descripcion + '">' +
                    '</div>' +
                    '<div class="col-sm-12">' +
                    '<label>Codigo</label>' +
                    '<input name="txt_codigo" type="text" class="form-control" value="' + strData.codigo + '">' +
                    '</div>' +
                    '</div>' +
                    '</form>',
                showCancelButton: true,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                inputValidator: (value) => {
                    if (!value) {
                        return 'Se requiere el ingreso de este dato!'
                    }
                },
                preConfirm: (dat) => {
                    let frmData = $('[name=txt_descripcion]').closest('form').serialize();

                    let url = '{{ route('tesoreria.administracion.solicitudes_tipos.store') }}';

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: frmData,
                        dataType: 'JSON',
                        success: function (response) {
                            if (response.error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.msg,
                                    type: 'error',
                                    confirmButtonText: 'Revisar'
                                });
                            } else {
                                location.reload();
                                //return response;
                            }
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status == 403) {
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
                },
            });

		}

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

	</script>

@stop



