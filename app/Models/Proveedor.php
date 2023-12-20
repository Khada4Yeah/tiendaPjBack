<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = "proveedores";

    protected $primaryKey = "id_proveedor";

    protected $dateFormat = "Y-m-d\TH:i:s";

    protected $fillable = [
        "nombre",
        "direccion",
        "telefono",
        "celular",
        "correo_electronico",
        "descripcion",
        "estado",
        "created_at",
        "updated_at",
    ];

    protected $hidden = [
        "id_proveedor",
        "nombre",
        "direccion",
        "telefono",
        "celular",
        "correo_electronico",
        "descripcion",
        "estado",
    ];

    protected $appends = [
        "providerId",
        "providerName",
        "providerAddress",
        "providerPhone",
        "providerCellphone",
        "providerEmail",
        "providerDescription",
        "providerStatus",
    ];

    public function producto()
    {
        return $this->hasMany(Producto::class, "id_producto");
    }

    public function getProviderIdAttribute()
    {
        return $this->attributes["id_proveedor"];
    }

    public function getProviderNameAttribute()
    {
        return $this->attributes["nombre"];
    }

    public function getProviderAddressAttribute()
    {
        return $this->attributes["direccion"];
    }

    public function getProviderPhoneAttribute()
    {
        return $this->attributes["telefono"];
    }

    public function getProviderCellphoneAttribute()
    {
        return $this->attributes["celular"];
    }

    public function getProviderEmailAttribute()
    {
        return $this->attributes["correo_electronico"];
    }

    public function getProviderDescriptionAttribute()
    {
        return $this->attributes["descripcion"];
    }

    public function getProviderStatusAttribute()
    {
        return $this->attributes["estado"];
    }
}
