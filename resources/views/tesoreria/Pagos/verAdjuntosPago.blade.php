<div class="modal fade" tabindex="-1" role="dialog" id="modal-verAdjuntosPago" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Ver adjuntos del pago</h3>
                </div>
            </div>
            
            <div class="modal-body">
                <input type="text" class="oculto" name="id_requerimiento_pago" />

                <fieldset class="group-table" id="fieldsetAdjuntosPago">
                    {{-- <h5 style="display:flex;justify-content: space-between;"><strong>Adjuntos</strong></h5> --}}
                    <div class="row">
                        <div class="col-md-12">
                            <table id="adjuntosPago" class="mytable table table-condensed table-bordered table-okc-view" >
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                
            </div>
        </div>
    </div>
</div>