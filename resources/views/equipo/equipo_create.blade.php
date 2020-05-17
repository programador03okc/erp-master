<div class="modal fade" tabindex="-1" role="dialog" id="modal-equipo_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <form id="form-equipo" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Equipo</h3>
            </div>
            <div class="modal-body">
                <input class="oculto" name="id_equipo" primary="ids">
                {{-- <input type="hidden" name="_token" value="{{csrf_token()}}" id="token"> --}}
                <div class="row">
                    <div class="col-md-3">
                        <h5>Categoría</h5>
                        <select class="form-control activation" onChange="elabora_descripcion('id_categoria');"
                            name="id_categoria" required>
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($categorias as $cat)
                                <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h5>Propietario</h5>
                        <select class="form-control activation" name="propietario" required>
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($propietarios as $prop)
                                <option value="{{$prop->id_empresa}}">{{$prop->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5>Kilometraje Inicial</h5>
                        <input type="text" class="form-control" name="kilometraje_inicial" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" class="form-control" readOnly name="codigo" >
                    </div>              
                    <div class="col-md-3">
                        <h5>Marca</h5>
                        <input type="text" class="form-control activation" name="marca" onChange="elabora_descripcion('marca');" >
                    </div>
                    <div class="col-md-3">
                        <h5>Modelo</h5>
                        <input type="text" class="form-control activation" name="modelo" onChange="elabora_descripcion('modelo');" >
                    </div>
                    <div class="col-md-3">
                        <h5>Placa</h5>
                        <input type="text" class="form-control activation" name="placa" onChange="elabora_descripcion('placa');" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código Tarj. de Propiedad</h5>
                        <input type="text" class="form-control activation" name="cod_tarj_propiedad" >
                    </div>
                    <div class="col-md-3">
                        <h5>Serie</h5>
                        <input type="text" class="form-control activation" name="serie" onChange="elabora_descripcion('serie');">
                    </div>
                    <div class="col-md-3">
                        <h5>Año de Fabricación</h5>
                        <input type="text" class="form-control activation" name="anio_fabricacion" >
                    </div>
                    <div class="col-md-3">
                        <h5>Tipo de Combustible</h5>
                        <div style="display:flex;">
                            <select class="form-control activation" name="tp_combustible" >
                                <option value="0" disabled>Elija una opción</option>
                                @foreach ($tp_combustible as $tp)
                                    <option value="{{$tp->id_tp_combustible}}">{{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-primary activation" title="Agregar Tipo de Combustible" onClick="agregar_tp_combustible();">
                            <i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Características Adicionales</h5>
                        <textarea name="caracteristicas_adic" class="form-control activation" rows="3" cols="50"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-success" value="Guardar"/>
                {{-- <button class="btn btn-sm btn-success" onClick="guardar_equipo();">Guardar</button> --}}
            </div>
            </form>
        </div>
    </div>  
</div>
