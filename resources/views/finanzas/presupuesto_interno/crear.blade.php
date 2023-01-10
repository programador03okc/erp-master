@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Presupuesto Interno
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<style>
    .lbl-codigo:hover{
        color:#007bff !important;
        cursor:pointer;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><i class="fa fa-usd"></i> Finanzas </li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
<div class="box box-danger">
    <div class="box-header with-border">
        <h3 class="box-title">NUEVO PRESUPUESTO INTERNO</h3>
        <div class="box-tools pull-right">
            {{-- <div class="btn-group" role="group"> --}}
                <button title="Volver a la lista de presupuesto interno"
                    class="btn btn-sm btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    Volver
                </button>
                <button title="Guardar"
                    class="btn btn-sm btn-success">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
                <!-- <a target="_blank" href="#" title="Imprimir" class="btn">
                    <i class="glyphicon glyphicon-search" aria-hidden="true"></i>
                </a> -->
            {{-- </div> --}}
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_grupo">Grupo :</label>
                    <select class="form-control" name="id_grupo" id="id_grupo">
                        <option value="">Seleccione...</option>
                        @foreach ($grupos as $item)
                        <option value="{{ $item->id_grupo }}">{{ $item->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_grupo">Area :</label>
                    <select class="form-control" name="id_grupo" id="id_grupo">
                        <option value="">Seleccione...</option>
                        @foreach ($area as $item)
                        <option value="{{ $item->id_area }}">{{ $item->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_grupo">Moneda :</label>
                    <select class="form-control" name="id_grupo" id="id_grupo">
                        <option value="">Seleccione...</option>
                        @foreach ($moneda as $item)
                        <option value="{{ $item->id_moneda }}">{{ $item->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="descripcion">Descripcion : </label>
                    <textarea id="descripcion" class="form-control" name="descripcion" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <table class="table table-sm table-hover table-bordered dt-responsive nowrap" id="listaPartidas">
                    <thead>
                        <tr>
                            <th>PARTIDA</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody data-table="presupuesto-detalle"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <div class="col-md-12" id="tab-partidas">

            <ul class="nav nav-tabs" id="myTabPartidas">
                <li class="active"><a data-toggle="tab" href="#partidas">Detalle </a></li>
                <li class=""><a data-toggle="tab" href="#gastos">Gastos por partidas</a></li>
            </ul>
            <div class="tab-content">
                <div id="partidas" class="tab-pane fade in active">

                    {{-- <div class="row" >
                        <div class="col-md-12"> --}}

                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Partidas</h3>
                                    <div class="box-tools pull-right">
                                        <div class="btn-group" role="group">
                                            <button data-toggle="tooltip" data-placement="bottom" title="Nuevo Título"
                                                class="btn btn-success btn-sm nuevo-titulo">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-sm table-hover table-bordered dt-responsive nowrap" id="listaPartidas">
                                                <thead style="background: gainsboro;">
                                                    <tr>
                                                        <th>Codigo</th>
                                                        <th>Descripción</th>
                                                        <th>Total</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{-- </div>
                    </div> --}}

                </div>
                <div id="gastos" class="tab-pane fade ">

                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cuadro de Gastos</h3>
                            <div class="box-tools pull-right">
                                <div class="btn-group" role="group">
                                    <button data-toggle="tooltip" data-placement="bottom" title="Exportar a excel"
                                        class="btn btn-success btn-sm exportar" style="color:#fff !important;" onClick="exportarCuadroCostos()">
                                        <i class="fas fa-file-excel"></i> Exportar a excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-hover table-bordered dt-responsive nowrap"
                                    id="listaGastosPartidas" style="font-size: 13px">
                                        <thead style="background: gainsboro;">
                                            <tr>
                                                <th>Empresa</th>
                                                <th>Fecha pago</th>
                                                <th>Cod.Req.</th>
                                                {{-- <th>OC/OS</th> --}}
                                                <th>Titulo</th>
                                                <th>Partida</th>
                                                {{-- <th>Proveedor o persona asignada</th> --}}
                                                <th>Descripción</th>
                                                <th>Cant.</th>
                                                <th>Unid.</th>
                                                {{-- <th>Mnd.</th> --}}
                                                <th>P. Unitario</th>
                                                <th>SubTotal</th>
                                                <th>I.G.V.</th>
                                                <th>P. Compra</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script>
        // let csrf_token = "{{ csrf_token() }}";
        // $(document).ready(function () {
        //     seleccionarMenu(window.location);
        // });
    </script>

    <script src="{{asset('js/finanzas/presupuesto_interno/crear.js') }}""></script>
@endsection
