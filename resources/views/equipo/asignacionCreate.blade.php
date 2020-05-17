<div class="modal fade" tabindex="-1" role="dialog" id="modal-asignacion" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Asignar Equipo
                        <h5 id="cod_equipo" style="padding:12px;margin:0px;"></h5>
                        <h5 id="des_equipo" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <form id="form-asignacion"  enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="text" name="usuario" class="oculto">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Area Solicitante</h5>
                            <input type="text" name="area_solicitud" readOnly class="form-control">
                        </div>
                        <div class="col-md-8">
                            <h5>Solicitado por</h5>
                            <input type="text" name="trabajador" readOnly class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Fecha de Asignación</h5>
                            <input class="oculto" name="id_equipo">
                            <input class="oculto" name="id_solicitud">
                            <input type="date" name="fecha_asignacion" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <h5>Fecha Inicio / Fecha Fin</h5>
                            <div style="display:flex;">
                                <input type="date" name="fecha_inicio" class="form-control"/>
                                <input type="date" name="fecha_fin" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Kilometraje</h5>
                            <input type="number" name="kilometraje" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <h5>Detalle de la Asignación</h5>
                            <textarea name="detalle_asignacion" cols="92" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Adjuntar CheckList</h5>
                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                data-buttonName="btn-primary" data-buttonText="Adjuntar"
                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                    {{-- <button class="btn btn-sm btn-success" onClick="guardar_asignacion();">Guardar</button> --}}
                </div>
            </form>
        </div>
    </div>  
</div>
