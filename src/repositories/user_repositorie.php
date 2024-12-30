<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../services/database.php';

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

}
