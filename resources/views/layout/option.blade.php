@section('option')
<div class="option-function">
    {{-- <button type="button" class="btn-okc" id="btnNuevo"><i class="fas fa-file fa-lg"></i> Nuevo</button>
    <button type="submit" class="btn-okc" id="btnGuardar"><i class="fas fa-save fa-lg"></i> Guardar</button>
    <button type="submit" class="btn-okc" id="btnEditar"><i class="fas fa-edit fa-lg"></i> Editar</button>
    <button type="submit" class="btn-okc" id="btnAnular"><i class="fas fa-trash fa-lg"></i> Anular</button>
    <button type="button" class="btn-okc" id="btnHistorial"><i class="fas fa-folder fa-lg"></i> Historial</button>
    <button type="button" class="btn-okc" id="btnCopiar" disabled ><i class="fas fa-copy fa-lg"></i> Copiar</button>
    <button type="button" class="btn-okc" id="btnCancelar"><i class="fas fa-times fa-lg"></i> Cancelar</button> --}}

    @if (!empty($modulo))
        @switch($modulo)
            @case('necesidades')
                @if (in_array(1,$array_accesos_botonera))
                <button type="button" class="btn-okc" id="btnNuevo"><i class="fas fa-file fa-lg"></i> Nuevo</button>
                @endif
                @if (in_array(2,$array_accesos_botonera))
                <button type="submit" class="btn-okc" id="btnGuardar"><i class="fas fa-save fa-lg"></i> Guardar</button>
                @endif
                @if (in_array(3,$array_accesos_botonera))
                <button type="submit" class="btn-okc" id="btnEditar"><i class="fas fa-edit fa-lg"></i> Editar</button>
                @endif
                @if (in_array(4,$array_accesos_botonera))
                <button type="submit" class="btn-okc" id="btnAnular"><i class="fas fa-trash fa-lg"></i> Anular</button>
                @endif
                @if (in_array(5,$array_accesos_botonera))
                <button type="button" class="btn-okc" id="btnHistorial"><i class="fas fa-folder fa-lg"></i> Historial</button>
                @endif
                @if (in_array(6,$array_accesos_botonera))
                <button type="button" class="btn-okc" id="btnCopiar" disabled ><i class="fas fa-copy fa-lg"></i> Copiar</button>
                @endif
                @if (in_array(7,$array_accesos_botonera))
                <button type="button" class="btn-okc" id="btnCancelar"><i class="fas fa-times fa-lg"></i> Cancelar</button>
                @endif
            @break
            @default
        @endswitch
    @else
        <input type="hidden" name="" value="sin accesos">
        <button type="button" class="btn-okc" id="btnNuevo"><i class="fas fa-file fa-lg"></i> Nuevo</button>
        <button type="submit" class="btn-okc" id="btnGuardar"><i class="fas fa-save fa-lg"></i> Guardar</button>
        <button type="submit" class="btn-okc" id="btnEditar"><i class="fas fa-edit fa-lg"></i> Editar</button>
        <button type="submit" class="btn-okc" id="btnAnular"><i class="fas fa-trash fa-lg"></i> Anular</button>
        <button type="button" class="btn-okc" id="btnHistorial"><i class="fas fa-folder fa-lg"></i> Historial</button>
        <button type="button" class="btn-okc" id="btnCopiar" disabled ><i class="fas fa-copy fa-lg"></i> Copiar</button>
        <button type="button" class="btn-okc" id="btnCancelar"><i class="fas fa-times fa-lg"></i> Cancelar</button>
    @endif
</div>

@endsection

