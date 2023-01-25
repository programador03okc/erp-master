<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Incidencias con Historial</h2>
    <br>
    <br>

    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Código</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Empresa</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cliente</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Nro Orden</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Factura</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Quien reporto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Contacto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cargo / Area</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Teléfono</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Dirección</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha reporte</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Responsable</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Falla reportada</b></th>

                <th style="background-color: #cccccc;" width="18"><b>Código</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha Reporte	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Responsable</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Acciones realizadas</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha registro</b></th>

                <th style="background-color: #cccccc;" width="18"><b>Serie</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Marca	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Producto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Tipo</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Modelo</b></th>

                <th style="background-color: #cccccc;" width="18"><b>Tipo de falla</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Modo	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Tipo garantía</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Tipo de servicio</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Medio reporte</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Atiende	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Equipo operativo</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Conformidad</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Nro. de caso</b></th>

                <th style="background-color: #cccccc;" width="18"><b>DPTO</b></th>
                <th style="background-color: #cccccc;" width="18"><b>PROV</b></th>
                <th style="background-color: #cccccc;" width="18"><b>DISTRITO</b></th>
                <th style="background-color: #cccccc;" width="18"><b>COSTO</b></th>
                <th style="background-color: #cccccc;" width="18"><b>PARTE REEMPLAZADO</b></th>
                <th style="background-color: #cccccc;" width="18"><b>PARTE FALLADA</b></th>


            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->codigo}}</td>
                <td>{{$d->estado_doc}}</td>
                <td>{{$d->empresa_razon_social}}</td>
                <td>{{$d->cliente}}</td>
                <td>{{$d->nro_orden}}</td>
                <td>{{$d->factura}}</td>
                <td>{{$d->usuario_final}}</td>
                <td>{{$d->nombre_contacto}}</td>
                <td>{{$d->cargo_contacto}}</td>
                <td>{{$d->telefono_contacto}}</td>
                <td>{{$d->direccion_contacto}}</td>
                <td>{{date('d-m-Y', strtotime($d->fecha_reporte))}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->falla_reportada}}</td>
                <td>{{$d->id_incidencia_reporte}}</td>
                <td>{{ date('d-m-Y', strtotime($d->fecha_reporte_detalle))}}</td>
                <td>{{$d->nombre_corto_detalle}}</td>
                <td>{{$d->acciones_realizadas}}</td>
                <td>{{ date('d-m-Y h:i', strtotime($d->fecha_registro_detalle)) }}</td>


                <td>{{ $d->serie }}</td>
                <td>{{ $d->marca }}</td>
                <td>{{ $d->producto }}</td>
                <td>{{ $d->tipo }}</td>
                <td>{{ $d->modelo }}</td>

                <td>{{ $d->tipo_de_falla }}</td>
                <td>{{ $d->modo }}</td>
                <td>{{ $d->tipo_garantía }}</td>
                <td>{{ $d->tipo_de_servicio }}</td>
                <td>{{ $d->medio_reporte }}</td>
                <td>{{ $d->atiende }}</td>
                <td>{{ $d->equipo_operativo }}</td>
                <td>{{ $d->conformidad }}</td>
                <td>{{ $d->nro_de_caso }}</td>

                <td>{{ $d->departamento_text }}</td>
                <td>{{ $d->provincia_text }}</td>
                <td>{{ $d->distrito_text }}</td>
                <td>{{ $d->importe_gastado }}</td>
                <td>{{ $d->parte_reemplazada }}</td>
                <td>{{ $d->comentarios_cierre }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
</body>
</html>
