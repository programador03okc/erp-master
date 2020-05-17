@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="prestamo">
    <legend><h2>Prestamos al Personal</h2></legend>
    <form id="form-prestamo" type="register" form="formulario">
        <input type="hidden" name="id_prestamo" primary="ids">
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
        </div>
        <div class="row">
            <div class="col-md-9">
                <h5>Concepto</h5>
                <input type="text" class="form-control activation" name="concepto" disabled="true" maxlength="150">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Fecha</h5>
                <input type="date" class="form-control activation" name="fecha_prestamo" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Monto</h5>
                <input type="number" class="form-control activation" name="monto_prestamo" step="any" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>(%) Interes</h5>
                <input type="number" class="form-control activation" name="porcentaje" step="any" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>N° Cuotas</h5>
                <input type="number" class="form-control activation" name="nro_cuotas" step="any" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaPrestamoTrab" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>CONCEPTO</th>
                            <th width="140">IMPORTE</th>
                            <th width="100">N° CUOTAS</th>
                            <th width="100">FECHA</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr><td></td><td colspan="4"> No hay datos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/prestamo.js')}}"></script>
@include('layout.fin_html')