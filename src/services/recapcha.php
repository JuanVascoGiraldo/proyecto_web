<?php

class RecaptchaValidator {
    private string $secretKey;

    /**
     * Constructor.
     *
     * @param string $secretKey La clave secreta de reCAPTCHA.
     */
    public function __construct() {
        $this->secretKey = "6LdtuasqAAAAAPwX1aKXwBBj5xiSpYBr8zYh4gMl";
    }

    /**
     * Valida el token de reCAPTCHA enviado desde el frontend.
     *
     * @param string $token El token generado por reCAPTCHA.
     * @param string $userIp (Opcional) La IP del usuario que envía el token.
     * @return bool Verdadero si el token es válido, falso en caso contrario.
     */
    public function validate(string $token, string $userIp = ''): bool {
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        // Configurar los datos para la solicitud
        $postData = [
            'secret' => $this->secretKey,
            'response' => $token,
        ];

        if (!empty($userIp)) {
            $postData['remoteip'] = $userIp;
        }

        // Hacer la solicitud a la API de Google reCAPTCHA
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Procesar la respuesta
        if (!$response) {
            return false; // Error en la solicitud
        }

        $responseData = json_decode($response, true);

        // Validar el éxito de la respuesta
        return isset($responseData['success']) && $responseData['success'] === true;
    }
}
?>
