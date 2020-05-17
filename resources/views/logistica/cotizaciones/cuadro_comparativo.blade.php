@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="cuadro_comparativo">
    <legend>
        <h2>Cuadro Comparativo</h2>
    </legend>
    <form id="form-cuadro_comparativo" type="register" form="formulario">
        <input type="hidden" name="id_grupo_cotizacion">

        <div class="row">
            <div class="col-md-12">
                <h5>Lista de cotizaciones por grupo</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaCuadroComparativos" width="100%">
                    <thead>
                        <tr>
                            <th hidden></th>
                            <th width="20">#</th>
                            <th width="120">COD. COTIZACIÓN</th>
                            <th width="120">COD. CUADRO COMP.</th>
                            <th width="120">EMPRESA</th>
                            <th width="120">COD. REQUERIMIENTO</th>
                            <th width="100">PROVEEDOR</th>
                            <th width="100">FECHA REGISTRO</th>
                            <th width="100">ESTADO</th>
                            <th width="10">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr>
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary btn-sm" title="Mostrar" name="btnMostrarCuadroComparativo" disabled>
                                        <i class="fas fa-table"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" title="Descargar" name="btnDescargarCuadroComparativo" disabled>
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


</div>

<div class="row">
    <div class="col-sm-12">

    </div>
</div>

<div class="row">
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
               <h4>Cuadro Comparativo</h4> 
            </div>
            <div class="panel-body"></div>

            <div id="head-cuadro"></div>
            <br>

            <table class="mytable table table-condensed table-bordered table-okc-view" id="cuadro_comparativo">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <legend>
        <h4> <strong>Buena Pro</strong></h4>
        </legend>

        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div id="panel-buena_pro"></div>
        </div>

    </div>
</div>

<div id="btn-action-buena_pro"></div>

</form>


</div>

@include('logistica.cotizaciones.modal_comparar_variables_proveedor')
@include('logistica.cotizaciones.modal_ultimas_compras')
<!-- @include('logistica.cotizaciones.modal_comparative_board_enabled_to_value') -->
@include('logistica.cotizaciones.modal_buena_pro')
<!-- @include('logistica.cotizaciones.modal_historial_cuadro_comparativo') -->
@include('logistica.cotizaciones.modal_valorizar_cotizacion')
@include('logistica.cotizaciones.modal_valorizacion_especificacion')

@include('layout.footer')
@include('layout.scripts')
<script src="{{ asset('/js/logistica/cuadro_comparativo/index.js')}}"></script>
<!-- <script src="{{ asset('/js/logistica/cotizacionModal.js')}}"></script> -->
@include('layout.fin_html')