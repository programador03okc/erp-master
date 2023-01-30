
<div class="modal fade" id="modal-historial-acciones" tabindex="-1" role="dialog" aria-labelledby="modal-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-historial-acciones" method="POST">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="titleCierreApertura">Historial de acciones</h3>
                </div>
                <div class="modal-body">
                    <fieldset class="group-table" id="fieldsetHistorialAcciones">
                        <table class="mytable table table-condensed table-bordered table-okc-view"
                            id="listaHistorialAcciones" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Año</th>
                                    <th>Mes</th>
                                    <th>Empresa</th>
                                    <th>Almacén</th>
                                    <th>Acción</th>
                                    <th>Comentario</th>
                                    <th>Registrado por</th>
                                    <th>Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </fieldset>
                    
                </div>
                {{-- <div class="modal-footer">
                    <button type="submit" class="btn btn-success shadow-none">Guardar</button>
                </div> --}}
            </form>
        </div>
    </div>
</div>