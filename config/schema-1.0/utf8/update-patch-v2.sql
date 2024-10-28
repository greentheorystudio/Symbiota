SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `omcollections`
    ADD COLUMN `ccpk` int(10) UNSIGNED NULL AFTER `CollID`,
    ADD COLUMN `isPublic` smallint(1) NOT NULL DEFAULT 1 AFTER `SortSeq`,
    ADD COLUMN `defaultRepCount` int(10) NULL AFTER `DataRecordingMethod`,
    ADD INDEX `isPublic`(`isPublic`),
    ADD CONSTRAINT `FK_collid_ccpk` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE RESTRICT ON UPDATE NO ACTION;

ALTER TABLE `omoccurrences`
    MODIFY COLUMN `eventID` int(11) UNSIGNED NULL DEFAULT NULL AFTER `fieldnumber`,
    MODIFY COLUMN `locationID` int(11) UNSIGNED NULL DEFAULT NULL AFTER `preparations`,
    DROP INDEX `Index_eventID`,
    ADD COLUMN `rep` int(10) NULL AFTER `samplingEffort`,
    ADD INDEX `rep`(`rep`),
    ADD COLUMN `eventTime` varchar(12) DEFAULT NULL AFTER `latestDateCollected`,
    ADD COLUMN `eventRemarks` text NULL AFTER `eventID`,
    ADD COLUMN `island` varchar(75) NULL DEFAULT NULL AFTER `locationID`,
    ADD COLUMN `islandGroup` varchar(75) NULL DEFAULT NULL AFTER `island`,
    ADD COLUMN `continent` varchar(45) NULL DEFAULT NULL AFTER `waterBody`,
    MODIFY COLUMN `minimumDepthInMeters` double NULL DEFAULT NULL AFTER `verbatimElevation`,
    MODIFY COLUMN `maximumDepthInMeters` double NULL DEFAULT NULL AFTER `minimumDepthInMeters`;

ALTER TABLE `omoccurdeterminations`
    MODIFY COLUMN `identifiedBy` varchar(60) NULL AFTER `occid`,
    MODIFY COLUMN `dateIdentified` varchar(45) NULL AFTER `idbyid`;

CREATE TABLE `omoccurcollectingevents` (
    `eventID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned DEFAULT NULL,
    `locationID` int(11) unsigned NOT NULL,
    `eventType` varchar(255) DEFAULT NULL,
    `fieldNotes` text,
    `fieldnumber` varchar(45) DEFAULT NULL,
    `recordedBy` varchar(255) DEFAULT NULL,
    `recordNumber` varchar(45) DEFAULT NULL,
    `recordedbyid` bigint(20) DEFAULT NULL,
    `associatedCollectors` varchar(255) DEFAULT NULL,
    `eventDate` date DEFAULT NULL,
    `latestDateCollected` date DEFAULT NULL,
    `eventTime` varchar(12) DEFAULT NULL,
    `year` int(10) DEFAULT NULL,
    `month` int(10) DEFAULT NULL,
    `day` int(10) DEFAULT NULL,
    `startDayOfYear` int(10) DEFAULT NULL,
    `endDayOfYear` int(10) DEFAULT NULL,
    `verbatimEventDate` varchar(255) DEFAULT NULL,
    `habitat` text,
    `substrate` varchar(500) DEFAULT NULL,
    `localitySecurity` int(10) DEFAULT NULL,
    `localitySecurityReason` varchar(100) DEFAULT NULL,
    `decimalLatitude` double DEFAULT NULL,
    `decimalLongitude` double DEFAULT NULL,
    `geodeticDatum` varchar(255) DEFAULT NULL,
    `coordinateUncertaintyInMeters` int(10) DEFAULT NULL,
    `footprintWKT` text,
    `eventRemarks` text,
    `georeferencedBy` varchar(255) DEFAULT NULL,
    `georeferenceProtocol` varchar(255) DEFAULT NULL,
    `georeferenceSources` varchar(255) DEFAULT NULL,
    `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
    `georeferenceRemarks` varchar(500) DEFAULT NULL,
    `minimumDepthInMeters` double DEFAULT NULL,
    `maximumDepthInMeters` double DEFAULT NULL,
    `verbatimDepth` varchar(50) DEFAULT NULL,
    `samplingProtocol` varchar(100) DEFAULT NULL,
    `samplingEffort` varchar(200) DEFAULT NULL,
    `repCount` int(10) DEFAULT NULL,
    `labelProject` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`eventID`),
    KEY `locationID` (`locationID`),
    KEY `eventType` (`eventType`),
    KEY `eventDate` (`eventDate`),
    KEY `eventTime` (`eventTime`),
    KEY `localitySecurity` (`localitySecurity`),
    KEY `decimalLatitude` (`decimalLatitude`),
    KEY `decimalLongitude` (`decimalLongitude`),
    KEY `FK_eventcollid` (`collid`),
    CONSTRAINT `FK_eventcollid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurlocations` (
    `locationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `locationName` varchar(255) DEFAULT NULL,
    `locationCode` varchar(50) DEFAULT NULL,
    `island` varchar(75) DEFAULT NULL,
    `islandGroup` varchar(75) DEFAULT NULL,
    `waterBody` varchar(255) DEFAULT NULL,
    `continent` varchar(45) DEFAULT NULL,
    `country` varchar(64) DEFAULT NULL,
    `stateProvince` varchar(255) DEFAULT NULL,
    `county` varchar(255) DEFAULT NULL,
    `municipality` varchar(255) DEFAULT NULL,
    `locality` text,
    `localitySecurity` int(10) DEFAULT NULL,
    `localitySecurityReason` varchar(100) DEFAULT NULL,
    `decimalLatitude` double DEFAULT NULL,
    `decimalLongitude` double DEFAULT NULL,
    `geodeticDatum` varchar(255) DEFAULT NULL,
    `coordinateUncertaintyInMeters` int(10) DEFAULT NULL,
    `footprintWKT` text,
    `coordinatePrecision` decimal(9,0) DEFAULT NULL,
    `locationRemarks` text,
    `verbatimCoordinates` varchar(255) DEFAULT NULL,
    `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
    `georeferencedBy` varchar(255) DEFAULT NULL,
    `georeferenceProtocol` varchar(255) DEFAULT NULL,
    `georeferenceSources` varchar(255) DEFAULT NULL,
    `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
    `georeferenceRemarks` varchar(500) DEFAULT NULL,
    `minimumElevationInMeters` int(6) DEFAULT NULL,
    `maximumElevationInMeters` int(6) DEFAULT NULL,
    `verbatimElevation` varchar(255) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`locationID`),
    KEY `locationName` (`locationName`),
    KEY `locationCode` (`locationCode`),
    KEY `waterBody` (`waterBody`),
    KEY `country` (`country`),
    KEY `stateProvince` (`stateProvince`),
    KEY `county` (`county`),
    KEY `localitySecurity` (`localitySecurity`),
    KEY `decimalLatitude` (`decimalLatitude`),
    KEY `decimalLongitude` (`decimalLongitude`),
    KEY `FK_collid` (`collid`),
    CONSTRAINT `FK_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_eventID` FOREIGN KEY (`eventID`) REFERENCES `omoccurcollectingevents` (`eventID`) ON DELETE RESTRICT ON UPDATE NO ACTION;

CREATE TABLE `omoccuradditionaldata` (
     `adddataID` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `eventID` int(11) unsigned NOT NULL,
     `field` varchar(250) NOT NULL,
     `datavalue` varchar(1000) DEFAULT NULL,
     `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (`adddataID`),
     UNIQUE KEY `INDEX_UNIQUE_event_field` (`eventID`,`field`),
     KEY `field` (`field`),
     KEY `datavalue` (`datavalue`),
     KEY `FK_event` (`eventID`),
     CONSTRAINT `FK_event` FOREIGN KEY (`eventID`) REFERENCES `omoccurcollectingevents` (`eventID`) ON UPDATE NO ACTION
);

