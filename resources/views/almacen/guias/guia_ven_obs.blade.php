<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_ven_obs">
    <div class="modal-dialog">
        <div class="modal-content" style="width:500px;">
            <form id="form-guia_ven_obs">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Ingrese el Motivo de la Anulación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_salida"/>
                    <input type="text" class="oculto" name="id_guia_ven"/>
                    <input type="text" class="oculto" name="id_od"/>
                    <input type="text" class="oculto" name="id_trans"/>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Seleccione un motivo:</h5>
                            <select class="form-control activation js-example-basic-single" name="id_motivo_obs_ven">
                                {{-- <option value="0">Elija una opción</option> --}}
                                @foreach ($motivos_anu as $mot)
                                    <option value="{{$mot->id_motivo}}">{{$mot->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observación:</h5>
                            <textarea name="observacion_guia_ven" class="form-control" rows="3" cols="8" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submitGuiaVenObs" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>