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

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $proveedores,
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
                "direccion" => "required",
                "telefono" => "required|size:9",
                "celular" => "required|size:10",
                "correo_electronico" => "required|email",
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

                    // CREAR EL PROVEEDOR
                    $proveedor = new Proveedor();
                    $proveedor->nombre = mb_strtoupper($params_array["nombre"]);
                    $proveedor->direccion = $params_array["direccion"];
                    $proveedor->telefono = $params_array["telefono"];
                    $proveedor->celular = $params_array["celular"];
                    $proveedor->correo_electronico =
                        $params_array["correo_electronico"];
                    $proveedor->descripcion = $params_array["descripcion"];

                    // GUARDAR EL PROVEEDOR
                    $proveedor->save();

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
