<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = "categorias";

    protected $primaryKey = "id_categoria";

    protected $fillable = [
        "nombre",
        "descripcion",
        "estado",
        "created_at",
        "updated_at",
    ];

    protected $hidden = ["id_categoria", "nombre", "descripcion", "estado"];

    protected $appends = [
        "categoryId",
        "categoryName",
        "categoryDescription",
        "categoryStatus",
    ];

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function producto()
    {
        return $this->hasMany(Producto::class, "id_producto");
    }

    public function atributos()
    {
        return $this->hasMany(Atributo::class, "id_atributo");
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes["id_categoria"];
    }

    public function getCategoryNameAttribute()
    {
        return $this->attributes["nombre"];
    }

    public function getCategoryDescriptionAttribute()
    {
        return $this->attributes["descripcion"];
    }

    public function getCategoryStatusAttribute()
    {
        return $this->attributes["estado"];
    }
}
