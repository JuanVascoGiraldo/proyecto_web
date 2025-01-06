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
                    "INSERT INTO Requests (id, casillero, status, created_at, updated_at, is_acepted, url_payment_document, periodo, user_id, until_at, url_acuse)
                    VALUES (:id, :casillero, :status, :created_at, :updated_at, :is_acepted, :url_payment_document, :periodo, :user_id, :until_at, :url_acuse);");
                $stm->execute([
                    'id' => $request->getId(),
                    'casillero' => $request->getCasillero(),
                    'status' => $request->getStatus(),
                    'created_at' => $request->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $request->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'is_acepted' => $request->getIsAcepted(),
                    'url_payment_document' => $request->getUrlPaymentDocument(),
                    'periodo' => $request->getPeriodo(),
                    'user_id' => $user_id,
                    'until_at' => $request->getUntilAt()->format('Y-m-d H:i:s'),
                    'url_acuse' => $request->getUrlAcuse()
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
                        $result['periodo'],
                        new DateTime($result['until_at']),
                        $result['url_acuse'],
                    );
                }
                return null;
            }catch (PDOException $e) {
                throw new Exception('Error al encontrar la solicitud'. $e->getMessage());
            }
        }
        
        /**
         * Encuentra todas las solicitudes de un usuario.
         * @param string $user_id id del usuario.
         * @throws \Exception Si ocurre un error al buscar las solicitudes.
         * @return RequestModel[] Arreglo de solicitudes.
         */
        public function find_all_request_by_user_id(string $user_id): array{
            try {
                $query = 'SELECT * FROM Requests WHERE user_id ="'.$user_id.'";';
                $stmt = $this->connection->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll();
                $requests = [];
                foreach($result as $request){
                    $new_req = new RequestModel(
                        $request['id'], $request['casillero'],
                        $request['status'], new DateTime($request['created_at']),
                        new DateTime($request['updated_at']), $request['is_acepted'],
                        $request['url_payment_document'], $request['periodo'],
                        new DateTime($request['until_at']),
                        $request['url_acuse'],
                    );
                    $requests[] = $new_req;
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
                        $result['periodo'],
                        new DateTime($result['until_at']),
                        $result['url_acuse'],
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
        
        /**
         * Actualiza la información del estudiante.
         * @param Student $student Instancia de estudiante.
         * @param string $user_id id del usuario.
         * @throws \Exception Si ocurre un error al actualizar.
         * @return bool Verdadero si se actualiza correctamente.
         */
        public function update_student(Student $student, string $user_id): bool{
            try{
                $stmt = $this->connection->prepare(
            'UPDATE Students SET telefono = :telefono,
                    first_name = :first_name, second_name = :second_name,
                    first_surname = :first_surname, second_surname = :second_surname,
                    height = :height, curp = :curp, credencial_url = :credencial_url,
                    horario_url = :horario_url, boleta = :boleta WHERE user_id = :id;');
                return $stmt->execute([
                    'telefono' => $student->getTelefono(),
                    'first_name' => $student->getFirstname(),
                    'second_name' => $student->getSecondname(),
                    'first_surname' => $student->getFirstsurname(),
                    'second_surname' => $student->getSecondsurname(),
                    'height' => $student->getHeight(),
                    'curp' => $student->getCurp(),
                    'credencial_url' => $student->getCredencial_url(),
                    'horario_url' => $student->getHorario_url(),
                    'boleta' => $student->getBoleta(),
                    'id' => $user_id
                ]);
            }catch (PDOException $e) {
                throw new Exception('Error al actualizar el estudiante'. $e->getMessage());
            }
        }

        /**
         * Actualiza la solicitud.
         * @param RequestModel $request Instancia de solicitud.
         * @throws \Exception Si ocurre un error al actualizar.
         * @return bool Verdadero si se actualiza correctamente.
         */
        public function update_request(RequestModel $request): bool{
            try{
                $stmt = $this->connection->prepare(
                    '
                        UPDATE Requests SET casillero = :casillero, status = :status,
                        is_acepted = :is_acepted, url_payment_document = :url_payment_document,
                        until_at = :until_at, url_acuse = :url_acuse
                        WHERE id = :id;
                    ');
                return $stmt->execute([
                    'casillero' => $request->getCasillero(),
                    'status' => $request->getStatus(),
                    'is_acepted' => $request->getIsAcepted(),
                    'url_payment_document' => $request->getUrlPaymentDocument(),
                    'until_at' => $request->getUntilAt()->format('Y-m-d H:i:s'),
                    'url_acuse' => $request->getUrlAcuse(),
                    'id' => $request->getId()
                    ]);
            }catch (PDOException $e) {
                throw new Exception('Error al actualizar la solicitud'. $e->getMessage());
            }
        }

        /**
         * Elimina el estudiante por el id del usuario.
         * @param string $student_id id del usuario.
         * @throws \Exception Si ocurre un error al borrar.
         * @return bool Verdadero si se borra correctamente.
         */
        public function delete_student_by_user_id(string $student_id): bool{
            try{
                $this->delete_all_request_by_user_id($student_id);
                $stmt = $this->connection->prepare(
                    'DELETE FROM Students WHERE  user_id = :user_id;');
                return $stmt->execute([
                    'user_id'=> $student_id
                    ]);
            }catch (PDOException $e) {
                throw new Exception('Error al borrar el estudiante'. $e->getMessage());
            }
        }
        
        /**
         * Elimina la solicitud por el id del usuario.
         * @param string $user_id id del usuario.
         * @throws \Exception Si ocurre un error al borrar.
         * @return bool Verdadero si se borra correctamente.
         */
        public function delete_all_request_by_user_id(string $user_id): bool{
            try{
                $stmt = $this->connection->prepare(
                    'DELETE FROM Requests WHERE user_id = :user_id;');
                return $stmt->execute([
                    'user_id'=> $user_id
                    ]);
            }catch (PDOException $e) {
                throw new Exception('Error al borrar la solicitud'. $e->getMessage());
            }
        }

        /**
         * Aceptar los terminos y condiciones.
         * @param string $user_id id del usuario.
         * @param string $request_id id de la solicitud.
         * @throws \Exception Si ocurre un error al aceptar.
         * @return bool Verdadero si se acepta correctamente.
         */
        public function acept_terms_and_conditions(string $user_id, string $request_id): bool{
            try{
                $stmt = $this->connection->prepare(
                    'UPDATE Requests SET is_acepted = 1 WHERE user_id = :user_id AND id = :request_id');
                return $stmt->execute([
                    'user_id'=> $user_id,
                    'request_id'=> $request_id
                ]);
            }catch (PDOException $e) {
                throw new Exception('Error al aceptar los terminos y condiciones'. $e->getMessage());
            }
        }

    }

?>