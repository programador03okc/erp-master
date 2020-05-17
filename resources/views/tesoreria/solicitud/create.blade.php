@php($tiene_menu = true)
@extends('tesoreria.main')

@section('cuerpo')
    <div class="page-main" type="tesoreria_solicitudes">
        <legend class="mylegend">
            <h2>Solicitud <span id="num_sol"></span></h2>
            <ol class="breadcrumb">
                <li>Tesoreria</li>
                <li>Solicitud</li>
            </ol>
        </legend>
        <div class="row">
            <div class="col-md-12">

                <form id="frmAccion" type="register" form="formulario">
                    <input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
                    <input type="hidden" name="reg_code" id="reg_code">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="reg_tipo">Tipo</label>
                            <select name="reg_tipo" id="reg_tipo" class="form-control activation"
                                    onChange="llenarOtroCombo('reg_subtipo', '{{ route('ajax.sol_subtipos',['::v']) }}', {v: this.value}, [{value: 'id', text: 'descripcion'}] );"
                                    required>
                                <option value="">Elija una opción</option>
                                @foreach ($solicitud_tipos as $tipo)
                                    <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>
                                @endforeach
                            </select>
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
                            <label for="reg_detalle">Detalle Adicional</label>
                            <input type="text" name="reg_detalle" id="reg_detalle" class="form-control activation"
                                   placeholder="Detalle su solicitud" required>
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
                                <span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>
                                <input name="reg_importe" id="reg_importe" type="number"
                                       class="form-control text-right activation" min="0" value="0" step="0.01" required>
                            </div>
                        </div>
                    </div>

                </form>


            </div>
        </div>


    </div>



@stop

@section('scripts')
    <script src="//cdn.datatables.net/plug-ins/1.10.19/api/fnReloadAjax.js"></script>
    <script type="text/javascript">

        //#### Rellenar Datos para EDIT ########

        @if(isset($solicitud))
        $(document).ready(function(){
            //changeStateInput($('#frmAccion').attr('id'), false);

            //OKC-S190501-001
            $('#num_sol').text('{{ $solicitud->codigo }}');
            $('#reg_id').val('{{ $solicitud->id }}');
            $('#reg_code').val('{{ $solicitud->codigo }}');

            $('#reg_tipo').val('{{ $solicitud->subtipo->solicitudes_tipos_id }}').change();
            $('#reg_subtipo').val('{{ $solicitud->subtipo->id }}').change();

            $('#reg_detalle').val('{{ $solicitud->detalle }}').change();

            $('#reg_empresa').val('{{ $solicitud->area->grupo->sede->id_empresa }}').change();
            $('#reg_sede').val('{{ $solicitud->area->grupo->id_sede }}').change();
            $('#reg_area').val('{{ $solicitud->area_id }}').change();

            $('#reg_moneda').val('{{ $solicitud->moneda_id }}').change();
            $('#reg_importe').val('{{ $solicitud->importe }}').change();



            changeStateButton('historial');
        });
        $('#btnNuevo').on('click', function(){
            $('#num_sol').text('');
            $('#reg_id').val('');
            $('#reg_code').val('');
        });
        $('#btnCancelar').on('click', function(){
            location.reload();
        });

        @endif



        // ####################### Partes de Formulario Manipulacion #############################

        $('#reg_num_docu').on('change', function () {
            if ($(this).val() !== ''){
                $('#reg_proveedor').attr('disabled', false);
            }
            else{
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


        function cambioMoneda(){
            console.log('Cambio');
            var monedaSel = $('#reg_moneda').find(':selected').data('simbolo');
            console.log(monedaSel);
            $('#reg_t_cambio').siblings('span').html('<b>' + monedaSel + '</b>');
            $('#reg_importe').siblings('span').html('<b>' + monedaSel + '</b>');
        }

        cambioMoneda();

        // #########################################        ACCIONES DE BOTONES SUPERIORES      ########################

        function guardarSolicitud(data, action){

            let error = false;
            let htmlE = '';
            $("[required]").each(function() {
                if($(this).val() === ''){
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    htmlE += '<li class="list-group-item">' + $(this).parent().find('label').text() + '</li>\n';
                    error = true;
                }
                else{
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                }
            });

            if (error){

                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '<ul class="list-group">\n' + htmlE + '</ul>',
                    confirmButtonText: 'Revisar'
                });
/*
                Swal.fire({
                    title: 'Error!',
                    text: 'Datos faltantes<br>aaaa',
                    type: 'error',
                    confirmButtonText: 'Cool'
                }); */
                throw new Error('Datos faltantes en el formulario');
            }
            else{
                if (action == 'register'){
                    baseUrl = '{{ route('tesoreria.solicitud.store') }}';
                    baseMethod = 'POST';
                } else if (action == 'edition'){
                    baseUrl = '{{ route('tesoreria.solicitud.update', (isset($solicitud->id)?$solicitud->id:'')) }}';
                    baseMethod = 'PATCH';
                }
                console.log(baseUrl);
                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response.error){
                            console.log(response.msg)
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                type: 'error',
                                confirmButtonText: 'Revisar'
                            });
                            throw new Error('Error en el procesamiento de datos');
                            //changeStateButton('guardar');
                        }
                        else{
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
                                            .textContent = Math.ceil((Swal.getTimerLeft()/1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
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
                                    let newUrl = '{{ route('tesoreria.solicitud.show', '::v') }}';
                                    newUrl = newUrl.replace('::v', response.data.id)
                                    window.location = newUrl;
                                }
                            })
                            //changeStateButton('guardar');
                        }
                        /*
                        if (response > 0){
                            alert('Mantenimiento registrado con éxito');
                            changeStateButton('guardar');
                            var id_equipo = $('[name=id_equipo]').val();
                            console.log('id_equipo'+id_equipo);
                            listar_mtto_pendientes(id_equipo);
                            $('[name=id_mtto]').val(response);
                        }
                        */
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){                    if(jqXHR.status == 403){
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

        function anularSolicitud(id){
            if(id >0){
                let baseMethod = 'DELETE';
                let baseUrl = '{{ route('tesoreria.solicitud.destroy', (isset($solicitud->id)?$solicitud->id:'')) }}';

                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    //data: data,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response.error){
                            console.log(response.msg)
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                type: 'error',
                                confirmButtonText: 'Revisar'
                            });
                            throw new Error('Error en el procesamiento de datos');
                            //changeStateButton('guardar');
                        }
                        else{
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
                                            .textContent = Math.ceil((Swal.getTimerLeft()/1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
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
                                    let newUrl = '{{ route('tesoreria.solicitud.index') }}';
                                    window.location = newUrl;
                                }
                            })
                            //changeStateButton('guardar');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){                    if(jqXHR.status == 403){
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
