<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_create" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:85%;">
        <div class="modal-content">
            <form id="form-guia_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="titulo">Ingresar Guía de Compra</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_orden_compra">
                    <input type="text" class="oculto" name="id_proveedor">
                    <input type="text" class="oculto" name="id_sede">
                    <input type="text" class="oculto" name="id_transformacion">
                    <input type="text" class="oculto" name="id_devolucion">
                    <input type="text" class="oculto" name="id_od">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" name="serie" onBlur="ceros_numero('serie');" placeholder="0000" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="numero" onBlur="ceros_numero('numero');" placeholder="0000000" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Proveedor</h5>
                            <input type="text" class="form-control" name="razon_social_proveedor" disabled>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control" name="fecha_emision" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Ingreso</h5>
                            <input type="date" class="form-control" name="fecha_almacen" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Almacén</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen" id="id_almacen" required>
                            </select>
                        </div>
                        <div class="col-md-3 compra">
                            <h5>Tipo de Operación</h5>
                            <input type="text" class="oculto" name="id_operacion" >
                            <input type="text" class="form-control" name="nombre_operacion" disabled>
                        </div>
                        <div class="col-md-3 transformacion">
                            <h5>Tipo de Operación</h5>
                            <input type="text" class="oculto" name="id_operacion" >
                            <input type="text" class="form-control" name="nombre_operacion" disabled>
                        </div>
                        <div class="col-md-3 devolucion">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control js-example-basic-single" name="id_operacion" >
                                <option value="">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                @if($tp->cod_sunat != 12)
                                <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <select class="form-control" name="id_guia_clas" readOnly required>
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones_guia as $clas)
                                <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 transformacion">
                            <h5>Moneda/Tipo cambio</h5>
                            <div style="display: flex;">
                                <select class="form-control" name="moneda_transformacion" >
                                    <option value="">Elija una opción</option>
                                    @foreach ($monedas as $clas)
                                    <option value="{{$clas->id_moneda}}">{{$clas->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control" name="tipo_cambio_transformacion" step="0.0001" readonly/>
                            </div>
                        </div>
                        <div class="col-md-2 devolucion">
                            <h5>Moneda/Tipo cambio</h5>
                            <div style="display: flex;">
                                <select class="form-control" name="moneda_devolucion" >
                                    <option value="">Elija una opción</option>
                                    @foreach ($monedas as $clas)
                                    <option value="{{$clas->id_moneda}}">{{$clas->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control" name="tipo_cambio_devolucion" step="0.0001" readonly/>
                            </div>
                        </div>
                        <div class="col-md-1 orden_transformacion">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <br>
                            <label>* Marque con un check los items que va a dar ingreso.</label>
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleOrdenSeleccionadas" style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>OC/HT</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <!-- <th>Categoría</th>
                                        <th>SubCategoría</th> -->
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th>Unitario</th>
                                        <th>Total</th>
                                        <th width="5%">
                                            <!-- <i class="fas fa-plus-circle icon-tabla green boton agregarSobrante" data-toggle="tooltip" data-placement="bottom" title="Agregar Sobrante" onClick="productoModal();"></i> -->
                                            <i class="fas fa-sync-alt" style="cursor:pointer;" title="Actualizar lista de items" onClick="actualizarDetalle();"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comentario</h5>
                            <textarea class="form-control" name="comentario"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_guia" class="btn btn-success" value="Guardar" />
                    <!-- <label id="mid_doc_com" style="display: none;"></label>
                    <button class="btn btn-sm btn-success" onClick="guardar_guia_create();">Guardar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>