@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="saldos_pres">
    <legend class="mylegend">
        <h2>Detalle del Consumo de un Presupuesto</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Buscar Presupuesto de Ejecución" 
                onClick="estPresejeModal();">Buscar</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-2">
            <h5>Código</h5>
            <input type="text" name="cod_preseje" class="form-control" readOnly/>
        </div>
        <div class="col-md-6">
            <h5>Descripción</h5>
            <input type="text" name="descripcion" class="form-control" readOnly/>
        </div>
        <div class="col-md-4">
            <h5>Cliente</h5>
            <input type="text" name="razon_social" class="form-control" readOnly/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="borde-group-verde">
                <table width="100%" id="totales"  style="font-size: 14px; margin-bottom: 0px;">
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                id="listaEstructura" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th class="right">Imp.Total</th>
                        <th class="right">Imp.OC/OS</th>
                        <th class="right">Saldo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.reportes.verDetallePartida')
@include('proyectos.reportes.estPresejeModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/reportes/saldos_pres.js')}}"></script>
<script src="{{('/js/proyectos/reportes/verDetallePartida.js')}}"></script>
<script src="{{('/js/proyectos/reportes/estPresejeModal.js')}}"></script>
@include('layout.fin_html')