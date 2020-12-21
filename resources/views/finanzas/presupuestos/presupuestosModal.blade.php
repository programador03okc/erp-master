<div class="modal fade" id="presupuestosModal" tabindex="-1" role="dialog" aria-labelledby="presupuestosModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="presupuestosModalLabel">Lista de Presupuestos</h3>
            </div>
            <div class="modal-body">
                <table id="listaPresupuestos" class="table table-sm table-hover table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr style="background: gainsboro;">
                            <th scope="col">Código</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Grupo</th>
                            <th scope="col">Fecha</th>
                            <!-- <th scope="col">Mnd</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presupuestos as $item)
                        <tr value="{{ $item->id_presup }}">
                            <td>{{ $item->codigo }}</td>
                            <td>{{ $item->descripcion }}</td>
                            <td>{{ $item->grupo->descripcion }}</td>
                            <td>{{ $item->fecha_emision }}</td>
                        </tr>
                        @empty
                        <tr><td colSpan="2">No hay registros para mostrar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
