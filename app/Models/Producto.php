<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = "productos";

    protected $primaryKey = "id_producto";

    protected $fillable = [
        "id_categoria",
        "id_proveedor",
        "id_marca",
        "EAN",
        "codigo",
        "nombre",
        "descripcion",
        "modelo",
        "ruta_imagen",
        "estado",
    ];

    protected $hidden = [
        "id_producto",
        "id_categoria",
        "id_proveedor",
        "id_marca",
        "EAN",
        "codigo",
        "nombre",
        "descripcion",
        "modelo",
        "ruta_imagen",
        "estado",
    ];

    protected $appends = [
        "productId",
        "categoryId",
        "providerId",
        "brandId",
        "barcode",
        "code",
        "productName",
        "productDescription",
        "productModel",
        "productImageUrl",
        "productStatus",
    ];

    public $timestamps = false;

    public function fromDateTime($value)
    {
        return Carbon::parse(parent::fromDateTime($value))->format(
            "Y-d-m H:i:s"
        );
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, "id_proveedor");
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, "id_marca");
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, "id_categoria");
    }

    public function atributo()
    {
        return $this->belongsToMany(
            Atributo::class,
            "atributos_productos",
            "id_producto",
            "id_atributo"
        )->withPivot("valor_unidad_medida");
    }

    public function precios_producto()
    {
        return $this->hasMany(PreciosProducto::class, "id_precio_producto");
    }

    public function inventario_producto()
    {
        return $this->hasMany(
            InventarioProducto::class,
            "id_inventario_producto"
        );
    }

    public function getProductIdAttribute()
    {
        return $this->attributes["id_producto"];
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes["id_categoria"];
    }

    public function getProviderIdAttribute()
    {
        return $this->attributes["id_proveedor"];
    }

    public function getBrandIdAttribute()
    {
        return $this->attributes["id_marca"];
    }

    public function getBarcodeAttribute()
    {
        return $this->attributes["EAN"];
    }

    public function getCodeAttribute()
    {
        return $this->attributes["codigo"];
    }

    public function getProductNameAttribute()
    {
        return $this->attributes["nombre"];
    }

    public function getProductDescriptionAttribute()
    {
        return $this->attributes["descripcion"];
    }

    public function getProductModelAttribute()
    {
        return $this->attributes["modelo"];
    }

    public function getProductImageUrlAttribute()
    {
        return $this->attributes["ruta_imagen"];
    }

    public function getProductStatusAttribute()
    {
        return $this->attributes["estado"];
    }
}
