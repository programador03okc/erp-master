<div class="modal fade" tabindex="-1" role="dialog" id="modal-tp_combustible" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <form id="form-tp_combustible" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Nuevo Tipo de Combustible</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{-- <input type="hidden" name="_token" value="{{csrf_token()}}" id="token"> --}}
                            <input class="oculto" name="id_tp_combustible">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Abreviatura</h5>
                                    <input type="text" class="form-control" name="tp_codigo" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Descripción</h5>
                                    <input type="text" class="form-control" name="tp_descripcion" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success" value="Guardar"/>
                    {{-- <button type="submit" class="btn btn-success">Guardar <span class="fas fa-save"></span></button> --}}
                </div>
            </form>
        </div>
    </div>  
</div>

