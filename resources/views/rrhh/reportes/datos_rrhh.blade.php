@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="datos_rrhh">
    <fieldset>
        <legend><h2>Datos Generales de los Trabajadores</h2></legend>
        <div class="row">
            <div class="col-md-2">
                <button class="btn btn-sm btn-flat  btn-block bg-primary" onclick="crearReporteDatosGrl();">
                    <i class="fa fa-search"></i> Buscar
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-flat  btn-block bg-olive" onclick="reporteExcelDatosGrl();">
                    <i class="fa fa-download"></i> Descargar Excel
                </button>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="group-table">
                    <table class="table table-striped table-bordered table-okc-view" id="my-datos-grl-table" width="100%"></table>
                </div>
            </div>
        </div>
    </fieldset>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/reportes/reporte_rrhh.js')}}"></script>
@include('layout.fin_html')