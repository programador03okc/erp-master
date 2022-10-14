@extends('layout.main')
@include('layout.menu_powerbi')

@section('cabecera') PowerBi - Cobranzas @endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <iframe title="Reporte Cobranzas" width="100%" height="550px" src="https://app.powerbi.com/reportEmbed?reportId=1f9023d3-9e78-4639-8133-21bf96d58210&autoAuth=true&ctid=e5cdaa4d-557f-46cd-80ee-ff584f5964ac" 
                frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
