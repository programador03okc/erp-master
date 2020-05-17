<div class="row">
    <!-- <div class="col-md-12">
        <button class="btn btn-xs btn-danger activation" onClick="detalleRequerimientoModal(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom" title="Descargar PDF" disabled>
            <i class="fas fa-file-pdf"></i> Descargar
        </button>
    </div> -->
    <div class="col-md-8">
        <table class="mytable table table-hover table-condensed table-bordered table-okc-view dataTable no-footer" id="listaHistorialAprobacion">
        <caption>HISTORIAL</caption>                
            <thead>
                <tr>
                    <th>Estado [Fase]</th>
                    <th>Usuario</th>
                    <th>Comentario</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="mytable table table-hover table-condensed table-bordered table-okc-view dataTable no-footer" id="listaFlujoAprobacion">
            <caption>FLUJO DE APROBACIÓN</caption>
            <thead>
                <tr>
                    <th>Nivel Aprob.</th>
                    <th>Fase</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>