<div class="modal fade" tabindex="-1" role="dialog" id="modal-procesarTransformacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-procesarTransformacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Procesar Transformación</h3>
                </div>
                <div class="modal-body">
                    <input style="display:none;" name="id_transformacion">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Responsable</h5>
                            <select name="responsable" class="form-control js-example-basic-single" required>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Observación</h5>
                            <textarea name="observacion" id="observacion" class="form-control"  required
                                cols="110" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_procesarTransformacion" class="btn btn-success" value="Procesar"/>
                    <!-- <button class="btn btn-sm btn-success" onClick="procesar_transformacion();">Procesar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>