@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="retencion">
    <legend><h2>Retenciones al Personal</h2></legend>
    <form id="form-retencion" type="register" form="formulario">
        <input type="hidden" name="id_retencion" primary="ids">
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
                <input type="date" class="form-control activation" name="fecha" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Tipo Retención</h5>
                <select class="form-control activation" name="id_variable_retencion" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($reten as $reten)
                        <option value="{{$reten->id_variable_retencion}}">{{$reten->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Afecto</h5>
                <select class="form-control activation" name="afecto" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                    <option value="SI">Si</option>
                    <option value="NO">No</option>
                </select>
            </div>
            <div class="col-md-2">
                <h5>Importe</h5>
                <input type="number" class="form-control activation" name="importe" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Motivo</h5>
                <textarea class="form-control activation" name="motivo" disabled="true"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaRetencionTrab" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>TIPO</th>
                            <th>AFECTO</th>
                            <th width="120">IMPORTE</th>
                            <th>FECHA</th>
                        </tr>
                    </thead>
                    <tbody id="trab-retencion">
                        <tr><td></td><td colspan="4"> No hay datos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/remuneraciones/retencion.js')}}"></script>
@include('layout.fin_html')