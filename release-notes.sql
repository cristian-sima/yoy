USE `yoy_ro_date`;

ALTER TABLE `completare_mecanica` DROP ` total_premii `;
DROP TABLE impozit;
DROP TABLE `carnete_bilete`, `completare_bilete`;
DELETE FROM `taxa` WHERE `tip`= 'bilet';
DROP TABLE completare_electronica;
DROP TABLE index_electronic;
ALTER TABLE `firma` DROP ` restanta `;

ALTER TABLE `incasare`
  DROP `restanta`,
  DROP `taxaAparate`,
  DROP `taxaBilete`,
  DROP `bilete`;

DROP TABLE platataxa;

DELETE FROM `taxa` WHERE `taxa`.`tip` = "suma";
DELETE FROM `taxa` WHERE `taxa`.`id` = "procent";


-- v3.0

ALTER TABLE `firma` ENGINE = InnoDB;
ALTER TABLE `procent` ENGINE = InnoDB;

ALTER TABLE `firma` ADD UNIQUE(`id`);
ALTER TABLE `procent` ADD INDEX(`idFirma`);
ALTER TABLE procent
  ADD CONSTRAINT fk_name
  FOREIGN KEY (idFirma)
  REFERENCES firma(id)
  ON DELETE CASCADE;
