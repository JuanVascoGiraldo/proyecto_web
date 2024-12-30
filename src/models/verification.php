<?php

    class Verification{

        private string $id;
        private string $code;
        private string $email;

        private DateTime $expiration_date;

        public function __construct(
            string $id, string $code, string $email, DateTime $expiration_date
        ){
            $this->id = $id;
            $this->code = $code;
            $this->email = $email;
            $this->expiration_date = $expiration_date;
        }

        public function getId(): string
        {
            return $this->id;
        }

        public function getCode(): string
        {
            return $this->code;
        }

        public function getEmail(): string
        {
            return $this->email;
        }


        public function getExpirationDate(): DateTime
        {
            return $this->expiration_date;
        }

        /**
         * Validacr si el código introducido es correcto.
        *
        * @param string $code Código a validar.
        * @return bool Regrea si el codigo es correcto.
        */
        public function is_valid_code(string $code): bool
        {
            return $this->code === $code;
        }

    }

?>