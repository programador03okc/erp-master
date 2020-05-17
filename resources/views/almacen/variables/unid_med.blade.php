@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="unidmed">
    <legend><h2>Unidades de Medida</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaUnidMed">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Abreviatura</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-unidmed" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Id</h5>
                        <input type="text" class="form-control" readOnly name="id_unidad_medida" primary="ids">
                    </div>
                    <div class="col-md-4">
                        <h5>Abreviatura</h5>
                        <input type="text" class="form-control activation"  name="abreviatura" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div> --}}
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/unid_med.js')}}"></script>
@include('layout.fin_html')