<?php

    class Session{

        private $id;
        private $created_at;
        private $expiration_date;

        public function __construct($id, $created_at, $expiration_date){
            $this->id = $id;
            $this->created_at = $created_at;
            $this->expiration_date = $expiration_date;
        }

        public function getId(){
            return $this->id;
        }

        public function getCreatedAt(){
            return $this->created_at;
        }

        public function getExpirationDate(){
            return $this->expiration_date;
        }

        public function setId($id){
            $this->id = $id;
        }

        public function setExpirationDate($expiration_date){
            $this->expiration_date = $expiration_date;
        }
    }
?>