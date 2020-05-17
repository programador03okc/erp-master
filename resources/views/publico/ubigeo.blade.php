<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-ubigeo">
    <div class="modal-dialog" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ubigeo</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Departamento</h5>
                        <select class="form-control input-sm" name="depart" id="depart" onchange="cargarProv(this.value);">
                            <option value="0" selected disabled>Elija una opción</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Provincia</h5>
                        <select class="form-control input-sm" name="provin" id="provin" onchange="cargarDist(this.value);"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Distrito</h5>
                        <select class="form-control input-sm" name="distri" id="distri"></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="enviarUbigeo();">Aceptar</button>
            </div>
        </div>
    </div>
</div>