@extends('layout.head')
@include('layout.menu_proyectos')

@section('cabecera')
Información General del Proyecto
@endsection

@section('content')
<div class="page-main" type="curvas">
    <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
        <legend class="mylegend">
            <h2>Información General del Proyecto</h2>
            <ol class="breadcrumb" style="background-color: white;">
                <li><label id="codigo"></label></li>
                {{-- <li>Total Proyectado: <label id="total"></label></li> --}}
                {{-- <li><i class="fas fa-file-excel icon-tabla green boton"
                    data-toggle="tooltip" data-placement="bottom" 
                    title="Exportar a Excel" onclick="exportTableToExcel('listaPartidas','Valorizacion')"></i></li> --}}
            </ol>
        </legend>
        <div class="row">
            <div class="col-md-2">
                <h5>Propuesta Cliente:</h5>
            </div>
            <div class="col-md-10">
                <div class="input-group-okc">
                    <input class="oculto" name="id_presup" >
                    <input type="text" class="form-control" aria-describedby="basic-addon2" 
                        readonly name="nombre_opcion" disabled="true">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text btn btn-primary " id="basic-addon2" data-toggle="tooltip" 
                            data-placement="bottom" title="Buscar Propuesta Cliente"
                            onClick="propuestaModal('curvas');">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Curva "S" de Ejecución Financiera</label>
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="PresProgramadoEjecutado" style="margin-top:10px;">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-6">
                <label>Curva "S" de Ejecución Física</label>
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="ProgramadoEjecutado" style="margin-top:10px;">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <canvas id="chartPres" width="600" height="300"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="chartPro" width="600" height="300"></canvas>                
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Gestión del Valor Ganado</label>
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="ValorGanado" style="margin-top:10px;">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <canvas id="chartValor" width="600" height="300"></canvas>
            </div>
            <div class="col-md-6">
                <label>Indicadores del Mes</label>
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="Indicadores" style="margin-top:10px;">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                {{-- <canvas id="chartPro"></canvas> --}}
            </div>
        </div>
    </div>
</div>
@include('proyectos.presupuesto.propuestaModal')
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
    <script src="{{ asset('template/plugins/chartjs/Chart.min.js') }}"></script>

    <script src="{{('/js/proyectos/reportes/curvas.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/propuestaModal.js')}}"></script>
@endsection