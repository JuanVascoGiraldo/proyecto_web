<?php
    class CodeGenerator {
        /**
         * Genera un código alfanumérico de 6 caracteres.
         *
         * @return string Código alfanumérico de 6 caracteres.
         */
        public static function generateAlphanumericCode(): string {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $code = '';
            $length = 6;

            for ($i = 0; $i < $length; $i++) {
                $index = random_int(0, strlen($characters) - 1);
                $code .= $characters[$index];
            }

            return $code;
        }
    }
?>
