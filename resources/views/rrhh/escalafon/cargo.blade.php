@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="cargo">
    <legend><h2>Cargos</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaCargo">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="40">Cod.</th>
                            <th>Descripción</th>
                            <th width="55">Sueldo Min</th>
                            <th width="55">Sueldo Max</th>
                            <th width="55">Sueldo Fijo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-cargo" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_cargo" primary="ids">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion del cargo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Sueldo Mínimo</h5>
                        <input type="number" class="form-control activation" name="sueldo_rango_minimo" disabled="true" value="0.00" step="any" min="0.00"
                        style="text-align: center;">
                    </div>
                    <div class="col-md-4">
                        <h5>Sueldo Máximo</h5>
                        <input type="number" class="form-control activation" name="sueldo_rango_maximo" disabled="true" value="0.00" step="any" min="0.00"
                        style="text-align: center;">
                    </div>
                    <div class="col-md-4">
                        <h5>Sueldo Fijo</h5>
                        <input type="number" class="form-control activation" name="sueldo_fijo" disabled="true" value="0.00" step="any" min="0.00"
                        style="text-align: center;">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/cargo.js')}}"></script>
@include('layout.fin_html')