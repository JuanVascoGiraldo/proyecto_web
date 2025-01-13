-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema casilleros
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema casilleros
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `casilleros` DEFAULT CHARACTER SET utf8 ;
USE `casilleros` ;

-- -----------------------------------------------------
-- Table `casilleros`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casilleros`.`Users` ;

CREATE TABLE IF NOT EXISTS `casilleros`.`Users` (
  `id` VARCHAR(50) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `role` INT NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `status` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casilleros`.`Sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casilleros`.`Sessions` ;

CREATE TABLE IF NOT EXISTS `casilleros`.`Sessions` (
  `id` VARCHAR(50) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `user_id` VARCHAR(50) NOT NULL,
  `expiration_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_id_relation`
    FOREIGN KEY (`user_id`)
    REFERENCES `casilleros`.`Users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `user_id_relation_idx` ON `casilleros`.`Sessions` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `casilleros`.`Students`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casilleros`.`Students` ;

CREATE TABLE IF NOT EXISTS `casilleros`.`Students` (
  `boleta` VARCHAR(10) NOT NULL,
  `telefono` VARCHAR(10) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `second_name` VARCHAR(45) NULL,
  `first_surname` VARCHAR(45) NOT NULL,
  `second_surname` VARCHAR(45) NOT NULL,
  `height` INT NOT NULL,
  `curp` VARCHAR(18) NOT NULL,
  `credencial_url` VARCHAR(100) NOT NULL,
  `horario_url` VARCHAR(45) NOT NULL,
  `user_id` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_id_student_reference`
    FOREIGN KEY (`user_id`)
    REFERENCES `casilleros`.`Users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `user_id_reference_idx` ON `casilleros`.`Students` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `casilleros`.`Requests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casilleros`.`Requests` ;

CREATE TABLE IF NOT EXISTS `casilleros`.`Requests` (
  `id` VARCHAR(50) NOT NULL,
  `casillero` INT NULL,
  `status` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `is_acepted` INT NOT NULL,
  `url_payment_document` VARCHAR(100) NOT NULL,
  `user_id` VARCHAR(50) NOT NULL,
  `periodo` VARCHAR(45) NOT NULL,
  `until_at` DATETIME NOT NULL,
  `url_acuse` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_id_request_reference`
    FOREIGN KEY (`user_id`)
    REFERENCES `casilleros`.`Students` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `user_id_request_reference_idx` ON `casilleros`.`Requests` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `casilleros`.`Verifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casilleros`.`Verifications` ;

CREATE TABLE IF NOT EXISTS `casilleros`.`Verifications` (
  `id` VARCHAR(50) NOT NULL,
  `code` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `expiration_date` DATETIME NOT NULL,
  `attemps` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `casilleros`.`Users`
-- -----------------------------------------------------
START TRANSACTION;
USE `casilleros`;
INSERT INTO `casilleros`.`Users` (`id`, `username`, `password`, `role`, `email`, `created_at`, `updated_at`, `status`) VALUES ('USER#677790162eea76.98914690', 'Admin', '$2y$10$t38aHEWKuhHhyhAyZ73uAuzUkipc6EXtoQz1TATz.b3EwnaWVwNWi', 1, 'sigca2024@gmail.com', '2025-01-01 15:20:15', '2025-01-01 15:20:15', 1);

COMMIT;

