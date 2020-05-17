@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<fieldset>
    <legend><h2>Cumpleaños de los Trabajadores</h2></legend>
    <div class="row">
        <div class="col-md-3">
            <h5>Filtro: Meses</h5>
            <select class="form-control input-sm" id="filtro">
                <option value="0" selected disabled>Elija una opción</option>
                <option value="13">TODAS LAS OPCIONES</option>
                <option value="1">ENERO</option>
                <option value="2">FEBRERO</option>
                <option value="3">MARZO</option>
                <option value="4">ABRIL</option>
                <option value="5">MAYO</option>
                <option value="6">JUNIO</option>
                <option value="7">JULIO</option>
                <option value="8">AGOSTO</option>
                <option value="9">SETIEMBRE</option>
                <option value="10">OCTUBRE</option>
                <option value="11">NOVIEMBRE</option>
                <option value="12">DICIEMBRE</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm bg-olive" style="margin-top: 34px;" onclick="crearReporteCumple();">
                <i class="fa fa-search"></i> Buscar
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="group-table">
                <table class="table table-striped table-bordered table-okc-view" id="my-cumple-table" width="100%"></table>
            </div>
        </div>
    </div>
</fieldset>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/reportes/reporte_rrhh.js')}}"></script>
@include('layout.fin_html')