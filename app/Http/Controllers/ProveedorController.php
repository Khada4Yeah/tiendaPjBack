<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
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
        $proveedores = Proveedor::orderBy("nombre", "asc")->get();

        return response()->json($proveedores, 200);
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
                "providerName" => "required",
                "providerAddress" => "required",
                "providerPhone" => "required|size:9",
                "providerCellphone" => "required|size:10",
                "providerEmail" => "required|email",
                "providerDescription" => "required",
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

                    // CREAR EL PROVEEDOR
                    $proveedor = new Proveedor();
                    $proveedor->nombre = mb_strtoupper(
                        $params_array["providerName"]
                    );
                    $proveedor->direccion = $params_array["providerAddress"];
                    $proveedor->telefono = $params_array["providerPhone"];
                    $proveedor->celular = $params_array["providerCellphone"];
                    $proveedor->correo_electronico =
                        $params_array["providerEmail"];
                    $proveedor->descripcion =
                        $params_array["providerDescription"];
                    $proveedor->estado = "A";

                    // GUARDAR EL PROVEEDOR
                    $proveedor->save();

                    DB::commit();

                    return response()->json($proveedor, 201);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json(
                        [
                            "message" => "Error al guardar los datos...",
                            "errors" => $th,
                        ],
                        400
                    );
                }
            }
        } else {
            return response()->json(
                ["message" => "Error al enviar los datos"],
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function show(Proveedor $proveedor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proveedor $proveedor)
    {
        //
    }
}
