<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
    <link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/DataTables/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/Buttons/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('template/plugins/iCheck/line/blue.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/gantt/dhtmlxgantt.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">
    @yield('styles_modulo')
    @yield('styles_seccion')
</head>
<body class="hold-transition skin-okc sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            @include('layout.navbar')
        </header>
        <aside class="main-sidebar">
