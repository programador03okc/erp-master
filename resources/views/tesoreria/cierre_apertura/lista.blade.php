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
        let csrf_token = '{{ csrf_token() }}';
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

    </script>
@endsection