@extends('layout.main')
@include('layout.menu_migracion')

@section('cabecera') Migraciones de productos por serie @endsection

@section('estilos')
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stepper.css')}}">
    <style>
        .mt-2 {
            margin-top: 10px;
        }
        .mt-3 {
            margin-top: 15px;
        }
        .mt-4 {
            margin-top: 20px;
        }
        .bootstrap-select button {
            border-radius: 0;
            border: 1px solid #ccc;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Migraciones</a></li>
    <li>SoftLink</li>
    <li>Almacenes</li>
    <li class="active">Productos por serie</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="row" style="margin-bottom: 15px;">
                    <form id="formulario" action="{{ route('migracion.softlink.importar') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="POST">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <h6>Almacen</h6>
                                <select name="almacen" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" data-size="10" title="Seleccione un almacén">
                                    @foreach ($almacenes as $item)
                                        <option value="{{ $item->id_almacen }}">{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <h6>Seleccione el archivo (Excel)</h6>
                                <input type="file" name="archivo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-sm btn-block btn-primary btn-flat" id="procesar">Procesar</button>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-sm btn-block btn-danger btn-flat" onclick="exportar();">Exportar reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2" id="divMensaje"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script>
        var routeLink = "{{ route('migracion.softlink.index') }}";
        $(function(){
            $("#formulario").on("submit", function() {
                var data = new FormData(this);
                $.ajax({
                    type: "POST",
                    url : $(this).attr('action'),
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (response) {
                        Util.mensaje('#divMensaje', response.alert, response.message);
                        if (response.response == 'ok') {
                            setTimeout(function(){ window.location.href =  routeLink }, 3000);
                        }
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });

        function exportar() {
            var routeLink = "{{ route('migracion.softlink.exportar') }}";
            window.open(routeLink, '_blank');
            return false;
        }
    </script>
@endsection