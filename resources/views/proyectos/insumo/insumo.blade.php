@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="insumo">
    <legend class="mylegend">
        <h2>Lista de Insumos</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un Insumo Base" 
                onClick="open_insumo_create();">Crear Insumo</button>
                {{-- <a onClick="generar_factura();">
                    <input type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Generar Factura de Compra" 
                    value="Generar Factura"/>
                </a>
                <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                data-placement="bottom" title="Ver Ingreso a Almacén" 
                onClick="abrir_ingreso();">Ver Ingreso </button> --}}
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">

            <table class="mytable table table-condensed table-bordered table-okc-view" 
            id="listaInsumo">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Und</th>
                        <th>Ult.Precio</th>
                        <th>Flete</th>
                        <th>Peso</th>
                        <th>IU</th>
                        <th width='90px'>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            
        </div>
    </div>
</div>
@include('proyectos.insumo.insumoCreate')
@include('proyectos.variables.add_unid_med')
@include('proyectos.insumo.insumoPrecioModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/insumos/insumo.js')}}"></script>
<script src="{{('/js/proyectos/insumos/insumoCreate.js')}}"></script>
<script src="{{('/js/proyectos/variables/add_unid_med.js')}}"></script>
<script src="{{('/js/proyectos/insumos/insumoPrecioModal.js')}}"></script>
@include('layout.fin_html')