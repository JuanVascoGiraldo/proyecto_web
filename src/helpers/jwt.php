<?php
    require_once __DIR__."./constants.php";
    require_once __DIR__.'/../../vendor/autoload.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    const ALGORITHM = 'HS256';

    /**
     * Genera un JWT.
     *
     * @param array $data Datos a incluir en el payload del JWT.
     * @param int $expiration Tiempo en segundos para que el token expire.
     * @return string El token JWT generado.
     */
    function generateJWT(array $data, int $expiration = 3600): string {
        $issuedAt = time();
        $payload = [
            'iat' => $issuedAt,            // Tiempo de emisión
            'exp' => $issuedAt + $expiration, // Tiempo de expiración
            'data' => $data               // Datos personalizados
        ];

        return JWT::encode($payload, SECRET_KEY, ALGORITHM);
    }

    /**
     * Obtiene los datos de un JWT.
     *
     * @param string $jwt El token JWT a decodificar.
     * @return array Los datos del payload del JWT.
     * @throws Exception Si el token no es válido o ha expirado.
     */
    function getJWTData(string $jwt): array {
        try {
            $decoded = JWT::decode($jwt, new Key(SECRET_KEY, ALGORITHM));
            return (array) $decoded->data;
        } catch (Exception $e) {
            throw new Exception('Token inválido o expirado: ' . $e->getMessage());
        }
    }

?>