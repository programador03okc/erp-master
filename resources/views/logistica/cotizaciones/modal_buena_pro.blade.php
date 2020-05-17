<div class="modal fade" tabindex="-1" role="dialog" id="modal-buena_pro">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Buena Pro</h3>            
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-buena_pro">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="negrita">Proveedor</td>
                            <td id="buena_pro_proveedor"></td>
                        </tr>
                        <tr>
                            <td class="negrita">Item</td>
                            <td id="buena_pro_item"></td>
                        </tr>
                    </tbody>
                    </table>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Justificación</label>
                        <div class="col-sm-9">
                        <textarea class="form-control" rows="1" id="justificacionBuenaPro" placeholder="Motivo por el cual se le da la Buena Pro..." ></textarea>

                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" name="idbtnSelectBuenaPro">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success btn-flat" onClick="addBuenaPro(event);" >Añadir</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>