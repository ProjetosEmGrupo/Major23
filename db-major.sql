CREATE DATABASE major
USE major

CREATE TABLE `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `tipo` VARCHAR(45) NOT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `preparavel` VARCHAR(15) NOT NULL,
  `disponivel` int NOT NULL,	
  PRIMARY KEY (`id`));

CREATE TABLE `requests` (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `status` VARCHAR(45) NOT NULL,
  `mesa` INT NULL,
  `viagem` INT NOT NULL,
  `observacao` VARCHAR(200) NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `request_items` (
  `requests_id` INT NOT NULL,
  `products_id` INT NOT NULL,
  `qtd` INT NOT NULL,
  PRIMARY KEY (`requests_id`, `products_id`),
  CONSTRAINT `fk_request_itens_requests`
    FOREIGN KEY (`requests_id`) REFERENCES `requests` (`id`),
  CONSTRAINT `fk_request_itens_products1`
    FOREIGN KEY (`products_id`) REFERENCES `products` (`id`));