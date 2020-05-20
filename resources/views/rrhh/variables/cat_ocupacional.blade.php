@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="cat_ocupacional">
    <legend><h2>Categoría Ocupacional</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaCatOcupacional">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="40">N°</th>
                            <th>Descripción</th>
                            <th width="70">Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-cat_ocupacional" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_categoria_ocupacional" primary="ids">
                <div class="row">
                    <div class="col-md-10">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion dela categoría">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/variables/cat_ocupacional.js')}}"></script>
@include('layout.fin_html')