@extends('layout.base')

@section('cabecera')
Módulos
@endsection

@section('body')

		@include('layout.header')

        <div class="okc-content">
            <section class="content">
            <div class="container">
                <div class="row">{!! $modulos !!}</div>
            </div>
            </section>
        </div>
    </div>
    
@endsection