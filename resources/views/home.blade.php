<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Sistema ERP</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('fonts/awesome/css/all.min.css')}}">
<link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.min.css')}}">
<link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/app.css')}}">
</head>
<body class="hold-transition skin-okc sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            @include('layout.navbar')
        </header>
        <div class="okc-content">
            <section class="content">
            <div class="container">
                <div class="row">{!! $modulos !!}</div>
            </div>
            </section>
        </div>
    </div>
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('template/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('template/dist/js/app.min.js')}}"></script>
</body>
</html>
