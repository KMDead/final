CREATE SCHEMA IF NOT EXISTS `warehouse` DEFAULT CHARACTER SET utf8;
USE `warehouse`;

DROP TABLE IF EXISTS `warehouse`.`warehouse`;

CREATE TABLE IF NOT EXISTS `warehouse`.`warehouse` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;