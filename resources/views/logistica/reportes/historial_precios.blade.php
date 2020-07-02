@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Historial de Precios
@endsection

@section('content')

<div class="page-main" type="reporte-historial_precios">
    <legend>
        <div class="row">
            <div class="col-xs-12 col-md-7"><h2>Reporte - Historial de Precios</h2></div>  
        </div>
    </legend>
    <form id="form-historial_precios" type="register" form="formulario">
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
                <select class="form-control input-sm" name="empresa" disabled>
                    <option value="0" data-ruc="0">Elija una opción</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{$empresa->id_empresa}}" >{{$empresa->razon_social}}</option>
                        @endforeach
                </select>
            </div>
            
        </div>

        <div class="row">
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
                <button class="btn btn-success" onClick="reporteHistorialPrecios(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom"  title="Ejecutar">
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
                id="listaHistorialPrecios">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>#</th>
                        <th>Item</th>
                        <th>Producto</th>
                        <th>Unidad</th>
                        <th>Precio Unitario</th>
                        <th>Proveedor</th>
                        <th>Documento</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
    <script src="{{ asset('/js/logistica/reportes/historial_precios.js') }}"></script>
@endsection