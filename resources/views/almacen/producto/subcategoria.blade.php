@extends('layout.main')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    SubCategoría
@endsection

@section('content')
<div class="page-main" type="subcategoria">
    <legend><h2>SubCategoría</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSubCategoria">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            {{-- <th>Estado</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-subcategoria" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Codigo</h5>
                        <input type="hidden" class="form-control" name="id_subcategoria" primary="ids">
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-4">
                        <h5>Tipo</h5>
                        <input type="text" class="form-control" readonly name="tipo_descripcion">                        
                    </div>
                    <div class="col-md-5">
                        <h5>Categoria</h5>
                        <div class="input-group-okc">
                            <input type="hidden" class="form-control" name="id_categoria">
                            <input type="text" class="form-control" readonly 
                                aria-describedby="basic-addon2" name="cat_descripcion">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text" id="basic-addon2" onClick="categoriaModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                    {{-- <div class="col-md-3">
                        <h5>Estado</h5>
                        <select class="form-control activation" name="estado" readonly>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="nombre_corto">Registrado por: <label></label></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
    <script src="{{('/js/almacen/producto/subcategoria_producto.js')}}"></script>
@endsection
