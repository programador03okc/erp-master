
<div class="modal fade" id="modal-nuevo-cierre-apertura" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="nuevo-cierre-apertura" method="POST">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" value="0">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="title"></h3>
                </div>
                <div class="modal-body">
                    <fieldset class="group-table" id="fieldsetPeriodo">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Año *</h5>
                                {{-- <input type="number" name="compra" class="form-control input-sm text-center" step="any" min="0" value="0.00"> --}}
                                <select class="form-control" name="anio" style="background: khaki;">
                                    <option value="2022" >2022</option>
                                    <option value="2023" selected>2023</option>
                                    <option value="2024" >2024</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <h5>Mes *</h5>
                                {{-- <input type="number" name="venta" class="form-control input-sm text-center" step="any" min="0" value="0.00"> --}}
                                <select class="form-control" name="mes"  style="background: khaki;">
                                    <option value="Enero" selected>Enero</option>
                                    <option value="Febrero" >Febrero</option>
                                    <option value="Marzo" >Marzo</option>
                                    <option value="Abril" >Abril</option>
                                    <option value="Mayo" >Mayo</option>
                                    <option value="Junio" >Junio</option>
                                    <option value="Julio" >Julio</option>
                                    <option value="Agosto" >Agosto</option>
                                    <option value="Setiembre" >Setiembre</option>
                                    <option value="Octubre" >Octubre</option>
                                    <option value="Noviembre" >Noviembre</option>
                                    <option value="Diciembre" >Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <h5>Acción *</h5>
                                <select class="form-control" name="accion" style="background: khaki;font-weight: bold;">
                                    <option value="0" selected>Cierre</option>
                                    <option value="1" >Apertura</option>
                                    
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <br/>
                    <fieldset class="group-table" id="fieldsetAlmacenes">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Empresa</h5>
                                {{-- <input type="number" name="compra" class="form-control input-sm text-center" step="any" min="0" value="0.00"> --}}
                                <select class="form-control" name="id_empresa">
                                    <option value="0" selected>Todas las empresas</option>
                                    @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Sede</h5>
                                <select class="form-control" name="id_sede">
                                    <option value="0" selected>Todos las sedes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Almacén (usar la tecla ctrl para elegir mas de uno)</h5>
                                <select class="form-control" name="id_almacen" multiple="multiple">
                                    {{-- <select name="id_almacen[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10"> --}}
                                    <option value="0" selected>Todos los almacenes</option>
                                    @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <br/>
                    <fieldset class="group-table" id="fieldsetComentario">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Comentario</h5>
                                {{-- <textarea name="comentario" cols="30" rows="10"></textarea> --}}
                                <textarea class="form-control" name="comentario" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="font-size: 14px;">* Campos obligatorios</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success shadow-none">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>