<?php

namespace CoMit\ApiBd\Jwt;

class Jwt
{
    public function __construct(private string $key) {}

    private function base64URLEncode($text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64URLDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        return base64_decode($data);
    }

    public function encode(array $payload, int $expiresIn): string
    {
        $header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]);

        if ($expiresIn <= 0) {
            $expiresIn = 600;
        }

        $header = $this->base64URLEncode($header);
        $payload['exp'] = time() + $expiresIn;
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

        $decodedPayloadJson = $this->base64URLDecode($payload);
        $decodedPayload = json_decode($decodedPayloadJson, true);

        if (isset($decodedPayload['exp']) && time() > $decodedPayload['exp']) {
            return false;
        }

        return hash_equals($expected, $signature);
    }
}
