<div class="modal fade" tabindex="-1" role="dialog" id="modal-grupo_despacho_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1200px;">
        <div class="modal-content">
            <form id="form-grupo_despacho">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Despacho</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_od_grupo">
                    <input type="text" class="oculto" name="id_sede_grupo">
                    <div class="row">
                        <div class="col-md-3">
                            <input class="oculto" name="mov_propia_valor"/>
                            <input type="checkbox" name="mov_propia" style="margin-right: 10px; margin-left: 7px;"/> Movilidad Propia
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Fecha de Despacho</h5>
                            <input type="date" class="form-control activation" name="fecha_despacho_grupo" value="<?=date('Y-m-d');?>">
                        </div>
                        <div class="col-md-6" id="proveedor">
                            <h5>Proveedor</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="gd_id_proveedor"/>
                                <input type="text" class="form-control" name="gd_razon_social" placeholder="Seleccione un proveedor..." 
                                    aria-describedby="basic-addon1" disabled="true">
                                <button type="button" class="input-group-text activation" id="basic-addon1" onClick="proveedorModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                    <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6" id="trabajador">
                            <h5>Responsable</h5>
                            <select class="form-control activation" name="responsable_grupo">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observaciones</h5>
                            <textarea name="observaciones" id="observaciones" cols="150" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="modal-header" style="display:flex;">
                        <h4 class="modal-title"> Ordenes de Despacho: </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="detalleODs"  style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Codigo</th>
                                            <th>Cliente</th>
                                            <th>Requerimiento</th>
                                            <th>Concepto</th>
                                            <th>Ubigeo</th>
                                            <th>Dirección Destino</th>
                                            <th>Fecha Despacho</th>
                                            <th>Fecha Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-success" id="btnGrupoDespacho" onClick="guardar_grupo_despacho();" >Guardar</button>
                    <!-- <input type="submit" id="submit_orden_despacho" class="btn btn-success" value="Guardar"/> -->
                </div>
            </form>
        </div>
    </div>
</div>