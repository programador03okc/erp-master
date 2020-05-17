@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="periodo">
    <legend><h2>Periodo de Asistencia</h2></legend>
    <div class="row">
        <div class="col-md-5">
            <form id="form-periodo" type="register" form="formulario">
                <input type="hidden" name="id_asistencia" primary="ids">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo asistencia</h5>
                        <select class="form-control activation" name="id_tipo_asistencia" disabled="true">
                            <option value="0">Elija una opción</option>
                            <option value="1">Semanal</option>
                            <option value="2">Mensual</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Fecha Inicio</h5>
                        <input type="date" class="form-control activation" name="fecha_inicio" disabled="true">
                    </div>
                    <div class="col-sm-6">
                        <h5>Fecha Fin</h5>
                        <input type="date" class="form-control activation" name="fecha_fin" disabled="true">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <fieldset class="group-table">
                        <h5>Tabla de resultados</h5>
                        <table class="mytable table table-bordered table-condensed table-okc-view" id="listaPeriodo">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>DESCRIPCION</th>
                                    <th>FECHA INICIO</th>
                                    <th>FECHA FIN</th>
                                    <th>ESTADO</th>
                                </tr>
                            </thead>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/periodo.js')}}"></script>
@include('layout.fin_html')