<?php
    require_once __DIR__ . '/../helpers/date_manage.php';
    require_once __DIR__ .'/student.php';
    require_once __DIR__ .'/session.php';
    class User {
        private $id;
        private $username;
        private $password;
        private $role;
        private $email;
        private $status;
        private $createdAt;
        private $updatedAt;

        private ?Student $student = null;

        private array $sessions = [];

        private DateManage $dateManage = new DateManage();

        public function __construct($id, $username, $password, $email, $role, $status, $createdAt, $updatedAt) {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->role = $role;
            $this->status = $status;
            $this->createdAt = $createdAt;
            $this->updatedAt = $updatedAt;
        }

        public function getId() {
            return $this->id;
        }

        public function getUsername() {
            return $this->username;
        }

        public function getPassword() {
            return $this->password;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getRole() {
            return $this->role;
        }

        public function getStatus() {
            return $this->status;
        }

        public function getCreatedAt() {
            return $this->createdAt;
        }

        public function getUpdatedAt() {
            return $this->updatedAt;
        }

        public function setPassword($password) {
            $this->password = $password;
        }

        public function setEmail($email) {
            $this->email = $email;
            $this->updatedAt = $this->dateManage->getCurrentUTC();
        }

        public function setRole($role) {
            $this->role = $role;
            $this->updatedAt = $this->dateManage->getCurrentUTC();
        }

        public function setStatus($status) {
            $this->status = $status;
            $this->updatedAt = $this->dateManage->getCurrentUTC();
        }

        public function get_student(): ?Student {
            return $this->student;
        }

        public function set_student(Student $student) {
            $this->student = $student;
        }

        public function get_sessions(): array {
            return $this->sessions;
        }

        public function set_sessions(array $sessions) {
            $this->sessions = $sessions;
        }

        /** 
         * Funcion para agregar una sesion a un usuario, al agregar la sesión
         * desactivamos todas las otras anteriores sesiones del usuario
         * 
         * @param string $session Sesión a agregar
        */
        public function add_session(Session $session) {
            $time = $this->dateManage->getCurrentUTC();
            foreach ($this->sessions as $s) {
                $s->setExpirationDate($time);
            }
            $this->sessions[] = $session;
        }


    }
?>