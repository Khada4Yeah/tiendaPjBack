<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware("api.auth")->except(["store", "login"]);
    }

    public function login(Request $request)
    {
        // RECOGER LOS DATOS POR POST
        $json = $request->input("json", null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // LIMPIAR DATOS
            $params_array = array_map("trim", $params_array);

            // VALIDAR DATOS
            $validar_datos = Validator::make($params_array, [
                "correo" => "required|email",
                "contrasena" => "required",
            ]);

            if ($validar_datos->fails()) {
                // LA VALIDACION HA FALLADO
                $data = [
                    "code" => 400,
                    "status" => "error",
                    "message" => "El usuario no se ha podido indentificar",
                    "errores" => $validar_datos->errors(),
                ];
            } else {
                $jwtAuth = new JwtAuth();

                // CIFRAR LA CLAVE
                $pwd = DB::select(
                    "SELECT dbo.fnc_encriptar_clave(?) AS pwd_crip",
                    [$params_array["contrasena"]]
                );

                // DEVOLVER EL TOKEN
                $data = $jwtAuth->singup(
                    $params_array["correo"],
                    $pwd[0]->pwd_crip
                );
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

    public function getIdentity($token)
    {
        $jwtAuth = new JwtAuth();

        return $jwtAuth->decodeToken($token);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $usuarios = Usuario::all();

            return response()->json([
                "code" => 200,
                "status" => "success",
                "data" => $usuarios,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Error al obtener los datos",
                "errores" => $th,
            ]);
        }
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
                "cedula" => "required",
                "nombres" => "required",
                "apellido_p" => "required",
                "apellido_m" => "required",
                "sexo" => "required",
                "fecha_nacimiento" => "required",
                "celular" => "required",
                "correo_electronico" => "required",
                "clave" => "required",
                "tipo" => "required",
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

                    // CIFRAR LA CLAVE
                    $pwd = DB::select(
                        "SELECT dbo.fnc_encriptar_clave(?) AS pwd_crip",
                        [$params_array["clave"]]
                    );

                    // CREAR EL USUARIO
                    $usuario = new Usuario();
                    $usuario->cedula = $params_array["cedula"];
                    $usuario->nombres = mb_strtoupper($params_array["nombres"]);
                    $usuario->apellido_p = mb_strtoupper(
                        $params_array["apellido_p"]
                    );
                    $usuario->apellido_m = mb_strtoupper(
                        $params_array["apellido_m"]
                    );
                    $usuario->sexo = $params_array["sexo"];
                    $usuario->fecha_nacimiento =
                        $params_array["fecha_nacimiento"];

                    $usuario->celular = $params_array["celular"];
                    $usuario->correo_electronico =
                        $params_array["correo_electronico"];
                    $usuario->clave = $pwd[0]->pwd_crip;

                    // GUARDAR EL USUARIO
                    $usuario->save();

                    // CREAR USUARIO CLIENTE O ADMINISTRADOR SEGUN PARAMETRO
                    $tipo_usuario = "";

                    switch ($params_array["tipo"]) {
                        case "UC":
                            $tipo_usuario = "cliente";
                            break;

                        case "UA":
                            $tipo_usuario = "administrador";
                            break;

                        default:
                            # code...
                            break;
                    }

                    if ($tipo_usuario == "cliente") {
                        $cliente = new Cliente();
                        $cliente->id_usuario = $usuario->id_usuario;
                        $cliente->direccion_envio = "";
                        $cliente->informacion_pago = "";
                        $cliente->save();
                    } elseif ($tipo_usuario == "administrador") {
                        $administrador = new Administrador();
                        $administrador->id_usuario = $usuario->id_usuario;
                        $administrador->save();
                    }

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
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        //
    }
}
