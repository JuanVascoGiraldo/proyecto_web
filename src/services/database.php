<?php

    class Database {
        private ?PDO $connection = null;

        /**
         * Constructor que inicializa la conexión con la base de datos.
         *
         * @param string $host Dirección del servidor de base de datos.
         * @param string $dbname Nombre de la base de datos.
         * @param string $username Usuario para la conexión.
         * @param string $password Contraseña para la conexión.
         * @throws PDOException Si no se puede conectar a la base de datos.
         */
        public function __construct(string $host, string $dbname, string $username, string $password) {
            try {
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
                $this->connection = new PDO($dsn, $username, $password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al conectar con la base de datos: " . $e->getMessage());
            }
        }

        /**
         * Devuelve la conexión PDO actual.
         *
         * @return PDO
         */
        public function getConnection(): PDO {
            return $this->connection;
        }

        /**
         * Método para cerrar la conexión (opcional).
         */
        public function closeConnection(): void {
            $this->connection = null;
        }
    }
?>