<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AtributoController extends Controller
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
        $atributos = Atributo::orderBy("nombre", "asc")->get();

        return response()->json($atributos, 200);
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
                "categoryId" => "required",
                "attributeName" => "required",
                "unitOfMeasure" => "required",
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

                    // CREAR EL ATRIBUTO
                    $atributo = new Atributo();
                    $atributo->id_categoria = $params_array["categoryId"];
                    $atributo->nombre = mb_strtoupper(
                        $params_array["attributeName"]
                    );
                    $atributo->unidad_de_medida =
                        $params_array["unitOfMeasure"];

                    // GUARDAR EL ATRIBUTO
                    $atributo->save();

                    DB::commit();

                    return response()->json($atributo, 200);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json(
                        [
                            "message" => "Error al guardar los datos",
                            "errors" => $th,
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
     * @param  \App\Models\Atributo  $atributo
     * @return \Illuminate\Http\Response
     */
    public function show(Atributo $atributo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Atributo  $atributo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Atributo $atributo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Atributo  $atributo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Atributo $atributo)
    {
    }

    /**
     * Obtener todos los atributos segun la categoria.
     *
     * @param int $idCategoria
     * @return Illuminate\Database\Eloquent\Collection
     */

    public function obtenerAtributosPorCategoria($idCategoria)
    {
        $atributos = Atributo::where("id_categoria", $idCategoria)
            ->orderBy("nombre", "asc")
            ->get();

        return response()->json($atributos, 200);
    }
}
