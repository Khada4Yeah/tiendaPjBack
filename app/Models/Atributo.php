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

    protected $hidden = [
        "id_atributo",
        "id_categoria",
        "nombre",
        "unidad_de_medida",
    ];

    protected $appends = [
        "attributeId",
        "categoryId",
        "attributeName",
        "unitOfMeasure",
    ];

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

    public function atributos_producto()
    {
        return $this->hasMany(AtributosProducto::class, "id_atributo_producto");
    }

    public function getAttributeIdAttribute()
    {
        return $this->attributes["id_atributo"];
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes["id_categoria"];
    }

    public function getAttributeNameAttribute()
    {
        return $this->attributes["nombre"];
    }

    public function getUnitOfMeasureAttribute()
    {
        return $this->attributes["unidad_de_medida"];
    }
}
