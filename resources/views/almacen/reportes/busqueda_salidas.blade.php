@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Detalle de Salidas
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="busqueda_salidas">
    @if (in_array(167,$array_accesos))
    <div class="box box-solid">
        <div class="box-body">
            <div class="row" style="padding-left:0px;padding-right:0px;">
                <div class="col-md-12">
                    @if (in_array(167,$array_accesos))
                    <button type="button" class="btn btn-primary" data-toggle="tooltip"
                            data-placement="bottom" title="Ingrese los filtros"
                            onClick="open_filtros();">
                            <i class="fas fa-search"></i>  Filtros</button>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view"
                        id="listaBusquedaSalidas">
                        <thead>
                            <tr>
                                <th hidden></th>
                                <th>Tp</th>
                                <th>Serie-Número</th>
                                <th>Fecha Emisión</th>
                                <th>RUC</th>
                                <th>Razon Social</th>
                                <th>Condición</th>
                                <th>Código</th>
                                <th>Cod.Anexo</th>
                                <th width="30%">Descripción</th>
                                <th>Cant.</th>
                                <th>Estado</th>
                                <th>Almacén</th>
                                <th>Cod. Req.</th>
                                <th>CDP</th>
                                <th>Responsable</th>
                                <th>Fecha Registro</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-busq_filtros">
    <div class="modal-dialog">
        <div class="modal-content" style="width:500px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros de Búsqueda de Salidas</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Empresas</h5>
                        <div style="display:flex">
                            <input type="checkbox" name="todas_empresas" style="width:30px;margin-top:10px;"/>
                            <h5 style="width:50px;">Todas</h5>
                            <select class="form-control" name="id_empresa" >
                                @foreach ($empresas as $alm)
                                    <option value="{{$alm->id_empresa}}">{{$alm->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Almacén</h5>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_almacenes" style="width:30px;margin-top:10px;"/>
                            <h5 style="width:50px;">Todos</h5>
                            <select class="form-control" name="almacen" multiple>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Ingrese un criterio de búsqueda</h5>
                        <div style="display:flex;">
                            <input class="oculto" name="id_proveedor"/>
                            <input class="oculto" name="id_contrib"/>
                            <select class="form-control" name="buscar" style="width:30%;">
                                {{-- <option value="0">Elija una opción</option> --}}
                                <option value="1">Frase</option>
                                <option value="2">Código</option>
                                <option value="3">Nro.Parte</option>
                            </select>
                            <input type="text" class="form-control" name="descripcion" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Documentos</h5>
                        <select class="form-control" name="documento" multiple>
                            @foreach ($tp_doc_almacen as $alm)
                                <option value="{{$alm->id_tp_doc_almacen}}">{{$alm->descripcion}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_documentos" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Rango de Fechas</h5>
                        <div style="display:flex;">
                            <span class="form-control" style="width:100px;"> Desde: </span>
                            <input type="date" class="form-control" name="fecha_inicio">
                            <span class="form-control" style="width:100px;"> Hasta: </span>
                            <input type="date" class="form-control" name="fecha_fin">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="actualizarLista();">Listar</button>
            </div>
        </div>
    </div>
</div>
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/almacen/reporte/busqueda_salidas.js')}}"></script>
    <script>
    $(document).ready(function(){
        vista_extendida();
        seleccionarMenu(window.location);
    });
    </script>
@endsection
