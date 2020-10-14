<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden-requerimiento">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <form id="form-orden-requerimiento" type="register" form="formulario">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Orden <span id="codigo_requeriento_seleccionado"></span></h3>
                </div>
                <div class="modal-body">
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
                        <div class="col-md-2" id="group-codigo">
                            <h5>Código Orden</h5>
                            <input class="form-control" id="codigo_orden" type="text" placeholder="OC#####" value="" readOnly>
                        </div>
                        <div class="col-md-2" id="group-fecha_orden">
                            <h5>Fecha</h5>
                            <input class="form-control" id="fecha" type="date" placeholder="DD/MM/AA" value={{ date('Y-m-d H:i:s') }} readOnly>
                        </div>
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Condición</h5>
                            <div style="display:flex;">
                                <select class="form-control group-elemento activation" name="id_condicion" onchange="handlechangeCondicion(event);"
                                    style="width:120px;text-align:center;" disabled="true">
                
                                </select>
                                <input type="number" name="plazo_dias"  class="form-control activation group-elemento" style="text-align:right; width:50px; " />
                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" />
                            </div>
                        </div>
                        <div class="col-md-2" id="group-fecha_orden">
                            <h5>Plazo Entrega</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_entrega" class="form-control activation group-elemento" style="text-align:right;" />
                                <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="id_moneda" >
                                <option value="0">Elija una opción</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="group-fecha_orden">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control activation" 
                                name="id_tp_documento" disabled="true">
                                <option value="0">Elija una opción</option>
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
                                        <option value="{{$sede->id_sede}}">{{ $sede->descripcion}}</option>
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
                        <div class="col-md-6 right">
                            <h5>&nbsp;</h5>
                            <button class="btn btn-primary" type="button" id="btnCrearOrdenCompra" onClick="openModalCrearOrdenCompra();">
                                <i class="fas fa-plus"></i> Agregar Nuevo Item
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                id="listaDetalleOrden" width="100%">
                                <thead>
                                    <tr>
                                        <th width="20"></th>
                                        <th width="20">#</th>
                                        <th width="80">COD. ITEM</th>
                                        <th width="200">PRODUCTO</th>
                                        <th width="30">UNIDAD</th>
                                        <th width="50">CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_orden_requerimiento" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>