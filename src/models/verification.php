<?php

    class Verification{

        private string $id;
        private string $code;
        private string $email;

        private DateTime $expiration_date;

        private int $attemps;

        public function __construct(
            string $id, string $code,
            string $email, DateTime $expiration_date,
            int $attemps = 0
        ){
            $this->id = $id;
            $this->code = $code;
            $this->email = $email;
            $this->expiration_date = $expiration_date;
            $this->attemps = $attemps;
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


        public function getAttemps(): int
        {
            return $this->attemps;
        }

        public function setAttemps(int $attemps): void
        {
            $this->attemps = $attemps;
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

        /**
         * Incrementa los intentos de verificación.
         */
        public function increase_attempts(): void
        {
            $this->attemps += 1;
        }


    }

?>