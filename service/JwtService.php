<?php

class JwtService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = 'PlL2AtSY2kfBKStPsNsGYv56IPRc063pCYZtzNmDqbC';
    }

    public function generateToken(array $data, int $expireInSeconds = 3600): string {
        $issuedAt = time();
        $expire = $issuedAt + $expireInSeconds;

        $payload = json_encode([
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $data
        ]);

        $base64Payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $base64Payload, $this->secretKey);

        return "$base64Payload.$signature";
    }
}
