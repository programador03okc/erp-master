@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="residente">
    <legend class="mylegend">
        <h2>Gestión de Residentes</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Residente" 
                onClick="open_residente_create('');">Crear Residente</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaResidentes">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>DNI</th>
                        <th>Nombres y Apellidos</th>
                        <th>Colegiatura</th>
                        {{-- <th>Especialidad</th> --}}
                        <th>Estado</th>
                        <th width="100px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.residentes.residenteCreate')
@include('proyectos.residentes.trabajadorModal')
@include('proyectos.proyecto.proyectoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/residentes/residentes.js')}}"></script>
<script src="{{('/js/proyectos/residentes/trabajadorModal.js')}}"></script>
<script src="{{('/js/proyectos/proyecto/proyectoModal.js')}}"></script>
@include('layout.fin_html')