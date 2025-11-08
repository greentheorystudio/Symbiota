SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `configurations`
    MODIFY COLUMN `configurationValue` longtext NOT NULL AFTER `configurationDataType`;

ALTER TABLE `omcollections`
    ADD COLUMN `ccpk` int(10) UNSIGNED NULL AFTER `CollID`,
    ADD COLUMN `isPublic` smallint(1) NOT NULL DEFAULT 1 AFTER `SortSeq`,
    ADD COLUMN `defaultRepCount` int(10) NULL AFTER `DataRecordingMethod`,
    ADD COLUMN `dwcaPublishTimestamp` timestamp NULL AFTER `dwcaUrl`,
    CHANGE COLUMN `dynamicProperties` `configJson` longtext NULL AFTER `accessrights`,
    ADD INDEX `isPublic`(`isPublic`),
    ADD CONSTRAINT `FK_collid_ccpk` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE RESTRICT ON UPDATE NO ACTION;

CREATE TABLE `omcolldatauploadparameters` (
    `uspid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `CollID` int(10) unsigned NOT NULL,
    `UploadType` int(10) unsigned NOT NULL DEFAULT '1',
    `title` varchar(45) NOT NULL,
    `dwcpath` text,
    `queryparamjson` text,
    `cleansql` text,
    `configjson` text,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`uspid`),
    KEY `FK_omcolldatauploadparameters_coll` (`CollID`),
    CONSTRAINT `omcolldatauploadparameters_ibfk_1` FOREIGN KEY (`CollID`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO omcolldatauploadparameters(uspid,CollID,UploadType,title,dwcpath)
SELECT uspid,CollID,UploadType,title,Path
FROM uploadspecparameters;

CREATE TABLE `omcollmediauploadparameters` (
    `spprid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `title` varchar(100) NOT NULL,
    `filenamepatternmatch` varchar(500) DEFAULT NULL,
    `patternmatchfield` varchar(255) DEFAULT NULL,
    `configjson` text,
    `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`spprid`),
    KEY `FK_omcollmediauploadparameters_coll` (`collid`),
    CONSTRAINT `omcollmediauploadparameters_ibfk_1` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

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
    KEY `locality`(`locality`(100)),
    KEY `FK_collid` (`collid`),
    CONSTRAINT `FK_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_eventID` FOREIGN KEY (`eventID`) REFERENCES `omoccurcollectingevents` (`eventID`) ON DELETE RESTRICT ON UPDATE NO ACTION;

CREATE TABLE `ommofextension` (
    `mofID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `eventID` int(10) unsigned DEFAULT NULL,
    `occId` int(10) unsigned DEFAULT NULL,
    `field` varchar(250) NOT NULL,
    `datavalue` varchar(1000) DEFAULT NULL,
    `enteredBy` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mofID`),
    UNIQUE KEY `INDEX_UNIQUE_event_field` (`eventID`,`field`),
    UNIQUE KEY `INDEX_UNIQUE_OCCID` (`occId`,`field`),
    KEY `field` (`field`),
    KEY `datavalue` (`datavalue`),
    KEY `FK_eventID` (`eventID`),
    CONSTRAINT `FK_event` FOREIGN KEY (`eventID`) REFERENCES `omoccurcollectingevents` (`eventID`),
    CONSTRAINT `FK_ommofextension_occid` FOREIGN KEY (`occId`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE NO ACTION
);

ALTER TABLE `media`
    MODIFY COLUMN `accessuri` varchar(255) NOT NULL AFTER `occid`,
    ADD COLUMN `sourceurl` varchar(255) NULL AFTER `accessuri`,
    ADD COLUMN `descriptivetranscripturi` varchar(255) NULL AFTER `sourceurl`,
    ADD INDEX `sourceurl`(`sourceurl`),
    ADD INDEX `INDEX_media_descriptivetranscripturi`(`descriptivetranscripturi`);

ALTER TABLE `images`
    ADD COLUMN `altText` varchar(355) NULL AFTER `caption`,
    ADD INDEX `sourceurl`(`sourceurl`),
    ADD INDEX `images_sortsequence`(`sortsequence`),
    ADD INDEX `INDEX_images_altText`(`altText`);

ALTER TABLE `imagetag`
    DROP FOREIGN KEY `FK_imagetag_tagkey`;

ALTER TABLE `fmchecklists`
    CHANGE COLUMN `dynamicsql` `searchterms` text NULL AFTER `politicalDivision`,
    MODIFY COLUMN `expiration` datetime NULL DEFAULT NULL AFTER `SortSequence`,
    MODIFY COLUMN `SortSequence` int(10) UNSIGNED NULL DEFAULT 50 AFTER `uid`,
    ADD INDEX `fmchecklists_parentclid`(`parentclid`);

ALTER TABLE `fmchklsttaxalink`
    ADD COLUMN `cltlid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`cltlid`),
    MODIFY COLUMN `morphospecies` varchar(45) NULL DEFAULT NULL AFTER `CLID`;

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

