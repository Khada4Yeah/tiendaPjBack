<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = "usuarios";

    protected $primaryKey = "id_usuario";

    protected $fillable = [
        "cedula",
        "nombres",
        "apellido_p",
        "apellido_m",
        "sexo",
        "fecha_nacimiento",
        "celular",
        "correo_electronico",
        "estado",
        "created_at",
        "updated_at",
    ];

    protected $hidden = ["clave"];

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, "id_usuario");
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, "id_usuario");
    }
}
