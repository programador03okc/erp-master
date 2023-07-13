@extends('layout.main')
@include('layout.menu_powerbi')

@section('cabecera') PowerBi - Inventario @endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <iframe title="Reporte Inventario" width="100%" height="550px" src="https://app.powerbi.com/reportEmbed?reportId=ccfdc3ed-4d74-4b3b-9131-5a707d97819b&autoAuth=true&ctid=e5cdaa4d-557f-46cd-80ee-ff584f5964ac"
                frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <iframe title="Reporte Inventario" width="100%" height="550px" src="https://app.powerbi.com/reportEmbed?reportId=64e4aa23-2ff3-4d80-9ce6-4118f3a2a16e&autoAuth=true&ctid=e5cdaa4d-557f-46cd-80ee-ff584f5964ac"
                frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