ALTER TABLE `media`
    ADD COLUMN `sourceurl` varchar(255) NULL AFTER `accessuri`,
    ADD INDEX `sourceurl`(`sourceurl`);

ALTER TABLE `images`
    ADD INDEX `sourceurl`(`sourceurl`);

CREATE TABLE `keycharacterheadings` (
    `chid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `headingname` varchar(255) NOT NULL,
    `language` varchar(45) NOT NULL DEFAULT 'English',
    `langid` int(11) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`chid`),
    KEY `headingname` (`headingname`),
    KEY `FK_kmcharheading_lang_idx` (`langid`),
    KEY `language` (`language`),
    CONSTRAINT `keycharacterheadings_ibfk_1` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
);

CREATE TABLE `keycharacters` (
    `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `chid` int(10) unsigned NOT NULL,
    `charactername` varchar(150) NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `infourl` varchar(500) DEFAULT NULL,
    `language` varchar(45) NOT NULL DEFAULT 'English',
    `langid` int(11) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cid`),
    KEY `charactername` (`charactername`),
    KEY `language` (`language`),
    KEY `chid` (`chid`),
    KEY `langid` (`langid`),
    CONSTRAINT `chid` FOREIGN KEY (`chid`) REFERENCES `keycharacterheadings` (`chid`) ON UPDATE CASCADE,
    CONSTRAINT `langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
);

CREATE TABLE `keycharacterstates` (
    `csid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `cid` int(10) unsigned NOT NULL,
    `characterstatename` varchar(255) NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `infourl` varchar(500) DEFAULT NULL,
    `language` varchar(45) NOT NULL DEFAULT 'English',
    `langid` int(11) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`csid`),
    KEY `cid` (`cid`),
    KEY `characterstatename` (`characterstatename`),
    KEY `language` (`language`),
    KEY `kcs_langid` (`langid`),
    CONSTRAINT `cid` FOREIGN KEY (`cid`) REFERENCES `keycharacters` (`cid`) ON UPDATE CASCADE,
    CONSTRAINT `kcs_langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
);

CREATE TABLE `keycharacterdependence` (
    `cdid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `cid` int(10) unsigned NOT NULL,
    `dcid` int(10) unsigned NOT NULL,
    `dcsid` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`cdid`),
    KEY `kcd_cid` (`cid`),
    KEY `kcd_dcid` (`dcid`),
    KEY `kcd_dcsid` (`dcsid`),
    CONSTRAINT `kcd_cid` FOREIGN KEY (`cid`) REFERENCES `keycharacters` (`cid`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `kcd_dcid` FOREIGN KEY (`dcid`) REFERENCES `keycharacters` (`cid`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `kcd_dcsid` FOREIGN KEY (`dcsid`) REFERENCES `keycharacterstates` (`csid`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE `keycharacterstatetaxalink` (
    `cstlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `cid` int(10) unsigned NOT NULL,
    `csid` int(10) unsigned NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cstlid`),
    KEY `kcstl_cid` (`cid`),
    KEY `kcstl_csid` (`csid`),
    KEY `kcstl_tid` (`tid`),
    CONSTRAINT `kcstl_cid` FOREIGN KEY (`cid`) REFERENCES `keycharacters` (`cid`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `kcstl_csid` FOREIGN KEY (`csid`) REFERENCES `keycharacterstates` (`csid`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `kcstl_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE NO ACTION
);

SET FOREIGN_KEY_CHECKS = 1;
