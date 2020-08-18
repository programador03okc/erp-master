<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_create">
    <div class="modal-dialog" style="width:900px;">
        <div class="modal-content" >
            <form id="form-guia_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Guía de Compra</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_orden_compra">
                    <input type="text" class="oculto" name="id_proveedor">
                    <input type="text" class="oculto" name="id_sede">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                    name="serie" onBlur="ceros_numero('serie');" placeholder="0000" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" 
                                    name="numero" onBlur="ceros_numero('numero');" placeholder="0000000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control" name="fecha_emision" value="<?=date('Y-m-d');?>" required>
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha de Almacén</h5>
                            <input type="date" class="form-control" name="fecha_almacen" value="<?=date('Y-m-d');?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén</h5>
                            <select class="form-control js-example-basic-single" name="id_almacen" required>
                                <!-- @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->codigo}} - {{$alm->descripcion}}</option>
                                @endforeach -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control js-example-basic-single" name="id_operacion" required>
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                    <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <select class="form-control" name="id_guia_clas" required>
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones as $clas)
                                    <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                id="detalleOrdenSeleccionadas"  style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>O.C.</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Categoría</th>
                                        <th>SubCategoría</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <input type="submit" id="submit_guia" class="btn btn-success" value="Guardar"/>
                    <!-- <label id="mid_doc_com" style="display: none;"></label>
                    <button class="btn btn-sm btn-success" onClick="guardar_guia_create();">Guardar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>