<div class="modal fade" tabindex="-1" role="dialog" id="modal-mtto_programacion" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Programación de Mantenimientos</h3>
                <div style="display:flex;">
                    <h5 id="cod_equipo_mtto" style="padding:12px 0px;margin:0px;"></h5>
                    <h5 id="des_equipo_mtto" style="padding:12px;margin:0px;"></h5>
                    <h5 style="padding:12px;margin:0px;">Kilometraje Actual: </h5>
                    <h5 id="kactual" style="padding:12px;margin:0px;"></h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-mtto_programacion"  method="post">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <input class="oculto" name="id_equipo">
                            <input class="oculto" name="usuario">
                            <table id="mtto_programacion" width="100%">
                                <tbody>
                                    <tr>
                                        <td width="40%">
                                            <h5>Descripción</h5>
                                            <input type="text" name="descripcion" class="form-control" required/>
                                        </td>
                                        <td width="15%">
                                            <h5>Según</h5>
                                            <select class="form-control activation" name="segun" class="form-control group-elemento" 
                                                onChange="cambio_segun();">
                                                <option value="1" selected>Kilometraje</option>
                                                <option value="2">Tiempo</option>
                                            </select>
                                        </td>
                                        <td id="kilom" >
                                            <h5>Rango en Kilom.</h5>
                                            <input type="number" name="kilometraje_rango" class="form-control"/>
                                        </td>
                                        <td id="tiempo" class="oculto">
                                            <h5>Tiempo</h5>
                                            <div style="display:flex; text-align: right;">
                                                <input type="number" name="tiempo" class="form-control group-elemento" />
                                                <select class="form-control activation" name="unid_program" class="form-control group-elemento" >
                                                    <option value="0">Elija una opción</option>
                                                    @foreach ($unid_program as $unid)
                                                        <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Add</h5>
                                            <input type="submit" class="btn btn-success boton" value="Agregar"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="listaProgramaciones" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Descripción</th>
                                    <th>Cada/(kilom.)</th>
                                    <th>Cada/(Tiempo)</th>
                                    <th>Fecha Ult.</th>
                                    <th>Kilom. Ult.</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
