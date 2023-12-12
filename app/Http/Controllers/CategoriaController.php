<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware("api.auth");
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorias = Categoria::orderBy("nombre", "asc")->get();

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $categorias,
        ]);
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
        $json = $request->input("json", null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // LIMPIAR DATOS
            $params_array = array_map("trim", $params_array);

            // VALIDAR DATOS
            $validar_datos = Validator::make($params_array, [
                "nombre" => "required",
            ]);

            if ($validar_datos->fails()) {
                // LA VALIDACION HA FALLADO
                $data = [
                    "code" => 400,
                    "status" => "error",
                    "message" => "Error al validar los datos",
                    "errores" => $validar_datos->errors(),
                ];
            } else {
                try {
                    DB::beginTransaction();

                    // VALIDACION CORRECTA

                    // CREAR LA CATEGORIA
                    $categoria = new Categoria();
                    $categoria->nombre = mb_strtoupper($params_array["nombre"]);
                    $categoria->descripcion = array_key_exists(
                        "descripcion",
                        $params_array
                    )
                        ? $params_array["descripcion"]
                        : null;

                    // GUARDAR LA CATEGORIA
                    $categoria->save();

                    DB::commit();

                    $data = [
                        "code" => 200,
                        "status" => "success",
                    ];
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json([
                        "code" => 400,
                        "status" => "error",
                        "message" => "Error al guardar los datos",
                        "errores" => $th,
                    ]);
                }
            }
        } else {
            $data = [
                "code" => 400,
                "status" => "error",
                "message" => "Error al enviar los datos",
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        //
    }
}
