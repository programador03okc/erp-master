<!-- modal obs -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-obs-req">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <form id="form-obs-requerimiento">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Observación de Requerimientos</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" >
                        <h5>Roles del Usuario</h5>
                            <div class="input-group-okc">
                                <select class="form-control input-sm activation" name="rol_usuario">
                                @foreach ($roles as $rol)
                                    <option value="{{$rol->id_rol}}" data-id-area="">{{$rol->rol_concepto}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12 text-left">
                            <input type="hidden" name="id_doc_aprob">
                            <h5>Motivo/Justificación</h5>
                            <textarea class="form-control input-sm" name="motivo_req" id="motivo_req" rows="5"></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-success" onClick="GrabarObservacion();">Grabar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal obs detalle -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-obs-motivo">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <form id="form-obs-detalle">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Observación</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id_requerimiento">
                            <input type="hidden" name="id_detalle_requerimiento">
                            <h5>Motivo/Justificación</h5>
                            <textarea class="form-control input-sm" name="motivo_obs" id="motivo_obs" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-sm btn-success" value="Grabar">
                </div>
            </form>
        </div>
    </div>
</div>