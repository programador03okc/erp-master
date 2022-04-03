@extends('layout.main')
@include('layout.menu_migracion')

@section('cabecera') Migraciones SoftLink @endsection

@section('estilos')
    <link rel="stylesheet" href="{{ asset('css/stepper.css')}}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Migraciones</a></li>
    <li>SoftLink</li>
    <li class="active">Almacenes</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="row" style="margin-bottom: 15px;">
                    <form id="formulario" action="{{ route('migracion.importar') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="POST">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <h6>Tipo de archivo</h6>
                                <select name="tipo" class="form-control input-sm">
                                    <option value="" disabled selected>Elija una opción..</option>
                                    <option value="1">Almacenes</option>
                                    <option value="2">Categorías</option>
                                    <option value="3">Sub Categorías</option>
                                    <option value="4">Unidades de Medida</option>
                                    <option value="5">Productos</option>
                                    <option value="6">Series</option>
                                    <option value="7">Saldos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <h6>Seleccione el archivo (Excel)</h6>
                                <input type="file" name="archivo" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-block btn-primary btn-flat" id="procesar">Procesar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3" id="divMensaje"></div>
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
    <script>
        var routeLink = "{{route('migracion.index')}}";
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
                        setTimeout(function(){
                            window.location.href = routeLink
                        }, 2000);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });
    </script>
@endsection