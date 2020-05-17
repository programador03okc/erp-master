<div class="modal fade" tabindex="-1" role="dialog" id="modal-residente_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width:1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Residente</h3>
            </div>
            <div class="modal-body">
                <input class="oculto" name="id_residente" >
                <div class="row">
                    <div class="col-md-3">
                        <h5>Nro. Documento</h5>
                        <input class="oculto" name="id_op_com"  >
                        <input type="text"  name="nro_documento" placeholder="00000000" class="form-control activation" readOnly>
                    </div>
                    <div class="col-md-6">
                        <h5>Nombre Trabajador</h5>
                        <div style="display:flex;">
                            <input class="oculto" name="id_trabajador"/>
                            <input type="text" class="form-control activation" name="nombre_trabajador" placeholder="Seleccione un trabajador..." 
                                aria-describedby="basic-addon1" disabled>
                            <button type="button" class="input-group-text activation btn-primary" id="basic-addon1" onClick="trabajadorModal();">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Colegiatura</h5>
                        <input type="text" name="colegiatura" class="form-control"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-residente_contrato"  enctype="multipart/form-data" method="post">
                            <input class="oculto" name="id_residente">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="20%">
                                            <h5>Cargo</h5>
                                            <select class="form-control" name="id_cargo">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($cargos as $tp)
                                                    <option value="{{$tp->id_cargo}}">{{$tp->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="50%">
                                            <h5>Proyecto</h5>
                                            <div style="width:100%; display:flex;">
                                                <div style="width:90%; display:flex;">
                                                    {{-- <input class="oculto" name="id_insumo"> --}}
                                                    <input type="text" name="id_proyecto" class="oculto"/>
                                                    <input type="text" name="razon_social" class="oculto"/>
                                                    <input type="text" name="simbolo" class="oculto"/>
                                                    <input type="text" name="importe" class="oculto"/>
                                                    {{-- <input type="text" name="cod_preseje" class="oculto"/> --}}
                                                    <input type="text" name="codigo" class="form-control input-sm" readOnly style="width:120px;height:34px;" />
                                                    <input type="text" name="descripcion" class="form-control input-sm" readOnly style="height:34px;"/>
                                                </div>
                                                <div style="width:10%;">
                                                    <span class="input-group-addon input-sm " style="cursor:pointer;height:33px;" 
                                                        onClick="proyectoModal();">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="10%">
                                            <h5>Participación (%)</h5>
                                            <input type="number" name="participacion" class="form-control"/>
                                        </td>
                                        <td width="10%">
                                            <h5>Fecha Inicio</h5>
                                            <input type="date" name="fecha_inicio" class="form-control" value="<?=date('Y-m-d');?>"/>
                                        </td>
                                        <td>
                                            <h5>Add</h5>
                                            {{-- <input type="submit" value="Agregar" class="btn btn-success"/> --}}
                                            <button type="button" class="btn btn-success input-sm" id="basic-addon2" onClick="agregar();">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
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
                            id="listaResidenteProyectos" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th hidden>N°</th>
                                    <th>Cargo</th>
                                    <th>Fec.Inicio</th>
                                    <th>Fec.Fin</th>
                                    <th>Particip.</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Cliente</th>
                                    <th>Mnd</th>
                                    <th>Importe</th>
                                    {{-- <th>Pres.Ejec.</th> --}}
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_residente();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
