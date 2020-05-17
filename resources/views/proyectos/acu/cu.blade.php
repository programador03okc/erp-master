@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="cu">
    <legend class="mylegend">
        <h2>Gestión Nombres de A.C.U.</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un Nombre ACU" 
                onClick="open_acu_create();">Crear Nombre ACU</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaCu">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th width="130px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.acu.acuCreate')
@include('proyectos.presupuesto.verPartidaCu')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/acus/cu.js')}}"></script>
<script src="{{('/js/proyectos/acus/acuCreate.js')}}"></script>
@include('layout.fin_html')