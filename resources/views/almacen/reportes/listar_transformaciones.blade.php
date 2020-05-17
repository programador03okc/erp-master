@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="transformaciones">
    <legend class="mylegend">
        <h2>Transformaciones en Almacén</h2>
    </legend>
    <div class="row">
        <div class="col-md-4">
            <h5>Almacén</h5>
            <select class="form-control activation" name="id_almacen">
                <option value="0">Elija una opción</option>
                @foreach ($almacenes as $alm)
                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <h5>Actualizar</h5>
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Actualizar" 
                onClick="listarTransformaciones();">Actualizar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaTransformaciones">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Fecha Trans.</th>
                        <th>Código</th>
                        <th>Serie-Número</th>
                        <th>Empresa</th>
                        <th>Almacén</th>
                        <th>Responsable</th>
                        <th>Registrado por</th>
                        <th>Estado</th>
                        <th width="10%">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('almacen.reportes.transferencia_detalle') --}}
{{-- @include('almacen.guias.guia_venta') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/listar_transformaciones.js')}}"></script>
{{-- <script src="{{('/js/almacen/transferencia_detalle.js')}}"></script> --}}
{{-- <script src="{{('/js/almacen/guia_venta.js')}}"></script> --}}
@include('layout.fin_html')