ALTER TABLE `uploaddetermtemp`
    ADD COLUMN `updid` int(50) NOT NULL AUTO_INCREMENT FIRST,
    ADD COLUMN `tid` int(10) UNSIGNED NULL AFTER `sciname`,
    ADD PRIMARY KEY (`updid`);

CREATE TABLE `uploadmediatemp` (
    `upmid` int(50) NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned DEFAULT NULL,
    `url` varchar(255) DEFAULT NULL,
    `thumbnailurl` varchar(255) DEFAULT NULL,
    `originalurl` varchar(255) DEFAULT NULL,
    `accessuri` varchar(255) DEFAULT NULL,
    `descriptivetranscripturi` varchar(255) DEFAULT NULL,
    `photographer` varchar(100) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `imagetype` varchar(50) DEFAULT NULL,
    `format` varchar(45) DEFAULT NULL,
    `caption` varchar(100) DEFAULT NULL,
    `altText` varchar(355) DEFAULT NULL,
    `description` varchar(1000) DEFAULT NULL,
    `creator` varchar(45) DEFAULT NULL,
    `owner` varchar(100) DEFAULT NULL,
    `type` varchar(45) DEFAULT NULL,
    `sourceUrl` varchar(255) DEFAULT NULL,
    `furtherinformationurl` varchar(2048) DEFAULT NULL,
    `referenceurl` varchar(255) DEFAULT NULL,
    `language` varchar(45) DEFAULT NULL,
    `copyright` varchar(255) DEFAULT NULL,
    `accessrights` varchar(255) DEFAULT NULL,
    `usageterms` varchar(255) DEFAULT NULL,
    `rights` varchar(255) DEFAULT NULL,
    `locality` varchar(250) DEFAULT NULL,
    `locationcreated` varchar(1000) DEFAULT NULL,
    `bibliographiccitation` varchar(255) DEFAULT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `collid` int(10) unsigned DEFAULT NULL,
    `dbpk` varchar(150) DEFAULT NULL,
    `publisher` varchar(255) DEFAULT NULL,
    `contributor` varchar(255) DEFAULT NULL,
    `sourceIdentifier` varchar(150) DEFAULT NULL,
    `notes` varchar(350) DEFAULT NULL,
    `anatomy` varchar(100) DEFAULT NULL,
    `username` varchar(45) DEFAULT NULL,
    `sortsequence` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`upmid`),
    KEY `Index_uploadimg_occid` (`occid`),
    KEY `Index_uploadimg_collid` (`collid`),
    KEY `Index_uploadimg_dbpk` (`dbpk`),
    KEY `Index_url` (`url`),
    KEY `Index_originalurl` (`originalurl`),
    KEY `Index_accessuri` (`accessuri`),
    KEY `Index_format` (`format`),
    KEY `Index_uploadimg_ts` (`initialtimestamp`)
);

CREATE TABLE `uploadmoftemp` (
    `upmfid` int(50) NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned DEFAULT NULL,
    `dbpk` varchar(150) DEFAULT NULL,
    `eventdbpk` varchar(150) DEFAULT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `eventID` int(10) unsigned DEFAULT NULL,
    `field` varchar(250) DEFAULT NULL,
    `datavalue` varchar(1000) DEFAULT NULL,
    `enteredBy` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`upmfid`) USING BTREE,
    KEY `Index_uploaddet_occid` (`occid`),
    KEY `Index_collid` (`collid`),
    KEY `Index_uploaddet_dbpk` (`dbpk`),
    KEY `Index_eventdbpk` (`eventdbpk`),
    KEY `Index_eventID` (`eventID`),
    KEY `Index_field` (`field`),
    KEY `Index_datavalue` (`datavalue`)
);

ALTER TABLE `uploadspecmap` DROP FOREIGN KEY `FK_uploadspecmap_usp`;

