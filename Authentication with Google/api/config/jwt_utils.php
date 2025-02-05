<?php
class JWTUtils {
    private static $secret_key = "YOUR_SECRET_KEY_HERE";
    private static $issuer = "localhost";
    private static $audience = "app_users";

    public static function generateToken($user_id, $username) {
        $issued_at = time();
        $expiration = $issued_at + (60 * 60); // Valid for 1 hour

        $payload = array(
            "iss" => self::$issuer,
            "aud" => self::$audience,
            "iat" => $issued_at,
            "exp" => $expiration,
            "data" => array(
                "id" => $user_id,
                "username" => $username
            )
        );

        return self::encode($payload);
    }

    public static function validateToken($token) {
        try {
            $decoded = self::decode($token);
            return $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $header = base64_encode($header);
        $payload = base64_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$header.$payload", self::$secret_key, true);
        $signature = base64_encode($signature);
        
        return "$header.$payload.$signature";
    }

    private static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new Exception('Invalid token format');
        }

        list($header, $payload, $signature) = $parts;
        
        $valid_signature = hash_hmac('sha256', "$header.$payload", self::$secret_key, true);
        $valid_signature_encoded = base64_encode($valid_signature);
        
        if ($signature !== $valid_signature_encoded) {
            throw new Exception('Invalid signature');
        }

        $payload = json_decode(base64_decode($payload));
        if ($payload->exp < time()) {
            throw new Exception('Token has expired');
        }

        return $payload;
    }
}
?> 