<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = "administradores";

    protected $primaryKey = "id_administrador";

    protected $fillable = ["id_usuario", "estado", "created_at", "updated_at"];

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
