<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $table = "marcas";

    protected $primaryKey = "id_marca";

    protected $dateFormat = "Y-m-d\TH:i:s";

    protected $fillable = ["nombre", "created_at", "updated_at"];

    protected $hidden = ["id_marca", "nombre"];

    protected $appends = ["brandId", "brandName"];

    public function producto()
    {
        return $this->hasMany(Producto::class, "id_producto");
    }

    public function getBrandIdAttribute()
    {
        return $this->attributes["id_marca"];
    }

    public function getBrandNameAttribute()
    {
        return $this->attributes["nombre"];
    }
}
