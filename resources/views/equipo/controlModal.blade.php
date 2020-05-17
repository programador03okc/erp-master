<div class="modal fade" tabindex="-1" role="dialog" id="modal-control" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Datos del Recorrido
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-2">
                        <h5>Fecha del Recorrido</h5>
                        <input class="oculto" name="id_control">
                        <input class="oculto" name="id_asignacion">
                        <input type="date" name="fecha_recorrido" class="form-control" value="<?=date('Y-m-d');?>">
                    </div>
                    <div class="col-md-6">
                        <h5>Chofer</h5>
                        <select class="form-control" name="chofer">
                            <option value="0">Elija una opción</option>
                            @foreach ($trabajadores as $det)
                                <option value="{{$det->id_trabajador}}">{{$det->nombre_trabajador}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Kilometraje Inicial / Final</h5>
                        <div style="display:flex;">
                            <input type="number" name="kilometraje_inicio" class="form-control right">
                            <input type="number" name="kilometraje_fin" onBlur="valida_kilometraje(this.value);" class="form-control right">
                        </div>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <h5>Importe Gastado (S/)</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control right" value="S/" readOnly style="text-align:center;width:30px;padding-left:2px;padding-right:2px;"/>
                            <input type="number" name="importe" class="form-control right" onChange="calcula_galones();"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Nombre del Grifo / Precio Unitario </h5>
                        <div style="display:flex;">
                            <input type="text" name="grifo" class="form-control" style="width:200px;"/>
                            <input type="text" class="form-control right" value="S/" readOnly style="text-align:center;width:30px;padding-left:2px;padding-right:2px;"/>
                            <input type="number" name="precio_unitario" class="form-control right" onChange="calcula_galones();"/>
                            <input type="number" name="galones" class="form-control right" readOnly/>
                            <input type="text" class="form-control" value="gal" readOnly style="width:50px;"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Hora Inicio / Hora Fin</h5>
                        <div style="display:flex;">
                            <input type="time" name="hora_inicio" class="form-control"/>
                            <input type="time" name="hora_fin" onBlur="valida_hora(this.value);" class="form-control"/>
                        </div>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción del Recorrido</h5>
                        <input type="text" name="descripcion_recorrido" class="form-control"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Observaciones</h5>
                        <textarea name="observaciones" cols="135" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_control();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
