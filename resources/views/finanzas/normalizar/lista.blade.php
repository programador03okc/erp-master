@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Normalizar
@endsection

@section('estilos')
<link rel="stylesheet" href="{{asset('template/plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
<style>
    .contenido-detalle{
        height: 0;
        transition: .3s height;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{route('finanzas.index')}}"><i class="fa fa-usd"></i> Finanzas</a></li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Normalizar Requerimientos de pago/Ordenes</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <form action="" data-form="buscar">
                            @csrf
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Meses</label>
                                    <select class="form-control" name="mes" required>
                                        <option value="01" selected>Enero</option>
                                        <option value="02">Febrero</option>
                                        <option value="03">Marzo</option>
                                        <option value="04">Abril</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Divisiones</label>
                                    <select class="form-control" name="division" required>
                                        @foreach ($division as $key=>$item)
                                        @if ($key===0)
                                            <option value="{{$item->id_division}}" selected>{{$item->descripcion}}</option>
                                        @else
                                            <option value="{{$item->id_division}}">{{$item->descripcion}}</option>
                                        @endif

                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Requerimientos de Pagos</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Ordenes</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <table class="table table-bordered table-hover dataTable" id="lista-requerimientos-pagos">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th >Código</th>
                                    <th>Concepto</th>
                                    <th >Fecha Aprovación</th>
                                    <th>Creado por</th>
                                    <th>Monto total</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <table class="table table-hover"  id="lista-ordenes">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    <th >Código</th>
                                    <th >Fecha de emisión</th>
                                    <th >Estado del Pago</th>
                                    <th >Importe total orden</th>
                                    <th >-</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs pull-right">
                    <li><a href="#tab_2-2" data-toggle="tab">Ordenes</a></li>
                    <li class="active"><a href="#tab_1-1" data-toggle="tab">Requerimeintos de pagos</a></li>

                    <li class="pull-left header"><i class="fa fa-th"></i> </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1-1">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <table class="table table-bordered table-hover dataTable"
                                    id="lista-requerimientos-pagos">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th >Código</th>
                                            <th>Concepto</th>
                                            <th >Fecha Emisión</th>
                                            <th>Creado por</th>
                                            <th>Monto total</th>
                                            <th>-</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table> --}}
                            </div>
                        </div>

                    </div>
                  <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2-2">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <table class="table table-hover"
                                    id="lista-ordenes">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th >Código</th>
                                            <th >Fecha de emisión</th>
                                            <th >Estado del Pago</th>
                                            <th >Importe total orden</th>
                                            <th >-</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table> --}}
                            </div>
                        </div>
                    </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="normalizar-partida" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">Normalizar Requerimientos de pago / Partidas de Presupuesto Interno</h5>

                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
<script src="{{asset('template/plugins/select2/select2.min.js')}}"></script>

    <script>
        // console.log(ruta);
    </script>
    <script src="{{asset('js/normalizar/lista-normalizar.js')}}"></script>


@endsection
