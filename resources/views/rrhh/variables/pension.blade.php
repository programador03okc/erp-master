@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="pension">
    <legend><h2>Fondos de Pensiones</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaPensiones">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="30">N°</th>
                            <th>Descripción</th>
                            <th>Porcentaje</th>
                            <th>Aporte</th>
                            <th>Prima</th>
                            <th>Comisión</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-pension" type="register" form="formulario">
                <input type="hidden" name="id_pension" primary="ids">
                <div class="row">
                    <div class="col-md-10">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion del fondo de pension">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <h5>Porcentaje</h5>
                        <input type="number" class="form-control activation" name="porcentaje_general" step="any" min="0.00"  disabled="true">
                    </div>
                    <div class="col-md-5">
                        <h5>Aporte</h5>
                        <input type="number" class="form-control activation" name="aporte" step="any" min="0.00" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <h5>Prima</h5>
                        <input type="number" class="form-control activation" name="prima_seguro" step="any" min="0.00"  disabled="true">
                    </div>
                    <div class="col-md-5">
                        <h5>Comisión</h5>
                        <input type="number" class="form-control activation" name="comision" step="any" min="0.00" disabled="true">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/variables/pension.js')}}"></script>
@include('layout.fin_html')