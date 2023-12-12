<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreciosProducto extends Model
{
    use HasFactory;

    protected $table = "precios_productos";

    protected $primaryKey = "id_precio_producto";

    public $timestamps = false;

    // COMO ASIGNAR TIPO DE DATO A CADA ATRIBUTO
    protected $casts = [
        "id_producto" => "integer",
        "precio_compra_sin_iva" => "double",
        "porcentaje_iva_compra" => "double",
        "precio_venta_sin_iva" => "double",
        "porcentaje_iva_venta" => "double",
        "fecha_actualizacion" => "date",
    ];

    protected $fillable = [
        "id_producto",
        "precio_compra_sin_iva",
        "porcentaje_iva_compra",
        "precio_venta_sin_iva",
        "porcentaje_iva_venta",
        "fecha_actualizacion",
    ];

    protected $hidden = [
        "id_precio_producto",
        "id_producto",
        "precio_compra_sin_iva",
        "porcentaje_iva_compra",
        "precio_venta_sin_iva",
        "porcentaje_iva_venta",
        "fecha_actualizacion",
    ];

    protected $appends = [
        "productPricesId",
        "productId",
        "purchasePriceWoIva",
        "purchaseIvaPercentage",
        "salePriceWoIva",
        "saleIvaPercentage",
        "updatedAt",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function precios_producto()
    {
        return $this->hasMany(PreciosProducto::class, "id_precio_producto");
    }

    public function getProductPricesIdAttribute()
    {
        return $this->attributes["id_precio_producto"];
    }

    public function getProductIdAttribute()
    {
        return $this->attributes["id_producto"];
    }

    public function getPurchasePriceWoIvaAttribute()
    {
        return $this->attributes["precio_compra_sin_iva"];
    }

    public function getPurchaseIvaPercentageAttribute()
    {
        return $this->attributes["porcentaje_iva_compra"];
    }

    public function getSalePriceWoIvaAttribute()
    {
        return $this->attributes["precio_venta_sin_iva"];
    }

    public function getSaleIvaPercentageAttribute()
    {
        return $this->attributes["porcentaje_iva_venta"];
    }

    public function getUpdatedAtAttribute()
    {
        return $this->attributes["fecha_actualizacion"];
    }
}
