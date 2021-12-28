<div class="modal fade" tabindex="-1" role="dialog" id="modal-lista-oc-softlink" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista OC - Softlink</h3>
            </div>
            <div class="modal-body">

            <div style="display:flex;">
                <input type="date" class="form-control handleChangeFiltroFechaVincularOcSoftlink" value={{ date('Y-m-d H:i:s') }} name="filtroFecha" style="width: 20rem;">
                <select class="form-control handleChangeFiltroEmpresaVincularOcSoftlink" name="filtroEmpresa" style="width: 100%;">
                @foreach ($empresas as $empresa)
                    @if($empresa->id_empresa ==1)
                        <option value="{{$empresa->id_empresa}}" data-codigo-empresa="{{$empresa->codigo}}" selected>{{$empresa->razon_social}}</option>
                    @else
                    <option value="{{$empresa->id_empresa}}" data-codigo-empresa="{{$empresa->codigo}}">{{$empresa->razon_social}}</option>
                    @endif
                @endforeach
                </select>
            </div>
            <br>
                <table class="table table-condensed table-bordered table-okc-view" width="100%" id="listaOcSoftlink">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código OC</th>
                            <th>Proveedor</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>