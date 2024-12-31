<?php

    require_once __DIR__ .'/../models/verification.php';
    require_once __DIR__ .'/../services/database.php';
    require_once __DIR__ . '/../helpers/date_manage.php';

    class VerificationRepository{

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
         * Añade una verificación a la base de datos.
         * @param Verification $verification Instancia de verificación.
         * @throws \Exception Si ocurre un error al guardar.
         * @return bool Verdadero si se guarda correctamente.
         */
        public function add_verification(Verification $verification): bool{
            try{
                $stmt = $this->connection->prepare(
                    "INSERT INTO Verifications (id, code, email, expiration_date, attemps)
                    VALUES (:id, :code, :email, :expiration_date, :attemps);");
                return $stmt->execute(
                    [
                        "id"=> $verification->getId(),
                        "code"=> $verification->getCode(),
                        "email"=> $verification->getEmail(),
                        "expiration_date"=> $verification->getExpirationDate()->format('Y-m-d H:i:s'),
                        "attemps"=> $verification->getAttemps()
                    ]
                );
            }catch(Exception $e){
                throw new Exception("Error al guardar la verificación: " . $e->getMessage());
            }
            
        }

        /**
         * Actualiza los intentos de verificación.
         * @param Verification $verification Instancia de verificación.
         * @throws \Exception Si ocurre un error al actualizar.
         * @return bool Verdadero si se actualiza correctamente.
         */
        public function update_attemps(Verification $verification): bool{
            try{
                $stmt = $this->connection->prepare(
                    "UPDATE Verifications SET attemps = :attemps WHERE id = :id;");
                return $stmt->execute(
                    [
                        "id"=> $verification->getId(),
                        "attemps"=> $verification->getAttemps()
                    ]
                );
            }catch(Exception $e){
                throw new Exception("Error al actualizar". $e->getMessage());
            }
        }

        /**
         * encontrar una verificación por el email.
         * @param string $email Email a buscar.
         * @throws \Exception Si ocurre un error al buscar la verificación.
         * @return Verification|null Instancia de verificación o nulo.
         */
        public function find_by_email(string $email): ?Verification{
            try{
                
                $stmt = $this->connection->prepare(
                    "SELECT * FROM Verifications WHERE email = :email AND expiration_date > :expiration_date AND attemps < 3;");
                $stmt->execute(
                    [
                        "email"=> $email,
                        "expiration_date"=> getCurrentUTC()->format('Y-m-d H:i:s')
                    ]
                );
                $result = $stmt->fetch();
                if($result){
                    return new Verification(
                        $result['id'],
                        $result['code'],
                        $result['email'],
                        new DateTime($result['expiration_date']),
                        $result['attemps']
                    );
                }
                return null;
            }catch(Exception $e){
                throw new Exception("Error al encontrar la verificacion: ". $e->getMessage());
            }
        }


    }

?>