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
