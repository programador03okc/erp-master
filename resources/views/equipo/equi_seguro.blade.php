<div class="modal fade" tabindex="-1" role="dialog" id="modal-equi_seguro" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Gestión de Documentos
                        <h5 id="cod_equipo" style="padding:12px;margin:0px;"></h5>
                        <h5 id="des_equipo" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-seguro"  enctype="multipart/form-data" method="post">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <input class="oculto" name="id_equipo">
                            <table id="seguro" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5>Tipo de Doc.</h5>
                                            <div style="display:flex;">
                                                <select class="form-control" name="id_tp_seguro" required>
                                                    <option value="0" disabled>Elija una opción</option>
                                                    @foreach ($tp_seguro as $tp)
                                                        <option value="{{$tp->id_tp_seguro}}">{{$tp->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn-primary" title="Agregar Tipo" onClick="agregar_tipo();">
                                                <strong>+</strong></button>
                                            </div>
                                        </td>
                                        <td width="100px">
                                            <h5>Nro. del Doc.</h5>
                                            <input type="text" name="nro_poliza" class="form-control" required/>
                                        </td>
                                        <td>
                                            <h5>Proveedor</h5>
                                            <div style="display:flex;">
                                                <input class="oculto" name="id_proveedor"/>
                                                <input class="oculto" name="id_contrib"/>
                                                <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                                    aria-describedby="basic-addon1" required>
                                                <button type="button" class="input-group-text" id="basic-addon1" onClick="proveedorModal();">
                                                    <i class="fa fa-search"></i>
                                                </button>                                                                                    
                                                <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                                <i class="fas fa-plus"></i></button>
                                            </div>
                                            {{-- <div style="display:flex;">
                                                <select class="form-control activation" name="id_proveedor" >
                                                    <option value="0" disabled>Elija una opción</option>
                                                    @foreach ($proveedores as $pro)
                                                        <option value="{{$pro->id_proveedor}}">{{$pro->razon_social}}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn-primary" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                                <strong>+</strong></button>
                                            </div> --}}
                                        </td>
                                        <td width="160px" >
                                            <h5>Fecha Inicio</h5>
                                            <input type="date" name="fecha_inicio" class="form-control group-elemento" required/>
                                        </td>
                                        <td width="160px">
                                            <h5>Fecha Fin</h5>
                                            <input type="date" name="fecha_fin" class="form-control group-elemento" required/>
                                        </td>
                                        <td width="100px">
                                            <h5>Importe</h5>
                                            <input type="number" name="importe" class="form-control right" required/>
                                        </td>
                                        <td>
                                            <h5>Adjunto</h5>
                                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                                data-buttonName="btn-warning" data-buttonText="Adjuntar"
                                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
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
                            id="listaSeguros" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Nro.Doc</th>
                                    <th width="350px">Proveedor</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Importe</th>
                                    <th>Adjunto</th>
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
