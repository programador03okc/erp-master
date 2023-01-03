@extends('layout.main')
@include('layout.menu_migracion')

@section('cabecera') Actualizar produtos @endsection

@section('estilos')
    <style>
        .d-none{
            display: none;
        }
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
    {{-- <div class="box-header with-border">
        <h3 class="box-title">Actualizar productos</h3>

    </div> --}}
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
            <button class="btn btn-link descargar-modelo" type="button" title="Descargar modelo de excel"><i class="fa fa-download"></i> Modelo de excel</button>
            <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Migrar</button>
        </div>
    </form>
</div>
<div class="box box-success d-none box-productos-migrados">
    <div class="box-header">
      <h3 class="box-title">Productos migrados</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool ver-productos-migrados" data-widget="collapse" data-toggle="tooltip"
                title="Collapse"
                style="display: none;"
                >
          <i class="fa fa-mini"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="table-productos-migrados" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Código Agile</th>
                    <th>Código Softlink</th>
                    <th>Part Number</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody data-table="productos-migrados">

            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<div class="box box-danger d-none box-productos">
    <div class="box-header">
      <h3 class="box-title">Productos no migrados</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool ver-productos" data-widget="collapse" data-toggle="tooltip"
                title="Collapse"
                style="display: none;"
                >
          <i class="fa fa-mini"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="table-productos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Código Agile</th>
                    <th>Código Softlink</th>
                    <th>Part Number</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody data-table="productos-faltantes">

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

        });
        $(document).on('click','.descargar-modelo',function () {
            window.open('descargar-modelo');
        });
        $(document).on('submit','[data-form="actualizar-productos"]',function (e) {
            e.preventDefault();
            var data = new FormData($(this)[0]),
                html='';
            $('.ver-productos').click();
            $('.ver-productos-migrados').click();
            $('.box-productos').addClass('d-none');


            Swal.fire({
                title: 'Actualizacion',
                text: "¿Está seguro de actualizar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'no',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'JSON',
                        beforeSend: (data) => {

                        }
                    }).done(function(response) {
                        return response
                    }).fail( function( jqXHR, textStatus, errorThrown ){
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    });

                },
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.faltantes.length>0) {
                        html='';
                        $.each(result.value.faltantes, function (index, element) {
                                html+='<tr>';
                                    html+='<td>'+(element[0]?element[0]:'-')+'</td>';
                                    html+='<td>'+(element[1]?element[1]:'-')+'</td>';
                                    html+='<td>'+(element[2]?element[2]:'-')+'</td>';
                                    html+='<td>'+
                                        (element[3]?element[3]:'-')+
                                    '</td>';
                                html+='</tr>';
                        });
                        $('[data-table="productos-faltantes"]').html(html);
                        $('#table-productos').DataTable();
                        $('.ver-productos').click();
                        $('.box-productos').removeClass('d-none');
                    }

                    if (result.value.productos_migrados.length>0) {
                        html='';
                        $.each(result.value.productos_migrados, function (index, element) {
                                html+='<tr>';
                                    html+='<td>'+(element[0]?element[0]:'-')+'</td>';
                                    html+='<td>'+(element[1]?element[1]:'-')+'</td>';
                                    html+='<td>'+(element[2]?element[2]:'-')+'</td>';
                                    html+='<td>'+
                                        (element[3]?element[3]:'-')+
                                    '</td>';
                                html+='</tr>';
                        });
                        $('[data-table="productos-migrados"]').html(html);
                        $('#table-productos-migrados').DataTable();
                        $('.ver-productos-migrados').click();
                        $('.box-productos-migrados').removeClass('d-none');
                    }
                    console.log(result.value);
                    Swal.fire(
                        '¡Éxito!',
                        'Se actualizo con éxito',
                        'success'
                    )
                }
            })

        });
    </script>
@endsection
