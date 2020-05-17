@include('layout.head')
@include('layout.menu_config')
@include('layout.body')
<div class="page-main" type="modulo">
    <legend><h2>Historial de Aprobaciones</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaHistorialAprobación">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="30">Flujo</th>
                            <th>Documento</th>
                            <th>VoBo</th>
                            <th>Detalle</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Area</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
 
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/configuracion/flujo_aprobacion/historialAprobaciones.js')}}"></script>
@include('layout.fin_html')