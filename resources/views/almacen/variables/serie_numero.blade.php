@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="serie_numero">
    <legend><h2>Series-Numeros de Documentos</h2></legend>
        <div class="row">
            <div class="col-md-7">
                <fieldset class="group-table">
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" 
                        id="listaSerieNumero">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Tipo Doc</th>
                                <th>Empresa-Sede</th>
                                <th>Serie</th>
                                <th>Numero</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
            <div class="col-md-5">
                <form id="form-serie_numero" type="register" form="formulario">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_serie_numero" primary="ids">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control activation js-example-basic-single" name="id_tp_documento" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($tipos as $tp)
                                    <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Empresa-Sede</h5>
                            <select class="form-control activation js-example-basic-single" name="id_sede" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($sedes as $tp)
                                    <option value="{{$tp->id_sede}}">{{$tp->razon_social}} - {{$tp->codigo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="serie" 
                                    onBlur="ceros_serie('serie');" placeholder="0000" disabled="true">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="numero" 
                                    onBlur="ceros_numero('numero');" placeholder="0000000" disabled="true">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Crear Números</h6></legend>
                                <div class="input-group">
                                    <span class="input-group-addon"> Desde: </span>
                                    <input type="number" name="numero_desde" class="form-control activation" disabled="true">
                                    <span class="input-group-addon"> Hasta: </span>
                                    <input type="number" name="numero_hasta" class="form-control activation" disabled="true">
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- </div> --}}
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/variables/serie_numero.js')}}"></script>
@include('layout.fin_html')