@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera') Cierre / Apertura de Periodo @endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
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
@if (in_array(304,$array_accesos))
<div class="box box-solid">
    <div class="box-body">
        <div class="page-main" type="periodo">

            <div class="row" style="padding-top:10px;">
                <div class="col-md-12">
                    <div style="display: flex;width: 100%;" class="btn-group " data-toggle="buttons">
                        <button id="btn_nuevo" class="btn btn-success" onClick="openCierreApertura();">Nuevo cierre / apertura</button>
                        <button id="btn_autogenerar" class="btn btn-primary" onClick="autogenerarPeriodos();">Autogenerar Periodos</button>
                        {{-- <form id="form-cierre-anual" style="display: flex"> --}}
                            <select class="form-control" name="anio_lista" style="width: 300px;">
                                @foreach ($anios as $a)
                                <option value="{{$a->anio}}">{{$a->anio}}</option>
                                @endforeach
                            </select>
                            <select class="form-control" name="id_empresa_lista" style="width: 350px;">
                                <option value="0" selected>Todas las empresas</option>
                                @foreach ($empresas as $emp)
                                <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                @endforeach
                            </select>
                            @if (in_array(305,$array_accesos))
                                <button id="btn_cierre_anual" class="btn btn-info shadow-none" onClick="guardarCierreAnual();">Cierre Anual Contable</button>
                            @endif
                            @if (in_array(306,$array_accesos))
                            <button id="btn_cierre_anual_operativo" class="btn btn-success shadow-none" onClick="guardarCierreAnualOperativo();">Cierre Anual Operativo</button>
                            @endif
                        {{-- </form> --}}
                    </div>

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
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif


@include('tesoreria.cierre_apertura.nuevo')
@include('tesoreria.cierre_apertura.cierreApertura')
@include('tesoreria.cierre_apertura.historialAcciones')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('js/tesoreria/cierreApertura/listarPeriodos.js')}}"></script>
    <script src="{{ asset('js/tesoreria/cierreApertura/nuevoCierreApertura.js')}}"></script>
    <script>
        // let csrf_token = '{{ csrf_token() }}';
        let vardataTables = funcDatatables();
        $(document).ready(function () {
            listar();
            var anio = $('[name=anio]').val();
            cargarMeses(anio);

            $("#cierre-apertura").on("submit", function () {
                var data = $(this).serializeArray();
                console.log(data);

                $.ajax({
                    type: "POST",
                    url: "guardar",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.tipo == 'success') {
                            $('#modal-cierre-apertura').modal('hide');
                            $('#listaPeriodos').DataTable().ajax.reload(null, false);
                        }
                        Util.notify(response.tipo, response.mensaje);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });

            $("#form-nuevo-cierre-apertura").on("submit", function () {
                // var data = $(this).serialize();
                var data = 'anio='+$('[name=anio]').val()+
                '&mes='+$('[name=mes]').val()+
                '&id_almacen='+JSON.stringify($('[name=id_almacen]').val())+
                '&comentario='+$('[name=comentario]').val()+
                '&id_estado='+$('[name=id_estado]').val();
                console.log(data);

                $.ajax({
                    type: "POST",
                    url: "guardarVarios",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        console.log(response);
                        if (response.tipo == 'success') {
                            $('#modal-nuevo-cierre-apertura').modal('hide');
                            $('#listaPeriodos').DataTable().ajax.reload(null, false);
                        }
                        Util.notify(response.tipo, response.mensaje);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });

        });

        function guardarCierreAnual(){
            var data = 'anio='+$('[name=anio_lista]').val()+
            '&id_empresa='+$('[name=id_empresa_lista]').val();
            console.log(data);
            $('#btn_cierre_anual').prop('disabled', 'true');

            $.ajax({
                type: "POST",
                url: "guardarCierreAnual",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    console.log(response);
                    if (response.tipo == 'success') {
                        $('#listaPeriodos').DataTable().ajax.reload(null, false);
                        $('#btn_cierre_anual').removeAttr("disabled");
                    }
                    Util.notify(response.tipo, response.mensaje);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        }

        function guardarCierreAnualOperativo(){
            var data = 'anio='+$('[name=anio_lista]').val()+
            '&id_empresa='+$('[name=id_empresa_lista]').val();
            console.log(data);
            $('#btn_cierre_anual_operativo').prop('disabled', 'true');

            $.ajax({
                type: "POST",
                url: "guardarCierreAnualOperativo",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    console.log(response);
                    if (response.tipo == 'success') {
                        $('#listaPeriodos').DataTable().ajax.reload(null, false);
                        $('#btn_cierre_anual_operativo').removeAttr("disabled");
                    }
                    Util.notify(response.tipo, response.mensaje);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        }
    </script>
@endsection
