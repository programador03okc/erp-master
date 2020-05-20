@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="categoria">
    <legend><h2>Categoría</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <form id="form-categoria" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-2">
                        <h5>Codigo</h5>
                        <input type="hidden" class="form-control" name="id_categoria" primary="ids">
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                    <div class="col-md-4">
                        <h5>Tipo</h5>
                        <select class="form-control" name="id_tipo_producto" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{$tipo->id_tipo_producto}}">{{$tipo->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-3">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-2">
                        <h5 id="estado">Estado: <label></label></h5>
                    </div>
                    <div class="col-md-4">
                        <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('almacen.producto.categoriaModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/categoriaModal.js')}}"></script>
<script src="{{('/js/almacen/categoria_producto.js')}}"></script>
@include('layout.fin_html')