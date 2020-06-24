@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Mejores Proveedores
@endsection

@section('content')
<div class="page-main" type="reporte-mejores_proveedores">
    <legend>
        <div class="row">
            <div class="col-xs-12 col-md-7"><h2>Reporte - Mejores Proveedores</h2></div>  
        </div>
    </legend>
    <form id="form-mejores_proveedores" type="register" form="formulario">
        <input type="hidden" name="id_producto">

        <div class="row">
            <div class="col-md-9">
                    <h5>Producto</h5>
                    <div class="input-group-okc">
                        <input type="text" class="form-control" name="producto" placeholder="" disabled>
                        <div class="input-group-append">
                            <button type="button" class="input-group-text" id="basic-addon1" onclick="catalogoProductosModal();">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
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
            <div class="col-md-3">
                <h5>Tipo</h5>
                <select class="form-control input-sm" name="tipo_periodo">
                        <option value="MENSUAL" >Ultimas Compras</option>
                        <!-- <option value="ANUAL" >Anual</option> -->
                 </select>
            </div>
            <div class="col-md-2">
                <h5>Año</h5>
                <div class="input-group-okc">
                    <input type="text" class="form-control input-sm" name="año" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <h5>Precio</h5>
                <div style="display:flex;">
                    <select class="form-control group-elemento" name="condicion_precio"  style="width:120px;text-align:center;" >
                            <option value="igual">=</option>
                            <option value="menor"><</option>
                            <option value="menor_igual"><=</option>
                            <option value="mayor">></option>
                            <option value="mayor_igual">>=</option>
                    </select>
                    <input type="number" name="precio" class="form-control group-elemento" style="text-align:right;" >
                </div>
            </div>
            <div class="col-md-3">
                <h5>Garantia</h5>
                <div style="display:flex;">
                    <select class="form-control group-elemento" name="condicion_garantia"  style="width:120px;text-align:center;" >
                            <option value="igual">=</option>
                            <option value="menor"><</option>
                            <option value="menor_igual"><=</option>
                            <option value="mayor">></option>
                            <option value="mayor_igual">>=</option>
                    </select>
                    <input type="number" name="garantia" class="form-control group-elemento" style="text-align:right;" >
                </div>
            </div>
            <div class="col-md-3">
                <h5>Tiempo de Entrega</h5>
                <div style="display:flex;">
                    <select class="form-control group-elemento" name="condicion_tiempo_entrega"  style="width:120px;text-align:center;" >
                            <option value="igual">=</option>
                            <option value="menor"><</option>
                            <option value="menor_igual"><=</option>
                            <option value="mayor">></option>
                            <option value="mayor_igual">>=</option>
                    </select>
                    <input type="number" name="tiempo_entrega" class="form-control group-elemento" style="text-align:right;" >
                    <input type="text" name="tiempo_entrega" class="form-control group-elemento" style="text-align:center;" disabled value="Dias">
                </div>
            </div>
            <div class="col-md-3">
            <h5>&nbsp;</h5>
            <div style="display:flex; col-md-12">
            <label class="radio-inline">
                    <input type="checkbox" name="optionMejorPrecio" id="optionMejorPrecio" onChange="checkMejorPrecio(event);"> Mejor Precio
                </label>
            </div>
            </div>
        </div>
 
        <br>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" onClick="reporteMejoresProveedores(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom"  title="Ejecutar">
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
                id="listaMejoresProveedores">
                <thead>
                    <tr>
                        <th rowspan="2" hidden></th>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Producto</th>
                        <th colspan="5" id="proveedor01" >compra #1</th>
                        <th colspan="5" id="proveedor02">compra #2</th>
                        <th colspan="5" id="proveedor03">compra #3</th>
                        <th colspan="5" id="proveedor04">compra #4</th>
                        <th colspan="5" id="proveedor05">compra #5</th>
                    </tr>
                    <tr>
                        <td>Unidad</td>
                        <td>Precio</td>
                        <td>Garantia</td>
                        <td>Plazo (Días)</td>
                        <td>Flete</td>
                        <td>Unidad</td>
                        <td>Precio</td>
                        <td>Garantia</td>
                        <td>Plazo (Días)</td>
                        <td>Flete</td>
                        <td>Unidad</td>
                        <td>Precio</td>
                        <td>Garantia</td>
                        <td>Plazo (Días)</td>
                        <td>Flete</td>
                        <td>Unidad</td>
                        <td>Precio</td>
                        <td>Garantia</td>
                        <td>Plazo (Días)</td>
                        <td>Flete</td>
                        <td>Unidad</td>
                        <td>Precio</td>
                        <td>Garantia</td>
                        <td>Plazo (Días)</td>
                        <td>Flete</td>
                    </tr>
                </thead>
                <tbody>
 
 
                </tbody>
            </table>
        </div>
    </div>
 
    </form>

        
 
</div>

@include('logistica.reportes.modal_catalogo_productos')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('/js/logistica/reportes/mejores_proveedores.js') }}"></script>
@endsection