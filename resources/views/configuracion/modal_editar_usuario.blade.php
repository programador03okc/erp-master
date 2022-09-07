<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-usuario">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-detalle_nota_lanzamiento">Editar Usuario</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control icd-okc" name="id_usuario" />
                <div class="row">
                    <div class="col-md-3">
                        <h5>Nombre</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="nombres" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Apellido Paterno</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="apellido_paterno" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Apellido Materno</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="apellido_materno" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Nombre Corto</h5>
                        <input type="text" class="form-control icd-okc" name="nombre_corto" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Usuario</h5>
                        <div style="display:flex;">
                            <input type="text" class="form-control icd-okc" name="usuario" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Contraseña</h5>
                        <div style="display:flex;">
                            <input type="password" class="form-control icd-okc" name="contraseña" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Email</h5>
                        <input type="text" class="form-control icd-okc" name="email" />
                    </div>
                    <div class="col-md-3">
                        <h5>Rol</h5>
                        <select class="form-control icd-okc" name="rol">
                            <option value="0" selected disabled>Elija una opción</option>
                            @foreach ($roles as $rol)
                                <option value="{{$rol->id_rol}}">{{$rol->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ----- --}}
                <div class="row">
                    <div class="col-md-12">
                        <h4>DATOS PERSONALES</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nro_documento">N° de documento</label>
                            <input type="number" class="form-control" id="nro_documento" name="nro_documento" required>
                            <small id="emailHelp" class="form-text text-muted">N° de documento de identificación.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombres">Nombres:</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno : </label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno : </label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de nacimiento : </label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="sexo">Sexo : </label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sexo" id="inlineRadio1" value="M" required>
                                    <label class="form-check-label" for="inlineRadio1">Masculino</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sexo" id="inlineRadio2" value="F" required>
                                    <label class="form-check-label" for="inlineRadio2">Femenino</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_estado_civil">Estado civil : </label>
                            <select id="id_estado_civil" class="form-control" name="id_estado_civil" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($estado_civil as $item)
                                    <option value="{{$item->id_estado_civil}}" >{{$item->descripcion}}</option>
                                @endforeach

                              </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Telefono : </label>
                            <input type="number" class="form-control" id="telefono" name="telefono" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="direccion">Direccion : </label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email : </label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="brevette">Brevette : </label>
                            <input type="text" class="form-control" id="brevette" name="brevette" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pais">Pais : </label>
                            <select id="pais" class="form-control" name="pais" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($pais as $item)
                                    <option value="{{$item->id_pais}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ubigeo">Ubigeo : </label>
                            <input type="text" class="form-control" id="ubigeo" name="ubigeo" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tipo_trabajador">Tipo trabajador : </label>
                            <select id="id_tipo_trabajador" class="form-control" name="id_tipo_trabajador" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($tipo_trabajador as $item)
                                    <option value="{{$item->id_tipo_trabajador}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_categoria_ocupacional">Categoria ocupacional : </label>
                            <select id="id_categoria_ocupacional" class="form-control" name="id_categoria_ocupacional" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($categoria_ocupacional as $item)
                                    <option value="{{$item->id_categoria_ocupacional}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_tipo_planilla">Tipo de planilla : </label>
                            <select id="id_tipo_planilla" class="form-control" name="id_tipo_planilla" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($tipo_planilla as $item)
                                    <option value="{{$item->id_tipo_planilla}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="condicion">Condición : </label>
                            <input type="text" class="form-control" id="condicion" name="condicion" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hijos">Hijos : </label>
                            <input type="number" class="form-control" id="hijos" name="hijos" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_pension">Pension : </label>
                            <select id="id_pension" class="form-control" name="id_pension" required>
                                <option value="" >Seleccione...</option>
                                @foreach ($pension as $item)
                                    <option value="{{$item->id_pension}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cuspp">Cuspp : </label>
                            <input type="text" class="form-control" id="cuspp" name="cuspp" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="seguro">Seguro : </label>
                            <input type="text" class="form-control" id="seguro" name="seguro" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confianza : </label>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="confianza" id="confianza1" value="t" required>
                                        <label class="form-check-label" for="confianza1">Si</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="confianza" id="confianza2" value="f" required>
                                        <label class="form-check-label" for="confianza2">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>CREDENCIALES</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario">Usuario : </label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clave">Clave : </label>
                            <input type="password" class="form-control" id="clave" name="clave" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_corto">Nombre corto : </label>
                            <input type="text" class="form-control" id="nombre_corto" name="nombre_corto" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codvent_softlink">Codvend Softlink : </label>
                            <input type="text" class="form-control" id="codvent_softlink" name="codvent_softlink" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_grupo">Grupo : </label>
                            <select id="id_grupo" class="selectpicker" name="id_grupo[]" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10" required>
                                @foreach ($grupo as $item)
                                    <option value="{{$item->id_grupo}}" >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_rol">Rol : </label>
                            <select id="id_rol" class="selectpicker" name="id_rol[]" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10" required>
                                {{-- @foreach ($rol as $item)
                                    <option value="{{$item->id_rol}}" >{{$item->descripcion}}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" name="btnActualizarPerfilUsuario" onClick="actualizarPerfilUsuario();">Actualizar</button>

            </div>
        </div>
    </div>
</div>
