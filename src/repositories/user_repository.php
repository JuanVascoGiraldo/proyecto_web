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
                "INSERT INTO user (id, username, password, role, email, status, created_at, updated_at) 
                VALUES (:id, :username, :password, :role, :email, :status, :created_at, :updated_at);"
            );
            return $stmt->execute([
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "password" => $user->getPassword(),
                "role" => $user->getRole(),
                "email"=> $user->getEmail(),
                "status" => $user->getStatus(),
                "created_at" => $user->getCreatedAt(),
                "updated_at"=> $user->getUpdatedAt()
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
            $stmt = $this->connection->prepare("SELECT * FROM usuarios WHERE id = :id");
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
                    $result['created_at'],
                    $result['updated_at']
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
                "SELECT * FROM user WHERE username = :username"
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
                    $result["created_at"],
                    $result["updated_at"]
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
                "SELECT * FROM user WHERE email = :email"
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
                    $result["created_at"],
                    $result["updated_at"]
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
                "created_at"=> $session->getCreatedAt(),
                "expiration_date"=> $session->getExpirationDate()
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
                    "UPDATE sessions SET expiration_date = :expiration_date WHERE id = :id");
                $stmt->execute([
                    "expiration_date" => $session->getExpirationDate(),
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
                "expiration_date"=> getCurrentUTC()
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


}

?>