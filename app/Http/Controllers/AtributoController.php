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

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $atributos,
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
                "id_categoria" => "required",
                "nombre" => "required",
                "unidad_de_medida" => "required",
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

                    // CREAR EL ATRIBUTO
                    $atributo = new Atributo();
                    $atributo->id_categoria = $params_array["id_categoria"];
                    $atributo->nombre = mb_strtoupper($params_array["nombre"]);
                    $atributo->unidad_de_medida =
                        $params_array["unidad_de_medida"];

                    // GUARDAR EL ATRIBUTO
                    $atributo->save();

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

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $atributos,
        ]);
    }
}
