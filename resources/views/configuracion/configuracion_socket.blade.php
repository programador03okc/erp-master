@include('layout.head')
@include('layout.menu_config')
@include('layout.body')
<div class="page-main" type="configuracion_socket">
    <legend><h2>Configuración de Socket</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaConfiguracionSocket">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Modo</th>
                            <th>Host</th>
                            <th>Activado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-configuracion_socket" type="register" form="formulario">
                <input type="hidden" name="id" primary="ids">
                <div class="row">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Modo</h5>
                        <input type="text" class="form-control activation" name="modo" disabled="true">
                    </div>
                    <div class="col-md-6">
                        <h5>Host</h5>
                        <input type="text" class="form-control activation" name="host" disabled="true" >
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <h5>Activado</h5>
                        <select class="form-control activation" name="activado" disabled="true">
                            <option value="0" selected disabled>Elija una opción</option>
                            <option value="true" >Si</option>
                            <option value="false" >No</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/configuracion/configuracion_socket.js')}}"></script>
@include('layout.fin_html')