<div class="modal fade" tabindex="-1" role="dialog" id="modal-transformacionesPendientes" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 500px;">
        <div class="modal-content">
            <form id="form-transformacionesPendientes">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Transformaciones pendientes</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-hover table-striped table-okc-view" 
                                id="listaTransformacionesPendientes" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>Fecha Priorización</th>
                                        <th>Requerimiento</th>
                                        <th>Cod.CDP</th>
                                        <th width="50%">Cliente/Entidad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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