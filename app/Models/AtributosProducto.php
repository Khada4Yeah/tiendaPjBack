<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributosProducto extends Model
{
    use HasFactory;

    protected $table = "atributos_productos";

    protected $primaryKey = "id_atributo_producto";

    public $timestamps = false;

    protected $fillable = ["id_producto", "id_atributo", "valor_unidad_medida"];

    protected $hidden = [
        "id_atributo_producto",
        "id_producto",
        "id_atributo",
        "valor_unidad_medida",
    ];

    protected $appends = [
        "productAtributesId",
        "productId",
        "attributeId",
        "valueUnitMeasure",
    ];

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, "id_atributo");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function getProductAtributesIdAttribute()
    {
        return $this->attributes["id_atributo_producto"];
    }

    public function getProductIdAttribute()
    {
        return $this->attributes["id_producto"];
    }

    public function getAttributeIdAttribute()
    {
        return $this->attributes["id_atributo"];
    }

    public function getValueUnitMeasureAttribute()
    {
        return $this->attributes["valor_unidad_medida"];
    }
}
