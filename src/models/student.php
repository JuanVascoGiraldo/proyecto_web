<?php
    require_once __DIR__ . '/../helpers/date_manage.php';
    require_once __DIR__ .'./request_model.php';
    class Student{
        private string $boleta;
        private string $telefono;
        private string $first_name;
        private string $second_name;
        private string $first_surname;
        private string $second_surname;
        private int $height;
        private string $curp;
        private string $credencial_url;
        private string $horario_url;
        private array $requests = [];

        public function __construct(
            string $boleta, string $telefono, string $first_name, string $second_name,
            string $first_surname, string $second_surname, int $height,
            string $curp, string $credencial_url, string $horario_url
        ){
            $this->boleta = $boleta;
            $this->telefono = $telefono;
            $this->first_name = $first_name;
            $this->second_name = $second_name;
            $this->first_surname = $first_surname;
            $this->second_surname = $second_surname;
            $this->height = $height;
            $this->curp = $curp;
            $this->credencial_url = $credencial_url;
            $this->horario_url = $horario_url;
        }

        public function getBoleta(): string{
            return $this->boleta;
        }

        public function getTelefono(): string{
            return $this->telefono;
        }

        public function getFirstname(): string{
            return $this->first_name;
        }

        public function getSecondname(): string{
            return $this->second_name;
        }

        public function getFirstsurname(): string{
            return $this->first_surname;
        }

        public function getSecondsurname(): string{
            return $this->second_surname;
        }

        public function getHeight(): int{
            return $this->height;
        }
        public function getCurp(): string{
            return $this->curp;
        }
        public function getCredencial_url(): string{
            return $this->credencial_url;
        }

        public function getHorario_url(): string{
            return $this->horario_url;
        }

        public function setBoleta(string $boleta): void{
            $this->boleta = $boleta;
        }

        public function setTelefono(string $telefono): void{
            $this->telefono = $telefono;
        }

        public function setFirstname(string $first_name): void{
            $this->first_name = $first_name;
        }

        public function setSecondname(string $second_name): void{
            $this->second_name = $second_name;
        }

        public function setFirstsurname(string $first_surname): void{
            $this->first_surname = $first_surname;
        }

        public function setSecondsurname(string $second_surname): void{
            $this->second_surname = $second_surname;
        }

        public function setCurp(string $curp): void{
            $this->curp = $curp;
        }

        public function setHeight(int $height): void{
            $this->height = $height;
        }

        public function setcredencial_url(string $credencial_url): void{
            $this->credencial_url = $credencial_url;
        }

        public function setHorario_url(string $horario_url): void{
            $this->horario_url = $horario_url;
        }

        public function get_requests(): array{
            return $this->requests;
        }

        public function set_requests(array $requests): void{
            $this->requests = $requests;
        }

        public function add_request(RequestModel $request): void{
            $this->requests[] = $request;
        }

        /**
         * Devuelve el nombre completo del estudiante.
         * @return string Nombre completo del estudiante.
         */
        public function getCompleteName(): string{
            $complete_name = $this->first_name;
            if ($this->second_name !== ""){
                $complete_name .= " ". $this->second_name;
            }
            $complete_name .= " ". $this->first_surname. " ". $this->second_surname;
            return $complete_name;
        }

    }
?>