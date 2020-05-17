@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="tolerancia">
    <legend><h2>Tolerancia</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaTolerancia">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="40">N°</th>
                            <th>Tiempo</th>
                            <th>Periodo</th>
                            <th width="70">Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-tolerancia" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_tolerancia" primary="ids">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Tiempo (min)</h5>
                        <input type="number" class="form-control activation" name="tiempo" disabled="true">
                    </div>
                    <div class="col-md-6">
                        <h5>Periodo</h5>
                        <select class="form-control activation" name="periodo" disabled="true">
                            <option value="">Elija una opcion..</option>
                            <option value="1">DIARIO</option>
                            <option value="2">SEMANAL</option>
                            <option value="3">MENSUAL</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/variables/tolerancia.js')}}"></script>
@include('layout.fin_html')