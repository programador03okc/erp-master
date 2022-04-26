<div class="modal fade" tabindex="-1" role="dialog" id="modal-kardex_filtro">
    <div class="modal-dialog">
        <div class="modal-content" style="width:520px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Almacén</h5>
                        <select name="almacen" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" data-size="10">
                            @foreach ($almacenes as $item)
                                <option value="{{ $item->id_almacen }}">{{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Rango de Fechas</h5>
                        <div class="input-group">
                            <span class="input-group-addon"> Desde: </span>
                            <input type="date" class="form-control activation" name="fecha_inicio">
                            <span class="input-group-addon"> Hasta: </span>
                            <input type="date" class="form-control activation" name="fecha_fin">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="actualizarKardex();">Cerrar</button>
            </div>
        </div>
    </div>
</div>
