<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-items-para-compra">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-agregar-items-para-compra" onClick="$('#modal-agregar-items-para-compra').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Item's Base<span id="codigo_requeriento_seleccionado"></span></h3>
            </div>
            <div class="modal-body">
                <fieldset class="group-table">   
                    <div class="row">
                        <div class="col-sm-12">
                            <fieldset class="group-importes"><legend style="background: #557092;"><h6>Item's Base</h6></legend>
                            <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaItemsParaComprar" width="100%" style="width: 100%;background: #ecf1f3;">
                                <thead>
                                    <tr>
                                        <th class="invisible">#</th>
                                        <th width="70">CODIGO</th>
                                        <th width="70">PART NUMBER</th>
                                        <th width="70">CATEGORIA</th>
                                        <th width="70">SUBCATEGORIA</th>
                                        <th width="70">CLASIFICACIÓN</th>
                                        <th width="200">DESCRIPCION</th>
                                        <th width="60">UNIDAD</th>
                                        <th width="70">CANTIDAD</th>
                                        <th width="120">ACCIÓN</th>
                                </tr>
                            </thead>
                                <tbody id="body_detalle_requerimiento">
                                    <tr id="default_tr">
                                        <td></td>
                                        <td colspan="11"> No hay datos registrados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <br>
                <fieldset class="group-table" id="group-detalle-cuadro-costos">
                        <div class="row">
                            <div class="col-sm-12">
                                <fieldset class="group-importes" ><legend style="background: #5d4d6d;"><h6 name='titulo_tabla_detalle_cc'>Detalles de cuadro de Costos</h6></legend>
                                <table class="mytable table table-striped table-condensed table-bordered dataTable no-footer" id="ListaModalDetalleCuadroCostos" width="100%" style="width: 100%;background: #f8f3f9;">
                                    <thead>
                                        <tr>
                                            <th>Part No.</th>
                                            <th>Descripción</th>
                                            <th>P.V.U. O/C (sinIGV) S/</th>
                                            <th>Flete O/C (sinIGV) S/</th>
                                            <th>Cant.</th>
                                            <th>Garant. meses</th>
                                            <th>Proveedor seleccionado</th>
                                            <th>Creado Por</th>
                                            <th>Fecha Creación</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset> 

                    <div class="row">
                        <div class="col-md-12 right">
                            <button class="btn btn-success" role="button"   id="btnIrACrearOrden" onClick="irACrearOrden();" disabled>
                                Guardar y Continuar <i class="fas fa-chevron-circle-right"></i>
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>



