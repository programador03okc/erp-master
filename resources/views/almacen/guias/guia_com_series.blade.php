<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_barras" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:35%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ingrese la(s) Series <label id="descripcion"></label></h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="cant_items" />
                <input type="text" class="oculto" name="id_guia_com_det" />
                <input type="text" class="oculto" name="id_oc_det" />
                <input type="text" class="oculto" name="id_trans_detalle" />
                <input type="text" class="oculto" name="id_producto" />
                <input type="text" class="oculto" name="id_producto_sobrante" />
                <input type="text" class="oculto" name="id_producto_transformado" />
                <input type="text" class="oculto" name="id_detalle_transformacion" />
                <input type="text" class="oculto" name="id_detalle_devolucion" />
                <input type="text" class="oculto" name="id_almacen_detalle" />
                <input type="text" class="oculto" name="edit" />
                <input type="text" class="oculto" name="anulados" />
                <div class="row cabecera">
                    <div class="col-md-12">
                        <label>Ingrese una serie:</label>
                        <div style="width: 100%; display:flex; font-size:12px;">
                            <div style="width:83%;">
                                <input name="serie_prod" class="form-control" type="text" style="height:30px;" onKeyPress="handleKeyPress(event);">
                            </div>
                            <div style="width:17%;">
                                <button type="button" class="btn btn-warning btn-flat" id="basic-addon2" style="padding:0px;height:34px;width:98%;height:30px;font-size:12px;" onClick="agregar_serie();" data-toggle="tooltip" data-placement="right" title="Agregar Serie">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                                <!-- <button type="button" class="btn btn-danger btn-flat" id="basic-addon2" onClick="autogenerar();" data-toggle="tooltip" data-placement="right" title="Autogenerar Series">
                                    <i class="fas fa-magic"></i> Autogenerar
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row cabecera">
                    <div class="col-md-12">
                        <form id="frm-example" name="frm-example">
                            <label>Seleccione un archivo:</label>
                            <h5>El archivo debe ser de excel, en la <strong>'Hoja1'</strong> tener una columna con la palabra <strong>'serie'</strong>
                                y debajo de ella todas las series.</h5>
                            <input type="file" id="importar" class="btn btn-info filestyle" data-buttonName="btn-primary" data-buttonText="Importar" data-size="sm" data-iconName="fa fa-folder-open" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br />
                        <table class="mytable table table-striped table-condensed table-bordered" id="listaBarras">
                            <thead>
                                <tr>
                                    <td hidden></td>
                                    <td>#</td>
                                    <td width="90%">Serie</td>
                                    <td width="10%">Acción</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_barra" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_series_compra();">Guardar</button>
            </div>
        </div>
    </div>
</div>