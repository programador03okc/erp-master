<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema ERP</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('datatables/Datatables/css/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('fonts/Awesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.css')}}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">
</head>
<body class="hold-transition skin-okc sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            @include('layout.navbar')
        </header>
        <aside class="main-sidebar">
            @include('layout.menu')
        </aside>
        <!-- contenido -->
        <div class="content-wrapper">
            @include('layout.option')
            <!-- Vistas -->
            <section class="content">