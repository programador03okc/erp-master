@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Proveedores con Producto Determinado
@endsection

@section('content')
<div class="page-main" type="reporte-proveedores_producto_determinado">
    <legend>
        <div class="row">
            <div class="col-xs-12 col-md-7"><h2>Reporte - Proveedores con Producto Determinado</h2></div>  
        </div>
    </legend>
    <form id="form-proveedores_producto_determinado" type="register" form="formulario">
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
        </div>
 
        <br>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" onClick="reporteProveedoresProductoDeterminado(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom"  title="Ejecutar">
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
                id="listaProveedoresProductoDeterminado">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>#</th>
                        <th>Razon Social</th>
                        <th>Documento</th>
                        <th>Dirección</th>
                        <th>Telefono</th>
                        <th>País</th>
                        <th>Contacto</th>
                    </tr>
                    
                </thead>
                <tbody>
 
 
                </tbody>
            </table>
        </div>
    </div>
 
    </form>




    <div class="modal fade" tabindex="-1" role="dialog" id="modal-lista-contacto">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Contacto</h3>
            </div>
            <div class="modal-body">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                        id="listaContacto">
                        <thead>
                            <tr>
                                <th hidden></th>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>Cargo</th>
                                <th>Correo</th>
                                <th>Telefono</th>
                                <th>Tipo Establecimiento</th>
                                <th>Dirección</th>
                            </tr>
                            
                        </thead>
                        <tbody>
        
        
                        </tbody>
                    </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

 
 
</div>


@include('layout.footer')
@include('layout.scripts')
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
    <script src="{{ asset('/js/logistica/reportes/proveedores_producto_determinado.js') }}"></script>
@endsection