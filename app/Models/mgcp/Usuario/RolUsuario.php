<?php

namespace App\Models\mgcp\Usuario;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model {
    
    // use HasFactory;
    protected $table = 'mgcp_usuarios.roles_usuario';
    public $timestamps = false;
    
    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }
   
}
