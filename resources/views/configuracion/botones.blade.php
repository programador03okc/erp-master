@include('layout.head')
@include('layout.menu_config')
@include('layout.body')
<div class="page-main" type="botones">
    <legend><h2>Botones del menú</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaBotones">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="40">N°</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-botones" type="register" form="formulario">
                <input type="hidden" name="id_boton" primary="ids">
                <div class="row">
                    <div class="col-md-10">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Descripcion del boton">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/configuracion/botones.js')}}"></script>
@include('layout.fin_html')