ALTER TABLE `uploadspecmap`
    ADD CONSTRAINT `Fk_uploadspecmap_uspid` FOREIGN KEY (`uspid`) REFERENCES `omcolldatauploadparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `uploadspectemp`
    DROP COLUMN `recordNumberPrefix`,
    DROP COLUMN `recordNumberSuffix`,
    DROP COLUMN `CollectorFamilyName`,
    DROP COLUMN `CollectorInitials`,
    DROP COLUMN `host`,
    DROP COLUMN `associatedOccurrences`,
    DROP COLUMN `associatedMedia`,
    DROP COLUMN `associatedReferences`,
    DROP COLUMN `associatedSequences`,
    DROP COLUMN `elevationNumber`,
    DROP COLUMN `elevationUnits`,
    DROP COLUMN `previousIdentifications`,
    DROP COLUMN `genericcolumn1`,
    DROP COLUMN `genericcolumn2`,
    DROP COLUMN `paleoJSON`,
    DROP COLUMN `modified`,
    DROP COLUMN `recordEnteredBy`,
    MODIFY COLUMN `occid` int(10) UNSIGNED NULL DEFAULT NULL AFTER `upspid`,
    ADD COLUMN `eventTime` varchar(12) NULL AFTER `eventDate`,
    ADD COLUMN `eventID` int(11) UNSIGNED NULL AFTER `fieldnumber`,
    ADD COLUMN `eventdbpk` varchar(150) NULL AFTER `eventID`,
    ADD COLUMN `eventType` varchar(255) NULL AFTER `eventdbpk`,
    ADD COLUMN `eventRemarks` text NULL AFTER `eventType`,
    ADD COLUMN `rep` int(10) NULL AFTER `samplingEffort`,
    ADD COLUMN `locationID` int(11) UNSIGNED NULL AFTER `preparations`,
    ADD COLUMN `island` varchar(75) NULL AFTER `locationID`,
    ADD COLUMN `islandGroup` varchar(75) NULL AFTER `island`,
    ADD COLUMN `waterBody` varchar(255) NULL AFTER `islandGroup`,
    ADD COLUMN `continent` varchar(45) NULL AFTER `waterBody`,
    ADD COLUMN `locationName` varchar(255) NULL AFTER `continent`,
    ADD COLUMN `locationCode` varchar(50) NULL AFTER `locationName`,
    ADD COLUMN `repCount` int(10) UNSIGNED NULL AFTER `duplicateQuantity`,
    ADD INDEX `Index_eventdbpk`(`eventdbpk`);

ALTER TABLE `fmchecklists`
    MODIFY COLUMN `searchterms` longtext NULL AFTER `politicalDivision`,
    MODIFY COLUMN `footprintWKT` longtext NULL AFTER `pointradiusmeters`,
    MODIFY COLUMN `defaultSettings` longtext NULL AFTER `Access`;

ALTER TABLE `fmprojects`
    MODIFY COLUMN `dynamicProperties` longtext NULL AFTER `ispublic`;

ALTER TABLE `omcolldatauploadparameters`
    MODIFY COLUMN `queryparamjson` longtext NULL AFTER `dwcpath`,
    MODIFY COLUMN `cleansql` longtext NULL AFTER `queryparamjson`,
    MODIFY COLUMN `configjson` longtext NULL AFTER `cleansql`;

ALTER TABLE `omcollmediauploadparameters`
    MODIFY COLUMN `configjson` longtext NULL AFTER `patternmatchfield`;

ALTER TABLE `taxamaps`
    ADD COLUMN `altText` varchar(355) NULL AFTER `title`;

ALTER TABLE `users`
    DROP COLUMN `department`,
    DROP COLUMN `address`,
    DROP COLUMN `city`,
    DROP COLUMN `state`,
    DROP COLUMN `zip`,
    DROP COLUMN `country`,
    DROP COLUMN `phone`,
    DROP COLUMN `RegionOfInterest`,
    DROP COLUMN `url`,
    DROP COLUMN `Biography`,
    DROP COLUMN `notes`,
    DROP COLUMN `ispublic`,
    DROP COLUMN `defaultrights`,
    DROP COLUMN `rightsholder`,
    DROP COLUMN `rights`,
    DROP COLUMN `accessrights`,
    DROP COLUMN `usergroups`;

SET FOREIGN_KEY_CHECKS = 1;
