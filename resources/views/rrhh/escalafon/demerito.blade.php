@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="sancion">
    <legend><h2>Deméritos del Personal</h2></legend>
    <form id="form-sancion" type="register" form="formulario">
        <input type="hidden" name="id_sancion" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar DNI</h5>
                <input type="hidden" class="form-control" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5>Nombre del trabajador</h5>
                <input type="text" class="form-control" name="datos_trabajador" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Fecha</h5>
                <input type="date" class="form-control activation" name="fecha_sancion" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Tipo demérito</h5>
                <select class="form-control activation" name="id_variable_sancion" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($sanci as $sanci)
                        <option value="{{$sanci->id_variable_sancion}}">{{$sanci->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <h5>Concepto</h5>
                <input type="text" class="form-control activation" name="concepto" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <h5>Motivo</h5>
                <textarea class="form-control activation" name="motivo" disabled="true"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaSancionTrab" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>TIPO DEMERITO</th>
                            <th>CONCEPTO</th>
                            <th width="90">FECHA</th>
                        </tr>
                    </thead>
                    <tbody id="trab-sancion">
                        <tr><td></td><td colspan="3"> No hay datos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/demerito.js')}}"></script>
@include('layout.fin_html')