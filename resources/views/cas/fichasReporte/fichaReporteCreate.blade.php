<div class="modal fade" tabindex="-1" role="dialog" id="modal-fichaReporte" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 700px;">
        <div class="modal-content">
            <form id="form-fichaReporte">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Ficha Reporte - <label id="codigo_ficha" >FR-22000</label></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_incidencia_reporte">
                    <input type="text" class="oculto" name="padre_id_incidencia">
                    {{-- <input type="text" class="oculto" name="origen"/> --}}
                    
                    {{-- <fieldset class="group-table" id="fieldsetDetallesEntidad"> --}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <label class="col-sm-6 control-label" style="text-align: left;">Fecha ficha reporte</label>
                                        <div class="col-sm-6">
                                            <input type="date" class="form-control limpiarReporte" name="fecha_reporte"/>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group" style="margin-bottom:0px">
                                        <label class="col-sm-6 control-label">Nombre</label>
                                        <div class="col-sm-6">
                                            <div class="form-control-static limpiar nombre"></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-horizontal">
                                    
                                    <div class="form-group" style="margin-bottom:0px">
                                        <label class="col-sm-6 control-label">Responsable</label>
                                        <div class="col-sm-6">
                                            <select class="form-control js-example-basic-single limpiarReporte" 
                                                name="id_usuario" required>
                                                <option value="">Elija una opción</option>
                                                @foreach ($usuarios as $usuario)
                                                <option value="{{$usuario->id_usuario}}">{{$usuario->nombre_corto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group" style="margin-bottom:0px">
                                        <label class="col-sm-3 control-label">Ubigeo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static limpiar ubigeo"></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><strong>Ingrese las acciones realizadas:</strong></h5>
                                <textarea class="form-control limpiarReporte" name="acciones_realizadas"></textarea>
                            </div>
                        </div>
                    {{-- </fieldset> style="display:flex;justify-content: space-between;"--}}
                    
                </div>
                <div class="modal-footer">
                    {{-- <button id="btn_cerrar" class="btn btn-default" onClick="cerrarFicha();">Cerrar</button> --}}
                    <input type="submit" id="submit_guardar_reporte" class="btn btn-success" value="Guardar ficha reporte"/>
                </div>
            </form>
        </div>
    </div>
</div>