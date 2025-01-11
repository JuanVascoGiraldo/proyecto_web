<?php
    require_once __DIR__ . '/../helpers/date_manage.php';

    class RequestModel{

        private string $id;
        private int $casillero;
        private int $status;
        // 0 = pendiente, 1 = aceptado, 2 = terminado, 3 = rechazado
        private DateTime $created_at;
        private DateTime $updated_at;
        private int $is_acepted;
        private string $url_payment_document;
        private string $periodo;
        private string $url_acuse;

        private string $user_id;

        private DateTime $until_at;

        public function __construct(
            string $id, int $casillero, int $status,
            DateTime $created_at, DateTime $updated_at,
            int $is_acepted, string $url_payment_document,
            string $periodo, DateTime $until_at = null, string $url_acuse = "",
            string $user_id = ""
        ){
            $this->id = $id;
            $this->casillero = $casillero;
            $this->status = $status;
            $this->created_at = $created_at;
            $this->updated_at = $updated_at;
            $this->is_acepted = $is_acepted;
            $this->url_payment_document = $url_payment_document;
            $this->periodo = $periodo;
            $this->until_at = $until_at;
            $this->url_acuse = $url_acuse;
            $this->user_id = $user_id;
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

        public function setCreatedAt(DateTime $created_at): void
        {
            $this->created_at = $created_at;
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

        public function setPeriodo(string $periodo): void
        {
            $this->periodo = $periodo;
        }

        public function getUntilAt(): DateTime
        {
            return $this->until_at;
        }

        public function setUntilAt(DateTime $until): void
        {
            $this->until_at = $until;
        }

        public function toArray(): array
        {
            return get_object_vars($this);
        }

        public function getUrlAcuse(): string
        {
            return $this->url_acuse;
        }

        public function setUrlAcuse(string $url_acuse): void
        {
            $this->url_acuse = $url_acuse;
        }

        public function isDelayed(): bool{
            if($this->getStatus() == 1 && $this->getUntilAt() < getCurrentUTC()){
                return true;
            }
            return false;
        }

        public function get_user_id(): string{
            return $this->user_id;
        }

        public function set_user_id(string $user_id): void{
            $this->user_id = $user_id;
        }

        public function toArrayAdmin(): array{
            return [
                "id" => $this->getId(),
                "casillero" => $this->getCasillero(),
                "status" => $this->getStatus(),
                "created_at" => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                "updated_at" => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
                "is_acepted" => $this->getIsAcepted(),
                "url_payment_document" => $this->getUrlPaymentDocument(),
                "periodo" => $this->getPeriodo(),
                "until_at" => $this->getUntilAt()->format('Y-m-d H:i:s'),
                "is_delayed" => $this->isDelayed()
            ];
        }

    }
?>