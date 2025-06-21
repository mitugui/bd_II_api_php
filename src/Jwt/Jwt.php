<?php

namespace CoMit\ApiBd\Jwt;

class Jwt
{
    public function __construct(private string $key) {}

    private function base64URLEncode($text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    public function encode(array $payload): string
    {
        $header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]);

        $header = $this->base64URLEncode($header);
        $payload = json_encode($payload);
        $payload = $this->base64URLEncode($payload);

        $signature = hash_hmac("sha256", "$header.$payload", $this->key, true);
        $signature = $this->base64URLEncode($signature);

        return "$header.$payload.$signature";
    }

    public function isValid(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        [$header, $payload, $signature] = $parts;

        $expected = $this->base64URLEncode(
            hash_hmac("sha256", "$header.$payload", $this->key, true)
        );

        return hash_equals($expected, $signature);
    }
}
