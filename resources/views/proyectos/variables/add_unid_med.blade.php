<div class="modal fade" tabindex="-1" role="dialog" id="modal-unid_med" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 300px;">
        <div class="modal-content">
            <form id="form-unid_med" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Nueva Unidad de Medida</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <input class="oculto" name="id_unidad_medida">
                            <input class="oculto" name="tipo">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Descripción</h5>
                                    <input type="text" name="descripcion_unidad" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Abreviatura</h5>
                                    <input type="text" name="abreviatura_unidad" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button class="btn btn-sm btn-success" onClick="guardar_unid_med();">Guardar</button> --}}
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>

