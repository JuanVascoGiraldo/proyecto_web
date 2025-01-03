<?php
    require_once __DIR__ . '/../helpers/date_manage.php';

    class RequestModel{

        private string $id;
        private int $casillero;
        private int $status;
        // 0 = pendiente, 1 = aceptado, 2 = rechazado
        private DateTime $created_at;
        private DateTime $updated_at;
        private int $is_acepted;
        private string $url_payment_document;
        private string $periodo;

        public function __construct(
            string $id, int $casillero, int $status,
            DateTime $created_at, DateTime $updated_at,
            int $is_acepted, string $url_payment_document,
            string $periodo
        ){
            $this->id = $id;
            $this->casillero = $casillero;
            $this->status = $status;
            $this->created_at = $created_at;
            $this->updated_at = $updated_at;
            $this->is_acepted = $is_acepted;
            $this->url_payment_document = $url_payment_document;
            $this->periodo = $periodo;
        }

        public function getId(): string{
            return $this->id;
        }

        public function getCasillero(): int
        {
            return $this->casillero;
        }

        public function getStatus(): int
        {
            return $this->status;
        }

        public function getCreatedAt(): DateTime
        {
            return $this->created_at;
        }

        public function getUpdatedAt(): DateTime
        {
            return $this->updated_at;
        }

        public function getUrlPaymentDocument(): string
        {
            return $this->url_payment_document;
        }

        public function getPeriodo(): string
        {
            return $this->periodo;
        }

        public function getIsAcepted(): int
        {
            return $this->is_acepted;
        }

        public function set_casillero(int $casillero): void
        {
            $this->casillero = $casillero;
            $this->updated_at = getCurrentUTC();
        }

        public function set_status(int $status): void
        {
            $this->status = $status;
            $this->updated_at = getCurrentUTC();
        }

        public function set_is_acepted(int $is_acepted): void
        {
            $this->is_acepted = $is_acepted;
            $this->updated_at = getCurrentUTC();
        }

        public function set_url_payment_document(string $url_payment_document): void
        {
            $this->url_payment_document = $url_payment_document;
            $this->updated_at = getCurrentUTC();
        }

    }
?>