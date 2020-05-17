<div class="row">
    <!-- <div class="col-md-12">
        <button class="btn btn-xs btn-danger activation" onClick="detalleRequerimientoModal(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom" title="Descargar PDF" disabled>
            <i class="fas fa-file-pdf"></i> Descargar
        </button>
    </div> -->

    <div class="col-md-12">
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#historial" aria-controls="historial" role="tab" data-toggle="tab">Historial</a></li>
                <li role="presentation"><a href="#flujo-aprobacion" aria-controls="flujo-aprobacion" role="tab" data-toggle="tab">Flujo de Aprobación</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="historial">
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
                <div role="tabpanel" class="tab-pane" id="flujo-aprobacion">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view dataTable no-footer" id="listaFlujoAprobacion">
                    <caption>FLUJO DE APROBACIÓN</caption>
                    <thead>
                        <tr>
                            <th>Nivel Aprob.</th>
                            <th>Fase</th>
                            <th>Usuario</th>
                            <th>Criterio Monto</th>
                            <th>Criterio Prioridad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

</div>