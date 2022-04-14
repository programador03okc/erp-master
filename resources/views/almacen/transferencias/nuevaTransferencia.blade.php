<div class="modal fade" tabindex="-1" role="dialog" id="modal-nuevaTransferencia" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:75%;">
        <div class="modal-content">
            <form id="form-nuevaTransferencia">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Nueva Transferencia</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_transferencia_nuevo">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Almacén Origen</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen_origen_nueva" required>
                                <option value="">Elija una opción</option>
                                @foreach ($todos_almacenes as $tp)
                                <option value="{{$tp->id_almacen}}">{{$tp->codigo}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Almacén Destino</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen_destino_nueva" required>
                                <option value="">Elija una opción</option>
                                @foreach ($todos_almacenes as $tp)
                                <option value="{{$tp->id_almacen}}">{{$tp->codigo}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Concepto</h5>
                            <input type="text" class="form-control" name="concepto" required>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                id="detalleTransferencia" style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Código softlink</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Disponible</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th>
                                            <i class="fas fa-plus-circle icon-tabla green boton agregarProducto" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Producto" onClick="agregarProducto();"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_nuevaTransferencia" class="btn btn-success" value="Guardar Transferencia" />
                    <!-- <button class="btn btn-sm btn-success" onClick="generar_transferencia();">Generar Transferencia</button> -->
                </div>
            </form>
        </div>
    </div>
</div>