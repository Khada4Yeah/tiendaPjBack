<?php

namespace App\Http\Controllers;

use App\Models\AtributosProducto;
use App\Models\InventarioProducto;
use App\Models\PreciosProducto;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request as Rq;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int $pagina
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productos = Producto::with([
            "categoria",
            "proveedor",
            "marca",
            "precios_producto",
            "inventario_producto",
            "atributos_producto.atributo",
        ])->paginate(10);

        $productos->getCollection()->transform(function ($producto) {
            $producto->setAttribute("categoryProduct", $producto->categoria);
            unset($producto->categoria);

            $producto->setAttribute("providerProduct", $producto->proveedor);
            unset($producto->proveedor);

            $producto->setAttribute("brandProduct", $producto->marca);
            unset($producto->marca);

            $producto->setAttribute(
                "pricesProduct",
                $producto->precios_producto
            );
            unset($producto->precios_producto);

            $producto->setAttribute(
                "product_inventory",
                $producto->inventario_producto
            );
            unset($producto->inventario_producto);

            // Mapear los atributos sin eliminar la relación "atributo"
            $producto->setAttribute(
                "attributesProduct",
                $producto->atributos_producto->map(function ($atr) {
                    return [
                        "attribute" => $atr->atributo,
                        "productAtributesId" => $atr->productAtributesId,
                        "productId" => $atr->productId,
                        "attributeId" => $atr->attributeId,
                        "valueUnitMeasure" => $atr->valueUnitMeasure,
                    ];
                })
            );
            unset($producto->atributos_producto);

            return $producto;
        });

        $filteredProducts = collect($productos)
            ->except([
                "current_page",
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "links",
                "next_page_url",
                "path",
                "per_page",
                "prev_page_url",
                "to",
            ])
            ->values();

        return response()->json($filteredProducts, 200);
    }

    /**
     * Obtener todos los productos para el Home
     *
     * @return \Illuminate\Http\Response
     */
    public function indexHome()
    {
        $productos = DB::table("productos", "p")
            ->join("categorias AS c", "c.id_categoria", "p.id_categoria")
            ->join("marcas AS m", "m.id_marca", "p.id_marca")
            ->join("precios_productos AS pp", "pp.id_producto", "p.id_producto")
            ->select(
                "p.id_producto AS productId",
                "p.nombre AS productName",
                "p.descripcion AS productDescription",
                "p.modelo AS productModel",
                "p.ruta_imagen AS productImageUrl",
                "c.nombre AS categoryName",
                "m.nombre AS brandName",
                "pp.precio_venta_sin_iva AS salePriceWoIva",
                "pp.porcentaje_iva_venta AS ivaPercentage"
            )
            ->get();

        $filteredProducts = collect($productos)
            ->except([
                "current_page",
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "links",
                "next_page_url",
                "path",
                "per_page",
                "prev_page_url",
                "to",
            ])
            ->values();

        return response()->json($filteredProducts, 200);
    }

    /**
     * Obtener todos los productos para la tabla CMS
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCms()
    {
        $productos = DB::table("productos", "p")
            ->join("marcas AS m", "m.id_marca", "p.id_marca")
            ->select(
                "p.id_producto AS productId",
                "p.nombre AS productName",
                "p.EAN AS productCode",
                "p.descripcion AS productDescription",
                "p.modelo AS productModel",
                "p.ruta_imagen AS productImageUrl",
                "p.estado AS productStatus",
                "m.nombre AS brandName"
            )
            ->orderBy("p.nombre", "ASC")
            ->get();

        return response()->json($productos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // RECOGER LOS DATOS POR POST
        $producto_request = json_decode(
            $request->input("producto", null),
            true
        );
        $precios_request = json_decode(
            $request->input("precios_producto", null),
            true
        );
        $atributos_request = json_decode(
            $request->input("atributos_producto", null),
            true
        );
        $imagen_request = $request->file("imagen", null);
        $cantidad_request = $request->input("cantidad", null);

        if (
            !empty($producto_request) &&
            !empty($precios_request) &&
            !empty($atributos_request) &&
            is_file($imagen_request) &&
            !empty($cantidad_request)
        ) {
            // return response()->json([
            //     "producto_request" => $producto_request,
            //     "precios_request" => $precios_request,
            //     "atributos_request" => $atributos_request,
            //     "imagen_request" => $imagen_request,
            //     "cantidad_request" => $cantidad_request,
            // ]);

            $producto_request = array_map("trim", $producto_request);
            $atributos_request = array_map("trim", $atributos_request);

            // VALIDAR DATOS DEL PRODUCTO
            $validar_producto = Validator::make($producto_request, [
                "id_categoria" => "required",
                "id_proveedor" => "required",
                "id_marca" => "required",
                "EAN" => "required",
                "codigo" => "required",
                "nombre" => "required",
                "descripcion" => "required",
                "modelo" => "required",
                "ruta_imagen" => "required",
            ]);

            if ($validar_producto->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json([
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al validar los datos del producto",
                    "errores" => $validar_producto->errors(),
                ]);
            }

            // VALIDAR DATOS DEL PRECIO DEL PRODUCTO
            $validar_precios = Validator::make($precios_request, [
                "precio_compra_sin_iva" => "required|numeric",
                "porcentaje_iva_compra" => "required|numeric",
                "precio_venta_sin_iva" => "required|numeric",
                "porcentaje_iva_venta" => "required|numeric",
            ]);

            if ($validar_precios->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json([
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al validar los precios",
                    "errores" => $validar_precios->errors(),
                ]);
            }

            // VALIDAR IMAGEN DEL PRODUCTO
            $validar_imagen = Validator::make(
                ["archivo" => $imagen_request],
                [
                    "archivo" => "image",
                ]
            );

            if ($validar_precios->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json([
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al validar la imagen",
                    "errores" => $validar_imagen->errors(),
                ]);
            }

            // VALIDAR CANTIDAD DEL PRODUCTO
            $validar_cantidad = Validator::make(
                ["cantidad" => $cantidad_request],
                [
                    "cantidad" => "required|numeric|min:1",
                ]
            );

            if ($validar_cantidad->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json([
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al validar la cantidad",
                    "errores" => $validar_cantidad->errors(),
                ]);
            }

            try {
                DB::beginTransaction();

                // VALIDACION CORRECTA

                // CREAR EL PRODUCTO
                $producto = new Producto();
                $producto->id_categoria = $producto_request["id_categoria"];
                $producto->id_proveedor = $producto_request["id_proveedor"];
                $producto->id_marca = $producto_request["id_marca"];
                $producto->EAN = $producto_request["EAN"];
                $producto->codigo = $producto_request["codigo"];
                $producto->nombre = mb_strtoupper($producto_request["nombre"]);
                $producto->descripcion = $producto_request["descripcion"];
                $producto->modelo = $producto_request["modelo"];

                // GENERAR UN ID UNICO PARA LA IMAGEN
                $nombre_imagen =
                    Str::uuid() . "." . $imagen_request->guessExtension();
                $producto->ruta_imagen = $nombre_imagen;

                // GUARDAR EL PRODUCTO
                $producto->save();

                // GUARDAR LOS ATRIBUTOS DEL PRODUCTO
                foreach ($atributos_request as $id => $atributo) {
                    $atributos_producto = new AtributosProducto();

                    $atributos_producto->id_atributo = $id;
                    $atributos_producto->id_producto = $producto->id_producto;
                    $atributos_producto->valor_unidad_medida = $atributo;

                    $atributos_producto->save();
                }

                $precios_producto = new PreciosProducto();
                $precios_producto->id_producto = $producto->id_producto;
                $precios_producto->precio_compra_sin_iva =
                    $precios_request["precio_compra_sin_iva"];
                $precios_producto->porcentaje_iva_compra =
                    $precios_request["porcentaje_iva_compra"];
                $precios_producto->precio_venta_sin_iva =
                    $precios_request["precio_venta_sin_iva"];
                $precios_producto->porcentaje_iva_venta =
                    $precios_request["porcentaje_iva_venta"];
                $precios_producto->fecha_actualizacion = Carbon::now();

                $precios_producto->save();

                $inventario_producto = new InventarioProducto();
                $inventario_producto->id_producto = $producto->id_producto;
                $inventario_producto->id_precio_producto =
                    $precios_producto->id_precio_producto;
                $inventario_producto->cantidad = $cantidad_request;

                $inventario_producto->save();

                // GUARDAR LA IMAGEN DEL PRODUCTO
                $imagen_request->storeAs("public/productos", $nombre_imagen);

                DB::commit();

                return response()->json([
                    "code" => 200,
                    "status" => "success",
                ]);
            } catch (\Exception $th) {
                DB::rollBack();

                return response()->json([
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al guardar los datos",
                    "errores" => $th,
                ]);
            }
        } else {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Error al validar los datos",
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int idProducto
     * @return \Illuminate\Http\Response444
     */
    public function show($idProducto)
    {
        $producto = Producto::with([
            "categoria",
            "proveedor",
            "marca",
            "precios_producto",
            "inventario_producto",
            "atributos_producto",
        ])
            ->where("id_producto", $idProducto)
            ->first();

        $producto = [
            "producto" => [
                "id_producto" => $producto->id_producto,
                "id_categoria" => $producto->id_categoria,
                "id_proveedor" => $producto->id_proveedor,
                "id_marca" => $producto->id_marca,
                "EAN" => $producto->EAN,
                "codigo" => $producto->codigo,
                "nombre" => $producto->nombre,
                "descripcion" => $producto->descripcion,
                "modelo" => $producto->modelo,
                "ruta_imagen" => $producto->ruta_imagen,
                "estado" => $producto->estado,
            ],
            "categoria" => $producto->categoria,
            "proveedor" => $producto->proveedor,
            "marca" => $producto->marca,
            "precios_producto" => $producto->precios_producto,
            "inventario_producto" => $producto->inventario_producto,
            "atributos_producto" => $producto->atributos_producto,
        ];

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $producto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int idProducto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idProducto)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
    }

    /**
     * Cambiar estado del producto
     *
     * @param  int $idProducto
     * @return \Illuminate\Http\Response
     */

    public function cambiarEstadoProducto($idProducto)
    {
        $producto = Producto::find($idProducto);

        if ($producto) {
            $producto->estado = $producto->estado == "A" ? "I" : "A";
            $producto->save();

            return response()->json([
                "code" => 200,
                "status" => "success",
                "message" => "Estado del producto actualizado correctamente",
            ]);
        } else {
            return response()->json([
                "code" => 404,
                "status" => "error",
                "message" => "El producto no existe",
            ]);
        }
    }

    /**
     * Obtener todos los productos con sus atributos según la categoría
     *
     * @param  int $idCategoria
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function obtenerProductosPorCategoria($idCategoria)
    {
        $productos_categoria = DB::table("productos as pr")
            ->join("marcas as ma", "ma.id_marca", "=", "pr.id_marca")
            ->join(
                "categorias as ca",
                "ca.id_categoria",
                "=",
                "pr.id_categoria"
            )
            ->select(
                "ca.id_categoria",
                "pr.id_producto",
                "ma.nombre",
                "pr.codigo",
                "pr.nombre",
                "pr.descripcion",
                "pr.modelo"
            )
            ->where("ca.id_categoria", "=", $idCategoria)
            ->where("pr.estado", "=", "A")
            ->get();

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $productos_categoria,
        ]);
    }

    /**
     * Obtener todos los productos con sus atributos
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function obtenerProductosAtributos()
    {
        //
    }
}
