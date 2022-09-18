<div class="modal fade" tabindex="-1" role="dialog" id="modal-fichaTecnica" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 700px;">
        <div class="modal-content">

            <form id="form-fichaTecnica" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Ficha técnica</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_ficha">
                    <input type="text" class="oculto" name="padre_id_devolucion">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Adjuntar Archivo(s)</h5>
                            <input type="file" name="archivos[]" multiple="true" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button id="btn_cerrar" class="btn btn-default" onClick="cerrarFicha();">Cerrar</button> --}}
                    <input type="submit" id="submit_guardar_ficha" class="btn btn-success" value="Guardar Ficha"/>
                </div>
            </form>

        </div>
    </div>
</div>