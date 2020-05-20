@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="equi_sol">
    <legend><h2>Asignación de Equipos</h2></legend>
    <form id="form-equi_sol" type="register" form="formulario">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        {{-- <input class="oculto" name="id_solicitud">
        <input class="oculto" name="area_solicitud">
        <input class="oculto" name="trabajador">
        <input class="oculto" name="fecha_inicio">
        <input class="oculto" name="fecha_fin"> --}}
        <div class="row">
            <div class="col-md-12">
                {{-- <h5>Lista de Solicitudes</h5> --}}
                <fieldset class="group-table">
                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                        id="listaSolicitudes">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Fecha Solicitud</th>
                                <th>Solicitado por</th>
                                <th>Area</th>
                                <th>Categoria</th>
                                <th>Cantidad</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</div>
@include('equipo.asignacionCreate')
@include('equipo.asignacion_equipos')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/asignacion.js')}}"></script>
<script src="{{('/js/equipo/asignacionCreate.js')}}"></script>
<script src="{{('/js/equipo/asignacion_equipos.js')}}"></script>
@include('layout.fin_html')