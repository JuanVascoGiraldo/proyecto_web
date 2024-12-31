<?php
    class Response_model{

        private string $message;
        private int $code;

        private bool $success = false;

        private array $data = [];

        public function __construct(string $message, int $code, bool $success = False , array $data = []){
            $this->message = $message;
            $this->code = $code;
            $this->success = $success;
            $this->data = $data;
        }

        public function toArray(): array{
            return [
                'message' => $this->message,
                'code' => $this->code,
                'success' => $this->success,
                'data'=> $this->data
            ];
        }

    }


?>