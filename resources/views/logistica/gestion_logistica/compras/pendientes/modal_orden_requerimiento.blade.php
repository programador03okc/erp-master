<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden-requerimiento">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-orden-requerimiento" onClick="$('#modal-orden-requerimiento').modal('hide');"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Generar Orden<span></span></h3>
            </div>
            <div class="modal-body">
 
                <form id="form-modal-orden-requerimiento" type="register" form="formulario">
                    <input class="oculto" name="id_requerimiento"/>
                    <div class="row">
                        <div class="col-md-3"  id="group-tipo_orden">
                            <h5>Tipo de Orden</h5>
                            <select class="form-control" 
                                name="id_tipo_doc" disabled>
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_documento as $tp)
                                    @if($tp->descripcion == 'Orden de Compra')
                                            <option value="{{$tp->id_tp_documento}}" selected>{{$tp->descripcion}}</option>
                                    @else
                                            <option value="{{$tp->id_tp_documento}}">{{$tp->descripcion}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Condición</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento activation" name="id_condicion" onchange="handlechangeCondicion(event);"
                                    style="width:120px;text-align:center;" >
                                    @foreach ($condiciones as $cond)
                                        <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="plazo_dias"  class="form-control activation group-elemento" style="text-align:right; width:50px; " disabled />
                                <input type="text" value="días" class="form-control group-elemento" style="width:40px;text-align:center;" />
                            </div>
                        </div>
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Plazo Entrega</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_entrega" class="form-control activation group-elemento" style="text-align:right;" />
                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" />
                            </div>
                        </div>
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="id_moneda" >
                                @foreach ($tp_moneda as $tpm)
                                    <option value="{{$tpm->id_moneda}}" data-simbolo-moneda="{{$tpm->simbolo}}" >{{$tpm->descripcion}} ( {{$tpm->simbolo}} )</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control activation" 
                                name="id_tp_documento">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_doc as $tp)
                                    @if($tp->descripcion == 'Factura')
                                        <option value="{{$tp->id_tp_doc}}" selected>{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                    @else
                                        <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="group-codigo_orden" >
                            <h5>Código Orden Softlink</h5>
                            <input class="form-control" name="codigo_orden" type="text" placeholder="">
                        </div>
                        <div class="col-md-3" id="group-sede">
                            <h5>Sede</h5>
                                <select name="sede" class="form-control activation"  required>
                                    @foreach ($sedes as $sede)
                                        @if($sede->id_sede == 4)
                                        <option value="{{$sede->id_sede}}" selected>{{ $sede->descripcion}}</option>
                                        @else
                                        <option value="{{$sede->id_sede}}">{{ $sede->descripcion}}</option>
                                        @endif

                                    @endforeach                    
                                </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="group-proveedor">
                            <h5>Proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_proveedor"/>
                                <input class="oculto" name="id_contrib"/>
                                <input type="text" class="form-control" name="razon_social" disabled
                                    aria-describedby="basic-addon1" required>
                                <button type="button" class="group-text" id="basic-addon1" onClick="proveedorModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                                <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-3" id="group-proveedor">
                            <h5>Contacto</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_contacto"/>
                                <input type="text" class="form-control" name="razon_social" disabled
                                    aria-describedby="basic-addon1" required>
                                <button type="button" class="group-text" id="basic-addon1" onClick="contactoModal();">
                                    <i class="fa fa-search"></i>
                                </button> 
                                <button type="button" class="btn-primary activation" title="Agregar Contacto" onClick="agregar_contacto();"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 left" hidden>
                            <h5>&nbsp;</h5>
                            <button class="btn btn-primary" type="button" id="btnAgregarNuevoItem" onClick="agregarNuevoItem();" >
                                <i class="fas fa-plus"></i> Agregar Nuevo Item
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaDetalleOrden" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>REQ.</th>
                                        <th>COD. ITEM</th>
                                        <th>PRODUCTO</th>
                                        <th>UNIDAD</th>
                                        <th>CANTIDAD</th>
                                        <th>PRECIO</th>
                                        <th>STOCK COMPROMETIDO</th>
                                        <th>CANTIDAD A COMPRAR</th>
                                        <th>TOTAL</th>
                                        <th>ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                <dl class="dl-horizontal">
                                        <dt>Total:</dt>
                                        <dd class="text-center"><var name=total></var></dd>
                                    </dl>
                                </div>
                            </div>
                            <!-- <p class="c"><strong>Total: </strong> <var name="total"></var></p> -->
                        </div>
                    </div>
                    <div class="modal-footer">
                    <div class="form-inline">
                        <div class="checkbox" id="check-guarda_en_requerimiento" style="display:none">
                            <label>
                                <input type="checkbox" name="guardarEnRequerimiento"> Guardar nuevos items en requerimiento?
                            </label>
                        </div> 

                        <input type="submit" id="submit_orden_requerimiento" class="btn btn-success" value="Guardar"/>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

 