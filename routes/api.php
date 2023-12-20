<?php

use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AtributoController;
use App\Http\Controllers\AtributosProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UsuarioController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//?? RUTAS DE USUARIO ?/
//** Resource de usuarios */
Route::resource("usuarios", UsuarioController::class, [
    "except" => ["create", "edit"],
]);

//** Login de los usuarios */
Route::post("usuarios/login", [UsuarioController::class, "login"]);

//** Obtener la identidad del usuario a partir del token */
Route::get("usuarios/getIdentity/{token}", [
    UsuarioController::class,
    "getIdentity",
]);

//?? RUTAS DE CLIENTES ?/
//** Resource de clientes */
Route::resource("clientes", ClienteController::class, [
    "except" => ["create", "edit"],
]);

//?? RUTAS DE ADMINISTRADORES ?/
//** Resource de clientes */
Route::resource("administradores", AdministradorController::class, [
    "except" => ["create", "edit"],
]);

//?? RUTAS DE MARCAS ?/
//** Resource de marcas */
Route::resource("marcas", MarcaController::class, [
    "except" => ["create", "edit"],
]);

//?? RUTAS DE PROVEEDORES ?/
//** Resource de proveedores */
Route::resource("proveedores", ProveedorController::class, [
    "except" => ["create", "edit"],
]);

//?? RUTAS DE CATEGORIAS ?/
//** Resource de categorias */
Route::resource("categorias", CategoriaController::class, [
    "except" => ["create", "edit"],
]);

//?? RUTAS DE ATRIBUTOS ?/
//** Resource de atributos */
Route::resource("atributos", AtributoController::class, [
    "except" => ["create", "edit"],
]);

//** Obtener los atributos por categoria */
Route::get("atributos/atributosPorCategoria/{idCategoria}", [
    AtributoController::class,
    "obtenerAtributosPorCategoria",
]);

//?? RUTAS DE ATRIBUTOS_PRODUCTOS ?/
//** Resource de atributos_productos */
Route::resource("atributos_productos", AtributosProductoController::class, [
    "except" => ["create", "edit"],
]);

//** Obtener los atributos por categoria */
Route::get("atributos/atributosPorCategoria/{idCategoria}", [
    AtributoController::class,
    "obtenerAtributosPorCategoria",
]);

//?? RUTAS DE PRODUCTOS ?/
//** Productos para el Home */
Route::get("productos/indexHome", [ProductoController::class, "indexHome"]);

//** Productos para la tabla del CMS */
Route::get("productos/indexCms", [ProductoController::class, "indexCms"]);

//** Cambiar estado del producto */
Route::patch("productos/cambiarEstado/{idProducto}", [
    ProductoController::class,
    "cambiarEstadoProducto",
]);

//** Resource de productos */
Route::apiResource("productos", ProductoController::class);

//** Obtener todos los productos segun la categoria */
Route::get("productos/productosCategoria/{idCategoria}", [
    ProductoController::class,
    "obtenerProductosPorCategoria",
]);

//** Obtener la imagen del producto */
Route::get("recursos/imagenes/productos/{rutaImagen}", [
    ProductoController::class,
    "obtenerImagenProducto",
]);
