<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <!--[if mso]>
    <noscript>
    <xml>
    <o:OfficeDocumentSettings>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style>
        table, td, div, h1, p {font-family: Arial, sans-serif;}
        table, td {border:0px solid #000000 !important;}
    </style>

    </head>
    <body style="margin:0;padding:0; background-color:#e0e0e0 ">

        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#e0e0e0;">
            <tr>
                <td align="center" style="padding:0;">
                    <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                        <tr>
                            <td style="padding:0; background:#fff;text-align: center;" >
                                <img src="{{ asset('images/logo_okc.png') }}" alt="" width="" height="120px" style="text-align: center;" />
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:36px 30px 42px 30px; background:#fff">
                                <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0; background:#fff;">
                                    <tr>
                                        <td style="padding:0;text-align: center;">
                                            <h1>Recuperar Clave</h1>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding:0;text-align: center;">
                                            <P>Código de verificación</P>
                                            <p>{{$data->codigo}}</p>
                                            {{-- <P>Señor(a): {{$contact->nombre}} </P>
                                            <p> Se le respondera a su correo ( {{$contact->email}} ) o se le llamara a su número personal {{$contact->telefono}}</p> --}}

                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:30px;text-align: center;">
                                            <a href="{{route("recuperar.clave.ingresar")}}" style="padding: 15PX;background-color:#940206;border-radius: 23px;
                                            color: #ffff;
                                            text-decoration: none;">INGRESAR AHORA</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:30px; background:#940206">
                                <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0; background:#fffff;">
                                    <tr>
                                        <td style="" align="center">
                                            <img src="{{ asset('images/logo_okc.png') }}" height="100px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="" align="center">
                                            <a href="mailto:pyventas01@proyectec.com.pe" style="color: #fff;text-decoration: none">pyventas01@proyectec.com.pe</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="tel:+51 966 003 009" style="color: #fff; text-decoration: none">+51 966 003 009</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </body>

</html>
