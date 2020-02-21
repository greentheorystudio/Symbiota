INSERT IGNORE INTO schemaversion (versionnumber) values ("1.2");

ALTER TABLE `images`
  ADD INDEX `Index_images_datelastmod` (`InitialTimeStamp` ASC);

ALTER TABLE `kmcharacters`
  ADD COLUMN `display` varchar(45) AFTER `notes`;

UPDATE omcrowdsourcecentral c INNER JOIN omcrowdsourcequeue q ON c.omcsid = q.omcsid
  INNER JOIN userroles r ON c.collid = r.tablepk AND q.uidprocessor = r.uid
  SET q.isvolunteer = 0
  WHERE r.role IN("CollAdmin","CollEditor") AND q.isvolunteer = 1;

ALTER TABLE `omoccuredits`
    ADD COLUMN `editType` INT NULL DEFAULT 0 COMMENT '0 = general edit, 1 = batch edit' AFTER `AppliedStatus`;

UPDATE omoccuredits e INNER JOIN (SELECT initialtimestamp, uid, count(DISTINCT occid) as cnt
    FROM omoccuredits
    GROUP BY initialtimestamp, uid
    HAVING cnt > 2) as inntab ON e.initialtimestamp = inntab.initialtimestamp AND e.uid = inntab.uid
    SET edittype = 1;

ALTER TABLE `omoccurrences`
  CHANGE COLUMN `labelProject` `labelProject` varchar(250) DEFAULT NULL,
  DROP INDEX `idx_occrecordedby`;

REPLACE omoccurrencesfulltext(occid,locality,recordedby)
  SELECT occid, CONCAT_WS("; ", municipality, locality), recordedby
  FROM omoccurrences;

CREATE TABLE `taxonkingdoms` (
    `kingdom_id` int(11) NOT NULL,
    `kingdom_name` varchar(250) NOT NULL,
    PRIMARY KEY (`kingdom_id`),
    INDEX `INDEX_kingdom_name` (`kingdom_name` ASC),
    KEY `INDEX_kingdoms` (`kingdom_id`,`kingdom_name`)
);

INSERT INTO `taxonkingdoms` VALUES (1, 'Bacteria');
INSERT INTO `taxonkingdoms` VALUES (2, 'Protozoa');
INSERT INTO `taxonkingdoms` VALUES (3, 'Plantae');
INSERT INTO `taxonkingdoms` VALUES (4, 'Fungi');
INSERT INTO `taxonkingdoms` VALUES (5, 'Animalia');
INSERT INTO `taxonkingdoms` VALUES (6, 'Chromista');
INSERT INTO `taxonkingdoms` VALUES (7, 'Archaea');
INSERT INTO `taxonkingdoms` VALUES (100, 'Unknown');

ALTER TABLE `taxa`
    ADD COLUMN `kingdomId` int(11) NULL DEFAULT 100 AFTER `kingdomName`,
    ADD INDEX `kingdomid_index`(`kingdomId`) USING BTREE,
    ADD CONSTRAINT `FK_kingdom_id` FOREIGN KEY (`kingdomId`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON DELETE SET NULL ON UPDATE SET NULL;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `taxonunits`;

ALTER TABLE `taxonunits`
    ADD COLUMN `kingdomid` int(11) NOT NULL AFTER `taxonunitid`,
    ADD UNIQUE INDEX `INDEX-Unique`(`kingdomid`, `rankid`) USING BTREE,
    ADD CONSTRAINT `FK-kingdomid` FOREIGN KEY (`kingdomid`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON UPDATE CASCADE ON DELETE CASCADE;

INSERT INTO `taxonunits`(`kingdomid`, `rankid`, `rankname`, `dirparentrankid`, `reqparentrankid`) VALUES
    (1, 10, 'Kingdom', 10, 10),
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

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE `uploadtaxa`
    DROP INDEX `UNIQUE_sciname` ,
    ADD COLUMN `kingdomId` int(11) NULL AFTER `Family`,
    ADD COLUMN `kingdomName` varchar(250) NULL AFTER `kingdomId`,
    ADD UNIQUE INDEX `UNIQUE_sciname` (`SciName` ASC, `RankId` ASC, `Author` ASC, `AcceptedStr` ASC),
    ADD INDEX `kingdomId_index`(`kingdomId`) USING BTREE,
    ADD INDEX `kingdomName_index`(`kingdomName`) USING BTREE;

ALTER TABLE `uploadspectemp`
  CHANGE COLUMN `basisOfRecord` `basisOfRecord` VARCHAR(32) NULL DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation' ;

ALTER TABLE `users` ADD COLUMN `middleinitial` varchar(2) AFTER `firstname`;

UPDATE taxonkingdoms AS k LEFT JOIN taxa AS t ON k.kingdom_name = t.SciName
    SET t.kingdomid = k.kingdom_id
    WHERE t.TID IS NOT NULL;

UPDATE taxa AS t LEFT JOIN taxonkingdoms AS k ON t.kingdomName = k.kingdom_name
    SET t.kingdomid = k.kingdom_id
    WHERE (t.kingdomid = 100) AND t.kingdomName IS NOT NULL AND k.kingdom_id IS NOT NULL;

UPDATE taxa AS t LEFT JOIN taxaenumtree AS e ON t.TID = e.tid
    LEFT JOIN taxa AS t2 ON e.parenttid = t2.TID
    LEFT JOIN taxonkingdoms AS k ON t2.SciName = k.kingdom_name
    SET t.kingdomid = k.kingdom_id
    WHERE t2.RankId = 10 AND e.taxauthid = 1 AND (t.kingdomid = 100);
