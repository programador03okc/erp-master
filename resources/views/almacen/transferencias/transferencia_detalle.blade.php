<div class="modal fade" tabindex="-1" role="dialog" id="modal-transferencia_detalle">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Recepción de la Transferencia <label class="subtitulo_red" id="cod_trans"></label> - Guía: <label id="guia" class="subtitulo_blue"></label></h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="id_transferencia" >
                <input type="text" class="oculto" name="id_guia_ven" >
                <input type="text" class="oculto" name="estado" >
                <input type="text" class="oculto" name="guia_ingreso_compra" >
                <div class="row">
                    <div class="col-md-3">
                        <h5>Fecha Almacén</h5>
                        <input type="date" class="form-control" name="fecha_almacen" value="<?=date('Y-m-d');?>"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Almacén Destino</h5>
                        <input type="text" class="oculto" name="id_almacen_destino"/>
                        <input type="text" class="form-control" name="almacen_destino" disabled="true"/>
                    </div>
                    <div class="col-md-4">
                        <h5>Responsable Destino</h5>
                        <select class="form-control" name="responsable_destino" style="width:200px;">
                            <option value="0">Elija una opción</option>
                            @foreach ($usuarios as $usu)
                                <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <br/>
                <table class="mytable table table-striped table-condensed table-bordered table-okc-view" 
                id="listaTransferenciaDetalle">
                    <thead>
                        <tr>
                            <td width="3%">Nro</td>
                            <td width="10%">Código</td>
                            <td>PartNumber</td>
                            <td>Categoría</td>
                            <td>SubCategoría</td>
                            <td>Descripción</td>
                            <td class="right">Cant.Enviada</td>
                            <td>Cant.Recibida</td>
                            <td>Und</td>
                            <td width="10%">Ubicación</td>
                            <td width="10%">Observación</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_det" style="display: none;"></label>
                <button id="submit_transferencia" class="btn btn-sm btn-success" onClick="recibir();"></button>
            </div>
        </div>
    </div>
</div>