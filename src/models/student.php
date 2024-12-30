<?php
    require_once __DIR__ . '/../helpers/date_manage.php';
    require_once __DIR__ .'request_model.php';
    class Student{
        private $boleta;
        private $telefono;
        private $first_name;
        private $second_name;
        private $first_surname;
        private $second_surname;
        private $height;
        private $curp;
        private $credencial_url;
        private $horario_url;
        private array $requests = [];

        public function __construct(
            $boleta, $telefono, $first_name, $second_name,
            $first_surname, $second_surname, $height,
            $curp, $credencial_url, $horario_url
        ){
            $this->boleta = $boleta;
            $this->telefono = $telefono;
            $this->first_name = $first_name;
            $this->second_name = $second_name;
            $this->second_surname = $second_surname;
            $this->height = $height;
            $this->curp = $curp;
            $this->credencial_url = $credencial_url;
            $this->horario_url = $horario_url;
            $this->first_surname = $first_surname;
        }

        public function getBoleta(){
            return $this->boleta;
        }

        public function getTelefono(){
            return $this->telefono;
        }

        public function getFirstname(){
            return $this->first_name;
        }

        public function getSecondname(){
            return $this->second_name;
        }

        public function getFirstsurname(){
            return $this->first_surname;
        }

        public function getSecondsurname(){
            return $this->second_surname;
        }

        public function getHeight(){
            return $this->height;
        }
        public function getCurp(){
            return $this->curp;
        }
        public function getcredencial_url(){
            return $this->credencial_url;
        }

        public function getHorario_url(){
            return $this->horario_url;
        }

        public function setBoleta($boleta){
            $this->boleta = $boleta;
        }

        public function setTelefono($telefono){
            $this->telefono = $telefono;
        }

        public function setFirstname($first_name){
            $this->first_name = $first_name;
        }

        public function setSecondname($second_name){
            $this->second_name = $second_name;
        }

        public function setFirstsurname($first_surname){
            $this->first_surname = $first_surname;
        }

        public function setSecondsurname($second_surname){
            $this->second_surname = $second_surname;
        }

        public function setWidth($width){
            $this->width = $width;
        }

        public function setCurp($curp){
            $this->curp = $curp;
        }

        public function setcredencial_url($credencial_url){
            $this->credencial_url = $credencial_url;
        }

        public function setHorario_url($horario_url){
            $this->horario_url = $horario_url;
        }

        public function set_requests(array $requests){
            $this->requests = $requests;
        }

        public function add_request(RequestModel $request){
            $this->requests[] = $request;
        }

    }
?>