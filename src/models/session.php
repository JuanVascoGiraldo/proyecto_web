<?php

    class Session{

        private string $id;
        private DateTime $created_at;
        private DateTime $expiration_date;

        public function __construct(
            string $id, DateTime $created_at, DateTime $expiration_date
        ){
            $this->id = $id;
            $this->created_at = $created_at;
            $this->expiration_date = $expiration_date;
        }

        public function getId(): string{
            return $this->id;
        }

        public function getCreatedAt(): DateTime{
            return $this->created_at;
        }

        public function getExpirationDate(): DateTime{
            return $this->expiration_date;
        }

        public function setId(string $id): void{
            $this->id = $id;
        }

        public function setExpirationDate(DateTime $expiration_date): void{
            $this->expiration_date = $expiration_date;
        }
    }
?>