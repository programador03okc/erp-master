<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-obs">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button> --}}
                <h3 class="modal-title"><label id="titulo"></label></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {{-- <h5>Codigo</h5> --}}
                        <input class="oculto" name="obligatorio">
                        <input class="oculto" name="mensaje">
                        <textarea name="observacion" class="form-control" rows="4" cols="30"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="enviarObs();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
