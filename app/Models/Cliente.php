<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = "clientes";

    protected $primaryKey = "id_cliente";

    protected $fillable = [
        "id_usuario",
        "direccion_envio",
        "informacion_pago",
        "estado",
        "created_at",
        "updated_at",
    ];

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario");
    }
}
