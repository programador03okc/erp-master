@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="tipo_almacen">
    <legend><h2>Tipos de Almacén</h2></legend>
    {{-- <div class="container-okc"> --}}
        <div class="row">
            <div class="col-md-7">
                <fieldset class="group-table">
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaTipoAlmacen">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
            <div class="col-md-5">
                <form id="form-tipo_almacen" type="register" form="formulario">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_tipo_almacen" primary="ids">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <input type="text" class="form-control activation" name="descripcion" disabled="true">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- </div> --}}
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/ubicacion/tipo_almacen.js')}}"></script>
@include('layout.fin_html')