<div class="modal fade" tabindex="-1" role="dialog" id="modal-gestionar-establecimiento">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal-gestionar-establecimiento-title"></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Tipo</h5>
                        <select class="form-control group-elemento" name="tipo_establecimiento" 
                            style="text-align:center;">
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($tipo_establecimiento as $tip)
                                <option value="{{$tip->id_tipo_establecimiento}}">{{$tip->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <h5>Dirección</h5>
                        <input class="form-control icd-okc" name="direccion_establecimiento" />
                    </div>
                </div>
 
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-8">
                        <div name="text-status" class="text-animate"></div>
                    </div>
                    <div class="col-md-4">
                        <label style="display: none;" id="id_establecimiento"></label>
                        <div id="btnAction_establecimiento"></div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>

