<?php

namespace Tests\Feature\Logistica;

use App\Models\Configuracion\Usuario;
use App\Models\Rrhh\Persona;
use App\Models\Rrhh\Postulante;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class ProveedorTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected $usuario;

    public function setUp():void{
        parent::setUp();
        Session::start();
        $this->usuario = factory(Usuario::class)->create();
    }

    public function test_puede_acceder_formulario_lista()
    {
        
        $response = $this->actingAs($this->usuario)->get(route('logistica.gestion-logistica.proveedores.index'));
        $response->assertSee('Listado de proveedores');
    }

    public function test_puede_crear_proveedor_sin_ruc()
    {
        //Data sin RUC
        $data=[
            'tipoContribuyente'=>'1',
            'tipoDocumentoIdentidad'=>'2',
            'razonSocial'=>'RAULs EIRL',
            '_token'=>csrf_token()
        ];
        $response = $this->actingAs($this->usuario)->post(route('logistica.gestion-logistica.proveedores.guardar', $data));
        $response->assertJson(['status'=>'warning']);
        
    }

    public function test_puede_crear_proveedor_sin_razon_social()
    {
        //Data sin RUC
        $data=[
            'tipoContribuyente'=>'1',
            'tipoDocumentoIdentidad'=>'2',
            'nroDocumento'=>'12345678',
            '_token'=>csrf_token()
        ];
        $response = $this->actingAs($this->usuario)->post(route('logistica.gestion-logistica.proveedores.guardar', $data));
        $response->assertJson(['status'=>'warning']);
        
    }

    public function test_puede_crear_proveedor()
    {
        $data=[
            'tipoContribuyente'=>'1',
            'tipoDocumentoIdentidad'=>'2',
            'nroDocumento'=>'12345678',
            'razonSocial'=>'RAULs EIRL',
            '_token'=>csrf_token()
        ];
        $response = $this->actingAs($this->usuario)->post(route('logistica.gestion-logistica.proveedores.guardar', $data));
        $response->assertJson(['status'=>'success']);
    }
}
