<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarcaController extends Controller
{
    public function __construct()
    {
        //$this->middleware("api.auth");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = Marca::orderBy("nombre", "asc")->get();

        return response()->json($marcas, 200);
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
                "brandName" => "required",
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

                    // CREAR LA MARCA
                    $marca = new Marca();
                    $marca->nombre = mb_strtoupper($params_array["brandName"]);

                    // GUARDAR LA MARCA
                    $marca->save();

                    DB::commit();

                    return response()->json(
                        [
                            "status" => "success",
                        ],
                        201
                    );
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
                    "message" => "Error al enviar los datos",
                ],
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marca $marca)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marca $marca)
    {
        //
    }
}
