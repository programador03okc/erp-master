@extends('layout.main')
@include('layout.menu_powerbi')

@section('cabecera') PowerBi - Cobranzas @endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <iframe title="Reporte Cobranzas" width="100%" height="550px" src="https://app.powerbi.com/view?r=eyJrIjoiNDExMzk1OTItMzQ2ZS00MDNjLTkxNjUtZTc1ZTU3OTAxYTFiIiwidCI6ImU1Y2RhYTRkLTU1N2YtNDZjZC04MGVlLWZmNTg0ZjU5NjRhYyJ9"
                frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
