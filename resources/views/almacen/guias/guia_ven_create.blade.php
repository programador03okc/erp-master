<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_ven_create" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:85%;">
        <div class="modal-content">
            <form id="form-guia_ven_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Guia de Venta - <strong><span id="name_title"></span></strong>
                        <strong><span id="codigo_req"></span></strong> (<span id="almacen_req"></span>)</h3>
                        <span id="mensaje" class="red"></span>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_od">
                    <input type="text" class="oculto" name="id_sede">
                    <input type="text" class="oculto" name="id_persona">
                    <input type="text" class="oculto" name="id_requerimiento">
                    <input type="text" class="oculto" name="id_devolucion">
                    <div class="row">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token_guia">
                        <div class="col-md-3">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="oculto" name="id_serie_numero">
                                <input type="text" class="form-control" name="serie" onBlur="ceros_numero_ven('serie');" placeholder="0000" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="numero" onBlur="ceros_numero_ven('numero');" placeholder="000000" required>
                                <!-- onBlur="ceros_numero_guia();"  -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control" name="fecha_emision" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Salida</h5>
                            <input type="date" class="form-control" name="fecha_almacen" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <h5>Almacén</h5>
                            <input type="text" class="oculto " name="id_almacen" required>
                            <input type="text" class="form-control " name="almacen_descripcion" readOnly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Cliente</h5>
                            <div style="display:flex;">
                                <input type="text" class="oculto" name="id_cliente">
                                <input type="text" class="form-control" name="razon_social_cliente" disabled>
                                <button type="button" class="input-group-text activation" id="basic-addon1" onclick="openClienteModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control js-example-basic-single" name="id_operacion" required >
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Punto de partida</h5>
                            <input type="text" class="form-control" name="punto_partida">
                        </div>
                        <div class="col-md-3">
                            <h5>Punto de llegada</h5>
                            <input type="text" class="form-control" name="punto_llegada">
                        </div>
                        {{-- <div class="col-md-3">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <select class="form-control" name="id_guia_clas" required>
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones as $clas)
                                <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <br>
                            <label>* Marque con un check los items que va a dar salida.</label>
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="detalleGuiaVenta" style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>PartNumber</th>
                                        <th>Descripción</th>
                                        <th>Alm. Reserva</th>
                                        <th>Reserva</th>
                                        <th>Cant. despachada</th>
                                        <th>Cantidad</th>
                                        <th>Unid</th>
                                        <th>Series</th>
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
                    <button class="btn btn-sm btn-success" onClick="guardar_guia_ven_create();">Guardar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>
