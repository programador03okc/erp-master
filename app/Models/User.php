<?php

namespace App\Models;

use App\Models\mgcp\Usuario\RolUsuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'mgcp_usuarios.users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tieneRol($rol)
    {
        if (RolUsuario::where('id_usuario', $this->id)->where('id_rol', $rol)->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function obtenerPorRol($rol)
    {
        return User::whereRaw('id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE id_rol=?)', [$rol])->orderBy('name', 'asc')->get();
    }
}
