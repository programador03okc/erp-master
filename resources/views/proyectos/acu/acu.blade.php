@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="acu">
    <legend class="mylegend">
        <h2>Análisis de Costos Unitarios</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un ACU" 
                onClick="open_acu_partida_create();">Crear ACU</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaAcu">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        {{-- <th>Fecha Registro</th> --}}
                        <th>Rend</th>
                        <th>Und</th>
                        <th>Total</th>
                        <th>Presupuestos</th>
                        <th>Estado</th>
                        <th width="130px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.acu.acuPartidaCreate')
@include('proyectos.acu.acuCreate')
@include('proyectos.acu.acuModal')
@include('proyectos.acu.acuPresupuesto')
@include('proyectos.insumo.insumoModal')
@include('proyectos.insumo.insumoCreate')
@include('proyectos.insumo.insumoPrecioModal')
@include('proyectos.variables.add_unid_med')
@include('proyectos.presupuesto.verAcu')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/acus/acu.js')}}"></script>
<script src="{{('/js/proyectos/acus/acuPartidaCreate.js')}}"></script>
<script src="{{('/js/proyectos/acus/acuCreate.js')}}"></script>
<script src="{{('/js/proyectos/acus/acuModal.js')}}"></script>
<script src="{{('/js/proyectos/insumos/insumoModal.js')}}"></script>
<script src="{{('/js/proyectos/insumos/insumoPrecioModal.js')}}"></script>
<script src="{{('/js/proyectos/insumos/insumoCreate.js')}}"></script>
<script src="{{('/js/proyectos/variables/add_unid_med.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/verAcu.js')}}"></script>
@include('layout.fin_html')