@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="proyecto">
    <legend class="mylegend">
        <h2>Gestión de Proyectos</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Proyecto" 
                onClick="open_proyecto_create();">Crear Proyecto</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaProyecto">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Modalidad</th>
                        <th>Sis.Contrato</th>
                        <th>Mnd</th>
                        <th>Importe</th>
                        <th>Usuario</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th width="100px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.proyecto.proyectoContrato')
@include('proyectos.proyecto.proyectoCreate')
@include('proyectos.opcion.opcionModal')
@include('logistica.cotizaciones.clienteModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/proyecto/proyecto.js')}}"></script>
<script src="{{('/js/proyectos/opcion/opcionModal.js')}}"></script>
<script src="{{('/js/proyectos/proyecto/proyectoContrato.js')}}"></script>
<script src="{{('/js/logistica/clienteModal.js')}}"></script>
@include('layout.fin_html')