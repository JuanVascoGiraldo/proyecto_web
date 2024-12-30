<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService {
    private PHPMailer $mailer;

    /**
     * Constructor que configura el servicio de correo con Gmail.
     *
     * @param string $username Dirección de correo electrónico de Gmail.
     * @param string $password Contraseña de la cuenta o clave de aplicación de Gmail.
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = "sigca2024@gmail.com";
            $this->mailer->Password = "flucnzvwvyoyhnod";
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;

            $this->mailer->setFrom("sigca2024@gmail.com", 'Sistema de Gestión de Casilleros ESCOM');
        } catch (Exception $e) {
            error_log("Error al configurar PHPMailer: {$e->getMessage()}");
        }
    }

    /**
     * Envía un correo electrónico.
     *
     * @param string $toEmail Correo electrónico del destinatario.
     * @param string $toName Nombre del destinatario.
     * @param string $subject Asunto del correo.
     * @param string $body Cuerpo del mensaje (puede incluir HTML).
     * @return bool Verdadero si el correo se envió correctamente, falso en caso contrario.
     */
    public function sendEmail(string $toEmail, string $toName, string $subject, string $body): bool {
        try {
            $this->mailer->addAddress($toEmail, $toName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            // Enviar correo
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
?>
