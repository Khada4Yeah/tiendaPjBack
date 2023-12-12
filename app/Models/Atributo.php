<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = "atributos";

    protected $primaryKey = "id_atributo";

    public $timestamps = false;

    protected $fillable = ["id_categoria", "nombre", "unidad_de_medida"];

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, "id_categoria");
    }

    public function producto()
    {
        return $this->belongsToMany(
            Producto::class,
            "atributos_productos",
            "id_atributo",
            "id_producto"
        )->withPivot("valor_unidad_medida");
    }
}
