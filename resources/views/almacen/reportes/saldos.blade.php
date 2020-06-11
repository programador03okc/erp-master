@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="saldos">
    <legend class="mylegend">
        <h2>Reporte de Saldos Actuales por Almacén</h2>
        <ol class="breadcrumb">
            <li>
                <!-- {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="downloadKardexSunat();">Saldos por Almacén</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button> --}} -->
            </li>
        </ol>
    </legend>
    <div class="row">
        <!-- <div class="col-md-3">
            <h5>Saldo al:</h5>
            <input type="date" class="form-control" name="fecha">
        </div> -->
        <div class="col-md-6">
            <h5>Almacén</h5>
            <div style="display:flex;">
                <select class="form-control js-example-basic-single" name="almacen">
                    @foreach ($almacenes as $alm)
                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="listarSaldos();">Buscar</button>
            </div>
        </div>
        <div class="col-md-2">
            <h5>Tipo de Cambio Compra</h5>
            <input type="text" class="form-control" name="tipo_cambio" disabled/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSaldos">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Cód.Anexo</th>
                        <th>Cód.Antiguo</th>
                        <th>Descripción</th>
                        <th>Und</th>
                        <th>Stock</th>
                        <th>Mnd</th>
                        <th>Soles</th>
                        <th>Dolar</th>
                        <th>Costo Promedio</th>
                        <th>Reserva</th>
                        <th>Ubicación</th>
                        <th>Clasificación</th>
                        <th>Categoría</th>
                        <th>SubCategoría</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('almacen.kardex_filtro') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/reporte/saldos.js')}}"></script>
@include('layout.fin_html')