@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Compras por Proveedor
@endsection

@section('content')
<div class="page-main" type="reporte-compras_por_proveedor">
    <legend>
        <div class="row">
            <div class="col-xs-12 col-md-7"><h2>Reporte - Compras por Proveedor</h2></div>  
        </div>
    </legend>
    <form id="form-compras_por_proveedor" type="register" form="formulario">
        <input type="hidden" name="id_proveedor">

        <div class="row">
            <div class="col-md-6">
                <h5>Razon Social (opcional)</h5>
                    <select class="form-control js-example-basic-single" name="razon_social_proveedor">
                        <option value="0" data-ruc="0">Elija una opción</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{$proveedor->id_proveedor}}" data-numero-ruc="{{$proveedor->nro_documento}}">{{$proveedor->razon_social}}</option>
                        @endforeach
                    </select>
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
                        <option value="MENSUAL" >Mensual</option>
                        <!-- <option value="ANUAL" >Anual</option> -->
                 </select>
            </div>
            <div class="col-md-2">
                <h5>Año</h5>
                <div class="input-group-okc">
                    <input type="text" class="form-control input-sm" name="año" placeholder="">
                </div>
            </div>
        </div>
 
        <br>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" onClick="reporteComprasPorProveedor(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom"  title="Ejecutar">
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
                id="listaComprasPorProveedor">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>#</th>
                        <th>Razon Social - RUC </th>
                        <th>Enero</th>
                        <th>Febrero</th>
                        <th>Marzo</th>
                        <th>Abril</th>
                        <th>Mayo</th>
                        <th>Junio</th>
                        <th>Julio</th>
                        <th>Agosto</th>
                        <th>Septiembre</th>
                        <th>Octubre</th>
                        <th>Noviembre</th>
                        <th>Diciembre</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
 
    </form>

        
 
</div>


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
    <script src="{{ asset('/js/logistica/reportes/compras_por_proveedor.js') }}"></script>
@endsection