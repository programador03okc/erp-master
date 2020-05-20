@include('layout.head')
@include('layout.menu_contabilidad')
@include('layout.body')
<div class="page-main" type="cta_contable">
    <legend class="mylegend">
        <h2>Plan de Cuentas Contables</h2>
    </legend>
    {{-- <input type="hidden" name="_token" value="{{csrf_token()}}" id="token"> --}}
    <div class="row">
        <div id="tree"></div>
    </form>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/contabilidad/cta_contable.js')}}"></script>
@include('layout.fin_html')