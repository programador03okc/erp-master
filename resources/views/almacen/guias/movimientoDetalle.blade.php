<div class="modal fade" tabindex="-1" role="dialog" id="modal-movAlmDetalle" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ingreso a almacén</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" name="id_guia_com_detalle" class="oculto" />
                        <input type="text" name="id_mov_alm" class="oculto" />
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <label class="lbl-codigo" title="Abrir Ingreso" onClick="abrirIngreso()" id="cabecera">
                                </label>
                            </div>
                            <div class="panel-body">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <th colSpan="2">Guía de compra: </th>
                                            <td colSpan="3"><span id="guia_com"></span></td>
                                            <th colSpan="2">Almacén: </th>
                                            <td colSpan="2"><span id="almacen_descripcion"></span></td>
                                            <th colSpan="2">Fecha emisión: </th>
                                            <td colSpan="3"><span id="fecha_emision"></span></td>
                                        </tr>
                                        <tr>
                                            <th colSpan="2">Proveedor: </th>
                                            <td colSpan="3"><span id="prov_razon_social"></span></td>
                                            <th colSpan="2">Tipo de Operación: </th>
                                            <td colSpan="2"><span id="operacion_descripcion"></span></td>
                                            <th colSpan="2">Ordenes de Compra: </th>
                                            <td colSpan="3"><span id="ordenes_compra"></span></td>
                                        </tr>
                                        <tr>
                                            <th colSpan="2">Responsable: </th>
                                            <td colSpan="3"><span id="responsable_nombre"></span></td>
                                            <th colSpan="2">Requerimientos: </th>
                                            <td colSpan="2"><span id="requerimientos"></span></td>
                                            <th colSpan="2">Ordenes SoftLink: </th>
                                            <td colSpan="3"><span id="ordenes_soft_link"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleMovimiento" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>PartNumber</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid</th>
                                            <th>Guía Compra</th>
                                            <th>OC/HT/Tr</th>
                                            <th>Requerimiento</th>
                                            <th>Sede Req.</th>
                                            <th width="80px">Series</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>