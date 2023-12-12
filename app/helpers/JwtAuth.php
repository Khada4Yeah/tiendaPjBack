<?php

namespace App\Helpers;

use App\Models\Usuario;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth
{
    private $clave;

    public function __construct()
    {
        $this->clave = "%pj_back1-tienda%*";
    }

    public function singUp($correElectronico, $clave)
    {
        // BUSCAR SI EXISTE EL USUARIO CON SUS CREDENCIALES
        $usuario = Usuario::where([
            "correo_electronico" => $correElectronico,
            "clave" => $clave,
        ])->first();

        // COMPROBAR SI SON CORRECTAS
        $sing_up = false;

        if (is_object($usuario)) {
            $sing_up = true;
        }

        // GENERAR EL TOKEN CON LOS DATOS DEL USUARIO IDENTIFICADO
        if ($sing_up) {
            $tipo_usuario = "";
            if ($usuario->estado == "A") {
                if ($usuario->administrador) {
                    if ($usuario->administrador->estado == "A") {
                        $tipo_usuario = "admin";
                    } else {
                        return response()->json([
                            "code" => 400,
                            "status" => "error",
                            "message" =>
                                "Su usuario se encuentra desactivado...",
                        ]);
                    }
                } else {
                    if ($usuario->cliente->estado == "A") {
                        $tipo_usuario = "customer";
                    } else {
                        return response()->json([
                            "code" => 400,
                            "status" => "error",
                            "message" =>
                                "Su usuario se encuentra desactivado...",
                        ]);
                    }
                }

                $token = [
                    "sub" => $usuario->id_usuario,
                    "email" => $usuario->correo_electronico,
                    "name" => $usuario->nombres,
                    "user_type" => $tipo_usuario,
                    "surname_1" =>
                        $usuario->apellido_p != null
                            ? $usuario->apellido_p
                            : "",
                    "surname_2" =>
                        $usuario->apellido_m != null
                            ? $usuario->apellido_m
                            : "",
                    "iat" => time(),
                    "exp" => time() + 7 * 24 * 60 * 60, //EL TOKEN CADUCA EN UNA SEMANA
                ];

                $jwt = JWT::encode($token, $this->clave, "HS256");

                // DEVOLVER EL TOKEN
                $data = [
                    "code" => 200,
                    "status" => "success",
                    "token" => $jwt,
                ];
            } else {
                $data = [
                    "code" => 400,
                    "status" => "error",
                    "message" => "Su usuario se encuentra desactivado...",
                ];
            }
        } else {
            $data = [
                "code" => 400,
                "status" => "error",
                "message" => "Correo o contraseña incorrectos...",
            ];
        }

        return $data;
    }

    public function checkToken($jwt, $obtenerIdentidad = false)
    {
        $autenticado = false;

        try {
            $jwt = str_replace('"', "", $jwt);
            $decoded = JWT::decode($jwt, new Key($this->clave, "HS256"));
        } catch (\UnexpectedValueException $e) {
            $autenticado = false;
        } catch (\DomainException $e) {
            $autenticado = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $autenticado = true;
        } else {
            $autenticado = false;
        }

        if ($obtenerIdentidad) {
            return $decoded;
        }

        return $autenticado;
    }

    public function decodeToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->clave, "HS256"));
            return response()->json([
                "code" => 200,
                "status" => "success",
                "identity" => $decoded,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Error al obtener las credenciales",
                "errores" => $th,
            ]);
        }
    }
}
