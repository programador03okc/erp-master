<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_cambio">
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content">
            <form id="form-guia_com_cambio">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Cambio de Serie-Número</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_ingreso"/>
                    <input type="text" class="oculto" name="id_guia_com"/>
                    <input type="text" class="oculto" name="id_od"/>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="oculto" name="id_serie_numero">
                                <input type="text" class="form-control" name="serie_nuevo" onBlur="ceros_numero_cambio('serie');" placeholder="0000" required >
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="numero_nuevo" onBlur="ceros_numero_cambio('numero');" placeholder="000000" required >
                                    <!-- onBlur="ceros_numero_guia();"  -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Seleccione un motivo:</h5>
                            <select class="form-control activation js-example-basic-single" name="id_motivo_obs_cambio">
                                {{-- <option value="0">Elija una opción</option> --}}
                                @foreach ($motivos_anu as $mot)
                                    <option value="{{$mot->id_motivo}}">{{$mot->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-12">
                            <h5>Observación:</h5>
                            <textarea name="observacion_guia_ven" class="form-control" rows="3" cols="8" required></textarea>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_guia_com_cambio" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>