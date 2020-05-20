@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="tipo_doc">
    <legend><h2>Tipos de Documentos en Almacén</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaTiposDocsAlmacen">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Cod</th>
                            <th>Descripción</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_doc" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Codigo</h5>
                        <input type="text" class="oculto" name="usuario">
                        <input type="text" class="form-control activation" readonly name="id_tp_doc_almacen" primary="ids">
                    </div>
                    <div class="col-md-6">
                        <h5>Codigo Sunat</h5>
                        <select class="form-control activation js-example-basic-single" name="id_tp_doc" disabled="true" >
                            <option value="0">Elija una opción</option>
                            @foreach ($tp_doc as $tp)
                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tipo" readonly>
                            <option value="1" selected>Ingreso</option>
                            <option value="2">Salida</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5>Abreviatura</h5>
                        <input type="text" class="form-control activation" name="abreviatura">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/tipo_doc_almacen.js')}}"></script>
@include('layout.fin_html')