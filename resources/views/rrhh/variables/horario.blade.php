@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="horario">
    <legend><h2>Horario</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <form id="form-horario" type="register" form="formulario">
                <input type="hidden" name="id_horario" primary="ids">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion del horario">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <fieldset class="group-rrhh"><legend><h6>Lunes a Viernes</h6></legend>
                            <div class="row" style="padding: 0 10px;">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Ingreso</h5>
                                            <input type="time" class="form-control activation" name="hora_ini_reg" disabled="true">
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Salida</h5>
                                            <input type="time" class="form-control activation" name="hora_fin_reg" disabled="true">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Ingreso Almuerzo</h5>
                                            <input type="time" class="form-control activation" name="hora_ini_alm" disabled="true">
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Salida Almuerzo</h5>
                                            <input type="time" class="form-control activation" name="hora_fin_alm" disabled="true">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-4">
                        <fieldset class="group-rrhh"><legend><h6>Sábados</h6></legend>
                            <div class="row" style="padding: 0 10px;">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Ingreso</h5>
                                            <input type="time" class="form-control activation" name="hora_ini_sab" disabled="true">
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Salida</h5>
                                            <input type="time" class="form-control activation" name="hora_fin_sab" disabled="true">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Días (semana)</h5>
                                            <input type="number" class="form-control activation" name="dias_sem" step="any" min="0" disabled="true">
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Horas (semana)</h5>
                                            <input type="number" class="form-control activation" name="hora_sem" step="any" min="0" disabled="true">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-horario">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Horarios</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaHorario">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Descripción</th>
                            <th>Horario Regular</th>
                            <th>Horario Almuerzo</th>
                            <th>Horario Sábados</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/variables/horario.js')}}"></script>
@include('layout.fin_html')