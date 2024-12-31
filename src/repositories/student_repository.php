<?php
    require_once __DIR__ .'/../services/database.php';
    require_once __DIR__ .'/../models/student.php';
    require_once __DIR__ .'/../models/request_model.php';

    class StudentRepository{

        private PDO $connection;

        /**
         * Constructor que inicializa la conexión con la base de datos.
         *
         * @param Database $database Servicio de base de datos.
         */
        public function __construct(Database $database) {

            $this->connection = $database->getConnection();
        }

        /**
         * Guarda un estudiante en la base de datos.
         * @param Student $student
         * @return 
         * @throws Exception Si ocurre un error al guardar.
         */
        public function save_student_info(Student $student, string $user_id){
            try {
                $stmt = $this->connection->prepare("
                    INSERT INTO student (boleta, telefono, first_name, second_name, first_surname, second_surname, height, curp, credencial_url, horario_url, user_id)
                    VALUES (:boleta, :telefono, :first_name, :second_name, :first_surname, :second_surname, :height, :curp, :credencial_url, :horario_url, :user_id);
                ");
                return $stmt->execute(
                    [
                        "boleta" => $student->getBoleta(),
                        "telefono" => $student->getTelefono(),
                        "first_name" => $student->getFirstname(),
                        "second_name" => $student->getSecondname(),
                        "first_surname" => $student->getFirstsurname(),
                        "second_surname" => $student->getSecondsurname(),
                        "height" => $student->getHeight(),
                        "curp" => $student->getCurp(),
                        "credencial_url" => $student->getCredencial_url(),
                        "horario_url" => $student->getHorario_url(),
                        "user_id" => $user_id
                    ]
                    );
            }catch (PDOException $e) {
                throw new Exception("Error al guardar el usuario: " . $e->getMessage());
            }
        
        }


        /**
         * Encontrar al estudiante por el id del usuario
         * @param string $user_id id del usuario
         * @throws \Exception Si curre una excepcion al buscar al estudiante o al mapearlo.
         * @return Student|null Instancia de estudiante o nulo
         */
        public function find_student_by_user_id(string $user_id){
            try {
                $stmt = $this->connection->prepare("
                    SELECT * FROM student WHERE user_id = :user_id;
                ");
                $stmt->execute(['user_id' => $user_id]);
                $result = $stmt->fetch();
                if ($result){
                    return new Student(
                        $result['boleta'],
                        $result['telefono'],
                        $result['first_name'],
                        $result['second_name'],
                        $result['first_surname'],
                        $result['second_surname'],
                        $result['height'],
                        $result['curp'],
                        $result['credencial_url'],
                        $result['horario_url']
                    );
                }
                return null;
            }catch (PDOException $e) {
                throw new Exception("Error al buscar el estudiante: " . $e->getMessage());
            }
        }

        /**
         * Encontrar al estudiante por su boleta
         * @param string $boleta Boleta del estudiante
         * @throws \Exception Si ocurre una excepcion al buscar al estudiante o al mapearlo.
         * @return Student|null Instancia de estudiante o nulo
         */
        public function find_student_by_boleta(string $boleta){
            try {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM student WHERE boleta = :boleta;");
                $stmt->execute(["boleta"=> $boleta]);
                $result = $stmt->fetch();
                if ($result){
                    return new Student(
                        $result['boleta'],
                        $result['telefono'],
                        $result['first_name'],
                        $result['second_name'],
                        $result['first_surname'],
                        $result['second_surname'],
                        $result['height'],
                        $result['curp'],
                        $result['credencial_url'],
                        $result['horario_url']
                    );
                }
                return null;

            }catch (PDOException $e) {
                throw new Exception("Excepcion al buscar el estudiante: ". $e->getMessage());
            }
        }


        /**
         * Encontrar al estudiante por su CURP
         * @param string $curp CURP del estudiante
         * @throws \Exception Si ocurre una excepcion al buscar al estudiante o al mapearlo.
         * @return Student|null Instancia de estudiante o nulo
         */
        public function find_student_by_curp(string $curp){
            try {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM student WHERE curp = :curp;");
                $stmt->execute(["curp"=> $curp]);
                $result = $stmt->fetch();
                if ($result){
                    return new Student(
                        $result['boleta'],
                        $result['telefono'],
                        $result['first_name'],
                        $result['second_name'],
                        $result['first_surname'],
                        $result['second_surname'],
                        $result['height'],
                        $result['curp'],
                        $result['credencial_url'],
                        $result['horario_url']
                    );
                }
                return null;
            }catch (PDOException $e) {
                throw new Exception('Excepcion al buscar el estudiante'. $e->getMessage());
            }
        }
                        
    }

?>