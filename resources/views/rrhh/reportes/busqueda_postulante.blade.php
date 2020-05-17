@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<fieldset>
    <legend><h2>Busqueda Avanzada de Postulantes</h2></legend>
    <div class="row">
        <div class="col-md-3">
            <h5>Filtro</h5>
            <select class="form-control input-sm" id="filtro">
                <option value="0" selected disabled>Elija una opción</option>
                <option value="1">Carrera / Profesión</option>
                <option value="2">Nivel de Estudio</option>
                <option value="3">Institución</option>
                <option value="4">Provincia</option>
                <option value="5">Distrito</option>
                <option value="6">Cargo Ocupado</option>
                <option value="7">Funciones Realizadas</option>
            </select>
        </div>
        <div class="col-md-5">
            <h5>Descripción</h5>
            <input type="text" class="form-control input-sm" id="descripcion" placeholder="Escribe una descripcion">
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm bg-olive" style="margin-top: 34px;" onclick="crearReporte();">
                <i class="fa fa-search"></i> Buscar
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="group-table">
                <table class="table table-striped table-bordered table-okc-view" id="my-report-table" width="100%"></table>
            </div>
        </div>
    </div>
</fieldset>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-informacion-reporte">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Información del Postulante</h3>
            </div>
            <div class="modal-body" id="info-detail"></div>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/reportes/reporte_rrhh.js')}}"></script>
@include('layout.fin_html')