<?php

namespace CoMit\ApiBd\Middleware;

use CoMit\ApiBd\Jwt\Jwt;

class AuthMiddleware
{
    public static function authenticate(): void
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token ausente"]);
            exit;
        }

        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "Formato do token inválido"]);
            exit;
        }

        $token = $matches[1];
        $jwt = new Jwt($_ENV["SECRET_KEY"]);

        if (!$jwt->isValid($token)) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido"]);
            exit;
        }
    }
}
