<?php

    class Database {
        private ?PDO $connection = null;

        /**
         * Constructor que inicializa la conexión con la base de datos.
         * @throws PDOException Si no se puede conectar a la base de datos.
         */
        public function __construct() {
            try {
                $dsn = "mysql:host=127.0.0.1:3306;dbname=casilleros;charset=utf8mb4";
                $this->connection = new PDO($dsn, "root", "03042021");
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