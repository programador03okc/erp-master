@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera') Cierre / Apertura de Periodo @endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
<style>
    .color-abrir{
        background-color: lightpink !important;
        font-weight: bold;
    }
    .color-cerrar{
        background-color: #7fffd4 !important;
        font-weight: bold;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="periodo">
            
            <div class="row" style="padding-top:10px;">
                <div class="col-md-12">
                    <button id="btn_nuevo" class="btn btn-success" onClick="openCierreApertura();">Nuevo cierre / apertura</button>
                
                    {{-- <form id="formFiltrosIncidencias" method="POST" target="_blank"
                    action="{{route('cas.garantias.fichas.incidenciasExcel')}}">
                        @csrf()
                    </form> --}}
                    <table class="mytable table table-condensed table-bordered table-okc-view"
                        id="listaPeriodos" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Año</th>
                                <th>Mes</th>
                                <th>Empresa</th>
                                <th>Sede</th>
                                <th>Almacén</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
                    
        </div>
    </div>
</div>

@include('tesoreria.cierre_apertura.nuevo')
@include('tesoreria.cierre_apertura.cierreApertura')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/util.js')}}"></script>
    <script>
        let csrf_token = '{{ csrf_token() }}';
        let vardataTables = funcDatatables();
        $(document).ready(function() {
            listar();

            $("#cierre-apertura").on("submit", function() {
                var data = $(this).serializeArray();
                console.log(data);
                // data.push({_token: csrf_token});

                $.ajax({
                    type: "POST",
                    url : $(this).attr('action'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.tipo == 'success') {
                            $('#modal-cierre-apertura').modal('hide');
                            $('#listaPeriodos').DataTable().ajax.reload(null, false);
                        }
                        Util.notify(response.tipo, response.mensaje);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });

        function listar() {
            let botones = [];
            botones.push({
                text: ' Nuevo',
                action: function () {
                    // exportarIncidencias();
                }, className: 'btn-success btnNuevo'
            });
            var $tabla = $('#listaPeriodos').DataTable({
                // dom: vardataTables[1],
                dom: 'Bfrtip',
                buttons: {
                text: ' Nuevo',
                action: function () {
                    // exportarIncidencias();
                }, className: 'btn-success btnNuevo'
            },
                language: vardataTables[0],
                pageLength: 20,
                serverSide: true,
                initComplete: function (settings, json) {
                    const $filter = $('#listaPeriodos_filter');
                    const $input = $filter.find('input');
                    $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
                    $input.off();
                    $input.on('keyup', (e) => {
                        if (e.key == 'Enter') {
                            $('#btnBuscar').trigger('click');
                        }
                    });
                    $('#btnBuscar').on('click', (e) => {
                        $tabla.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#listaPeriodos_filter input').prop('disabled', false);
                    $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
                    $('#listaPeriodos_filter input').trigger('focus');
                },
                order: [[0, 'desc']],
                ajax: {
                    url: "{{ route('tesoreria.cierre-apertura.listar') }}",
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrf_token}
                },
                columns: [
                    // {data: 'id_periodo', className: 'text-center'},
                    {data: 'anio', className: 'text-center'},
                    {data: 'mes', className: 'text-center'},
                    {data: 'empresa', name:'adm_contri.razon_social', className: 'text-lefth'},
                    {data: 'sede', name:'sis_sede.codigo', className: 'text-lefth'},
                    {data: 'almacen', name:'alm_almacen.descripcion', className: 'text-lefth'},
                    {data: 'estado_nombre', name:'periodo_estado.nombre', className: 'text-center'},
                    {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
                ],
            });
            $tabla.on('search.dt', function() {
                $('#listaPeriodos_filter input').attr('disabled', true);
                $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
            });
            $tabla.on('init.dt', function(e, settings, processing) {
                $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
            });
            $tabla.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
                } else {
                    $(e.currentTarget).LoadingOverlay("hide", true);
                }
            });
        }

        function openCierreApertura() {
            $('#title').text('Nuevo Cierre / Apertura');
            // $('#formulario').attr('action', "{{ route('tesoreria.tipo-cambio.guardar') }}");
            $('#modal-nuevo-cierre-apertura').modal('show');
        }

        $("#listaPeriodos tbody").on("click", "button.abrir", function () {
            $('#titleCierreApertura').text('Abrir Periodo');
            $('[name=ca_anio]').removeClass('color-cerrar');
            $('[name=ca_anio]').addClass('color-abrir');
            $('[name=ca_mes]').removeClass('color-cerrar');
            $('[name=ca_mes]').addClass('color-abrir');
            $('[name=ca_almacen]').removeClass('color-cerrar');
            $('[name=ca_almacen]').addClass('color-abrir');
            $('[name=ca_estado]').removeClass('color-cerrar');
            $('[name=ca_estado]').addClass('color-abrir');

            var data = $("#listaPeriodos").DataTable().row($(this).parents("tr")).data();
            console.log(data);

            $('#modal-cierre-apertura').modal('show');
            $('#cierre-apertura').attr('action', "{{ route('tesoreria.cierre-apertura.guardar') }}");

            $('[name=ca_anio]').val(data.anio);
            $('[name=ca_mes]').val(data.mes);
            $('[name=ca_id_estado]').val(1);
            $('[name=ca_estado]').val('Abrir');
            $('[name=ca_almacen]').val(data.almacen);
            $('[name=ca_id_periodo]').val(data.id_periodo);
            $('[name=ca_comentario]').val('');
        });

        $("#listaPeriodos tbody").on("click", "button.cerrar", function () {
            $('#titleCierreApertura').text('Cerrar Periodo');
            
            $('[name=ca_anio]').removeClass('color-abrir');
            $('[name=ca_anio]').addClass('color-cerrar');
            $('[name=ca_mes]').removeClass('color-abrir');
            $('[name=ca_mes]').addClass('color-cerrar');
            $('[name=ca_almacen]').removeClass('color-abrir');
            $('[name=ca_almacen]').addClass('color-cerrar');
            $('[name=ca_estado]').removeClass('color-abrir');
            $('[name=ca_estado]').addClass('color-cerrar');

            var data = $("#listaPeriodos").DataTable().row($(this).parents("tr")).data();
            console.log(data);
            
            $('#modal-cierre-apertura').modal('show');
            $('#cierre-apertura').attr('action', "{{ route('tesoreria.cierre-apertura.guardar') }}");

            $('[name=ca_anio]').val(data.anio);
            $('[name=ca_mes]').val(data.mes);
            $('[name=ca_id_estado]').val(2);
            $('[name=ca_estado]').val('Cerrar');
            $('[name=ca_almacen]').val(data.almacen);
            $('[name=ca_id_periodo]').val(data.id_periodo);
            $('[name=ca_comentario]').val('');
        });

        $("[name=id_empresa]").on('change', function () {
            var id_empresa = $(this).val();
            console.log(id_empresa);

            if (id_empresa==0){
                $('[name=id_sede]').val(0);
                $('[name=id_almacen]').val(0);
            } else {

                $('[name=id_sede]').html('');
                $('[name=id_almacen]').html('');
                $.ajax({
                    type: 'GET',
                    // headers: { 'X-CSRF-TOKEN': token },
                    url: 'mostrarSedesPorEmpresa/' + id_empresa,
                    dataType: 'JSON',
                    success: function (response) {
                        console.log(response);
    
                        if (response.length > 0) {
                            $('[name=id_sede]').html('');
                            html = '<option value="0" >Todos las sedes</option>';
                            response.forEach(element => {
                                html += `<option value="${element.id_sede}" >${element.descripcion}</option>`;
                            });
                            $('[name=id_sede]').html(html);
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });

               
        $("[name=id_sede]").on('change', function () {
            var id_sede = $(this).val();
            console.log(id_sede);
            
            if (id_sede==0){
                $('[name=id_almacen]').val(0);
            } else {

                $('[name=id_almacen]').html('');
                $.ajax({
                    type: 'GET',
                    url: 'mostrarAlmacenesPorSede/' + id_sede,
                    dataType: 'JSON',
                    success: function (response) {
                        console.log(response);

                        if (response.length > 0) {
                            $('[name=id_almacen]').html('');
                            html = '<option value="0">Todos los almacenes</option>';
                            response.forEach(element => {
                                html += `<option value="${element.id_almacen}" >${element.descripcion}</option>`;
                            });
                            $('[name=id_almacen]').html(html);
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });

        $("[name=id_almacen]").on('change', function () {
            var id_almacen = $(this).val();
            console.log(id_almacen);
            
            if (id_almacen==0){
                $('[name=id_empresa]').val(0);
                $('[name=id_sede]').val(0);
            }

        });
    </script>
@endsection