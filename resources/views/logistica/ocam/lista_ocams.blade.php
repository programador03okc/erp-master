@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
    Lista OCAM'S
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li>OCAM's</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_ocams">
<div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#ocams_pendientes" onClick="mostrar_ordenes_propias_pendientes();" aria-controls="ocams_pendientes" role="tab" data-toggle="tab">Pendientes</a></li>
                        <li role="presentation" class=""><a href="#ocams_vinculadas" onClick="mostrar_ordenes_propias_vinculadas();" aria-controls="ocams_vinculadas" role="tab" data-toggle="tab">Vinculadas a un Requerimiento</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="ocams_pendientes">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Empresa</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="id_empresa_select_op_pendientes" onChange="handleChangeFilterEmpresaListOrdenesPropiasPendientesByEmpresa(event);">
                                                            <option value="0">Todas las Empresas</option>
                                                            @foreach ($empresas_am as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->empresa}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Año de publicación</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="descripcion_año_publicacion_op_pendientes" onChange="handleChangeFilterEmpresaListOrdenesPropiasPendientesByAñoPublicacion(event);">
                                                           
                                                            @foreach ($periodos as $periodo)
                                                                <option value="{{$periodo->descripcion}}">{{$periodo->descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaOrdenesPropiasPendientes" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>#</th>
                                                                <th>Empresa</th>
                                                                <th>AM</th>
                                                                <th>Entidad</th>
                                                                <th>Fecha publicación</th>
                                                                <th>Estado O/C</th>
                                                                <th>Fecha Estado</th>
                                                                <th>Estado Entrega</th>
                                                                <th>Fecha Entrega</th>
                                                                <th>Total</th>
                                                                <th>Estado Aprob. CC</th>
                                                                <th>Tipo Cuadro</th>
                                                                <th>ACCIÓN</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ocams_vinculadas">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Empresa</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="id_empresa_select_op_vinculadas" onChange="handleChangeFilterEmpresaListOrdenesPropiasVinculadasByEmpresa(event);">
                                                            <option value="0">Todas las Empresas</option>
                                                            @foreach ($empresas_am as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->empresa}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Año de publicación</h5>
                                                    <div style="display:flex;">
                                                    <select class="form-control" id="descripcion_año_publicacion_op_vinculadas" onChange="handleChangeFilterEmpresaListOrdenesPropiasVinculadasByAñoPublicacion(event);">
                                                           
                                                            @foreach ($periodos as $periodo)
                                                                <option value="{{$periodo->descripcion}}">{{$periodo->descripcion}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaOrdenesPropiasVinculadas" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>#</th>
                                                                <th>Empresa</th>
                                                                <th>AM</th>
                                                                <th>Entidad</th>
                                                                <th>Fecha publicación</th>
                                                                <th>Estado O/C</th>
                                                                <th>Fecha Estado</th>
                                                                <th>Estado Entrega</th>
                                                                <th>Fecha Entrega</th>
                                                                <th>Total</th>
                                                                <th>Estado Aprob. CC</th>
                                                                <th>Tipo Cuadro</th>
                                                                <th>ACCIÓN</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                        </div>
                    </div>
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

    <script src="{{asset('js/logistica/lista_ocams.js')}}"></script>
 
    <script>

    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializarRutasListadoOrdenesPropias(
            "{{route('logistica.gestion-logistica.ocam.listado.ordenes-propias')}}"
            );
        });
    </script>

@endsection