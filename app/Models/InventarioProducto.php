<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioProducto extends Model
{
    use HasFactory;

    protected $table = "inventario_productos";

    protected $primaryKey = "id_inventario_producto";

    public $timestamps = false;

    protected $fillable = ["id_producto", "id_precio_producto", "cantidad"];

    protected $hidden = [
        "id_inventario_producto",
        "id_producto",
        "id_precio_producto",
        "cantidad",
    ];

    protected $appends = [
        "inventoryProductId",
        "productId",
        "productPricesId",
        "quantity",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function precios_producto()
    {
        return $this->belongsTo(PreciosProducto::class, "id_precio_producto");
    }

    public function getInventoryProductIdAttribute()
    {
        return $this->attributes["id_inventario_producto"];
    }

    public function getProductIdAttribute()
    {
        return $this->attributes["id_producto"];
    }

    public function getProductPricesIdAttribute()
    {
        return $this->attributes["id_precio_producto"];
    }

    public function getQuantityAttribute()
    {
        return $this->attributes["cantidad"];
    }
}
