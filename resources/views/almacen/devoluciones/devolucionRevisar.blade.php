<div class="modal fade" tabindex="-1" role="dialog" id="modal-devolucionRevisar">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-devolucionRevisar">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Conformidad de la devolución</h3>
                </div>
                <div class="modal-body">
                    <input style="display:none;" name="id_devolucion">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Responsable</h5>
                            <select name="responsable_revision" class="form-control js-example-basic-single" required>
                                <option value="">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Comentario</h5>
                            <textarea name="comentario_revision" class="form-control"  required
                                cols="110" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_devolucionRevisar" class="btn btn-success" value="Procesar conformidad"/>
                    <!-- <button class="btn btn-sm btn-success" onClick="procesar_transformacion();">Procesar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>