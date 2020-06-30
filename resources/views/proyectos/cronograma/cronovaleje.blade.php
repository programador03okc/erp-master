@extends('layout.main')
@include('layout.menu_proyectos')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Cronograma Valorizado de Ejecución
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Ejecución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="cronovaleje">
    <form id="form-cronovaleje" type="register" form="formulario">
        <div class="thumbnail" style="padding-left: 20px;padding-right: 10px;padding-top: 20px;">
            <div class="row">
                <div class="col-md-1">
                    <h5>Presupuesto:</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group-okc">
                        <input type="text" class="oculto" name="id_presupuesto" primary="ids">
                        <input type="text" class="oculto" name="modo">
                        <input type="text" class="form-control" aria-describedby="basic-addon2" 
                            readonly name="nombre_opcion" disabled="true">
                        {{-- <div class="input-group-append">
                            <button type="button" class="input-group-text btn btn-primary " id="basic-addon2" data-toggle="tooltip" 
                                data-placement="bottom" title="Buscar Presupuesto de Ejecución"
                                onClick="presejeModal('crononuevo');">
                                <i class="fa fa-search"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>
                <div class="col-md-1">
                    <h5>Mostrar en:</h5>
                </div>
                <div class="col-md-2">
                    <div style="display:flex;">
                        <input type="number" class="form-control" name="numero"  disabled="true" style="width:80px;"/>
                        <select class="form-control group-elemento" name="unid_program" disabled="true" >
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($unid_program as $unid)
                                <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <input type="button" class="form-control btn btn-success" name="btn_actualizar" disabled="true"
                    onClick="mostrar_crono_valorizado();" style="width:100px;" value="Actualizar"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    Código del Presupuesto: <label id="codigo"></label>
                </div>
                <div class="col-md-3">
                    Duración Total: <label id="duracion"></label>
                </div>
                <div class="col-md-3">
                    Sub Total: <label id="importe"></label>
                </div>
                <div class="col-md-3">
                    Descargar <i class="fas fa-file-excel icon-tabla green boton"
                        data-toggle="tooltip" data-placement="bottom" 
                        title="Exportar a Excel" onclick="exportTableToExcel('listaPartidas','CronogramaValorizado')"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                        id="listaPartidas" style="margin-top:10px;">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th width="40%">Descripción</th>
                                <th>Duración</th>
                                <th width="70">Montos Parciales</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
@include('proyectos.presupuesto.presejeModal')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>

    <script src="{{ asset('js/proyectos/cronograma/cronovaleje.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/presejeModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection