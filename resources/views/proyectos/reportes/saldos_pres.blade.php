@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Saldos Presupuesto
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="saldos_pres">
    <div class="thumbnail" style="padding-left: 20px;padding-right: 20px;padding-top: 20px;">
        <div class="row">
            <div class="col-md-1">
                <h5>Código</h5>
                <input type="text" name="cod_preseje" class="form-control" readOnly/>
            </div>
            <div class="col-md-6">
                <h5>Descripción</h5>
                <input type="text" name="descripcion" class="form-control" readOnly/>
            </div>
            <div class="col-md-4">
                <h5>Cliente</h5>
                <input type="text" name="razon_social" class="form-control" readOnly/>
            </div>
            <div class="col-md-1">
                <h5>.</h5>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Buscar Presupuesto de Ejecución" 
                    onClick="estPresejeModal();">Buscar</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="borde-group-verde">
                    <table width="100%" id="totales"  style="font-size: 14px; margin-bottom: 0px;">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                    id="listaEstructura" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th class="right">Imp.Total</th>
                            <th class="right">Imp.OC/OS</th>
                            <th class="right">Saldo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('proyectos.reportes.verDetallePartida')
@include('proyectos.reportes.estPresejeModal')
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

    <script src="{{ asset('js/proyectos/reportes/saldos_pres.js')}}"></script>
    <script src="{{ asset('js/proyectos/reportes/verDetallePartida.js')}}"></script>
    <script src="{{ asset('js/proyectos/reportes/estPresejeModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection