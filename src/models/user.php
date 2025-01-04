<?php
    require_once __DIR__ . '/../helpers/date_manage.php';
    require_once __DIR__ .'/student.php';
    require_once __DIR__ .'/session.php';
    class User {
        private string $id;
        private string $username;
        private string $password;
        private int $role;
        private string $email;
        private int $status;
        private DateTime $createdAt;
        private DateTime $updatedAt;

        private ?Student $student = null;

        private array $sessions = [];

        public function __construct(
            string $id, string $username, string $password,
            string $email, int $role, int $status,
            DateTime $createdAt, DateTime $updatedAt
        ) {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->role = $role;
            $this->status = $status;
            $this->createdAt = $createdAt;
            $this->updatedAt = $updatedAt;
        }

        public function getId(): string {
            return $this->id;
        }

        public function getUsername(): string {
            return $this->username;
        }

        public function getPassword(): string {
            return $this->password;
        }

        public function getEmail(): string {
            return $this->email;
        }

        public function getRole(): int {
            return $this->role;
        }

        public function getStatus(): int {
            return $this->status;
        }

        public function getCreatedAt(): DateTime {
            return $this->createdAt;
        }

        public function getUpdatedAt(): DateTime {
            return $this->updatedAt;
        }

        public function setPassword(string $password): void {
            $this->password = $password;
        }

        public function setEmail(string $email): void {
            $this->email = $email;
            $this->updatedAt = getCurrentUTC();
        }

        public function setRole(int $role): void {
            $this->role = $role;
            $this->updatedAt = getCurrentUTC();
        }

        public function setStatus(int $status): void {
            $this->status = $status;
            $this->updatedAt = getCurrentUTC();
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

        public function set_sessions(array $sessions): void {
            $this->sessions = $sessions;
        }

        /** 
         * Funcion para agregar una sesion a un usuario, al agregar la sesión
         * desactivamos todas las otras anteriores sesiones del usuario
         * 
         * @param Session $session Sesión a agregar
        */
        public function add_session(Session $session) {
            $time = getCurrentUTC();
            foreach ($this->sessions as $s) {
                $s->setExpirationDate($time);
            }
            $this->sessions[] = $session;
        }

        /**
         * Verifica si la contraseña es correcta.
         * @param string $password Contraseña a verificar.
         * @return bool Verdadero si la contraseña es correcta.
         */
        public function checkPassword(string $password): bool {
            return password_verify($password, $this->password);
        }

        /**
         * Verifica si el usuario es administrador.
         * @return bool Verdadero si el usuario es administrador.
         */
        public function is_admin(): bool {
            return $this->role === 1;
        }

        /**
         * Devuelve un arreglo con los datos del usuario.
         * @return array Arreglo con los datos del usuario para la vista de admin.
         */
        public function toArray_to_admin(): array {
            $complete_name = $this->student->getFirstName();
            if ($this->student->getSecondname() !== ""){
                $complete_name .= " ". $this->student->getSecondname();
            }
            $complete_name .= " ". $this->student->getFirstsurname(). " ". $this->student->getSecondsurname();

            return [
                'id'=> $this->id,
                'email'=> $this->email,
                'username'=> $this->username,
                'user_created_at'=> $this->createdAt->format('Y-m-d H:i:s'),
                'boleta'=> $this->student->getBoleta(),
                'first_name'=> $this->student->getFirstName(),
                'second_name'=> $this->student->getSecondName(),
                'first_last_name'=> $this->student->getFirstsurname(),
                'second_last_name'=> $this->student->getSecondsurname(),
                'phone'=> $this->student->getTelefono(),
                'height'=> $this->student->getHeight(),
                'curp'=> $this->student->getCurp(),
                'credencial'=> $this->student->getCredencial_url(),
                'horario'=> $this->student->getHorario_url(),
                'casillero'=> $this->student->get_requests()[0]->getCasillero(),
                'estado_solicitud'=> $this->student->get_requests()[0]->getStatus(),
                'periodo'=> $this->student->get_requests()[0]->getPeriodo(),
                'complete_name'=> $complete_name
            ];
        }
    }
?>