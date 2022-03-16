 
    <div class="seccion">
        <table>
        <tr>
            <td style="width:5px;"></td>
            <td style="width:20px;"><strong>{{ $requerimiento['requerimiento'][0]['razon_social_empresa'] !=null ?  $requerimiento['requerimiento'][0]['razon_social_empresa'] :''}}</strong></td>
            <td style="width:30px;"></td>
            <td style="width:10px;"></td>
        </tr>
        <tr>
            <td style="width:5px;"></td>
            <td style="width:20px;"><strong>RUC: {{$requerimiento['requerimiento'][0]['nro_documento_empresa'] !=null ?$requerimiento['requerimiento'][0]['nro_documento_empresa']:''}}</strong></td>
            <td style="width:30px;"></td>
            <td style="width:10px;"></td>
        </tr>
        <tr>
            <td style="width:5px;"></td>
            <td style="width:20px;"></td>
            <td style="width:30px; font-size:20px;"><strong>SOLICITUD DE COTIZACION N°</strong></td>
            <td style="width:10px;"></td>
        </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <td style="width:5px;"></td>
                <td style="width:20px;"><strong>PROVEEDOR:</strong></td>
                <td style="width:20px;"><strong>CONTACTO:</strong></td>
            </tr>
            <tr>
                <td style="width:5px;"></td>
                <td style="width:20px;"><strong>RUC:</strong></td>
            </tr>
            <tr>
                <td style="width:5px;"></td>
                <th style="width:20px;"><strong>FECHA COTIZACIÓN:</strong></th>
                <th style="width:20px;"><strong>TELÉFONO:</strong></th>
            </tr>
        </thead>
    </table>
    <br>
    <table border="1">
        <thead>
            <tr>
                <th style="width:5px;"><strong>ITEM</strong></th>
                <th style="width:50px;"><strong>DESCRIPCIÓN DEL BIEN O SERVICIO</strong></th>
                <th style="width:20px;"><strong>UNIDAD DE MEDIDA</strong></th>
                <th style="width:10px;"><strong>CANTIDAD</strong></th>
                <th style="width:20px;"><strong>PRECIO UNITARIO</strong></th>
                <th style="width:10px;"><strong>SUB-TOTAL</strong></th>
                <th style="width:20px;"><strong>PLAZO ENTREGA</strong></th>
                <th style="width:20px;"><strong>GARANTÍA</strong></th>
                <th style="width:10px;"><strong>FICHA TECNICA</strong></th>
            </tr>
        </thead>
        <tbody>
        @foreach(($requerimiento['det_req']) as $clave => $item)
            <tr>
                <td>{{$clave + 1}}</td>
                <td>{{$item['descripcion']}}</td>
                <td>{{$item['unidad_medida']}}</td>
                <td>{{$item['cantidad']}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
             </tr>

        @endforeach
        </tbody>
 

    </table>
    <div class="seccion">
        <table border="1">
        <tr>
            <td style="width:20px;"><strong>DEBE ADJUNTAR:</strong></td>
        </tr>
        <tr>
            <td style="width:20px;"><strong>* CONDICIONES COMERCIALES GENERALES</strong></td>
        </tr>
        <tr>
            <td style="width:20px;">TIPO DE COMPROBANTE</td>
        </tr>
        <tr>
            <td style="width:20px;">CONDICIÓN DE COMPRA (contado/crédito)</td>
        </tr>
        <tr>
            <td style="width:20px;">N° DE CUENTA EMPRESA</td>
        </tr>
        <tr>
            <td style="width:20px;">N° DE CUENTA DETRACCIONES</td>
        </tr>
        </table>


        <p>* GARANTIA Y FICHA TECNICA DEL BIEN</p>
    </div>