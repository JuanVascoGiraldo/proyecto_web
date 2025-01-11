<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/session.php';
require_once __DIR__ . '/../services/database.php';
require_once __DIR__ . '/../helpers/date_manage.php';


class UserRepository {
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
     * Guarda un usuario en la base de datos.
     *
     * @param User $user Instancia de usuario.
     * @return bool Verdadero si se guarda correctamente.
     * @throws Exception Si ocurre un error al guardar.
     */
    public function save(User $user): bool {
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO Users (id, username, password, role, email, status, created_at, updated_at) 
                VALUES (:id, :username, :password, :role, :email, :status, :created_at, :updated_at);"
            );
            return $stmt->execute([
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "password" => $user->getPassword(),
                "role" => $user->getRole(),
                "email"=> $user->getEmail(),
                "status" => $user->getStatus(),
                "created_at" => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                "updated_at"=> $user->getUpdatedAt()->format("Y-m-d H:i:s"),
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al guardar el usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene un usuario por su ID.
     *
     * @param string $id ID del usuario.
     * @return User|null Instancia de usuario o null si no se encuentra.
     */
    public function findById(string $id): ?User {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM Users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();

            if ($result) {
                return new User(
                    $result['id'],
                    $result['username'],
                    $result['password'],
                    $result['email'],
                    $result['role'],
                    $result['status'],
                    new DateTime($result['created_at']),
                    new DateTime($result['updated_at'])
                );
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar el usuario: " . $e->getMessage());
        }
    }

    public function findByUsername(string $username): ?User {
        try {
            $stmt = $this->connection->prepare(
                "SELECT * FROM Users WHERE username = :username"
            );
            $stmt->execute([
                "username"=> $username
            ]);
            $result = $stmt->fetch();
            if ($result) {
                return new User(
                    $result["id"],
                    $result["username"],
                    $result["password"],
                    $result["email"],
                    $result["role"],
                    $result["status"],
                    new DateTime($result['created_at']),
                    new DateTime($result['updated_at'])
                    );
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar el usuario" . $e->getMessage());
        }
    }

    /**
     * Obtiene un usuario por su email.
     * @param string $email Email del usuario.
     * @throws \Exception Si ocurre un error al buscar el usuario.
     * @return User|null Instancia de usuario o nulo si no se encuentra.
     */
    public function findByEmail(string $email): ?User {
        try {
            $stmt = $this->connection->prepare(
                "SELECT * FROM Users WHERE email = :email"
            );
            $stmt->execute([
                "email"=> $email
            ]);
            $result = $stmt->fetch();
            if ($result) {
                return new User(
                    $result["id"],
                    $result["email"],
                    $result["password"],
                    $result["email"],
                    $result["role"],
                    $result["status"],
                    new DateTime($result['created_at']),
                    new DateTime($result['updated_at'])
                );
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar el usuario" . $e->getMessage());
        }
    }

    /**
     * Guardar una sesión en la base de datos.
     * @param User $user Usuario al que pertenece la sesión.
     * @param Session $session Sesión a guardar.
     * @throws \Exception Si ocurre un error al guardar la sesión.
     * @return bool Verdadero si se guarda correctamente.
     */
    public function save_sessions(User $user, Session $session): bool {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO Sessions (id, user_id, created_at, expiration_date) 
                VALUES (:id, :user_id, :created_at, :expiration_date);
            ");
            return $stmt->execute([
                "user_id"=> $user->getId(),
                "id"=> $session->getId(),
                "created_at"=> $session->getCreatedAt()->format("Y-m-d H:i:s"),
                "expiration_date"=> $session->getExpirationDate()->format("Y-m-d H:i:s"),
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al guardar la sesión: " . $e->getMessage());
        }
    }

    /**
     * Actualiza la fecha de expiración de todas las sesiones de un usuario.
     * @param User $user Usuario al que pertenecen las sesiones.
     * @throws \Exception Si ocurre un error al actualizar las sesiones.
     * @return bool Verdadero si se actualizan correctamente.
     */
    public function update_all_user_sessions(User $user): bool {
        try{
            foreach ($user->get_sessions() as $session) {
                $stmt = $this->connection->prepare(
                    "UPDATE Sessions SET expiration_date = :expiration_date WHERE id = :id");
                
                $stmt->execute([
                    "expiration_date" => $session->getExpirationDate()->format("Y-m-d H:i:s"),
                    "id" => $session->getId()
                ]);
            }
            return true;
        }catch (PDOException $e) {
            throw new Exception("Error al actualizar las sessiones". $e->getMessage());
        }
    }

    public function find_user_by_session_id(string $session_id): ?User {
        try {
            $stmt = $this->connection->prepare("
                SELECT user_id FROM sessions WHERE id = :id and expiration_date > :expiration_date;
            ");
            $stmt->execute([
                "id"=> $session_id,
                "expiration_date"=> getCurrentUTC()->format("Y-m-d H:i:s"),
                ]);
            $result = $stmt->fetch();
            if($result){
                return $this->findById($result['user_id']);
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar por la session: ". $e->getMessage());
        }
    }

    /**
     * Obtiene todos los estudiantes.
     * @throws \Exception Si ocurre un error al buscar los estudiantes.
     * @return User[] Lista de estudiantes.
     */
    public function get_all_students(): array {
        try {
            $stmt = $this->connection->prepare(
                "SELECT * FROM Users WHERE role = 2 ORDER BY created_at ASC"
            );
            $stmt->execute();
            $result = $stmt->fetchAll();
            $students = [];
            foreach($result as $student) {
                $students[] = new User(
                    $student["id"],
                    $student["username"],
                    $student["password"],
                    $student["email"],
                    $student["role"],
                    $student["status"],
                    new DateTime($student['created_at']),
                    new DateTime($student['updated_at'])
                );
            }
            return $students;
        }catch (PDOException $e) {
            throw new Exception("Error al buscar los estudiantes". $e->getMessage());
        }
    }

    /**
     * Actualiza un usuario.
     * @param User $user Usuario a actualizar.
     * @throws \Exception Si ocurre un error al actualizar el usuario.  
     * @return bool Verdadero si se actualiza correctamente.
     */
    public function update_user(User $user): bool {
        try {
            $stmt = $this->connection->prepare(
                "UPDATE Users SET username = :username, email = :email, role = :role, status = :status, updated_at = :updated_at, created_at = :created_at WHERE id = :id"
            );
            return $stmt->execute([
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "role"=> $user->getRole(),
                "status"=> $user->getStatus(),
                "updated_at"=> getCurrentUTC()->format("Y-m-d H:i:s"),
                "created_at"=> $user->getCreatedAt()->format("Y-m-d H:i:s"),
                "id"=> $user->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todas las sesiones activas de un usuario.
     * @param string $user_id ID del usuario.
     * @throws \Exception Si ocurre un error al buscar las sesiones.
     * @return Session[] Lista de sesiones.
     */
    public function get_all_sessions_by_user_id(string $user_id): array {
        try {
            $stmt = $this->connection->prepare(
                "SELECT * FROM Sessions WHERE user_id = :user_id and expiration_date > :expiration_date;"
            );
            $stmt->execute([
                "user_id"=> $user_id,
                "expiration_date"=> getCurrentUTC()->format("Y-m-d H:i:s")
            ]);
            $result = $stmt->fetchAll();
            $sessions = [];
            foreach ($result as $row) {
                $sessions[] = new Session(
                    id: $row["id"],
                    created_at: new DateTime($row["created_at"]),
                    expiration_date: new DateTime($row["expiration_date"])
                );
            }
            return $sessions;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar las sesiones". $e->getMessage());
        }
    }

    /**
     * Elimina un usuario.
     * @param string $id ID del usuario.
     * @throws \Exception Si ocurre un error al eliminar el usuario.
     * @return bool Verdadero si se elimina correctamente.
     */
    public function delete_user_by_id(string $id): bool {
        try {
            $this->delete_session_by_id($id);
            $stmt = $this->connection->prepare(
                "DELETE FROM Users WHERE id = :id"
            );
            return $stmt->execute([
                "id"=> $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al borrar el usuario". $e->getMessage());
        }
    }

    /**
     * Elimina todas las sesiónes de un usuario.
     * @param string $id ID del usuario.
     * @throws \Exception Si ocurre un error al eliminar la sesión.
     * @return bool Verdadero si se elimina correctamente.
     */
    public function delete_session_by_id(string $id): bool {
        try {
            $stmt = $this->connection->prepare(
                "DELETE FROM Sessions WHERE user_id = :user_id"
            );
            return $stmt->execute([
                "user_id"=> $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al borrar la sesión". $e->getMessage());
        }
    }

}

?>