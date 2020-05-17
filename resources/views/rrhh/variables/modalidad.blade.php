@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="modalidad">
    <legend><h2>Modalidades de Contrato</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaModalidad">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="40">N°</th>
                            <th>Descripción</th>
                            <th width="60">Dias de Trabajo</th>
                            <th width="60">Dias de Descanso</th>
                            <th width="70">Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-modalidad" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_modalidad" primary="ids">
                <div class="row">
                    <div class="col-md-8">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion de la modalidad">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Días de trabajo (mes)</h5>
                        <input type="number" class="form-control activation" value="0" name="dias_trabajo">
                    </div>
                    <div class="col-md-4">
                        <h5>Días de descanso (mes)</h5>
                        <input type="number" class="form-control activation" value="0" name="dias_descanso">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/variables/modalidad.js')}}"></script>
@include('layout.fin_html')