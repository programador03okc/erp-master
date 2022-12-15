@extends('layout.main')
@include('layout.menu_migracion')

@section('cabecera') Actualizar produtos @endsection

@section('estilos')
    <style>

    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Actualizar</a></li>
    <li>SoftLink</li>
    <li class="active">Productos por serie</li>
</ol>
@endsection

@section('content')
<div class="box box-danger">
    <div class="box-header with-border">
        {{-- <h3 class="box-title">Actualizar productos</h3> --}}
        <button class="btn btn-link descargar-modelo" type="button" title="Descargar modelo de excel"><i class="fa fa-download"></i> </button>
    </div>
    <form method="POST" action="{{ route('migracion.softlink.actualizar') }}" enctype="multipart/form-data" data-form="actualizar-productos">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="archivo">Seleccione su archivo</label>
                        <input id="archivo" class="form-control" type="file" name="archivo" accept=".xml, .xlsx" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
        </div>
    </form>
</div>
<div class="box">
    <div class="box-header">
      <h3 class="box-title">Data Table With Full Features</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="table-productos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Rendering engine</th>
                    <th>Browser</th>
                    <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Trident</td>
                    <td>Internet
                        Explorer 4.0
                    </td>
                    <td>Win 95+</td>
                    <td> 4</td>
                    <td>X</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
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
        $(document).ready(function () {
            $('#table-productos').DataTable()
        });
        $(document).on('click','.descargar-modelo',function () {
            window.open('descargar-modelo');
        });
        $(document).on('submit','[data-form="actualizar-productos"]',function (e) {
            e.preventDefault();
            var data = new FormData($(this)[0]);
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: data ,
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    // $('.submitBtn').attr("disabled","disabled");
                    // $('#fupForm').css("opacity",".5");
                },
                success: function(response){
                    console.log(response);
                }
            });
        });
    </script>
@endsection
