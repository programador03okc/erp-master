@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera')
    Registro
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> Gerencial</a></li>
    <li>Cobranzas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="usuarios">
    <legend class="mylegend">
        <h2>Usuarios</h2>
        <ol class="breadcrumb">
            <li>

            </li>
        </ol>
    </legend>

    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">Lista de usuarios</h3>
            <div class="pull-right box-tools">
                <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Nuevo Usuario" onClick="crear_usuario();">Nuevo Usuario</button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="mytable table table-striped table-condensed table-bordered" id="listar-registros">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="10">Emp</th>
								<th width="10">OCAM</th>
								<th width="150">Nombre del Cliente</th>
								<th>Fact.</th>
								<th>UU. EE.</th>
								<th>FTE. FTO.</th>
								<th>OC.</th>
								<th>SIAF</th>
								<th>Fecha Emis.</th>
								<th>Fecha Recep.</th>
								<th>Días A.</th>
								<th>Mon</th>
								<th>Importe</th>
								<th id="tdEst">Estado</th>
								<th id="tdResp">A. Respo.</th>
								<th width="10">Fase</th>
								<th class="hidden">Tipo</th>
								<th id="tdAct">-</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>


    <script src="{{ asset('js/gerencial/cobranza/registro.js') }}"></script>
@endsection
