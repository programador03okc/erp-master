@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="vacaciones">
    <legend><h2>Vacaciones al Personal</h2></legend>
    <form id="form-vacaciones" type="register" form="formulario">
        <input type="hidden" name="id_vacaciones" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar DNI</h5>
                <input type="hidden" class="form-control input-sm" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control input-sm" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5>Nombre del trabajador</h5>
                <input type="text" class="form-control input-sm" name="datos_trabajador" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Fecha Inicio</h5>
                        <input type="date" class="form-control input-sm activation" name="fecha_inicio" disabled="true">
                    </div>
                    <div class="col-md-4">
                        <h5>Fecha Fin</h5>
                        <input type="date" class="form-control input-sm activation" name="fecha_fin" disabled="true">
                    </div>
                    <div class="col-md-4">
                        <h5>Fecha Retorno</h5>
                        <input type="date" class="form-control input-sm activation" name="fecha_retorno" disabled="true">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-5">
                        <h5>Periodo</h5>
                        <input type="text" class="form-control input-sm activation" name="concepto" disabled="true">
                    </div>
                    <div class="col-md-3">
                        <h5>N° Días</h5>
                        <input type="number" class="form-control input-sm activation" name="dias" step="any" disabled="true">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaVacacionesTrab" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>PERIODO</th>
                            <th width="130">FECHA INICIO</th>
                            <th width="130">FECHA FIN</th>
                            <th width="130">FECHA RETORNO</th>
                            <th width="90">N° DIAS</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="trab-vacaciones">
                        <tr><td></td><td colspan="6"> No hay datos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/vacaciones.js')}}"></script>
@include('layout.fin_html')