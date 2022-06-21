-- ************************************** `Bien`

CREATE TABLE `Bien`
(
 `id`             int NOT NULL  AUTO_INCREMENT,
 `pr_id`          int NOT NULL ,
 `nom_fr`         varchar(45) NOT NULL ,
 `nom_en`         varchar(45) NOT NULL ,
 `description_fr` varchar(255) NOT NULL ,
 `description_en` varchar(255) NOT NULL ,

PRIMARY KEY (`id`)
);

-- ************************************** `Equipment_category`

CREATE TABLE `Equipment_category`
(
 `id`  int NOT NULL ,
 `nom` varchar(45) NOT NULL ,

PRIMARY KEY (`id`)
);






-- ************************************** `Equipment`

CREATE TABLE `Equipment`
(
 `id`          int NOT NULL ,
 `id_category` int NOT NULL ,
 `nom_fr`      varchar(45) NOT NULL ,
 `nom_en`      varchar(45) NOT NULL ,

PRIMARY KEY (`id`),
KEY `FK_40` (`id_category`),
CONSTRAINT `FK_38` FOREIGN KEY `FK_40` (`id_category`) REFERENCES `Equipment_category` (`id`)
);





-- ************************************** `Bien_equipment`

CREATE TABLE `Bien_equipment`
(
 `id_bien`      int NOT NULL ,
 `id_equipment` int NOT NULL ,

PRIMARY KEY (`id_bien`, `id_equipment`),
KEY `FK_44` (`id_bien`),
CONSTRAINT `FK_42` FOREIGN KEY `FK_44` (`id_bien`) REFERENCES `Bien` (`id`),
KEY `FK_48` (`id_equipment`),
CONSTRAINT `FK_46` FOREIGN KEY `FK_48` (`id_equipment`) REFERENCES `Equipment` (`id`)
);





