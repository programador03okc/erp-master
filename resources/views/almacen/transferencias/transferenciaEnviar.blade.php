<div class="modal fade" tabindex="-1" role="dialog" id="modal-transferenciaGuia" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">
            <form id="form-transferenciaGuia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Transferencia entre Almacenes - Guía Venta</h3>
                </div>
                <div class="modal-body">
                    <!-- <input type="text" class="oculto" name="id_guia_ven"/> -->
                    <input type="text" class="oculto" name="id_sede">
                    <input type="text" class="oculto" name="id_mov_alm">
                    <input type="text" class="oculto" name="id_guia_com">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_transferencia">
                    <div class="row">
                        <div class="col-md-3">
                            <!-- <div class="form-group"> -->
                            <h5>Serie-Número</h5>
                            <input type="text" class="oculto" name="id_serie_numero">
                            <div class="input-group">
                                <div class="form-group">
                                    <input type="text" class="form-control activation handleChangeSerie" name="trans_serie" placeholder="0000" onBlur="ceros_numero_trans('serie','transferencia');">
                                </div>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation handleChangeNumero" name="trans_numero" placeholder="0000000" onBlur="ceros_numero_trans('numero','transferencia');">
                            </div>
                            <!-- </div> -->
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision">
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha Salida</h5>
                            <input type="date" class="form-control" name="fecha_almacen">
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo de Operación</h5>
                            <input type="text" class="form-control" name="operacion" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Almacén Origen</h5>
                            <input type="text" class="oculto" name="id_almacen_origen">
                            <input type="text" class="form-control" name="almacen_origen_descripcion" disabled>
                        </div>
                        <div class="col-md-3">
                            <h5>Almacén Destino</h5>
                            <input type="text" class="oculto" name="id_almacen_destino">
                            <input type="text" class="form-control" name="almacen_destino_descripcion" disabled>
                        </div>
                        <div class="col-md-3">
                            <h5>Transportista</h5>
                            <input type="text" class="oculto" name="id_transportista">
                            <div style="display:flex;">
                                <input type="text" class="form-control" name="transportista" disabled>
                                <button type="button" class="input-group-text activation" id="basic-addon1" onClick="openTransportistaModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Guía Transportista</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="tra_serie" placeholder="0000" onBlur="ceros_numero_trans('serie','transporte');">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="tra_numero" placeholder="0000000" onBlur="ceros_numero_trans('numero','transporte');">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Punto de partida</h5>
                            <input type="text" class="form-control" name="punto_partida">
                        </div>
                        <div class="col-md-3">
                            <h5>Punto de llegada</h5>
                            <input type="text" class="form-control" name="punto_llegada">
                        </div>
                        <div class="col-md-3">
                            <h5>Placa</h5>
                            <input type="text" class="form-control" name="placa">
                        </div>
                        <div class="col-md-3">
                            <h5>Responsable Destino:</h5>
                            <select class="form-control js-example-basic-single " name="responsable_destino_trans">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleTransferencia" style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Transfe.</th>
                                        <th>Requerimiento</th>
                                        <!-- <th>Concepto</th> -->
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th>Series</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_transferencia" class="btn btn-success" value="Guardar" />
                </div>
            </form>
        </div>
    </div>
</div>