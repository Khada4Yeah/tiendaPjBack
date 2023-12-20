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

        return response()->json($categorias, 200);
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
                "categoryName" => "required|string",
                "categoryDescription" => "required|string",
            ]);

            if ($validar_datos->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json(
                    [
                        "message" => "Error al validar los datos...",
                        "errors" => $validar_datos->errors(),
                    ],
                    400
                );
            } else {
                try {
                    DB::beginTransaction();

                    // VALIDACION CORRECTA

                    // CREAR LA CATEGORIA
                    $categoria = new Categoria();
                    $categoria->nombre = mb_strtoupper(
                        $params_array["categoryName"]
                    );
                    $categoria->descripcion =
                        $params_array["categoryDescription"];
                    $categoria->estado = "A";

                    // GUARDAR LA CATEGORIA
                    $categoria->save();

                    DB::commit();

                    return response()->json($categoria, 201);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json(
                        [
                            "message" => "Error al guardar los datos...",
                            "errores" => $th,
                        ],
                        400
                    );
                }
            }
        } else {
            return response()->json(
                [
                    "message" => "Error al enviar los datos...",
                ],
                400
            );
        }
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
