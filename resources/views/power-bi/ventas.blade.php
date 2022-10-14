@extends('layout.main')
@include('layout.menu_powerbi')

@section('cabecera') PowerBi - Ventas @endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <iframe title="Reporte Ventas" width="100%" height="550px" src="https://app.powerbi.com/view?r=eyJrIjoiMmY1M2Y0NDQtZDhhNi00ODY5LWJhYmMtODNiYTBjNDg3MmEwIiwidCI6ImU1Y2RhYTRkLTU1N2YtNDZjZC04MGVlLWZmNTg0ZjU5NjRhYyJ9"
                frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
