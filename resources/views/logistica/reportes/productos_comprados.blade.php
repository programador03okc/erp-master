@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="reporte-productos_comprados">
    <legend>
        <div class="row">
            <div class="col-xs-12 col-md-7"><h2>Reporte - Productos Comprados</h2></div>  
        </div>
    </legend>
    <form id="form-productos_comprados" type="register" form="formulario">
        <input type="hidden" name="id_proveedor">

        <div class="row">
            <div class="col-md-6">
                <h5>Razon Social</h5>
                    <select class="form-control js-example-basic-single" name="razon_social_proveedor" onChange="get_id_proveedor(event);">
                        <option value="0" data-ruc="0">Elija una opción</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{$proveedor->id_proveedor}}" data-numero-ruc="{{$proveedor->nro_documento}}">{{$proveedor->razon_social}}</option>
                        @endforeach
                    </select>
            </div>
            <div class="col-md-3">
                <h5>N° RUC</h5>
                <div class="input-group-okc">
                    <input type="text" class="form-control input-sm" name="numero_ruc" placeholder="N° RUC" disabled>
                    <!-- <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon2" onClick="prov_por_numero_ruc();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div> -->
                </div>
            </div>
            <div class="col-md-3">
                <h5>Empresa</h5>
                <select class="form-control input-sm" name="empresa">
                    <option value="0" data-ruc="0">Elija una opción</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{$empresa->id_empresa}}" >{{$empresa->razon_social}}</option>
                        @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <h5>Desde</h5>
                <div class="input-group-okc">
                    <input type="date" class="form-control input-sm" name="fecha_desde" placeholder="Desde">
                </div>
            </div>
            <div class="col-md-2">
                <h5>Hasta</h5>
                <div class="input-group-okc">
                    <input type="date" class="form-control input-sm" name="fecha_hasta" placeholder="Hasta">
                </div>
            </div>
        </div>
 
        <br>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" onClick="reporteProductosComprados(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom"  title="Ejecutar">
                    Ejecutar
                </button>
                <label class="radio-inline">
                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="PREVISUALIZAR" checked> Previsualizar
                </label>
                <label class="radio-inline">
                    <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="EXCEL"> Excel
                </label>
            </div>
        </div>


        <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaProductosComprados">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>#</th>
                        <th>Proveedor</th>
                        <th>Código</th>
                        <th>Item</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Precio</th>
                        <th>IGV</th>
                        <th>Precio Sin IGV</th>
                        <th>Sub-Total</th>
                        <th>Flete</th>
                        <th>Procentaje Desc.</th>
                        <th>Monto Desc.</th>
                        <th>Garantia</th>
                        <th>Lugar de Despacho</th>
                        <th>Plazo de Entrega</th>
                        <th>Moneda</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
 
    </form>

        
 
</div>


@include('layout.footer')
@include('layout.scripts')
<script src="{{ asset('/js/logistica/reportes/productos_comprados.js') }}"></script>
 @include('layout.fin_html')