@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="kardex_series">
    <legend class="mylegend">
        <h2>Movimientos de Series</h2>
    </legend>
    <div class="row">
        <div class="col-md-5">
            <h5>Ingrese el Número de Serie</h5>
            <input type="text" class="form-control" name="serie" placeholder="Ingrese un Nro. de Serie..."/>
        </div>
        <div class="col-md-7">
            <h5>Seleccione el producto</h5>
            <div class="input-group-okc">
                <input class="oculto" name="id_producto"/>
                <input type="text" class="form-control" placeholder="Ingrese la descripción de un producto..." 
                    aria-describedby="basic-addon2" name="descripcion"/>
                {{-- <div class="input-group-append">
                    <button type="button" class="input-group-text" id="basic-addon2" onClick="productoModal();">
                        <i class="fa fa-search"></i>
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Kardex" 
                onClick="listarKardexSeries();">Actualizar Kardex</button>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Exportar Kardex" 
                onClick="download_kardex_excel();">Excel</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id ="datos_producto" class="table-group">
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaKardexSeries">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Serie</th>
                        <th>Descripción</th>
                        <th>Fec.Ingreso</th>
                        <th>Doc.Ingreso</th>
                        <th>Proveedor</th>
                        <th>Alm.Ingreso</th>
                        <th>Fec.Salida</th>
                        <th>Doc.Salida</th>
                        <th>Cliente</th>
                        <th>Alm.Salida</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('almacen.producto.productoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/kardex_series.js')}}"></script>
<script src="{{('/js/almacen/productoModal.js')}}"></script>
@include('layout.fin_html')