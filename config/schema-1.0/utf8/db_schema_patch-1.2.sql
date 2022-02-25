ALTER TABLE `adminlanguages`
    ADD COLUMN `ISO 639-3` varchar(3) NULL AFTER `iso639_2`;

CREATE TABLE `configurations`
(
    `id`                    int(11) NOT NULL AUTO_INCREMENT,
    `configurationName`     varchar(100) NOT NULL,
    `configurationDataType` varchar(15)  NOT NULL DEFAULT 'string',
    `configurationValue`    text         NOT NULL,
    `dateApplied`           timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

ALTER TABLE `fmchklstcoordinates`
DROP
FOREIGN KEY `FKchklsttaxalink`,
DROP INDEX `IndexUnique`;

ALTER TABLE `fmchklstprojlink`
    ADD COLUMN `sortSequence` int(11) NULL AFTER `mapChecklist`;

ALTER TABLE `fmchklsttaxalink`
DROP
FOREIGN KEY `FK_chklsttaxalink_cid`,
  DROP
FOREIGN KEY `FK_chklsttaxalink_tid`,
  ADD INDEX `FK_chklsttaxalink_tid`(`TID`);

ALTER TABLE `fmchklsttaxastatus`
DROP
FOREIGN KEY `FK_fmchklsttaxastatus_clidtid`,
DROP INDEX `FK_fmchklsttaxastatus_clid_idx`;

ALTER TABLE `fmcltaxacomments`
DROP
FOREIGN KEY `FK_clcomment_cltaxa`,
DROP INDEX `FK_clcomment_cltaxa`;

ALTER TABLE `fmprojects`
    MODIFY COLUMN `fulldescription` varchar (5000) NULL DEFAULT NULL AFTER `briefdescription`;

ALTER TABLE `fmvouchers`
DROP
FOREIGN KEY `FK_fmvouchers_occ`,
  DROP
FOREIGN KEY `FK_vouchers_cl`,
  ADD COLUMN `vid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT AFTER `TID`,
  DROP
PRIMARY KEY,
  ADD PRIMARY KEY (`vid`),
DROP INDEX `chklst_taxavouchers`,
  ADD UNIQUE INDEX `UNIQUE_voucher`(`CLID`, `occid`);

ALTER TABLE `images`
    ADD INDEX `Index_images_datelastmod` (`InitialTimeStamp` ASC),
    MODIFY COLUMN `caption` varchar(750) NULL DEFAULT NULL AFTER `format`,
    MODIFY COLUMN `url` varchar(255) NULL DEFAULT NULL AFTER `tid`;

ALTER TABLE `institutions`
    CHANGE COLUMN `IntialTimeStamp` `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modifiedTimeStamp`;

ALTER TABLE `kmcharacters`
    ADD COLUMN `display` varchar(45) AFTER `notes`;

RENAME
TABLE `kmchardependance` TO `kmchardependence`;

ALTER TABLE `kmcslang`
    CHANGE COLUMN `intialtimestamp` `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `notes`;

ALTER TABLE `lkupstateprovince`
    MODIFY COLUMN `abbrev` varchar (3) NULL DEFAULT NULL AFTER `stateName`;

ALTER TABLE `media`
    DROP FOREIGN KEY `FK_media_uid`;

ALTER TABLE `media`
    DROP COLUMN `notes`,
    DROP COLUMN `mediaMD5`,
    CHANGE COLUMN `url` `accessuri` varchar(2048) NOT NULL AFTER `occid`,
    CHANGE COLUMN `caption` `title` varchar(255) NULL DEFAULT NULL AFTER `accessuri`,
    CHANGE COLUMN `authoruid` `creatoruid` int(10) UNSIGNED NULL DEFAULT NULL AFTER `title`,
    CHANGE COLUMN `author` `creator` varchar(45) NULL DEFAULT NULL AFTER `creatoruid`,
    CHANGE COLUMN `mediatype` `type` varchar(45) NULL DEFAULT NULL AFTER `creator`,
    CHANGE COLUMN `sourceurl` `furtherinformationurl` varchar(2048) NULL DEFAULT NULL AFTER `owner`,
    CHANGE COLUMN `locality` `locationcreated` varchar(1000) NULL DEFAULT NULL AFTER `furtherinformationurl`,
    ADD COLUMN `format` varchar(45) NULL AFTER `type`,
    ADD COLUMN `language` varchar(45) NULL AFTER `furtherinformationurl`,
    ADD COLUMN `usageterms` varchar(255) NULL AFTER `language`,
    ADD COLUMN `rights` varchar(255) NULL AFTER `usageterms`,
    ADD COLUMN `bibliographiccitation` varchar(255) NULL AFTER `rights`,
    ADD COLUMN `publisher` varchar(255) NULL AFTER `bibliographiccitation`,
    ADD COLUMN `contributor` varchar(255) NULL AFTER `publisher`,
    ADD INDEX `INDEX_format`(`format`);

ALTER TABLE `media`
    ADD CONSTRAINT `FK_media_uid`  FOREIGN KEY (`creatoruid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `omcollcategories`
    ADD COLUMN `sortsequence` int(11) NULL AFTER `notes`;

DROP TABLE IF EXISTS `omcollectioncontacts`;

ALTER TABLE `omcollections`
    ADD COLUMN `datasetID` varchar(250) NULL DEFAULT NULL AFTER `collectionId`,
    ADD COLUMN `contactJson` json NULL AFTER `email`,
    ADD COLUMN `dynamicProperties` text NULL AFTER `accessrights`;

UPDATE omcrowdsourcecentral c INNER JOIN omcrowdsourcequeue q ON c.omcsid = q.omcsid
    INNER JOIN userroles r ON c.collid = r.tablepk AND q.uidprocessor = r.uid
    SET q.isvolunteer = 0
    WHERE r.role IN ("CollAdmin", "CollEditor") AND q.isvolunteer = 1;

ALTER TABLE `omoccuredits`
    ADD COLUMN `editType` INT NULL DEFAULT 0 COMMENT '0 = general edit, 1 = batch edit' AFTER `AppliedStatus`;

UPDATE omoccuredits e INNER JOIN (SELECT initialtimestamp, uid, count (DISTINCT occid) as cnt
    FROM omoccuredits
    GROUP BY initialtimestamp, uid
    HAVING cnt > 2) as inntab
ON e.initialtimestamp = inntab.initialtimestamp AND e.uid = inntab.uid
    SET edittype = 1;

ALTER TABLE `omoccurgenetic`
    MODIFY COLUMN `notes` varchar (250) NULL DEFAULT NULL AFTER `resourceurl`,
    MODIFY COLUMN `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `notes`,
    ADD UNIQUE INDEX `UNIQUE_omoccurgenetic`(`occid`, `resourceurl`);

CREATE TABLE `omoccurpaleo`
(
    `paleoID`             int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid`               int(10) unsigned NOT NULL,
    `eon`                 varchar(65)   DEFAULT NULL,
    `era`                 varchar(65)   DEFAULT NULL,
    `period`              varchar(65)   DEFAULT NULL,
    `epoch`               varchar(65)   DEFAULT NULL,
    `earlyInterval`       varchar(65)   DEFAULT NULL,
    `lateInterval`        varchar(65)   DEFAULT NULL,
    `absoluteAge`         varchar(65)   DEFAULT NULL,
    `storageAge`          varchar(65)   DEFAULT NULL,
    `stage`               varchar(65)   DEFAULT NULL,
    `localStage`          varchar(65)   DEFAULT NULL,
    `biota`               varchar(65)   DEFAULT NULL COMMENT 'Flora or Fanua',
    `biostratigraphy`     varchar(65)   DEFAULT NULL COMMENT 'Biozone',
    `taxonEnvironment`    varchar(65)   DEFAULT NULL COMMENT 'Marine or not',
    `lithogroup`          varchar(65)   DEFAULT NULL,
    `formation`           varchar(65)   DEFAULT NULL,
    `member`              varchar(65)   DEFAULT NULL,
    `bed`                 varchar(65)   DEFAULT NULL,
    `lithology`           varchar(250)  DEFAULT NULL,
    `stratRemarks`        varchar(250)  DEFAULT NULL,
    `element`             varchar(250)  DEFAULT NULL,
    `slideProperties`     varchar(1000) DEFAULT NULL,
    `geologicalContextID` varchar(45)   DEFAULT NULL,
    `initialtimestamp`    timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`paleoID`),
    UNIQUE KEY `UNIQUE_occid` (`occid`),
    KEY                   `FK_paleo_occid_idx` (`occid`),
    CONSTRAINT `FK_paleo_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) COMMENT='Occurrence Paleo tables';

CREATE TABLE `omoccurpaleogts`
(
    `gtsid`            int(10) unsigned NOT NULL AUTO_INCREMENT,
    `gtsterm`          varchar(45) NOT NULL,
    `rankid`           int(11) NOT NULL,
    `rankname`         varchar(45) DEFAULT NULL,
    `parentgtsid`      int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`gtsid`),
    UNIQUE KEY `UNIQUE_gtsterm` (`gtsid`),
    KEY                `FK_gtsparent_idx` (`parentgtsid`),
    CONSTRAINT `FK_gtsparent` FOREIGN KEY (`parentgtsid`) REFERENCES `omoccurpaleogts` (`gtsid`) ON DELETE NO ACTION ON UPDATE CASCADE
);

ALTER TABLE `omoccurpoints`
DROP
COLUMN `errradiuspoly`,
  DROP
COLUMN `footprintpoly`;

ALTER TABLE `omoccurrences`
    ADD INDEX `Index_occurrenceRemarks`(`occurrenceRemarks`(100)),
    CHANGE COLUMN `labelProject` `labelProject` varchar(250) DEFAULT NULL,
    DROP INDEX `idx_occrecordedby`,
    MODIFY COLUMN `georeferenceRemarks` varchar(500) NULL DEFAULT NULL AFTER `georeferenceVerificationStatus`,
    ADD INDEX `Index_locationID`(`locationID`),
    ADD INDEX `Index_eventID`(`eventID`),
    ADD INDEX `Index_occur_localitySecurity`(`localitySecurity`),
    ADD INDEX `Index_latlng`(`decimalLatitude`, `decimalLongitude`),
    ADD INDEX `Index_ labelProject`(`labelProject`);

REPLACE
omoccurrencesfulltext(occid,locality,recordedby)
SELECT occid, CONCAT_WS("; ", municipality, locality), recordedby
FROM omoccurrences;

ALTER TABLE `referenceobject`
    CHANGE COLUMN `numbervolumnes` `numbervolumes` varchar (45) NULL DEFAULT NULL AFTER `volume`;

CREATE TABLE `taxonkingdoms`
(
    `kingdom_id`   int(11) NOT NULL,
    `kingdom_name` varchar(250) NOT NULL,
    PRIMARY KEY (`kingdom_id`),
    INDEX          `INDEX_kingdom_name` (`kingdom_name` ASC),
    KEY            `INDEX_kingdoms` (`kingdom_id`,`kingdom_name`)
);

INSERT INTO `taxonkingdoms`
VALUES (1, 'Bacteria');
INSERT INTO `taxonkingdoms`
VALUES (2, 'Protozoa');
INSERT INTO `taxonkingdoms`
VALUES (3, 'Plantae');
INSERT INTO `taxonkingdoms`
VALUES (4, 'Fungi');
INSERT INTO `taxonkingdoms`
VALUES (5, 'Animalia');
INSERT INTO `taxonkingdoms`
VALUES (6, 'Chromista');
INSERT INTO `taxonkingdoms`
VALUES (7, 'Archaea');
INSERT INTO `taxonkingdoms`
VALUES (100, 'Unknown');

ALTER TABLE `taxa`
    ADD COLUMN `kingdomId` int(11) NULL DEFAULT 100 AFTER `kingdomName`,
    ADD INDEX `kingdomid_index`(`kingdomId`),
    MODIFY COLUMN `UnitInd3` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `UnitName2`,
    ADD COLUMN `locked` int(11) NULL AFTER `Hybrid`,
    ADD CONSTRAINT `FK_kingdom_id` FOREIGN KEY (`kingdomId`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON
DELETE
SET NULL ON UPDATE SET NULL;

ALTER TABLE `taxadescrblock`
DROP
FOREIGN KEY `FK_taxadescrblock_tid`;

ALTER TABLE `taxadescrblock`
DROP INDEX `Index_unique`,
  ADD INDEX `FK_taxadescrblock_tid_idx`(`tid`),
  ADD CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON
UPDATE CASCADE;

ALTER TABLE `taxadescrstmts`
    MODIFY COLUMN `heading` varchar (75) NULL DEFAULT NULL AFTER `tdbid`;

ALTER TABLE `taxaresourcelinks`
    ADD UNIQUE INDEX `UNIQUE_taxaresource`(`tid`, `sourcename`);

ALTER TABLE `taxavernaculars`
    MODIFY COLUMN `Language` varchar (15) NULL DEFAULT NULL AFTER `VernacularName`,
DROP INDEX `unique-key`,
  ADD UNIQUE INDEX `unique-key`(`VernacularName`, `TID`, `langid`);

SET
FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `taxonunits`;

ALTER TABLE `taxonunits`
    ADD COLUMN `kingdomid` int(11) NOT NULL AFTER `taxonunitid`,
    ADD UNIQUE INDEX `INDEX-Unique`(`kingdomid`, `rankid`),
    ADD CONSTRAINT `FK-kingdomid` FOREIGN KEY (`kingdomid`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON
UPDATE CASCADE
ON
DELETE CASCADE;

INSERT INTO `taxonunits`(`kingdomid`, `rankid`, `rankname`, `dirparentrankid`, `reqparentrankid`)
VALUES (1, 10, 'Kingdom', 10, 10),
       (1, 20, 'Subkingdom', 10, 10),
       (1, 30, 'Phylum', 20, 10),
       (1, 40, 'Subphylum', 30, 30),
       (1, 50, 'Superclass', 40, 30),
       (1, 60, 'Class', 50, 30),
       (1, 70, 'Subclass', 60, 60),
       (1, 80, 'Infraclass', 70, 60),
       (1, 90, 'Superorder', 80, 60),
       (1, 100, 'Order', 90, 60),
       (1, 110, 'Suborder', 100, 100),
       (1, 120, 'Infraorder', 110, 100),
       (1, 130, 'Superfamily', 120, 100),
       (1, 140, 'Family', 130, 100),
       (1, 150, 'Subfamily', 140, 140),
       (1, 160, 'Tribe', 150, 140),
       (1, 170, 'Subtribe', 160, 140),
       (1, 180, 'Genus', 170, 140),
       (1, 190, 'Subgenus', 180, 180),
       (1, 220, 'Species', 190, 180),
       (1, 230, 'Subspecies', 220, 180),
       (2, 10, 'Kingdom', 10, 10),
       (2, 20, 'Subkingdom', 10, 10),
       (2, 25, 'Infrakingdom', 20, 10),
       (2, 30, 'Phylum', 25, 10),
       (2, 40, 'Subphylum', 30, 30),
       (2, 45, 'Infraphylum', 40, 30),
       (2, 47, 'Parvphylum', 45, 30),
       (2, 50, 'Superclass', 47, 30),
       (2, 60, 'Class', 50, 30),
       (2, 70, 'Subclass', 60, 60),
       (2, 80, 'Infraclass', 70, 60),
       (2, 90, 'Superorder', 80, 60),
       (2, 100, 'Order', 90, 60),
       (2, 110, 'Suborder', 100, 100),
       (2, 120, 'Infraorder', 110, 100),
       (2, 130, 'Superfamily', 120, 100),
       (2, 140, 'Family', 130, 100),
       (2, 150, 'Subfamily', 140, 140),
       (2, 160, 'Tribe', 150, 140),
       (2, 170, 'Subtribe', 160, 140),
       (2, 180, 'Genus', 170, 140),
       (2, 190, 'Subgenus', 180, 180),
       (2, 220, 'Species', 190, 180),
       (2, 230, 'Subspecies', 220, 180),
       (2, 240, 'Variety', 230, 180),
       (3, 10, 'Kingdom', 10, 10),
       (3, 20, 'Subkingdom', 10, 10),
       (3, 25, 'Infrakingdom', 20, 10),
       (3, 27, 'Superdivision', 25, 10),
       (3, 30, 'Division', 27, 10),
       (3, 40, 'Subdivision', 30, 30),
       (3, 45, 'Infradivision', 40, 30),
       (3, 50, 'Superclass', 45, 30),
       (3, 60, 'Class', 50, 30),
       (3, 70, 'Subclass', 60, 60),
       (3, 80, 'Infraclass', 70, 60),
       (3, 90, 'Superorder', 80, 60),
       (3, 100, 'Order', 90, 60),
       (3, 110, 'Suborder', 100, 100),
       (3, 140, 'Family', 110, 100),
       (3, 150, 'Subfamily', 140, 140),
       (3, 160, 'Tribe', 150, 140),
       (3, 170, 'Subtribe', 160, 140),
       (3, 180, 'Genus', 170, 140),
       (3, 190, 'Subgenus', 180, 180),
       (3, 200, 'Section', 190, 180),
       (3, 210, 'Subsection', 200, 180),
       (3, 220, 'Species', 210, 180),
       (3, 230, 'Subspecies', 220, 180),
       (3, 240, 'Variety', 220, 180),
       (3, 250, 'Subvariety', 240, 180),
       (3, 260, 'Form', 220, 180),
       (3, 270, 'Subform', 260, 180),
       (3, 300, 'Cultivated', 260, 180),
       (4, 10, 'Kingdom', 10, 10),
       (4, 20, 'Subkingdom', 10, 10),
       (4, 30, 'Division', 20, 10),
       (4, 40, 'Subdivision', 30, 30),
       (4, 60, 'Class', 40, 30),
       (4, 70, 'Subclass', 60, 60),
       (4, 90, 'Superorder', 70, 60),
       (4, 100, 'Order', 90, 60),
       (4, 110, 'Suborder', 100, 100),
       (4, 140, 'Family', 110, 100),
       (4, 150, 'Subfamily', 140, 140),
       (4, 160, 'Tribe', 150, 140),
       (4, 170, 'Subtribe', 160, 140),
       (4, 180, 'Genus', 170, 140),
       (4, 190, 'Subgenus', 180, 180),
       (4, 200, 'Section', 190, 180),
       (4, 210, 'Subsection', 200, 180),
       (4, 220, 'Species', 210, 180),
       (4, 230, 'Subspecies', 220, 180),
       (4, 240, 'Variety', 220, 180),
       (4, 250, 'Subvariety', 240, 180),
       (4, 260, 'Form', 220, 180),
       (4, 270, 'Subform', 260, 180),
       (5, 10, 'Kingdom', 10, 10),
       (5, 20, 'Subkingdom', 10, 10),
       (5, 25, 'Infrakingdom', 20, 10),
       (5, 27, 'Superphylum', 25, 10),
       (5, 30, 'Phylum', 27, 10),
       (5, 40, 'Subphylum', 30, 30),
       (5, 45, 'Infraphylum', 40, 30),
       (5, 50, 'Superclass', 45, 30),
       (5, 60, 'Class', 50, 30),
       (5, 70, 'Subclass', 60, 60),
       (5, 80, 'Infraclass', 70, 60),
       (5, 90, 'Superorder', 80, 60),
       (5, 100, 'Order', 90, 60),
       (5, 110, 'Suborder', 100, 100),
       (5, 120, 'Infraorder', 110, 100),
       (5, 124, 'Section', 120, 100),
       (5, 126, 'Subsection', 124, 100),
       (5, 130, 'Superfamily', 126, 100),
       (5, 140, 'Family', 130, 100),
       (5, 150, 'Subfamily', 140, 140),
       (5, 160, 'Tribe', 150, 140),
       (5, 170, 'Subtribe', 160, 140),
       (5, 180, 'Genus', 170, 140),
       (5, 190, 'Subgenus', 180, 180),
       (5, 220, 'Species', 190, 180),
       (5, 230, 'Subspecies', 220, 220),
       (5, 240, 'Variety', 220, 220),
       (5, 245, 'Form', 220, 220),
       (5, 250, 'Race', 220, 220),
       (5, 255, 'Stirp', 220, 220),
       (5, 260, 'Morph', 220, 220),
       (5, 265, 'Aberration', 220, 220),
       (5, 300, 'Unspecified', 220, 220),
       (6, 10, 'Kingdom', 10, 10),
       (6, 20, 'Subkingdom', 10, 10),
       (6, 25, 'Infrakingdom', 20, 10),
       (6, 27, 'Superdivision', 25, 10),
       (6, 30, 'Division', 27, 10),
       (6, 40, 'Subdivision', 30, 30),
       (6, 45, 'Infradivision', 40, 30),
       (6, 47, 'Parvdivision', 45, 30),
       (6, 50, 'Superclass', 47, 30),
       (6, 60, 'Class', 50, 30),
       (6, 70, 'Subclass', 60, 60),
       (6, 80, 'Infraclass', 70, 60),
       (6, 90, 'Superorder', 80, 60),
       (6, 100, 'Order', 90, 60),
       (6, 110, 'Suborder', 100, 100),
       (6, 140, 'Family', 110, 100),
       (6, 150, 'Subfamily', 140, 140),
       (6, 160, 'Tribe', 150, 140),
       (6, 170, 'Subtribe', 160, 140),
       (6, 180, 'Genus', 170, 140),
       (6, 190, 'Subgenus', 180, 180),
       (6, 200, 'Section', 190, 180),
       (6, 210, 'Subsection', 200, 180),
       (6, 220, 'Species', 210, 180),
       (6, 230, 'Subspecies', 220, 180),
       (6, 240, 'Variety', 230, 180),
       (6, 250, 'Subvariety', 240, 180),
       (6, 260, 'Form', 250, 180),
       (6, 270, 'Subform', 260, 180),
       (7, 10, 'Kingdom', 10, 10),
       (7, 20, 'Subkingdom', 10, 10),
       (7, 30, 'Phylum', 20, 10),
       (7, 40, 'Subphylum', 30, 30),
       (7, 50, 'Superclass', 40, 30),
       (7, 60, 'Class', 50, 30),
       (7, 70, 'Subclass', 60, 60),
       (7, 80, 'Infraclass', 70, 60),
       (7, 90, 'Superorder', 80, 60),
       (7, 100, 'Order', 90, 60),
       (7, 110, 'Suborder', 100, 100),
       (7, 120, 'Infraorder', 110, 100),
       (7, 130, 'Superfamily', 120, 100),
       (7, 140, 'Family', 130, 100),
       (7, 150, 'Subfamily', 140, 140),
       (7, 160, 'Tribe', 150, 140),
       (7, 170, 'Subtribe', 160, 140),
       (7, 180, 'Genus', 170, 140),
       (7, 190, 'Subgenus', 180, 180),
       (7, 220, 'Species', 190, 180),
       (7, 230, 'Subspecies', 220, 180),
       (100, 10, 'Kingdom', 10, 10),
       (100, 20, 'Subkingdom', 10, 10),
       (100, 30, 'Phylum', 20, 10),
       (100, 40, 'Subphylum', 30, 30),
       (100, 50, 'Superclass', 40, 30),
       (100, 60, 'Class', 50, 30),
       (100, 70, 'Subclass', 60, 60),
       (100, 80, 'Infraclass', 70, 60),
       (100, 90, 'Superorder', 80, 60),
       (100, 100, 'Order', 90, 60),
       (100, 110, 'Suborder', 100, 100),
       (100, 120, 'Infraorder', 110, 100),
       (100, 130, 'Superfamily', 120, 100),
       (100, 140, 'Family', 130, 100),
       (100, 150, 'Subfamily', 140, 140),
       (100, 160, 'Tribe', 150, 140),
       (100, 170, 'Subtribe', 160, 140),
       (100, 180, 'Genus', 170, 140),
       (100, 190, 'Subgenus', 180, 180),
       (100, 220, 'Species', 190, 180),
       (100, 230, 'Subspecies', 220, 180);

SET
FOREIGN_KEY_CHECKS = 1;

ALTER TABLE `taxstatus`
    ADD COLUMN `modifiedBy` varchar(45) NULL AFTER `SortSequence`,
DROP INDEX `Index_hierarchy`,
  ADD INDEX `Index_tid`(`tid`);

ALTER TABLE `uploadimagetemp`
    CHANGE COLUMN `specimengui` `sourceIdentifier` varchar (150) NULL DEFAULT NULL AFTER `dbpk`,
    MODIFY COLUMN `url` varchar (255) NULL DEFAULT NULL AFTER `tid`,
    ADD COLUMN `sourceUrl` varchar (255) NULL AFTER `owner`,
    ADD COLUMN `referenceurl` varchar (255) NULL AFTER `sourceUrl`,
    ADD COLUMN `copyright` varchar (255) NULL AFTER `referenceurl`,
    ADD COLUMN `accessrights` varchar (255) NULL AFTER `copyright`,
    ADD COLUMN `rights` varchar (255) NULL AFTER `accessrights`,
    ADD COLUMN `locality` varchar (250) NULL AFTER `rights`;

ALTER TABLE `uploadspecparameters`
    MODIFY COLUMN `Path` varchar (500) NULL DEFAULT NULL AFTER `Code`,
    ADD COLUMN `existingrecords` varchar (45) NOT NULL DEFAULT "update" AFTER `cleanupsp`;

ALTER TABLE `uploadspectemp`
    ADD COLUMN `upspid` int(50) NOT NULL AUTO_INCREMENT FIRST,
    ADD PRIMARY KEY (`upspid`);

ALTER TABLE `uploadspectemp`
    CHANGE COLUMN `basisOfRecord` `basisOfRecord` VARCHAR (32) NULL DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
    ADD COLUMN `paleoJSON` text NULL AFTER `exsiccatiNotes`,
    ADD INDEX `Index_uploadspec_othercatalognumbers`(`otherCatalogNumbers`),
    ADD INDEX `Index_decimalLatitude`(`decimalLatitude`),
    ADD INDEX `Index_ decimalLongitude`(`decimalLongitude`),
    ADD INDEX `Index_ institutionCode`(`institutionCode`);

CREATE TABLE `uploadspectemppoints`
(
    `geoID`  int(11) NOT NULL AUTO_INCREMENT,
    `upspid` int(50) NOT NULL,
    `point`  point NOT NULL,
    PRIMARY KEY (`geoID`),
    UNIQUE KEY `upspid` (`upspid`),
    SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM;

CREATE TRIGGER `uploadspectemp_insert` AFTER INSERT ON `uploadspectemp` FOR EACH ROW BEGIN
    IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO uploadspectemppoints (`upspid`,`point`)
		VALUES (NEW.`upspid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
    END IF;
END;

CREATE TRIGGER `uploadspectemp_delete` BEFORE DELETE ON `uploadspectemp` FOR EACH ROW BEGIN
    DELETE FROM uploadspectemppoints WHERE `upspid` = OLD.`upspid`;
END;

ALTER TABLE `uploadtaxa`
DROP INDEX `UNIQUE_sciname` ,
    ADD COLUMN `kingdomId` int(11) NULL AFTER `Family`,
    ADD COLUMN `kingdomName` varchar(250) NULL AFTER `kingdomId`,
    ADD UNIQUE INDEX `UNIQUE_sciname` (`SciName` ASC, `RankId` ASC, `Author` ASC, `AcceptedStr` ASC),
    ADD INDEX `kingdomId_index`(`kingdomId`),
    ADD INDEX `kingdomName_index`(`kingdomName`),
    MODIFY COLUMN `UnitInd3` varchar(45) NULL DEFAULT NULL AFTER `UnitName2`;

ALTER TABLE `userroles`
    ADD UNIQUE INDEX `Unique_userroles`(`uid`, `role`, `tablename`, `tablepk`);

ALTER TABLE `users`
    ADD COLUMN `middleinitial` varchar(2) AFTER `firstname`;

UPDATE taxonkingdoms AS k LEFT JOIN taxa AS t
ON k.kingdom_name = t.SciName
    SET t.kingdomid = k.kingdom_id
WHERE t.TID IS NOT NULL;

UPDATE taxa AS t LEFT JOIN taxonkingdoms AS k
ON t.kingdomName = k.kingdom_name
    SET t.kingdomid = k.kingdom_id
WHERE (t.kingdomid = 100) AND t.kingdomName IS NOT NULL AND k.kingdom_id IS NOT NULL;

UPDATE taxa AS t LEFT JOIN taxaenumtree AS e
ON t.TID = e.tid
    LEFT JOIN taxa AS t2 ON e.parenttid = t2.TID
    LEFT JOIN taxonkingdoms AS k ON t2.SciName = k.kingdom_name
    SET t.kingdomid = k.kingdom_id
WHERE t2.RankId = 10 AND e.taxauthid = 1 AND (t.kingdomid = 100);

ALTER TABLE `users`
    ADD COLUMN `username` varchar(45) NOT NULL AFTER `lastname`,
  ADD COLUMN `password` varchar(255) NOT NULL AFTER `username`,
  ADD COLUMN `lastlogindate` datetime AFTER `usergroups`;

UPDATE users AS u LEFT JOIN userlogin AS ul
ON u.uid = ul.uid
    SET u.username = ul.username,
        u.`password` = ul.`password`,
        u.lastlogindate = ul.lastlogindate;

DROP TABLE IF EXISTS `userlogin`;

DELETE te.* FROM taxaenumtree AS te LEFT JOIN taxauthority AS ta ON te.taxauthid = ta.taxauthid
WHERE ta.isprimary <> 1;

ALTER TABLE `taxaenumtree` DROP FOREIGN KEY `FK_tet_taxauth`;

ALTER TABLE `taxaenumtree`
    DROP COLUMN `taxauthid`,
    DROP INDEX `FK_tet_taxauth`;

DELETE ts.* FROM taxstatus AS ts LEFT JOIN taxauthority AS ta ON ts.taxauthid = ta.taxauthid
WHERE ta.isprimary <> 1;

ALTER TABLE `taxstatus` DROP FOREIGN KEY `FK_taxstatus_taid`;

ALTER TABLE `taxstatus`
    DROP COLUMN `taxauthid`,
    DROP INDEX `FK_taxstatus_taid`;

ALTER TABLE `configurations`
    ADD UNIQUE INDEX `configurationname`(`configurationname`);
