@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="servicio">
    <legend class="mylegend">
        <h2>Catálogo de Servicios</h2>
        <ol class="breadcrumb">
            <li><label id="tipo_descripcion"> </li>
        </ol>
    </legend>
    {{-- <div class="container-okc"> --}}
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaServicio">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-servicio" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_servicio" primary="ids">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Código</h5>
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                    <div class="col-md-8">
                        <h5>Tipo de Servicio</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tipo_servicio" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($tipos as $tp)
                                <option value="{{$tp->id_tipo_servicio}}">{{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Cuenta de Detracción</h5>
                        <select class="form-control activation js-example-basic-single" name="id_detra_det" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($detracciones as $det)
                                <option value="{{$det->id_detra_det}}">{{$det->cod_sunat}} - {{$det->descripcion}} - {{$det->porcentaje}}%</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-4">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>                  
            </div>
        </div>
        {{-- <div id="tree"></div> --}}
    </form>
    {{-- </div> --}}
</div>
{{-- @include('almacen.servicioModal') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/servicio.js')}}"></script>
{{-- <script src="{{('/js/almacen/servicioModal.js')}}"></script> --}}
@include('layout.fin_html')