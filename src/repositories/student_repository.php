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
        public function save_student_info(Student $student, string $user_id): bool{
            try {
                $stmt = $this->connection->prepare("
                    INSERT INTO Students (boleta, telefono, first_name, second_name, first_surname, second_surname, height, curp, credencial_url, horario_url, user_id)
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
        public function find_student_by_user_id(string $user_id): Student|null{
            try {
                $stmt = $this->connection->prepare("
                    SELECT * FROM Students WHERE user_id = :user_id;
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
        public function find_student_by_boleta(string $boleta): Student|null{
            try {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM Students WHERE boleta = :boleta;");
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
        public function find_student_by_curp(string $curp): Student|null{
            try {
                $stmt = $this->connection->prepare(
                    "SELECT * FROM Students WHERE curp = :curp;");
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
    
        public function save_request(RequestModel $request, string $user_id): void{
            try {
                $stm = $this->connection->prepare(
                    "INSERT INTO Requests (id, casillero, status, created_at, updated_at, is_acepted, url_payment_document, periodo, user_id)
                    VALUES (:id, :casillero, :status, :created_at, :updated_at, :is_acepted, :url_payment_document, :periodo, :user_id);");
                $stm->execute([
                    'id' => $request->getId(),
                    'casillero' => $request->getCasillero(),
                    'status' => $request->getStatus(),
                    'created_at' => $request->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $request->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'is_acepted' => $request->getIsAcepted(),
                    'url_payment_document' => $request->getUrlPaymentDocument(),
                    'periodo' => $request->getPeriodo(),
                    'user_id' => $user_id
                ]);
            }catch (PDOException $e) {
                throw new Exception('Error al guardar la solicitud'. $e->getMessage());
            }
        }

        public function find_request_by_id(string $id): ?RequestModel{
            try {
                $stm = $this->connection->prepare(
                    'SELECT * FROM Requests WHERE id = :id;');
                $stm->execute([
                    'id'=> $id
                ]);
                $result = $stm->fetch();
                if($result){
                    return new RequestModel(
                        $result['id'],
                        $result['casillero'],
                        $result['status'],
                        new DateTime($result['created_at']),
                        new DateTime($result['updated_at']),
                        $result['is_acepted'],
                        $result['url_payment_document'],
                        $result['periodo']
                    );
                }
                return null;
            }catch (PDOException $e) {
                throw new Exception('Error al encontrar la solicitud'. $e->getMessage());
            }
        }
        
        public function find_all_request_by_user_id(string $user_id): array{
            try {
                $stmt = $this->connection->prepare(
                    'SELECT * FROM Requests WHERE user_id = :user_id;');
                $stmt->execute(['user_id'=> $user_id]);
                $result = $stmt->fetchAll();
                $requests = [];
                foreach($result as $request){
                    $requests[] = new RequestModel(
                        $request['id'], $request['casillero'],
                        $request['status'], new DateTime($request['created_at']),
                        new DateTime($request['updated_at']), $request['is_acepted'],
                        $request['url_payment_document'], $request['periodo']
                    );
                }
                return $requests;
            }catch (PDOException $e) {
                throw new Exception('Error al buscar todas las solicitudes'. $e->getMessage());
            }
        }

        public function find_request_by_casillero_and_periodo(string $casillero_id, string $periodo_id): ?RequestModel{
            try {
                $stmt = $this->connection->prepare(
                    'SELECT * FROM Requests WHERE casillero = :casillero AND periodo = :periodo;');
                $stmt->execute([
                    'casillero'=> $casillero_id,
                    'periodo'=> $periodo_id
                ]);
                $result = $stmt->fetch();
                if($result){
                    return new RequestModel(
                        $result['id'],
                        $result['casillero'],
                        $result['status'],
                        new DateTime($result['created_at']),
                        new DateTime($result['updated_at']),
                        $result['is_acepted'],
                        $result['url_payment_document'],
                        $result['periodo']
                    );
                }
                return null;
            }catch (PDOException $e) {
                throw new Exception('Error al encontrar la solicitud'. $e->getMessage());
            }
        }

        public function how_request_by_periodo(string $periodo): int{
            try {
                $stmt = $this->connection->prepare(
                    'SELECT COUNT(*) as cuenta FROM Requests WHERE periodo = :periodo;');
                $stmt->execute([
                    'periodo'=> $periodo
                ]);
                $result = $stmt->fetch();
                if($result){
                    return $result['cuenta'];
                }
                return 0;
            }catch (PDOException $e) {
                throw new Exception('Error al ver la cantidad de casilleros'. $e->getMessage());
            }
        }
    }

?>