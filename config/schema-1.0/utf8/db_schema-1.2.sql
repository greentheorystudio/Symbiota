SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure
-- ----------------------------

CREATE TABLE `adminlanguages` (
    `langid` int(11) NOT NULL AUTO_INCREMENT,
    `langname` varchar(45) NOT NULL,
    `iso639_1` varchar(10) DEFAULT NULL,
    `iso639_2` varchar(10) DEFAULT NULL,
    PRIMARY KEY (`langid`),
    UNIQUE KEY `index_langname_unique` (`langname`),
    KEY `iso639_1` (`iso639_1`),
    KEY `iso639_2` (`iso639_2`)
);

CREATE TABLE `configurations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `configurationName` varchar(100) NOT NULL,
    `configurationDataType` varchar(15) NOT NULL DEFAULT 'string',
    `configurationValue` text NOT NULL,
    `dateApplied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `configurationname` (`configurationname`)
);

CREATE TABLE `fmchecklists` (
    `CLID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `Name` varchar(100) NOT NULL,
    `Title` varchar(150) DEFAULT NULL,
    `Locality` varchar(500) DEFAULT NULL,
    `Publication` varchar(500) DEFAULT NULL,
    `Abstract` text,
    `Authors` varchar(250) DEFAULT NULL,
    `Type` varchar(50) DEFAULT 'static',
    `politicalDivision` varchar(45) DEFAULT NULL,
    `dynamicsql` varchar(500) DEFAULT NULL,
    `Parent` varchar(50) DEFAULT NULL,
    `parentclid` int(10) unsigned DEFAULT NULL,
    `Notes` varchar(500) DEFAULT NULL,
    `LatCentroid` double(9,6) DEFAULT NULL,
    `LongCentroid` double(9,6) DEFAULT NULL,
    `pointradiusmeters` int(10) unsigned DEFAULT NULL,
    `footprintWKT` text,
    `percenteffort` int(11) DEFAULT NULL,
    `Access` varchar(45) DEFAULT 'private',
    `defaultSettings` varchar(250) DEFAULT NULL,
    `iconUrl` varchar(150) DEFAULT NULL,
    `headerUrl` varchar(150) DEFAULT NULL,
    `uid` int(10) unsigned DEFAULT NULL,
    `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
    `expiration` int(10) unsigned DEFAULT NULL,
    `DateLastModified` datetime DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`CLID`),
    KEY `FK_checklists_uid` (`uid`),
    KEY `name` (`Name`,`Type`),
    CONSTRAINT `FK_checklists_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
);

CREATE TABLE `fmchklstchildren` (
    `clid` int(10) unsigned NOT NULL,
    `clidchild` int(10) unsigned NOT NULL,
    `modifiedUid` int(10) unsigned NOT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`clid`,`clidchild`),
    KEY `FK_fmchklstchild_clid_idx` (`clid`),
    KEY `FK_fmchklstchild_child_idx` (`clidchild`),
    CONSTRAINT `FK_fmchklstchild_child` FOREIGN KEY (`clidchild`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_fmchklstchild_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `fmchklstprojlink` (
    `pid` int(10) unsigned NOT NULL,
    `clid` int(10) unsigned NOT NULL,
    `clNameOverride` varchar(100) DEFAULT NULL,
    `mapChecklist` smallint(6) DEFAULT '1',
    `sortSequence` int(11) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`pid`,`clid`),
    KEY `FK_chklst` (`clid`),
    CONSTRAINT `FK_chklstprojlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`),
    CONSTRAINT `FK_chklstprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`)
);

CREATE TABLE `fmchklsttaxalink` (
    `TID` int(10) unsigned NOT NULL DEFAULT '0',
    `CLID` int(10) unsigned NOT NULL DEFAULT '0',
    `morphospecies` varchar(45) NOT NULL DEFAULT '',
    `familyoverride` varchar(50) DEFAULT NULL,
    `Habitat` varchar(250) DEFAULT NULL,
    `Abundance` varchar(50) DEFAULT NULL,
    `Notes` varchar(2000) DEFAULT NULL,
    `explicitExclude` smallint(6) DEFAULT NULL,
    `source` varchar(250) DEFAULT NULL,
    `Nativity` varchar(50) DEFAULT NULL COMMENT 'native, introducted',
    `Endemic` varchar(45) DEFAULT NULL,
    `invasive` varchar(45) DEFAULT NULL,
    `internalnotes` varchar(250) DEFAULT NULL,
    `dynamicProperties` text,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`TID`,`CLID`,`morphospecies`),
    KEY `FK_chklsttaxalink_cid` (`CLID`),
    KEY `FK_chklsttaxalink_tid` (`TID`)
);

CREATE TABLE `fmdynamicchecklists` (
    `dynclid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) DEFAULT NULL,
    `details` varchar(250) DEFAULT NULL,
    `uid` varchar(45) DEFAULT NULL,
    `type` varchar(45) NOT NULL DEFAULT 'DynamicList',
    `notes` varchar(250) DEFAULT NULL,
    `expiration` datetime NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`dynclid`)
);

CREATE TABLE `fmdyncltaxalink` (
    `dynclid` int(10) unsigned NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`dynclid`,`tid`),
    KEY `FK_dyncltaxalink_taxa` (`tid`),
    CONSTRAINT `FK_dyncltaxalink_dynclid` FOREIGN KEY (`dynclid`) REFERENCES `fmdynamicchecklists` (`dynclid`) ON DELETE CASCADE,
    CONSTRAINT `FK_dyncltaxalink_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE
);

CREATE TABLE `fmprojectcategories` (
    `projcatid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `pid` int(10) unsigned NOT NULL,
    `categoryname` varchar(150) NOT NULL,
    `managers` varchar(100) DEFAULT NULL,
    `description` varchar(250) DEFAULT NULL,
    `parentpid` int(11) DEFAULT NULL,
    `occurrencesearch` int(11) DEFAULT '0',
    `ispublic` int(11) DEFAULT '1',
    `notes` varchar(250) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`projcatid`),
    KEY `FK_fmprojcat_pid_idx` (`pid`),
    CONSTRAINT `FK_fmprojcat_pid` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `fmprojects` (
    `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `projname` varchar(45) NOT NULL,
    `displayname` varchar(150) DEFAULT NULL,
    `managers` varchar(150) DEFAULT NULL,
    `briefdescription` varchar(300) DEFAULT NULL,
    `fulldescription` varchar(5000) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `iconUrl` varchar(150) DEFAULT NULL,
    `headerUrl` varchar(150) DEFAULT NULL,
    `occurrencesearch` int(10) unsigned NOT NULL DEFAULT '0',
    `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
    `dynamicProperties` text,
    `parentpid` int(10) unsigned DEFAULT NULL,
    `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`pid`),
    KEY `FK_parentpid_proj` (`parentpid`),
    CONSTRAINT `FK_parentpid_proj` FOREIGN KEY (`parentpid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `fmvouchers` (
    `TID` int(10) unsigned DEFAULT NULL,
    `vid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `CLID` int(10) unsigned NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `editornotes` varchar(50) DEFAULT NULL,
    `preferredImage` int(11) DEFAULT '0',
    `Notes` varchar(250) DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`vid`),
    UNIQUE KEY `UNIQUE_voucher` (`CLID`,`occid`)
);

CREATE TABLE `glossary` (
    `glossid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `term` varchar(150) NOT NULL,
    `definition` varchar(2000) DEFAULT NULL,
    `language` varchar(45) NOT NULL DEFAULT 'English',
    `source` varchar(1000) DEFAULT NULL,
    `translator` varchar(250) DEFAULT NULL,
    `author` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `resourceurl` varchar(600) DEFAULT NULL,
    `uid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`glossid`),
    KEY `Index_term` (`term`),
    KEY `Index_glossary_lang` (`language`),
    KEY `FK_glossary_uid_idx` (`uid`),
    CONSTRAINT `FK_glossary_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE `glossaryimages` (
    `glimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `glossid` int(10) unsigned NOT NULL,
    `url` varchar(255) NOT NULL,
    `thumbnailurl` varchar(255) DEFAULT NULL,
    `structures` varchar(150) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `createdBy` varchar(250) DEFAULT NULL,
    `uid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`glimgid`),
    KEY `FK_glossaryimages_gloss` (`glossid`),
    KEY `FK_glossaryimages_uid_idx` (`uid`),
    CONSTRAINT `FK_glossaryimages_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_glossaryimages_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE `glossarysources` (
    `tid` int(10) unsigned NOT NULL,
    `contributorTerm` varchar(1000) DEFAULT NULL,
    `contributorImage` varchar(1000) DEFAULT NULL,
    `translator` varchar(1000) DEFAULT NULL,
    `additionalSources` varchar(1000) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tid`),
    CONSTRAINT `FK_glossarysources_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
);

CREATE TABLE `glossarytaxalink` (
    `glossid` int(10) unsigned NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`glossid`,`tid`),
    KEY `glossarytaxalink_ibfk_1` (`tid`),
    CONSTRAINT `FK_glossarytaxa_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_glossarytaxa_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `glossarytermlink` (
    `gltlinkid` int(10) NOT NULL AUTO_INCREMENT,
    `glossgrpid` int(10) unsigned NOT NULL,
    `glossid` int(10) unsigned NOT NULL,
    `relationshipType` varchar(45) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`gltlinkid`),
    UNIQUE KEY `Unique_termkeys` (`glossgrpid`,`glossid`),
    KEY `glossarytermlink_ibfk_1` (`glossid`),
    CONSTRAINT `FK_glossarytermlink_glossgrpid` FOREIGN KEY (`glossgrpid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_glossarytermlink_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `guidimages` (
    `guid` varchar(45) NOT NULL,
    `imgid` int(10) unsigned DEFAULT NULL,
    `archivestatus` int(3) NOT NULL DEFAULT '0',
    `archiveobj` text,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`guid`),
    UNIQUE KEY `guidimages_imgid_unique` (`imgid`)
);

CREATE TABLE `guidoccurdeterminations` (
    `guid` varchar(45) NOT NULL,
    `detid` int(10) unsigned DEFAULT NULL,
    `archivestatus` int(3) NOT NULL DEFAULT '0',
    `archiveobj` text,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`guid`),
    UNIQUE KEY `guidoccurdet_detid_unique` (`detid`)
);

CREATE TABLE `guidoccurrences` (
    `guid` varchar(45) NOT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `archivestatus` int(3) NOT NULL DEFAULT '0',
    `archiveobj` text,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`guid`),
    UNIQUE KEY `guidoccurrences_occid_unique` (`occid`)
);

CREATE TABLE `images` (
    `imgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned DEFAULT NULL,
    `url` varchar(255) DEFAULT NULL,
    `thumbnailurl` varchar(255) DEFAULT NULL,
    `originalurl` varchar(255) DEFAULT NULL,
    `archiveurl` varchar(255) DEFAULT NULL,
    `photographer` varchar(100) DEFAULT NULL,
    `photographeruid` int(10) unsigned DEFAULT NULL,
    `imagetype` varchar(50) DEFAULT NULL,
    `format` varchar(45) DEFAULT NULL,
    `caption` varchar(750) DEFAULT NULL,
    `owner` varchar(250) DEFAULT NULL,
    `sourceurl` varchar(255) DEFAULT NULL,
    `referenceUrl` varchar(255) DEFAULT NULL,
    `copyright` varchar(255) DEFAULT NULL,
    `rights` varchar(255) DEFAULT NULL,
    `accessrights` varchar(255) DEFAULT NULL,
    `locality` varchar(250) DEFAULT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `notes` varchar(350) DEFAULT NULL,
    `anatomy` varchar(100) DEFAULT NULL,
    `username` varchar(45) DEFAULT NULL,
    `sourceIdentifier` varchar(150) DEFAULT NULL,
    `mediaMD5` varchar(45) DEFAULT NULL,
    `dynamicProperties` text,
    `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`imgid`),
    KEY `Index_tid` (`tid`),
    KEY `FK_images_occ` (`occid`),
    KEY `FK_photographeruid` (`photographeruid`),
    KEY `Index_images_datelastmod` (`InitialTimeStamp`),
    CONSTRAINT `FK_images_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
    CONSTRAINT `FK_photographeruid` FOREIGN KEY (`photographeruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_taxaimagestid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
);

CREATE TABLE `imagetag` (
    `imagetagid` bigint(20) NOT NULL AUTO_INCREMENT,
    `imgid` int(10) unsigned NOT NULL,
    `keyvalue` varchar(30) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`imagetagid`),
    UNIQUE KEY `imgid` (`imgid`,`keyvalue`),
    KEY `keyvalue` (`keyvalue`),
    KEY `FK_imagetag_imgid_idx` (`imgid`),
    CONSTRAINT `FK_imagetag_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_imagetag_tagkey` FOREIGN KEY (`keyvalue`) REFERENCES `imagetagkey` (`tagkey`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `imagetagkey` (
    `tagkey` varchar(30) NOT NULL,
    `shortlabel` varchar(30) NOT NULL,
    `description_en` varchar(255) NOT NULL,
    `sortorder` int(11) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tagkey`),
    KEY `sortorder` (`sortorder`)
);

CREATE TABLE `institutions` (
    `iid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `InstitutionCode` varchar(45) DEFAULT NULL,
    `InstitutionName` varchar(150) NOT NULL,
    `InstitutionName2` varchar(150) DEFAULT NULL,
    `Address1` varchar(150) DEFAULT NULL,
    `Address2` varchar(150) DEFAULT NULL,
    `City` varchar(45) DEFAULT NULL,
    `StateProvince` varchar(45) DEFAULT NULL,
    `PostalCode` varchar(45) DEFAULT NULL,
    `Country` varchar(45) DEFAULT NULL,
    `Phone` varchar(45) DEFAULT NULL,
    `Contact` varchar(65) DEFAULT NULL,
    `Email` varchar(45) DEFAULT NULL,
    `Url` varchar(250) DEFAULT NULL,
    `Notes` varchar(250) DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `modifiedTimeStamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`iid`),
    KEY `FK_inst_uid_idx` (`modifieduid`),
    CONSTRAINT `FK_inst_uid` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `kmcharacterlang` (
    `cid` int(10) unsigned NOT NULL,
    `charname` varchar(150) NOT NULL,
    `language` varchar(45) NOT NULL,
    `langid` int(11) NOT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `helpurl` varchar(500) DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cid`,`langid`),
    KEY `FK_charlang_lang_idx` (`langid`),
    CONSTRAINT `FK_characterlang_1` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_charlang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `kmcharacters` (
    `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `charname` varchar(150) NOT NULL,
    `chartype` varchar(2) NOT NULL DEFAULT 'UM',
    `defaultlang` varchar(45) NOT NULL DEFAULT 'English',
    `difficultyrank` smallint(5) unsigned NOT NULL DEFAULT '1',
    `hid` int(10) unsigned DEFAULT NULL,
    `units` varchar(45) DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `display` varchar(45) DEFAULT NULL,
    `helpurl` varchar(500) DEFAULT NULL,
    `enteredby` varchar(45) DEFAULT NULL,
    `sortsequence` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cid`),
    KEY `Index_charname` (`charname`),
    KEY `Index_sort` (`sortsequence`),
    KEY `FK_charheading_idx` (`hid`),
    CONSTRAINT `FK_charheading` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON UPDATE CASCADE
);

CREATE TABLE `kmchardependence` (
    `CID` int(10) unsigned NOT NULL,
    `CIDDependance` int(10) unsigned NOT NULL,
    `CSDependance` varchar(16) NOT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`CSDependance`,`CIDDependance`,`CID`),
    KEY `FK_chardependance_cid_idx` (`CID`),
    KEY `FK_chardependance_cs_idx` (`CIDDependance`,`CSDependance`),
    CONSTRAINT `FK_chardependance_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_chardependance_cs` FOREIGN KEY (`CIDDependance`, `CSDependance`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `kmcharheading` (
    `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `headingname` varchar(255) NOT NULL,
    `language` varchar(45) NOT NULL DEFAULT 'English',
    `langid` int(11) NOT NULL,
    `notes` longtext,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`hid`,`langid`),
    UNIQUE KEY `unique_kmcharheading` (`headingname`,`langid`),
    KEY `HeadingName` (`headingname`),
    KEY `FK_kmcharheading_lang_idx` (`langid`),
    CONSTRAINT `FK_kmcharheading_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
);

CREATE TABLE `kmchartaxalink` (
    `CID` int(10) unsigned NOT NULL DEFAULT '0',
    `TID` int(10) unsigned NOT NULL DEFAULT '0',
    `Status` varchar(50) DEFAULT NULL,
    `Notes` varchar(255) DEFAULT NULL,
    `Relation` varchar(45) NOT NULL DEFAULT 'include',
    `EditabilityInherited` bit(1) DEFAULT NULL,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`CID`,`TID`),
    KEY `FK_CharTaxaLink-TID` (`TID`),
    CONSTRAINT `FK_chartaxalink_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_chartaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `kmcs` (
    `cid` int(10) unsigned NOT NULL DEFAULT '0',
    `cs` varchar(16) NOT NULL,
    `CharStateName` varchar(255) DEFAULT NULL,
    `Implicit` tinyint(1) NOT NULL DEFAULT '0',
    `Notes` longtext,
    `Description` varchar(255) DEFAULT NULL,
    `IllustrationUrl` varchar(250) DEFAULT NULL,
    `StateID` int(10) unsigned DEFAULT NULL,
    `SortSequence` int(10) unsigned DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `EnteredBy` varchar(45) DEFAULT NULL,
    PRIMARY KEY (`cs`,`cid`),
    KEY `FK_cs_chars` (`cid`),
    CONSTRAINT `FK_cs_chars` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `kmcsimages` (
    `csimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `cid` int(10) unsigned NOT NULL,
    `cs` varchar(16) NOT NULL,
    `url` varchar(255) NOT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `sortsequence` varchar(45) NOT NULL DEFAULT '50',
    `username` varchar(45) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`csimgid`),
    KEY `FK_kscsimages_kscs_idx` (`cid`,`cs`),
    CONSTRAINT `FK_kscsimages_kscs` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `kmcslang` (
    `cid` int(10) unsigned NOT NULL,
    `cs` varchar(16) NOT NULL,
    `charstatename` varchar(150) NOT NULL,
    `language` varchar(45) NOT NULL,
    `langid` int(11) NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cid`,`cs`,`langid`),
    KEY `FK_cslang_lang_idx` (`langid`),
    CONSTRAINT `FK_cslang_1` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_cslang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `kmdescr` (
    `TID` int(10) unsigned NOT NULL DEFAULT '0',
    `CID` int(10) unsigned NOT NULL DEFAULT '0',
    `Modifier` varchar(255) DEFAULT NULL,
    `CS` varchar(16) NOT NULL,
    `X` double(15,5) DEFAULT NULL,
    `TXT` longtext,
    `PseudoTrait` int(5) unsigned DEFAULT '0',
    `Frequency` int(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Frequency of occurrence; 1 = rare... 5 = common',
    `Inherited` varchar(50) DEFAULT NULL,
    `Source` varchar(100) DEFAULT NULL,
    `Seq` int(10) DEFAULT NULL,
    `Notes` longtext,
    `DateEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`TID`,`CID`,`CS`),
    KEY `CSDescr` (`CID`,`CS`),
    CONSTRAINT `FK_descr_cs` FOREIGN KEY (`CID`, `CS`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_descr_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `lkupcountry` (
    `countryId` int(11) NOT NULL AUTO_INCREMENT,
    `countryName` varchar(100) NOT NULL,
    `iso` varchar(2) DEFAULT NULL,
    `iso3` varchar(3) DEFAULT NULL,
    `numcode` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`countryId`),
    UNIQUE KEY `country_unique` (`countryName`),
    KEY `Index_lkupcountry_iso` (`iso`),
    KEY `Index_lkupcountry_iso3` (`iso3`)
);

CREATE TABLE `lkupcounty` (
    `countyId` int(11) NOT NULL AUTO_INCREMENT,
    `stateId` int(11) NOT NULL,
    `countyName` varchar(100) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`countyId`),
    UNIQUE KEY `unique_county` (`stateId`,`countyName`),
    KEY `fk_stateprovince` (`stateId`),
    KEY `index_countyname` (`countyName`),
    CONSTRAINT `fk_stateprovince` FOREIGN KEY (`stateId`) REFERENCES `lkupstateprovince` (`stateId`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `lkupstateprovince` (
    `stateId` int(11) NOT NULL AUTO_INCREMENT,
    `countryId` int(11) NOT NULL,
    `stateName` varchar(100) NOT NULL,
    `abbrev` varchar(3) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`stateId`),
    UNIQUE KEY `state_index` (`stateName`,`countryId`),
    KEY `fk_country` (`countryId`),
    KEY `index_statename` (`stateName`),
    KEY `Index_lkupstate_abbr` (`abbrev`),
    CONSTRAINT `fk_country` FOREIGN KEY (`countryId`) REFERENCES `lkupcountry` (`countryId`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `media` (
    `mediaid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned DEFAULT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `accessuri` varchar(2048) NOT NULL,
    `title` varchar(255) DEFAULT NULL,
    `creatoruid` int(10) unsigned DEFAULT NULL,
    `creator` varchar(45) DEFAULT NULL,
    `type` varchar(45) DEFAULT NULL,
    `format` varchar(45) DEFAULT NULL,
    `owner` varchar(250) DEFAULT NULL,
    `furtherinformationurl` varchar(2048) DEFAULT NULL,
    `language` varchar(45) DEFAULT NULL,
    `usageterms` varchar(255) DEFAULT NULL,
    `rights` varchar(255) DEFAULT NULL,
    `bibliographiccitation` varchar(255) DEFAULT NULL,
    `publisher` varchar(255) DEFAULT NULL,
    `contributor` varchar(255) DEFAULT NULL,
    `locationcreated` varchar(1000) DEFAULT NULL,
    `description` varchar(1000) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mediaid`),
    KEY `FK_media_taxa_idx` (`tid`),
    KEY `FK_media_occid_idx` (`occid`),
    KEY `FK_media_uid_idx` (`creatoruid`),
    KEY `INDEX_format` (`format`),
    CONSTRAINT `FK_media_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_media_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON UPDATE CASCADE,
    CONSTRAINT `FK_media_uid` FOREIGN KEY (`creatoruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `omcollcategories` (
    `ccpk` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `category` varchar(75) NOT NULL,
    `icon` varchar(250) DEFAULT NULL,
    `acronym` varchar(45) DEFAULT NULL,
    `url` varchar(250) DEFAULT NULL,
    `inclusive` int(2) DEFAULT '1',
    `notes` varchar(250) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ccpk`)
);

CREATE TABLE `omcollcatlink` (
    `ccpk` int(10) unsigned NOT NULL,
    `collid` int(10) unsigned NOT NULL,
    `isPrimary` tinyint(1) DEFAULT '1',
    `sortsequence` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ccpk`,`collid`),
    KEY `FK_collcatlink_coll` (`collid`),
    CONSTRAINT `FK_collcatlink_cat` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_collcatlink_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omcollections` (
    `CollID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `InstitutionCode` varchar(45) DEFAULT NULL,
    `CollectionCode` varchar(45) DEFAULT NULL,
    `CollectionName` varchar(150) NOT NULL,
    `collectionId` varchar(100) DEFAULT NULL,
    `datasetID` varchar(250) DEFAULT NULL,
    `datasetName` varchar(100) DEFAULT NULL,
    `iid` int(10) unsigned DEFAULT NULL,
    `fulldescription` varchar(2000) DEFAULT NULL,
    `Homepage` varchar(250) DEFAULT NULL,
    `IndividualUrl` varchar(500) DEFAULT NULL,
    `Contact` varchar(250) DEFAULT NULL,
    `email` varchar(45) DEFAULT NULL,
    `contactJson` json DEFAULT NULL,
    `latitudedecimal` decimal(8,6) DEFAULT NULL,
    `longitudedecimal` decimal(9,6) DEFAULT NULL,
    `icon` varchar(250) DEFAULT NULL,
    `CollType` varchar(45) NOT NULL DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, HumanObservation, FossilSpecimen, LivingSpecimen, MaterialSample',
    `ManagementType` varchar(45) DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data',
    `DataRecordingMethod` varchar(45) NULL DEFAULT 'specimen',
    `defaultRepCount` int(10) DEFAULT NULL,
    `PublicEdits` int(1) unsigned NOT NULL DEFAULT '1',
    `collectionguid` varchar(45) DEFAULT NULL,
    `securitykey` varchar(45) DEFAULT NULL,
    `guidtarget` varchar(45) DEFAULT NULL,
    `rightsHolder` varchar(250) DEFAULT NULL,
    `rights` varchar(250) DEFAULT NULL,
    `usageTerm` varchar(250) DEFAULT NULL,
    `publishToGbif` int(11) DEFAULT NULL,
    `publishToIdigbio` int(11) DEFAULT NULL,
    `aggKeysStr` varchar(1000) DEFAULT NULL,
    `dwcaUrl` varchar(250) DEFAULT NULL,
    `bibliographicCitation` varchar(1000) DEFAULT NULL,
    `accessrights` varchar(1000) DEFAULT NULL,
    `dynamicProperties` text,
    `SortSeq` int(10) unsigned DEFAULT NULL,
    `isPublic` smallint(1) NOT NULL DEFAULT 1,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`CollID`),
    UNIQUE KEY `Index_inst` (`InstitutionCode`,`CollectionCode`),
    KEY `FK_collid_iid_idx` (`iid`),
    KEY `isPublic` (`isPublic`),
    CONSTRAINT `FK_collid_iid` FOREIGN KEY (`iid`) REFERENCES `institutions` (`iid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `omcollectionstats` (
    `collid` int(10) unsigned NOT NULL,
    `recordcnt` int(10) unsigned NOT NULL DEFAULT '0',
    `georefcnt` int(10) unsigned DEFAULT NULL,
    `familycnt` int(10) unsigned DEFAULT NULL,
    `genuscnt` int(10) unsigned DEFAULT NULL,
    `speciescnt` int(10) unsigned DEFAULT NULL,
    `uploaddate` datetime DEFAULT NULL,
    `datelastmodified` datetime DEFAULT NULL,
    `uploadedby` varchar(45) DEFAULT NULL,
    `dynamicProperties` longtext,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`collid`),
    CONSTRAINT `FK_collectionstats_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
);

CREATE TABLE `omcollectors` (
    `recordedById` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `familyname` varchar(45) NOT NULL,
    `firstname` varchar(45) DEFAULT NULL,
    `middlename` varchar(45) DEFAULT NULL,
    `startyearactive` int(11) DEFAULT NULL,
    `endyearactive` int(11) DEFAULT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `rating` int(11) DEFAULT '10',
    `guid` varchar(45) DEFAULT NULL,
    `preferredrecbyid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`recordedById`),
    KEY `fullname` (`familyname`,`firstname`),
    KEY `FK_preferred_recby_idx` (`preferredrecbyid`)
);

CREATE TABLE `omcollpublications` (
    `pubid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `targeturl` varchar(250) NOT NULL,
    `securityguid` varchar(45) NOT NULL,
    `criteriajson` varchar(250) DEFAULT NULL,
    `includedeterminations` int(11) DEFAULT '1',
    `includeimages` int(11) DEFAULT '1',
    `autoupdate` int(11) DEFAULT '0',
    `lastdateupdate` datetime DEFAULT NULL,
    `updateinterval` int(11) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`pubid`),
    KEY `FK_adminpub_collid_idx` (`collid`),
    CONSTRAINT `FK_adminpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omcollpuboccurlink` (
    `pubid` int(10) unsigned NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `verification` int(11) NOT NULL DEFAULT '0',
    `refreshtimestamp` datetime NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`pubid`,`occid`),
    KEY `FK_ompuboccid_idx` (`occid`),
    CONSTRAINT `FK_ompuboccid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ompubpubid` FOREIGN KEY (`pubid`) REFERENCES `omcollpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omcrowdsourcecentral` (
    `omcsid` int(11) NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `instructions` text,
    `trainingurl` varchar(500) DEFAULT NULL,
    `editorlevel` int(11) NOT NULL DEFAULT '0' COMMENT '0=public, 1=public limited, 2=private',
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`omcsid`),
    UNIQUE KEY `Index_omcrowdsourcecentral_collid` (`collid`),
    KEY `FK_omcrowdsourcecentral_collid` (`collid`),
    CONSTRAINT `FK_omcrowdsourcecentral_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
);

CREATE TABLE `omcrowdsourcequeue` (
    `idomcrowdsourcequeue` int(11) NOT NULL AUTO_INCREMENT,
    `omcsid` int(11) NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `reviewstatus` int(11) NOT NULL DEFAULT '0' COMMENT '0=open,5=pending review, 10=closed',
    `uidprocessor` int(10) unsigned DEFAULT NULL,
    `points` int(11) DEFAULT NULL COMMENT '0=fail, 1=minor edits, 2=no edits <default>, 3=excelled',
    `isvolunteer` int(2) NOT NULL DEFAULT '1',
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idomcrowdsourcequeue`),
    UNIQUE KEY `Index_omcrowdsource_occid` (`occid`),
    KEY `FK_omcrowdsourcequeue_occid` (`occid`),
    KEY `FK_omcrowdsourcequeue_uid` (`uidprocessor`),
    CONSTRAINT `FK_omcrowdsourcequeue_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_omcrowdsourcequeue_uid` FOREIGN KEY (`uidprocessor`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `omexsiccatinumbers` (
    `omenid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `exsnumber` varchar(45) NOT NULL,
    `ometid` int(10) unsigned NOT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`omenid`),
    UNIQUE KEY `Index_omexsiccatinumbers_unique` (`exsnumber`,`ometid`),
    KEY `FK_exsiccatiTitleNumber` (`ometid`),
    CONSTRAINT `FK_exsiccatiTitleNumber` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`)
);

CREATE TABLE `omexsiccatiocclink` (
    `omenid` int(10) unsigned NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `ranking` int(11) NOT NULL DEFAULT '50',
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`omenid`,`occid`),
    UNIQUE KEY `UniqueOmexsiccatiOccLink` (`occid`),
    KEY `FKExsiccatiNumOccLink1` (`omenid`),
    KEY `FKExsiccatiNumOccLink2` (`occid`),
    CONSTRAINT `FKExsiccatiNumOccLink1` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE CASCADE,
    CONSTRAINT `FKExsiccatiNumOccLink2` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE
);

CREATE TABLE `omexsiccatititles` (
    `ometid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(150) NOT NULL,
    `abbreviation` varchar(100) DEFAULT NULL,
    `editor` varchar(150) DEFAULT NULL,
    `exsrange` varchar(45) DEFAULT NULL,
    `startdate` varchar(45) DEFAULT NULL,
    `enddate` varchar(45) DEFAULT NULL,
    `source` varchar(250) DEFAULT NULL,
    `notes` varchar(2000) DEFAULT NULL,
    `lasteditedby` varchar(45) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ometid`),
    KEY `index_exsiccatiTitle` (`title`)
);

CREATE TABLE `omoccuraccessstats` (
    `oasid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `accessdate` date NOT NULL,
    `ipaddress` varchar(45) NOT NULL,
    `cnt` int(10) unsigned NOT NULL,
    `accesstype` varchar(45) NOT NULL,
    `dynamicProperties` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`oasid`),
    UNIQUE KEY `UNIQUE_occuraccess` (`occid`,`accessdate`,`ipaddress`,`accesstype`),
    CONSTRAINT `FK_occuraccess_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

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

CREATE TABLE `omoccurassociations` (
    `associd` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `occidassociate` int(10) unsigned DEFAULT NULL,
    `relationship` varchar(150) NOT NULL,
    `identifier` varchar(250) DEFAULT NULL COMMENT 'e.g. GUID',
    `basisOfRecord` varchar(45) DEFAULT NULL,
    `resourceurl` varchar(250) DEFAULT NULL,
    `verbatimsciname` varchar(250) DEFAULT NULL,
    `tid` int(11) unsigned DEFAULT NULL,
    `locationOnHost` varchar(250) DEFAULT NULL,
    `condition` varchar(250) DEFAULT NULL,
    `dateEmerged` datetime DEFAULT NULL,
    `dynamicProperties` text,
    `notes` varchar(250) DEFAULT NULL,
    `createduid` int(10) unsigned DEFAULT NULL,
    `datelastmodified` datetime DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`associd`),
    KEY `omossococcur_occid_idx` (`occid`),
    KEY `omossococcur_occidassoc_idx` (`occidassociate`),
    KEY `FK_occurassoc_tid_idx` (`tid`),
    KEY `FK_occurassoc_uidmodified_idx` (`modifieduid`),
    KEY `FK_occurassoc_uidcreated_idx` (`createduid`),
    KEY `INDEX_verbatimSciname` (`verbatimsciname`),
    CONSTRAINT `FK_occurassoc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_occurassoc_occidassoc` FOREIGN KEY (`occidassociate`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_occurassoc_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_occurassoc_uidcreated` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_occurassoc_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `omoccurcollectingevents` (
    `eventID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned DEFAULT NULL,
    `locationID` int(11) DEFAULT NULL,
    `eventType` varchar(255) DEFAULT NULL,
    `fieldNotes` text,
    `fieldnumber` varchar(45) DEFAULT NULL,
    `recordedBy` varchar(255) DEFAULT NULL,
    `recordNumber` varchar(45) DEFAULT NULL,
    `recordedbyid` bigint(20) DEFAULT NULL,
    `associatedCollectors` varchar(255) DEFAULT NULL,
    `eventDate` date DEFAULT NULL,
    `latestDateCollected` date DEFAULT NULL,
    `eventTime` time DEFAULT NULL,
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
    KEY `eventType` (`eventType`),
    KEY `eventDate` (`eventDate`),
    KEY `eventTime` (`eventTime`),
    KEY `localitySecurity` (`localitySecurity`),
    KEY `decimalLatitude` (`decimalLatitude`),
    KEY `decimalLongitude` (`decimalLongitude`),
    KEY `FK_eventcollid` (`collid`),
    KEY `locationID` (`locationID`),
    CONSTRAINT `FK_eventcollid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurdatasetlink` (
    `occid` int(10) unsigned NOT NULL,
    `datasetid` int(10) unsigned NOT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`occid`,`datasetid`),
    KEY `FK_omoccurdatasetlink_datasetid` (`datasetid`),
    KEY `FK_omoccurdatasetlink_occid` (`occid`),
    CONSTRAINT `FK_omoccurdatasetlink_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_omoccurdatasetlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurdatasets` (
    `datasetid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `sortsequence` int(11) DEFAULT NULL,
    `uid` int(11) unsigned DEFAULT NULL,
    `collid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`datasetid`),
    KEY `FK_omoccurdatasets_uid_idx` (`uid`),
    KEY `FK_omcollections_collid_idx` (`collid`),
    CONSTRAINT `FK_omcollections_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_omoccurdatasets_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `omoccurdeterminations` (
    `detid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `identifiedBy` varchar(60) NOT NULL,
    `idbyid` int(10) unsigned DEFAULT NULL,
    `dateIdentified` varchar(45) NOT NULL,
    `dateIdentifiedInterpreted` date DEFAULT NULL,
    `sciname` varchar(100) NOT NULL,
    `verbatimScientificName` varchar(255) DEFAULT NULL,
    `tid` int(10) unsigned DEFAULT NULL,
    `scientificNameAuthorship` varchar(100) DEFAULT NULL,
    `identificationQualifier` varchar(45) DEFAULT NULL,
    `iscurrent` int(2) DEFAULT '0',
    `printqueue` int(2) DEFAULT '0',
    `appliedStatus` int(2) DEFAULT '1',
    `detType` varchar(45) DEFAULT NULL,
    `identificationReferences` varchar(255) DEFAULT NULL,
    `identificationRemarks` varchar(500) DEFAULT NULL,
    `sourceIdentifier` varchar(45) DEFAULT NULL,
    `sortsequence` int(10) unsigned DEFAULT '10',
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`detid`),
    UNIQUE KEY `Index_unique` (`occid`,`dateIdentified`,`identifiedBy`,`sciname`),
    KEY `FK_omoccurdets_tid` (`tid`),
    KEY `FK_omoccurdets_idby_idx` (`idbyid`),
    KEY `Index_dateIdentInterpreted` (`dateIdentifiedInterpreted`),
    CONSTRAINT `FK_omoccurdets_idby` FOREIGN KEY (`idbyid`) REFERENCES `omcollectors` (`recordedById`) ON DELETE SET NULL ON UPDATE SET NULL,
    CONSTRAINT `FK_omoccurdets_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_omoccurdets_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
);

CREATE TABLE `omoccureditlocks` (
    `occid` int(10) unsigned NOT NULL,
    `uid` int(11) NOT NULL,
    `ts` int(11) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`occid`)
);

CREATE TABLE `omoccuredits` (
    `ocedid` int(11) NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `FieldName` varchar(45) NOT NULL,
    `FieldValueNew` text NOT NULL,
    `FieldValueOld` text NOT NULL,
    `ReviewStatus` int(1) NOT NULL DEFAULT '1' COMMENT '1=Open;2=Pending;3=Closed',
    `AppliedStatus` int(1) NOT NULL DEFAULT '0' COMMENT '0=Not Applied;1=Applied',
    `editType` int(11) DEFAULT '0' COMMENT '0 = general edit, 1 = batch edit',
    `guid` varchar(45) DEFAULT NULL,
    `uid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ocedid`),
    UNIQUE KEY `guid_UNIQUE` (`guid`),
    KEY `fk_omoccuredits_uid` (`uid`),
    KEY `fk_omoccuredits_occid` (`occid`),
    CONSTRAINT `fk_omoccuredits_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_omoccuredits_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurexchange` (
    `exchangeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `identifier` varchar(30) DEFAULT NULL,
    `collid` int(10) unsigned DEFAULT NULL,
    `iid` int(10) unsigned DEFAULT NULL,
    `transactionType` varchar(10) DEFAULT NULL,
    `in_out` varchar(3) DEFAULT NULL,
    `dateSent` date DEFAULT NULL,
    `dateReceived` date DEFAULT NULL,
    `totalBoxes` int(5) DEFAULT NULL,
    `shippingMethod` varchar(50) DEFAULT NULL,
    `totalExMounted` int(5) DEFAULT NULL,
    `totalExUnmounted` int(5) DEFAULT NULL,
    `totalGift` int(5) DEFAULT NULL,
    `totalGiftDet` int(5) DEFAULT NULL,
    `adjustment` int(5) DEFAULT NULL,
    `invoiceBalance` int(6) DEFAULT NULL,
    `invoiceMessage` varchar(500) DEFAULT NULL,
    `description` varchar(1000) DEFAULT NULL,
    `notes` varchar(500) DEFAULT NULL,
    `createdBy` varchar(20) DEFAULT NULL,
    `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`exchangeid`),
    KEY `FK_occexch_coll` (`collid`),
    CONSTRAINT `FK_occexch_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurgenetic` (
    `idoccurgenetic` int(11) NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `identifier` varchar(150) DEFAULT NULL,
    `resourcename` varchar(150) NOT NULL,
    `title` varchar(150) DEFAULT NULL,
    `locus` varchar(500) DEFAULT NULL,
    `resourceurl` varchar(500) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idoccurgenetic`),
    UNIQUE KEY `UNIQUE_omoccurgenetic` (`occid`,`resourceurl`),
    KEY `FK_omoccurgenetic` (`occid`),
    KEY `INDEX_omoccurgenetic_name` (`resourcename`),
    CONSTRAINT `FK_omoccurgenetic` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccuridentifiers` (
    `idomoccuridentifiers` int(11) NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `identifiervalue` varchar(45) NOT NULL,
    `identifiername` varchar(45) DEFAULT NULL COMMENT 'barcode, accession number, old catalog number, NPS, etc',
    `notes` varchar(250) DEFAULT NULL,
    `modifiedUid` int(10) unsigned NOT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idomoccuridentifiers`),
    KEY `FK_omoccuridentifiers_occid_idx` (`occid`),
    KEY `Index_value` (`identifiervalue`),
    CONSTRAINT `FK_omoccuridentifiers_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurlithostratigraphy` (
    `occid` int(10) unsigned NOT NULL,
    `chronoId` int(10) unsigned NOT NULL,
    `Group` varchar(255) DEFAULT NULL,
    `Formation` varchar(255) DEFAULT NULL,
    `Member` varchar(255) DEFAULT NULL,
    `Bed` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`occid`,`chronoId`),
    KEY `FK_occurlitho_chronoid` (`chronoId`),
    KEY `FK_occurlitho_occid` (`occid`),
    KEY `Group` (`Group`),
    KEY `Formation` (`Formation`),
    KEY `Member` (`Member`),
    CONSTRAINT `FK_occurlitho_chronoid` FOREIGN KEY (`chronoId`) REFERENCES `paleochronostratigraphy` (`chronoId`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_occurlitho_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurloans` (
    `loanid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `loanIdentifierOwn` varchar(30) DEFAULT NULL,
    `loanIdentifierBorr` varchar(30) DEFAULT NULL,
    `collidOwn` int(10) unsigned DEFAULT NULL,
    `collidBorr` int(10) unsigned DEFAULT NULL,
    `iidOwner` int(10) unsigned DEFAULT NULL,
    `iidBorrower` int(10) unsigned DEFAULT NULL,
    `dateSent` date DEFAULT NULL,
    `dateSentReturn` date DEFAULT NULL,
    `receivedStatus` varchar(250) DEFAULT NULL,
    `totalBoxes` int(5) DEFAULT NULL,
    `totalBoxesReturned` int(5) DEFAULT NULL,
    `numSpecimens` int(5) DEFAULT NULL,
    `shippingMethod` varchar(50) DEFAULT NULL,
    `shippingMethodReturn` varchar(50) DEFAULT NULL,
    `dateDue` date DEFAULT NULL,
    `dateReceivedOwn` date DEFAULT NULL,
    `dateReceivedBorr` date DEFAULT NULL,
    `dateClosed` date DEFAULT NULL,
    `forWhom` varchar(50) DEFAULT NULL,
    `description` varchar(1000) DEFAULT NULL,
    `invoiceMessageOwn` varchar(500) DEFAULT NULL,
    `invoiceMessageBorr` varchar(500) DEFAULT NULL,
    `notes` varchar(500) DEFAULT NULL,
    `createdByOwn` varchar(30) DEFAULT NULL,
    `createdByBorr` varchar(30) DEFAULT NULL,
    `processingStatus` int(5) unsigned DEFAULT '1',
    `processedByOwn` varchar(30) DEFAULT NULL,
    `processedByBorr` varchar(30) DEFAULT NULL,
    `processedByReturnOwn` varchar(30) DEFAULT NULL,
    `processedByReturnBorr` varchar(30) DEFAULT NULL,
    `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`loanid`),
    KEY `FK_occurloans_owninst` (`iidOwner`),
    KEY `FK_occurloans_borrinst` (`iidBorrower`),
    KEY `FK_occurloans_owncoll` (`collidOwn`),
    KEY `FK_occurloans_borrcoll` (`collidBorr`),
    CONSTRAINT `FK_occurloans_borrcoll` FOREIGN KEY (`collidBorr`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
    CONSTRAINT `FK_occurloans_borrinst` FOREIGN KEY (`iidBorrower`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE,
    CONSTRAINT `FK_occurloans_owncoll` FOREIGN KEY (`collidOwn`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
    CONSTRAINT `FK_occurloans_owninst` FOREIGN KEY (`iidOwner`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE
);

CREATE TABLE `omoccurloanslink` (
    `loanid` int(10) unsigned NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `returndate` date DEFAULT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`loanid`,`occid`),
    KEY `FK_occurloanlink_occid` (`occid`),
    KEY `FK_occurloanlink_loanid` (`loanid`),
    CONSTRAINT `FK_occurloanlink_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_occurloanlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurlocations` (
    `locationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `locationName` varchar(255) DEFAULT NULL,
    `locationCode` varchar(50) DEFAULT NULL,
    `waterBody` varchar(255) DEFAULT NULL,
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

CREATE TABLE `omoccurpaleo` (
    `paleoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `eon` varchar(65) DEFAULT NULL,
    `era` varchar(65) DEFAULT NULL,
    `period` varchar(65) DEFAULT NULL,
    `epoch` varchar(65) DEFAULT NULL,
    `earlyInterval` varchar(65) DEFAULT NULL,
    `lateInterval` varchar(65) DEFAULT NULL,
    `absoluteAge` varchar(65) DEFAULT NULL,
    `storageAge` varchar(65) DEFAULT NULL,
    `stage` varchar(65) DEFAULT NULL,
    `localStage` varchar(65) DEFAULT NULL,
    `biota` varchar(65) DEFAULT NULL COMMENT 'Flora or Fanua',
    `biostratigraphy` varchar(65) DEFAULT NULL COMMENT 'Biozone',
    `taxonEnvironment` varchar(65) DEFAULT NULL COMMENT 'Marine or not',
    `lithogroup` varchar(65) DEFAULT NULL,
    `formation` varchar(65) DEFAULT NULL,
    `member` varchar(65) DEFAULT NULL,
    `bed` varchar(65) DEFAULT NULL,
    `lithology` varchar(250) DEFAULT NULL,
    `stratRemarks` varchar(250) DEFAULT NULL,
    `element` varchar(250) DEFAULT NULL,
    `slideProperties` varchar(1000) DEFAULT NULL,
    `geologicalContextID` varchar(45) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`paleoID`),
    UNIQUE KEY `UNIQUE_occid` (`occid`),
    KEY `FK_paleo_occid_idx` (`occid`),
    CONSTRAINT `FK_paleo_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `omoccurpaleogts` (
    `gtsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `gtsterm` varchar(45) NOT NULL,
    `rankid` int(11) NOT NULL,
    `rankname` varchar(45) DEFAULT NULL,
    `parentgtsid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`gtsid`),
    UNIQUE KEY `UNIQUE_gtsterm` (`gtsid`),
    KEY `FK_gtsparent_idx` (`parentgtsid`),
    CONSTRAINT `FK_gtsparent` FOREIGN KEY (`parentgtsid`) REFERENCES `omoccurpaleogts` (`gtsid`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `omoccurpoints` (
    `geoID` int(11) NOT NULL AUTO_INCREMENT,
    `occid` int(11) NOT NULL,
    `point` point NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`geoID`),
    UNIQUE KEY `occid` (`occid`),
    SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM;

CREATE TABLE `omoccurrences` (
    `occid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `dbpk` varchar(150) DEFAULT NULL,
    `basisOfRecord` varchar(32) DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
    `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
    `catalogNumber` varchar(32) DEFAULT NULL,
    `otherCatalogNumbers` varchar(255) DEFAULT NULL,
    `ownerInstitutionCode` varchar(32) DEFAULT NULL,
    `institutionID` varchar(255) DEFAULT NULL,
    `collectionID` varchar(255) DEFAULT NULL,
    `datasetID` varchar(255) DEFAULT NULL,
    `institutionCode` varchar(64) DEFAULT NULL,
    `collectionCode` varchar(64) DEFAULT NULL,
    `family` varchar(255) DEFAULT NULL,
    `verbatimScientificName` varchar(255) DEFAULT NULL,
    `sciname` varchar(255) DEFAULT NULL,
    `tid` int(10) unsigned DEFAULT NULL,
    `genus` varchar(255) DEFAULT NULL,
    `specificEpithet` varchar(255) DEFAULT NULL,
    `taxonRank` varchar(32) DEFAULT NULL,
    `infraspecificEpithet` varchar(255) DEFAULT NULL,
    `scientificNameAuthorship` varchar(255) DEFAULT NULL,
    `taxonRemarks` text,
    `identifiedBy` varchar(255) DEFAULT NULL,
    `dateIdentified` varchar(45) DEFAULT NULL,
    `identificationReferences` text,
    `identificationRemarks` text,
    `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
    `typeStatus` varchar(255) DEFAULT NULL,
    `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
    `recordNumber` varchar(45) DEFAULT NULL COMMENT 'Collector Number',
    `recordedbyid` bigint(20) DEFAULT NULL,
    `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
    `eventDate` date DEFAULT NULL,
    `latestDateCollected` date DEFAULT NULL,
    `eventTime` time DEFAULT NULL,
    `year` int(10) DEFAULT NULL,
    `month` int(10) DEFAULT NULL,
    `day` int(10) DEFAULT NULL,
    `startDayOfYear` int(10) DEFAULT NULL,
    `endDayOfYear` int(10) DEFAULT NULL,
    `verbatimEventDate` varchar(255) DEFAULT NULL,
    `habitat` text COMMENT 'Habitat, substrait, etc',
    `substrate` varchar(500) DEFAULT NULL,
    `fieldNotes` text,
    `fieldnumber` varchar(45) DEFAULT NULL,
    `eventID` int(11) UNSIGNED NULL DEFAULT NULL,
    `eventRemarks` text,
    `occurrenceRemarks` text COMMENT 'General Notes',
    `informationWithheld` varchar(250) DEFAULT NULL,
    `dataGeneralizations` varchar(250) DEFAULT NULL,
    `associatedOccurrences` text,
    `associatedTaxa` text COMMENT 'Associated Species',
    `dynamicProperties` text,
    `verbatimAttributes` text,
    `behavior` varchar(500) DEFAULT NULL,
    `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
    `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
    `establishmentMeans` varchar(150) DEFAULT NULL,
    `lifeStage` varchar(45) DEFAULT NULL,
    `sex` varchar(45) DEFAULT NULL,
    `individualCount` varchar(45) DEFAULT NULL,
    `samplingProtocol` varchar(100) DEFAULT NULL,
    `samplingEffort` varchar(200) DEFAULT NULL,
    `rep` int(10) DEFAULT NULL,
    `preparations` varchar(100) DEFAULT NULL,
    `locationID` int(11) UNSIGNED NULL DEFAULT NULL,
    `waterBody` varchar(255) DEFAULT NULL,
    `country` varchar(64) DEFAULT NULL,
    `stateProvince` varchar(255) DEFAULT NULL,
    `county` varchar(255) DEFAULT NULL,
    `municipality` varchar(255) DEFAULT NULL,
    `locality` text,
    `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
    `localitySecurityReason` varchar(100) DEFAULT NULL,
    `decimalLatitude` double DEFAULT NULL,
    `decimalLongitude` double DEFAULT NULL,
    `geodeticDatum` varchar(255) DEFAULT NULL,
    `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
    `footprintWKT` text,
    `coordinatePrecision` decimal(9,7) DEFAULT NULL,
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
    `minimumDepthInMeters` double DEFAULT NULL,
    `maximumDepthInMeters` double DEFAULT NULL,
    `verbatimDepth` varchar(50) DEFAULT NULL,
    `previousIdentifications` text,
    `disposition` varchar(250) DEFAULT NULL,
    `storageLocation` varchar(100) DEFAULT NULL,
    `genericcolumn1` varchar(100) DEFAULT NULL,
    `genericcolumn2` varchar(100) DEFAULT NULL,
    `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
    `language` varchar(20) DEFAULT NULL,
    `observeruid` int(10) unsigned DEFAULT NULL,
    `processingstatus` varchar(45) DEFAULT NULL,
    `recordEnteredBy` varchar(250) DEFAULT NULL,
    `duplicateQuantity` int(10) unsigned DEFAULT NULL,
    `labelProject` varchar(250) DEFAULT NULL,
    `dynamicFields` text,
    `isPublic` smallint(1) NOT NULL DEFAULT 1,
    `dateEntered` datetime DEFAULT NULL,
    `dateLastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`occid`),
    UNIQUE KEY `Index_collid` (`collid`,`dbpk`),
    KEY `Index_sciname` (`sciname`),
    KEY `Index_family` (`family`),
    KEY `Index_country` (`country`),
    KEY `Index_state` (`stateProvince`),
    KEY `Index_county` (`county`),
    KEY `Index_collector` (`recordedBy`),
    KEY `Index_gui` (`occurrenceID`),
    KEY `Index_ownerInst` (`ownerInstitutionCode`),
    KEY `FK_omoccurrences_tid` (`tid`),
    KEY `FK_omoccurrences_uid` (`observeruid`),
    KEY `Index_municipality` (`municipality`),
    KEY `Index_collnum` (`recordNumber`),
    KEY `Index_catalognumber` (`catalogNumber`),
    KEY `FK_recordedbyid` (`recordedbyid`),
    KEY `Index_eventDate` (`eventDate`),
    KEY `Index_occurrences_procstatus` (`processingstatus`),
    KEY `occelevmax` (`maximumElevationInMeters`),
    KEY `occelevmin` (`minimumElevationInMeters`),
    KEY `Index_occurrences_cult` (`cultivationStatus`),
    KEY `Index_occurrences_typestatus` (`typeStatus`),
    KEY `Index_occurDateLastModifed` (`dateLastModified`),
    KEY `Index_occurDateEntered` (`dateEntered`),
    KEY `Index_occurRecordEnteredBy` (`recordEnteredBy`),
    KEY `Index_locality` (`locality`(100)),
    KEY `Index_otherCatalogNumbers` (`otherCatalogNumbers`),
    KEY `Index_latestDateCollected` (`latestDateCollected`),
    KEY `Index_occurrenceRemarks` (`occurrenceRemarks`(100)),
    KEY `Index_locationID` (`locationID`),
    KEY `Index_occur_localitySecurity` (`localitySecurity`),
    KEY `Index_latlng` (`decimalLatitude`,`decimalLongitude`),
    KEY `Index_ labelProject` (`labelProject`),
    KEY `Index_verbatimScientificName` (`verbatimScientificName`),
    KEY `isPublic` (`isPublic`),
    KEY `rep` (`rep`),
    CONSTRAINT `FK_omoccurrences_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_omoccurrences_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_omoccurrences_uid` FOREIGN KEY (`observeruid`) REFERENCES `users` (`uid`),
    CONSTRAINT `FK_eventID` FOREIGN KEY (`eventID`) REFERENCES `omoccurcollectingevents` (`eventID`) ON DELETE RESTRICT ON UPDATE NO ACTION
);

CREATE TABLE `omoccurrencetypes` (
    `occurtypeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned DEFAULT NULL,
    `typestatus` varchar(45) DEFAULT NULL,
    `typeDesignationType` varchar(45) DEFAULT NULL,
    `typeDesignatedBy` varchar(45) DEFAULT NULL,
    `scientificName` varchar(250) DEFAULT NULL,
    `scientificNameAuthorship` varchar(45) DEFAULT NULL,
    `tid` int(10) unsigned DEFAULT NULL,
    `basionym` varchar(250) DEFAULT NULL,
    `refid` int(11) DEFAULT NULL,
    `bibliographicCitation` varchar(250) DEFAULT NULL,
    `dynamicProperties` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`occurtypeid`),
    KEY `FK_occurtype_occid_idx` (`occid`),
    KEY `FK_occurtype_refid_idx` (`refid`),
    KEY `FK_occurtype_tid_idx` (`tid`),
    CONSTRAINT `FK_occurtype_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_occurtype_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_occurtype_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `omoccurrevisions` (
    `orid` int(11) NOT NULL AUTO_INCREMENT,
    `occid` int(10) unsigned NOT NULL,
    `oldValues` text,
    `newValues` text,
    `externalSource` varchar(45) DEFAULT NULL,
    `externalEditor` varchar(100) DEFAULT NULL,
    `guid` varchar(45) DEFAULT NULL,
    `reviewStatus` int(11) DEFAULT NULL,
    `appliedStatus` int(11) DEFAULT NULL,
    `errorMessage` varchar(500) DEFAULT NULL,
    `uid` int(10) unsigned DEFAULT NULL,
    `externalTimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`orid`),
    UNIQUE KEY `guid_UNIQUE` (`guid`),
    KEY `fk_omrevisions_occid_idx` (`occid`),
    KEY `fk_omrevisions_uid_idx` (`uid`),
    KEY `Index_omrevisions_applied` (`appliedStatus`),
    KEY `Index_omrevisions_reviewed` (`reviewStatus`),
    KEY `Index_omrevisions_source` (`externalSource`),
    KEY `Index_omrevisions_editor` (`externalEditor`),
    CONSTRAINT `fk_omrevisions_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_omrevisions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `paleochronostratigraphy` (
    `chronoId` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `Eon` varchar(255) DEFAULT NULL,
    `Era` varchar(255) DEFAULT NULL,
    `Period` varchar(255) DEFAULT NULL,
    `Epoch` varchar(255) DEFAULT NULL,
    `Stage` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`chronoId`),
    KEY `Eon` (`Eon`),
    KEY `Era` (`Era`),
    KEY `Period` (`Period`),
    KEY `Epoch` (`Epoch`),
    KEY `Stage` (`Stage`)
);

CREATE TABLE `referenceauthorlink` (
    `refid` int(11) NOT NULL,
    `refauthid` int(11) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`,`refauthid`),
    KEY `FK_refauthlink_refid_idx` (`refid`),
    KEY `FK_refauthlink_refauthid_idx` (`refauthid`),
    CONSTRAINT `FK_refauthlink_refauthid` FOREIGN KEY (`refauthid`) REFERENCES `referenceauthors` (`refauthorid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_refauthlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `referenceauthors` (
    `refauthorid` int(11) NOT NULL AUTO_INCREMENT,
    `lastname` varchar(100) NOT NULL,
    `firstname` varchar(100) DEFAULT NULL,
    `middlename` varchar(100) DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refauthorid`),
    KEY `INDEX_refauthlastname` (`lastname`)
);

CREATE TABLE `referencechecklistlink` (
    `refid` int(11) NOT NULL,
    `clid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`,`clid`),
    KEY `FK_refcheckllistlink_refid_idx` (`refid`),
    KEY `FK_refcheckllistlink_clid_idx` (`clid`),
    CONSTRAINT `FK_refchecklistlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_refchecklistlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `referencecollectionlink` (
    `refid` int(11) NOT NULL,
    `collid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`,`collid`),
    KEY `FK_refcollectionlink_collid_idx` (`collid`),
    CONSTRAINT `FK_refcollectionlink_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_refcollectionlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `referenceobject` (
    `refid` int(11) NOT NULL AUTO_INCREMENT,
    `parentRefId` int(11) DEFAULT NULL,
    `ReferenceTypeId` int(11) DEFAULT NULL,
    `title` varchar(150) NOT NULL,
    `secondarytitle` varchar(250) DEFAULT NULL,
    `shorttitle` varchar(250) DEFAULT NULL,
    `tertiarytitle` varchar(250) DEFAULT NULL,
    `alternativetitle` varchar(250) DEFAULT NULL,
    `typework` varchar(150) DEFAULT NULL,
    `figures` varchar(150) DEFAULT NULL,
    `pubdate` varchar(45) DEFAULT NULL,
    `edition` varchar(45) DEFAULT NULL,
    `volume` varchar(45) DEFAULT NULL,
    `numbervolumes` varchar(45) DEFAULT NULL,
    `number` varchar(45) DEFAULT NULL,
    `pages` varchar(45) DEFAULT NULL,
    `section` varchar(45) DEFAULT NULL,
    `placeofpublication` varchar(45) DEFAULT NULL,
    `publisher` varchar(150) DEFAULT NULL,
    `isbn_issn` varchar(45) DEFAULT NULL,
    `url` varchar(150) DEFAULT NULL,
    `guid` varchar(45) DEFAULT NULL,
    `ispublished` varchar(45) DEFAULT NULL,
    `notes` varchar(45) DEFAULT NULL,
    `cheatauthors` varchar(250) DEFAULT NULL,
    `cheatcitation` varchar(250) DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`),
    KEY `INDEX_refobj_title` (`title`),
    KEY `FK_refobj_parentrefid_idx` (`parentRefId`),
    KEY `FK_refobj_typeid_idx` (`ReferenceTypeId`),
    CONSTRAINT `FK_refobj_parentrefid` FOREIGN KEY (`parentRefId`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_refobj_reftypeid` FOREIGN KEY (`ReferenceTypeId`) REFERENCES `referencetype` (`ReferenceTypeId`)
);

CREATE TABLE `referenceoccurlink` (
    `refid` int(11) NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`,`occid`),
    KEY `FK_refoccurlink_refid_idx` (`refid`),
    KEY `FK_refoccurlink_occid_idx` (`occid`),
    CONSTRAINT `FK_refoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_refoccurlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `referencetaxalink` (
    `refid` int(11) NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`refid`,`tid`),
    KEY `FK_reftaxalink_refid_idx` (`refid`),
    KEY `FK_reftaxalink_tid_idx` (`tid`),
    CONSTRAINT `FK_reftaxalink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_reftaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `referencetype` (
    `ReferenceTypeId` int(11) NOT NULL AUTO_INCREMENT,
    `ReferenceType` varchar(45) NOT NULL,
    `IsParent` int(11) DEFAULT NULL,
    `Title` varchar(45) DEFAULT NULL,
    `SecondaryTitle` varchar(45) DEFAULT NULL,
    `PlacePublished` varchar(45) DEFAULT NULL,
    `Publisher` varchar(45) DEFAULT NULL,
    `Volume` varchar(45) DEFAULT NULL,
    `NumberVolumes` varchar(45) DEFAULT NULL,
    `Number` varchar(45) DEFAULT NULL,
    `Pages` varchar(45) DEFAULT NULL,
    `Section` varchar(45) DEFAULT NULL,
    `TertiaryTitle` varchar(45) DEFAULT NULL,
    `Edition` varchar(45) DEFAULT NULL,
    `Date` varchar(45) DEFAULT NULL,
    `TypeWork` varchar(45) DEFAULT NULL,
    `ShortTitle` varchar(45) DEFAULT NULL,
    `AlternativeTitle` varchar(45) DEFAULT NULL,
    `ISBN_ISSN` varchar(45) DEFAULT NULL,
    `Figures` varchar(45) DEFAULT NULL,
    `addedByUid` int(11) DEFAULT NULL,
    `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ReferenceTypeId`),
    UNIQUE KEY `ReferenceType_UNIQUE` (`ReferenceType`)
);

CREATE TABLE `specprocessorprojects` (
    `spprid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `title` varchar(100) NOT NULL,
    `projecttype` varchar(45) DEFAULT NULL,
    `specKeyPattern` varchar(45) DEFAULT NULL,
    `patternReplace` varchar(45) DEFAULT NULL,
    `replaceStr` varchar(45) DEFAULT NULL,
    `speckeyretrieval` varchar(45) DEFAULT NULL,
    `coordX1` int(10) unsigned DEFAULT NULL,
    `coordX2` int(10) unsigned DEFAULT NULL,
    `coordY1` int(10) unsigned DEFAULT NULL,
    `coordY2` int(10) unsigned DEFAULT NULL,
    `sourcePath` varchar(250) DEFAULT NULL,
    `targetPath` varchar(250) DEFAULT NULL,
    `imgUrl` varchar(250) DEFAULT NULL,
    `webPixWidth` int(10) unsigned DEFAULT '1200',
    `tnPixWidth` int(10) unsigned DEFAULT '130',
    `lgPixWidth` int(10) unsigned DEFAULT '2400',
    `jpgcompression` int(11) DEFAULT '70',
    `createTnImg` int(10) unsigned DEFAULT '1',
    `createLgImg` int(10) unsigned DEFAULT '1',
    `source` varchar(45) DEFAULT NULL,
    `lastrundate` date DEFAULT NULL,
    `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`spprid`),
    KEY `FK_specprocessorprojects_coll` (`collid`),
    CONSTRAINT `FK_specprocessorprojects_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `taxa` (
    `TID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `kingdomId` int(11) NOT NULL DEFAULT '100',
    `RankId` smallint(5) unsigned DEFAULT NULL,
    `SciName` varchar(250) NOT NULL,
    `UnitInd1` varchar(1) DEFAULT NULL,
    `UnitName1` varchar(50) NOT NULL,
    `UnitInd2` varchar(1) DEFAULT NULL,
    `UnitName2` varchar(50) DEFAULT NULL,
    `UnitInd3` varchar(15) DEFAULT NULL,
    `UnitName3` varchar(35) DEFAULT NULL,
    `Author` varchar(100) DEFAULT NULL,
    `tidaccepted` int(10) unsigned DEFAULT NULL,
    `parenttid` int(10) unsigned DEFAULT NULL,
    `family` varchar(50) DEFAULT NULL,
    `Source` varchar(250) DEFAULT NULL,
    `Notes` varchar(250) DEFAULT NULL,
    `Hybrid` varchar(50) DEFAULT NULL,
    `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
    `modifiedUid` int(10) unsigned DEFAULT NULL,
    `modifiedTimeStamp` datetime DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`TID`),
    UNIQUE KEY `sciname_unique` (`SciName`,`kingdomId`,`RankId`),
    KEY `rankid_index` (`RankId`),
    KEY `unitname1_index` (`UnitName1`,`UnitName2`),
    KEY `FK_taxa_uid_idx` (`modifiedUid`),
    KEY `sciname_index` (`SciName`),
    KEY `idx_taxacreated` (`InitialTimeStamp`),
    KEY `kingdomid_index` (`kingdomId`),
    KEY `tidaccepted` (`tidaccepted`),
    KEY `parenttid` (`parenttid`),
    KEY `family` (`family`),
    CONSTRAINT `FK_taxa_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION,
    CONSTRAINT `FK_kingdom_id` FOREIGN KEY (`kingdomId`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE TABLE `taxadescrblock` (
    `tdbid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned NOT NULL,
    `caption` varchar(40) DEFAULT NULL,
    `source` varchar(250) DEFAULT NULL,
    `sourceurl` varchar(250) DEFAULT NULL,
    `language` varchar(45) DEFAULT 'English',
    `langid` int(11) DEFAULT NULL,
    `displaylevel` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = short descr, 2 = intermediate descr',
    `uid` int(10) unsigned NOT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tdbid`),
    KEY `FK_taxadesc_lang_idx` (`langid`),
    KEY `FK_taxadescrblock_tid_idx` (`tid`),
    CONSTRAINT `FK_taxadesc_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON UPDATE CASCADE
);

CREATE TABLE `taxadescrstmts` (
    `tdsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tdbid` int(10) unsigned NOT NULL,
    `heading` varchar(75) DEFAULT NULL,
    `statement` text NOT NULL,
    `displayheader` int(10) unsigned NOT NULL DEFAULT '1',
    `notes` varchar(250) DEFAULT NULL,
    `sortsequence` int(10) unsigned NOT NULL DEFAULT '89',
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tdsid`),
    KEY `FK_taxadescrstmts_tblock` (`tdbid`),
    CONSTRAINT `FK_taxadescrstmts_tblock` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `taxaenumtree` (
    `tid` int(10) unsigned NOT NULL,
    `parenttid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tid`,`parenttid`),
    KEY `FK_tet_taxa` (`tid`),
    KEY `FK_tet_taxa2` (`parenttid`),
    CONSTRAINT `FK_tet_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tet_taxa2` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `taxaidentifiers` (
    `tidentid` int(11) NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned NOT NULL,
    `name` varchar(45) NOT NULL,
    `identifier` varchar(255) NOT NULL,
    PRIMARY KEY (`tidentid`),
    UNIQUE KEY `tid_name_unique` (`tid`,`name`),
    KEY `identifier` (`identifier`),
    CONSTRAINT `FK_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `taxamaps` (
    `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tid` int(10) unsigned NOT NULL,
    `url` varchar(255) NOT NULL,
    `title` varchar(100) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mid`),
    KEY `FK_tid_idx` (`tid`),
    CONSTRAINT `FK_taxamaps_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
);

CREATE TABLE `taxavernaculars` (
    `TID` int(10) unsigned NOT NULL DEFAULT '0',
    `VernacularName` varchar(80) NOT NULL,
    `Language` varchar(15) DEFAULT NULL,
    `langid` int(11) DEFAULT NULL,
    `Source` varchar(50) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `username` varchar(45) DEFAULT NULL,
    `isupperterm` int(2) DEFAULT '0',
    `SortSequence` int(10) DEFAULT '50',
    `VID` int(10) NOT NULL AUTO_INCREMENT,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`VID`),
    UNIQUE KEY `unique-key` (`VernacularName`,`TID`,`langid`),
    KEY `tid1` (`TID`),
    KEY `vernacularsnames` (`VernacularName`),
    KEY `FK_vern_lang_idx` (`langid`),
    CONSTRAINT `FK_vern_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_vernaculars_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
);

CREATE TABLE `taxonkingdoms` (
    `kingdom_id` int(11) NOT NULL AUTO_INCREMENT,
    `kingdom_name` varchar(250) NOT NULL,
    PRIMARY KEY (`kingdom_id`),
    KEY `INDEX_kingdom_name` (`kingdom_name`),
    KEY `INDEX_kingdoms` (`kingdom_id`,`kingdom_name`)
);

CREATE TABLE `taxonunits` (
    `taxonunitid` int(11) NOT NULL AUTO_INCREMENT,
    `kingdomid` int(11) NOT NULL,
    `rankid` smallint(5) unsigned NOT NULL DEFAULT '0',
    `rankname` varchar(15) NOT NULL,
    `suffix` varchar(45) DEFAULT NULL,
    `dirparentrankid` smallint(6) NOT NULL,
    `reqparentrankid` smallint(6) DEFAULT NULL,
    `modifiedby` varchar(45) DEFAULT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`taxonunitid`),
    UNIQUE KEY `INDEX-Unique` (`kingdomid`,`rankid`),
    CONSTRAINT `FK-kingdomid` FOREIGN KEY (`kingdomid`) REFERENCES `taxonkingdoms` (`kingdom_id`) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE `tmattributes` (
    `stateid` int(10) unsigned NOT NULL,
    `occid` int(10) unsigned NOT NULL,
    `modifier` varchar(100) DEFAULT NULL,
    `xvalue` double(15,5) DEFAULT NULL,
    `imgid` int(10) unsigned DEFAULT NULL,
    `imagecoordinates` varchar(45) DEFAULT NULL,
    `source` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `statuscode` tinyint(4) DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `datelastmodified` datetime DEFAULT NULL,
    `createduid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`stateid`,`occid`),
    KEY `FK_tmattr_stateid_idx` (`stateid`),
    KEY `FK_tmattr_occid_idx` (`occid`),
    KEY `FK_tmattr_imgid_idx` (`imgid`),
    KEY `FK_attr_uidcreate_idx` (`createduid`),
    KEY `FK_tmattr_uidmodified_idx` (`modifieduid`),
    CONSTRAINT `FK_tmattr_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_tmattr_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tmattr_stateid` FOREIGN KEY (`stateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tmattr_uidcreate` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_tmattr_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `tmstates` (
    `stateid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `traitid` int(10) unsigned NOT NULL,
    `statecode` varchar(2) NOT NULL,
    `statename` varchar(75) NOT NULL,
    `description` varchar(250) DEFAULT NULL,
    `refurl` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `sortseq` int(11) DEFAULT NULL,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `datelastmodified` datetime DEFAULT NULL,
    `createduid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`stateid`),
    UNIQUE KEY `traitid_code_UNIQUE` (`traitid`,`statecode`),
    KEY `FK_tmstate_uidcreated_idx` (`createduid`),
    KEY `FK_tmstate_uidmodified_idx` (`modifieduid`),
    CONSTRAINT `FK_tmstates_traits` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON UPDATE CASCADE,
    CONSTRAINT `FK_tmstates_uidcreated` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_tmstates_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `tmtraitdependencies` (
    `traitid` int(10) unsigned NOT NULL,
    `parentstateid` int(10) unsigned NOT NULL,
    `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`traitid`,`parentstateid`),
    KEY `FK_tmdepend_traitid_idx` (`traitid`),
    KEY `FK_tmdepend_stateid_idx` (`parentstateid`),
    CONSTRAINT `FK_tmdepend_stateid` FOREIGN KEY (`parentstateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tmdepend_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `tmtraits` (
    `traitid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `traitname` varchar(100) NOT NULL,
    `traittype` varchar(2) NOT NULL DEFAULT 'UM',
    `units` varchar(45) DEFAULT NULL,
    `description` varchar(250) DEFAULT NULL,
    `refurl` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `dynamicProperties` text,
    `modifieduid` int(10) unsigned DEFAULT NULL,
    `datelastmodified` datetime DEFAULT NULL,
    `createduid` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`traitid`),
    KEY `traitsname` (`traitname`),
    KEY `FK_traits_uidcreated_idx` (`createduid`),
    KEY `FK_traits_uidmodified_idx` (`modifieduid`),
    CONSTRAINT `FK_traits_uidcreated` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_traits_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `tmtraittaxalink` (
    `traitid` int(10) unsigned NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    `relation` varchar(45) NOT NULL DEFAULT 'include',
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`traitid`,`tid`),
    KEY `FK_traittaxalink_traitid_idx` (`traitid`),
    KEY `FK_traittaxalink_tid_idx` (`tid`),
    CONSTRAINT `FK_traittaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_traittaxalink_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `uploaddetermtemp` (
    `occid` int(10) unsigned DEFAULT NULL,
    `collid` int(10) unsigned DEFAULT NULL,
    `dbpk` varchar(150) DEFAULT NULL,
    `identifiedBy` varchar(60) NOT NULL,
    `dateIdentified` varchar(45) NOT NULL,
    `dateIdentifiedInterpreted` date DEFAULT NULL,
    `sciname` varchar(100) NOT NULL,
    `scientificNameAuthorship` varchar(100) DEFAULT NULL,
    `identificationQualifier` varchar(45) DEFAULT NULL,
    `iscurrent` int(2) DEFAULT '0',
    `detType` varchar(45) DEFAULT NULL,
    `identificationReferences` varchar(255) DEFAULT NULL,
    `identificationRemarks` varchar(255) DEFAULT NULL,
    `sourceIdentifier` varchar(45) DEFAULT NULL,
    `sortsequence` int(10) unsigned DEFAULT '10',
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `Index_uploaddet_occid` (`occid`),
    KEY `Index_uploaddet_collid` (`collid`),
    KEY `Index_uploaddet_dbpk` (`dbpk`)
);

CREATE TABLE `uploadglossary` (
    `term` varchar(150) DEFAULT NULL,
    `definition` varchar(1000) DEFAULT NULL,
    `language` varchar(45) DEFAULT NULL,
    `source` varchar(1000) DEFAULT NULL,
    `author` varchar(250) DEFAULT NULL,
    `translator` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `resourceurl` varchar(600) DEFAULT NULL,
    `tidStr` varchar(100) DEFAULT NULL,
    `synonym` tinyint(1) DEFAULT NULL,
    `newGroupId` int(10) DEFAULT NULL,
    `currentGroupId` int(10) DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `term_index` (`term`),
    KEY `relatedterm_index` (`newGroupId`)
);

CREATE TABLE `uploadspecmap` (
    `usmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `uspid` int(10) unsigned NOT NULL,
    `sourcefield` varchar(45) NOT NULL,
    `symbdatatype` varchar(45) NOT NULL DEFAULT 'string' COMMENT 'string, numeric, datetime',
    `symbspecfield` varchar(45) NOT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`usmid`),
    UNIQUE KEY `Index_unique` (`uspid`,`symbspecfield`,`sourcefield`),
    CONSTRAINT `FK_uploadspecmap_usp` FOREIGN KEY (`uspid`) REFERENCES `uploadspecparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `uploadspecparameters` (
    `uspid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `CollID` int(10) unsigned NOT NULL,
    `UploadType` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = Direct; 2 = DiGIR; 3 = File',
    `title` varchar(45) NOT NULL,
    `Platform` varchar(45) DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker',
    `server` varchar(150) DEFAULT NULL,
    `port` int(10) unsigned DEFAULT NULL,
    `driver` varchar(45) DEFAULT NULL,
    `Code` varchar(45) DEFAULT NULL,
    `Path` varchar(500) DEFAULT NULL,
    `PkField` varchar(45) DEFAULT NULL,
    `Username` varchar(45) DEFAULT NULL,
    `Password` varchar(45) DEFAULT NULL,
    `SchemaName` varchar(150) DEFAULT NULL,
    `QueryStr` varchar(2000) DEFAULT NULL,
    `cleanupsp` varchar(45) DEFAULT NULL,
    `existingrecords` varchar(45) NOT NULL DEFAULT 'update',
    `dlmisvalid` int(10) unsigned DEFAULT '0',
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`uspid`),
    KEY `FK_uploadspecparameters_coll` (`CollID`),
    CONSTRAINT `FK_uploadspecparameters_coll` FOREIGN KEY (`CollID`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `uploadspectemp` (
    `upspid` int(50) unsigned NOT NULL AUTO_INCREMENT,
    `collid` int(10) unsigned NOT NULL,
    `dbpk` varchar(150) DEFAULT NULL,
    `occid` int(10) unsigned DEFAULT NULL,
    `basisOfRecord` varchar(32) DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
    `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
    `catalogNumber` varchar(32) DEFAULT NULL,
    `otherCatalogNumbers` varchar(255) DEFAULT NULL,
    `ownerInstitutionCode` varchar(32) DEFAULT NULL,
    `institutionID` varchar(255) DEFAULT NULL,
    `collectionID` varchar(255) DEFAULT NULL,
    `datasetID` varchar(255) DEFAULT NULL,
    `institutionCode` varchar(64) DEFAULT NULL,
    `collectionCode` varchar(64) DEFAULT NULL,
    `family` varchar(255) DEFAULT NULL,
    `verbatimScientificName` varchar(255) DEFAULT NULL,
    `sciname` varchar(255) DEFAULT NULL,
    `tid` int(10) unsigned DEFAULT NULL,
    `genus` varchar(255) DEFAULT NULL,
    `specificEpithet` varchar(255) DEFAULT NULL,
    `taxonRank` varchar(32) DEFAULT NULL,
    `infraspecificEpithet` varchar(255) DEFAULT NULL,
    `scientificNameAuthorship` varchar(255) DEFAULT NULL,
    `taxonRemarks` text,
    `identifiedBy` varchar(255) DEFAULT NULL,
    `dateIdentified` varchar(45) DEFAULT NULL,
    `identificationReferences` text,
    `identificationRemarks` text,
    `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
    `typeStatus` varchar(255) DEFAULT NULL,
    `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
    `recordNumberPrefix` varchar(45) DEFAULT NULL,
    `recordNumberSuffix` varchar(45) DEFAULT NULL,
    `recordNumber` varchar(32) DEFAULT NULL COMMENT 'Collector Number',
    `CollectorFamilyName` varchar(255) DEFAULT NULL COMMENT 'not DwC',
    `CollectorInitials` varchar(255) DEFAULT NULL COMMENT 'not DwC',
    `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
    `eventDate` date DEFAULT NULL,
    `year` int(10) DEFAULT NULL,
    `month` int(10) DEFAULT NULL,
    `day` int(10) DEFAULT NULL,
    `startDayOfYear` int(10) DEFAULT NULL,
    `endDayOfYear` int(10) DEFAULT NULL,
    `LatestDateCollected` date DEFAULT NULL,
    `verbatimEventDate` varchar(255) DEFAULT NULL,
    `habitat` text COMMENT 'Habitat, substrait, etc',
    `substrate` varchar(500) DEFAULT NULL,
    `host` varchar(250) DEFAULT NULL,
    `fieldNotes` text,
    `fieldnumber` varchar(45) DEFAULT NULL,
    `occurrenceRemarks` text COMMENT 'General Notes',
    `informationWithheld` varchar(250) DEFAULT NULL,
    `dataGeneralizations` varchar(250) DEFAULT NULL,
    `associatedOccurrences` text,
    `associatedMedia` text,
    `associatedReferences` text,
    `associatedSequences` text,
    `associatedTaxa` text COMMENT 'Associated Species',
    `dynamicProperties` text COMMENT 'Plant Description?',
    `verbatimAttributes` text,
    `behavior` varchar(500) DEFAULT NULL,
    `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
    `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
    `establishmentMeans` varchar(32) DEFAULT NULL COMMENT 'cultivated, invasive, escaped from captivity, wild, native',
    `lifeStage` varchar(45) DEFAULT NULL,
    `sex` varchar(45) DEFAULT NULL,
    `individualCount` varchar(45) DEFAULT NULL,
    `samplingProtocol` varchar(100) DEFAULT NULL,
    `samplingEffort` varchar(200) DEFAULT NULL,
    `preparations` varchar(100) DEFAULT NULL,
    `country` varchar(64) DEFAULT NULL,
    `stateProvince` varchar(255) DEFAULT NULL,
    `county` varchar(255) DEFAULT NULL,
    `municipality` varchar(255) DEFAULT NULL,
    `locality` text,
    `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = display locality, 1 = hide locality',
    `localitySecurityReason` varchar(100) DEFAULT NULL,
    `decimalLatitude` double DEFAULT NULL,
    `decimalLongitude` double DEFAULT NULL,
    `geodeticDatum` varchar(255) DEFAULT NULL,
    `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
    `footprintWKT` text,
    `coordinatePrecision` decimal(9,7) DEFAULT NULL,
    `locationRemarks` text,
    `verbatimCoordinates` varchar(255) DEFAULT NULL,
    `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
    `latDeg` int(11) DEFAULT NULL,
    `latMin` double DEFAULT NULL,
    `latSec` double DEFAULT NULL,
    `latNS` varchar(3) DEFAULT NULL,
    `lngDeg` int(11) DEFAULT NULL,
    `lngMin` double DEFAULT NULL,
    `lngSec` double DEFAULT NULL,
    `lngEW` varchar(3) DEFAULT NULL,
    `verbatimLatitude` varchar(45) DEFAULT NULL,
    `verbatimLongitude` varchar(45) DEFAULT NULL,
    `UtmNorthing` varchar(45) DEFAULT NULL,
    `UtmEasting` varchar(45) DEFAULT NULL,
    `UtmZoning` varchar(45) DEFAULT NULL,
    `trsTownship` varchar(45) DEFAULT NULL,
    `trsRange` varchar(45) DEFAULT NULL,
    `trsSection` varchar(45) DEFAULT NULL,
    `trsSectionDetails` varchar(45) DEFAULT NULL,
    `georeferencedBy` varchar(255) DEFAULT NULL,
    `georeferenceProtocol` varchar(255) DEFAULT NULL,
    `georeferenceSources` varchar(255) DEFAULT NULL,
    `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
    `georeferenceRemarks` varchar(255) DEFAULT NULL,
    `minimumElevationInMeters` int(6) DEFAULT NULL,
    `maximumElevationInMeters` int(6) DEFAULT NULL,
    `elevationNumber` varchar(45) DEFAULT NULL,
    `elevationUnits` varchar(45) DEFAULT NULL,
    `verbatimElevation` varchar(255) DEFAULT NULL,
    `minimumDepthInMeters` int(11) DEFAULT NULL,
    `maximumDepthInMeters` int(11) DEFAULT NULL,
    `verbatimDepth` varchar(50) DEFAULT NULL,
    `previousIdentifications` text,
    `disposition` varchar(32) DEFAULT NULL COMMENT 'Dups to',
    `storageLocation` varchar(100) DEFAULT NULL,
    `genericcolumn1` varchar(100) DEFAULT NULL,
    `genericcolumn2` varchar(100) DEFAULT NULL,
    `exsiccatiIdentifier` int(11) DEFAULT NULL,
    `exsiccatiNumber` varchar(45) DEFAULT NULL,
    `exsiccatiNotes` varchar(250) DEFAULT NULL,
    `paleoJSON` text,
    `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
    `language` varchar(20) DEFAULT NULL,
    `recordEnteredBy` varchar(250) DEFAULT NULL,
    `duplicateQuantity` int(10) unsigned DEFAULT NULL,
    `labelProject` varchar(45) DEFAULT NULL,
    `processingStatus` varchar(45) DEFAULT NULL,
    `tempfield01` text,
    `tempfield02` text,
    `tempfield03` text,
    `tempfield04` text,
    `tempfield05` text,
    `tempfield06` text,
    `tempfield07` text,
    `tempfield08` text,
    `tempfield09` text,
    `tempfield10` text,
    `tempfield11` text,
    `tempfield12` text,
    `tempfield13` text,
    `tempfield14` text,
    `tempfield15` text,
    `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`upspid`),
    KEY `FK_uploadspectemp_coll` (`collid`),
    KEY `Index_uploadspectemp_occid` (`occid`),
    KEY `Index_uploadspectemp_dbpk` (`dbpk`),
    KEY `Index_uploadspec_sciname` (`sciname`),
    KEY `Index_uploadspec_catalognumber` (`catalogNumber`),
    KEY `Index_uploadspec_othercatalognumbers` (`otherCatalogNumbers`),
    KEY `Index_decimalLatitude` (`decimalLatitude`),
    KEY `Index_ decimalLongitude` (`decimalLongitude`),
    KEY `Index_ institutionCode` (`institutionCode`),
    CONSTRAINT `FK_uploadspectemp_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `uploadspectemppoints`
(
    `geoID`  int(11) NOT NULL AUTO_INCREMENT,
    `collid` int(10) NOT NULL,
    `upspid` int(50) NOT NULL,
    `point`  point NOT NULL,
    PRIMARY KEY (`geoID`),
    KEY `upspid` (`upspid`),
    SPATIAL KEY `point` (`point`),
    KEY `collid` (`collid`)
) ENGINE=MyISAM;

CREATE TABLE `uploadtaxa` (
    `TID` int(10) unsigned DEFAULT NULL,
    `SourceId` int(10) unsigned DEFAULT NULL,
    `Family` varchar(50) DEFAULT NULL,
    `kingdomId` int(11) DEFAULT NULL,
    `kingdomName` varchar(250) DEFAULT NULL,
    `RankId` smallint(5) DEFAULT NULL,
    `RankName` varchar(45) DEFAULT NULL,
    `scinameinput` varchar(250) NOT NULL,
    `SciName` varchar(250) DEFAULT NULL,
    `UnitInd1` varchar(1) DEFAULT NULL,
    `UnitName1` varchar(50) DEFAULT NULL,
    `UnitInd2` varchar(1) DEFAULT NULL,
    `UnitName2` varchar(50) DEFAULT NULL,
    `UnitInd3` varchar(45) DEFAULT NULL,
    `UnitName3` varchar(35) DEFAULT NULL,
    `Author` varchar(100) DEFAULT NULL,
    `InfraAuthor` varchar(100) DEFAULT NULL,
    `Acceptance` int(10) unsigned DEFAULT '1' COMMENT '0 = not accepted; 1 = accepted',
    `TidAccepted` int(10) unsigned DEFAULT NULL,
    `AcceptedStr` varchar(250) DEFAULT NULL,
    `SourceAcceptedId` int(10) unsigned DEFAULT NULL,
    `UnacceptabilityReason` varchar(24) DEFAULT NULL,
    `ParentTid` int(10) DEFAULT NULL,
    `ParentStr` varchar(250) DEFAULT NULL,
    `SourceParentId` int(10) unsigned DEFAULT NULL,
    `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
    `Source` varchar(250) DEFAULT NULL,
    `Notes` varchar(250) DEFAULT NULL,
    `vernacular` varchar(250) DEFAULT NULL,
    `vernlang` varchar(15) DEFAULT NULL,
    `Hybrid` varchar(50) DEFAULT NULL,
    `ErrorStatus` varchar(150) DEFAULT NULL,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `UNIQUE_sciname` (`SciName`,`RankId`,`Author`,`AcceptedStr`),
    KEY `sourceID_index` (`SourceId`),
    KEY `sourceAcceptedId_index` (`SourceAcceptedId`),
    KEY `sciname_index` (`SciName`),
    KEY `scinameinput_index` (`scinameinput`),
    KEY `parentStr_index` (`ParentStr`),
    KEY `acceptedStr_index` (`AcceptedStr`),
    KEY `unitname1_index` (`UnitName1`),
    KEY `sourceParentId_index` (`SourceParentId`),
    KEY `acceptance_index` (`Acceptance`),
    KEY `kingdomId_index` (`kingdomId`),
    KEY `kingdomName_index` (`kingdomName`)
);

CREATE TABLE `useraccesstokens` (
    `tokid` int(11) NOT NULL AUTO_INCREMENT,
    `uid` int(10) unsigned NOT NULL,
    `token` varchar(50) NOT NULL,
    `device` varchar(50) DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tokid`),
    KEY `FK_useraccesstokens_uid_idx` (`uid`),
    CONSTRAINT `FK_useraccess_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `userroles` (
    `userroleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(10) unsigned NOT NULL,
    `role` varchar(45) NOT NULL,
    `tablename` varchar(45) DEFAULT NULL,
    `tablepk` int(11) DEFAULT NULL,
    `secondaryVariable` varchar(45) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `uidassignedby` int(10) unsigned DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`userroleid`),
    UNIQUE KEY `Unique_userroles` (`uid`,`role`,`tablename`,`tablepk`),
    KEY `FK_userroles_uid_idx` (`uid`),
    KEY `FK_usrroles_uid2_idx` (`uidassignedby`),
    KEY `Index_userroles_table` (`tablename`,`tablepk`),
    CONSTRAINT `FK_userrole_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_userrole_uid_assigned` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE `users` (
    `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `firstname` varchar(45) DEFAULT NULL,
    `middleinitial` varchar(2) DEFAULT NULL,
    `lastname` varchar(45) NOT NULL,
    `username` varchar(45) NOT NULL,
    `password` varchar(255) NOT NULL,
    `title` varchar(150) DEFAULT NULL,
    `institution` varchar(200) DEFAULT NULL,
    `department` varchar(200) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(50) DEFAULT NULL,
    `zip` varchar(15) DEFAULT NULL,
    `country` varchar(50) DEFAULT NULL,
    `phone` varchar(45) DEFAULT NULL,
    `email` varchar(100) NOT NULL,
    `RegionOfInterest` varchar(45) DEFAULT NULL,
    `url` varchar(400) DEFAULT NULL,
    `Biography` varchar(1500) DEFAULT NULL,
    `notes` varchar(255) DEFAULT NULL,
    `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
    `defaultrights` varchar(250) DEFAULT NULL,
    `rightsholder` varchar(250) DEFAULT NULL,
    `rights` varchar(250) DEFAULT NULL,
    `accessrights` varchar(250) DEFAULT NULL,
    `guid` varchar(45) DEFAULT NULL,
    `validated` varchar(45) NOT NULL DEFAULT '0',
    `usergroups` varchar(100) DEFAULT NULL,
    `lastlogindate` datetime,
    `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`uid`),
    UNIQUE KEY `Index_email` (`email`,`lastname`)
);

CREATE TABLE `usertaxonomy` (
    `idusertaxonomy` int(11) NOT NULL AUTO_INCREMENT,
    `uid` int(10) unsigned NOT NULL,
    `tid` int(10) unsigned NOT NULL,
    `editorstatus` varchar(45) DEFAULT NULL,
    `geographicScope` varchar(250) DEFAULT NULL,
    `notes` varchar(250) DEFAULT NULL,
    `modifiedUid` int(10) unsigned NOT NULL,
    `modifiedtimestamp` datetime DEFAULT NULL,
    `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idusertaxonomy`),
    UNIQUE KEY `usertaxonomy_UNIQUE` (`uid`,`tid`,`editorstatus`),
    KEY `FK_usertaxonomy_uid_idx` (`uid`),
    KEY `FK_usertaxonomy_tid_idx` (`tid`),
    CONSTRAINT `FK_usertaxonomy_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_usertaxonomy_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
);

delimiter ;;
CREATE TRIGGER `omoccurrences_insert` AFTER INSERT ON `omoccurrences` FOR EACH ROW BEGIN
    IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO omoccurpoints (`occid`,`point`)
		VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
    END IF;
END
;;
delimiter ;

delimiter ;;
CREATE TRIGGER `omoccurrences_update` AFTER UPDATE ON `omoccurrences` FOR EACH ROW BEGIN
    IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
            UPDATE omoccurpoints
            SET `point` = Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`)
            WHERE `occid` = NEW.`occid`;
        ELSE
			INSERT INTO omoccurpoints (`occid`,`point`)
			VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
        END IF;
    END IF;
END
;;
delimiter ;

delimiter ;;
CREATE TRIGGER `omoccurrences_delete` BEFORE DELETE ON `omoccurrences` FOR EACH ROW BEGIN
    DELETE FROM omoccurpoints WHERE `occid` = OLD.`occid`;
END
;;
delimiter ;

delimiter ;;
CREATE TRIGGER `uploadspectemp_insert` AFTER INSERT ON `uploadspectemp` FOR EACH ROW BEGIN
    IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO uploadspectemppoints (`collid`,`upspid`,`point`)
		VALUES (NEW.`collid`,NEW.`upspid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
    END IF;
END
;;
delimiter ;

delimiter ;;
CREATE TRIGGER `uploadspectemp_delete` BEFORE DELETE ON `uploadspectemp` FOR EACH ROW BEGIN
    DELETE FROM uploadspectemppoints WHERE `upspid` = OLD.`upspid`;
END
;;
delimiter ;

-- ----------------------------
-- Data
-- ----------------------------

INSERT INTO `lkupcountry` VALUES (1,'Andorra','AD','AND',20,'2011-06-01 01:35:22'),(2,'United Arab Emirates','AE','ARE',784,'2011-06-01 01:35:22'),(3,'Afghanistan','AF','AFG',4,'2011-06-01 01:35:22'),(4,'Antigua and Barbuda','AG','ATG',28,'2011-06-01 01:35:22'),(5,'Anguilla','AI','AIA',660,'2011-06-01 01:35:22'),(6,'Albania','AL','ALB',8,'2011-06-01 01:35:22'),(7,'Armenia','AM','ARM',51,'2011-06-01 01:35:22'),(8,'Netherlands Antilles','AN','ANT',530,'2011-06-01 01:35:22'),(9,'Angola','AO','AGO',24,'2011-06-01 01:35:22'),(10,'Antarctica','AQ',NULL,NULL,'2011-06-01 01:35:22'),(11,'Argentina','AR','ARG',32,'2011-06-01 01:35:22'),(12,'American Samoa','AS','ASM',16,'2011-06-01 01:35:22'),(13,'Austria','AT','AUT',40,'2011-06-01 01:35:22'),(14,'Australia','AU','AUS',36,'2011-06-01 01:35:22'),(15,'Aruba','AW','ABW',533,'2011-06-01 01:35:22'),(16,'Azerbaijan','AZ','AZE',31,'2011-06-01 01:35:22'),(17,'Bosnia and Herzegovina','BA','BIH',70,'2011-06-01 01:35:22'),(18,'Barbados','BB','BRB',52,'2011-06-01 01:35:22'),(19,'Bangladesh','BD','BGD',50,'2011-06-01 01:35:22'),(20,'Belgium','BE','BEL',56,'2011-06-01 01:35:22'),(21,'Burkina Faso','BF','BFA',854,'2011-06-01 01:35:22'),(22,'Bulgaria','BG','BGR',100,'2011-06-01 01:35:22'),(23,'Bahrain','BH','BHR',48,'2011-06-01 01:35:22'),(24,'Burundi','BI','BDI',108,'2011-06-01 01:35:22'),(25,'Benin','BJ','BEN',204,'2011-06-01 01:35:22'),(26,'Bermuda','BM','BMU',60,'2011-06-01 01:35:22'),(27,'Brunei Darussalam','BN','BRN',96,'2011-06-01 01:35:22'),(28,'Bolivia','BO','BOL',68,'2011-06-01 01:35:22'),(29,'Brazil','BR','BRA',76,'2011-06-01 01:35:22'),(30,'Bahamas','BS','BHS',44,'2011-06-01 01:35:22'),(31,'Bhutan','BT','BTN',64,'2011-06-01 01:35:22'),(32,'Bouvet Island','BV',NULL,NULL,'2011-06-01 01:35:22'),(33,'Botswana','BW','BWA',72,'2011-06-01 01:35:22'),(34,'Belarus','BY','BLR',112,'2011-06-01 01:35:22'),(35,'Belize','BZ','BLZ',84,'2011-06-01 01:35:22'),(36,'Canada','CA','CAN',124,'2011-06-01 01:35:22'),(37,'Cocos (Keeling) Islands','CC',NULL,NULL,'2011-06-01 01:35:22'),(38,'Congo, the Democratic Republic of the','CD','COD',180,'2011-06-01 01:35:22'),(39,'Central African Republic','CF','CAF',140,'2011-06-01 01:35:22'),(40,'Congo','CG','COG',178,'2011-06-01 01:35:22'),(41,'Switzerland','CH','CHE',756,'2011-06-01 01:35:22'),(42,'Cote D\'Ivoire','CI','CIV',384,'2011-06-01 01:35:22'),(43,'Cook Islands','CK','COK',184,'2011-06-01 01:35:22'),(44,'Chile','CL','CHL',152,'2011-06-01 01:35:22'),(45,'Cameroon','CM','CMR',120,'2011-06-01 01:35:22'),(46,'China','CN','CHN',156,'2011-06-01 01:35:22'),(47,'Colombia','CO','COL',170,'2011-06-01 01:35:22'),(48,'Costa Rica','CR','CRI',188,'2011-06-01 01:35:22'),(49,'Serbia and Montenegro','CS',NULL,NULL,'2011-06-01 01:35:22'),(50,'Cuba','CU','CUB',192,'2011-06-01 01:35:22'),(51,'Cape Verde','CV','CPV',132,'2011-06-01 01:35:22'),(52,'Christmas Island','CX',NULL,NULL,'2011-06-01 01:35:22'),(53,'Cyprus','CY','CYP',196,'2011-06-01 01:35:22'),(54,'Czech Republic','CZ','CZE',203,'2011-06-01 01:35:22'),(55,'Germany','DE','DEU',276,'2011-06-01 01:35:22'),(56,'Djibouti','DJ','DJI',262,'2011-06-01 01:35:22'),(57,'Denmark','DK','DNK',208,'2011-06-01 01:35:22'),(58,'Dominica','DM','DMA',212,'2011-06-01 01:35:22'),(59,'Dominican Republic','DO','DOM',214,'2011-06-01 01:35:22'),(60,'Algeria','DZ','DZA',12,'2011-06-01 01:35:22'),(61,'Ecuador','EC','ECU',218,'2011-06-01 01:35:22'),(62,'Estonia','EE','EST',233,'2011-06-01 01:35:22'),(63,'Egypt','EG','EGY',818,'2011-06-01 01:35:22'),(64,'Western Sahara','EH','ESH',732,'2011-06-01 01:35:22'),(65,'Eritrea','ER','ERI',232,'2011-06-01 01:35:22'),(66,'Spain','ES','ESP',724,'2011-06-01 01:35:22'),(67,'Ethiopia','ET','ETH',231,'2011-06-01 01:35:22'),(68,'Finland','FI','FIN',246,'2011-06-01 01:35:22'),(69,'Fiji','FJ','FJI',242,'2011-06-01 01:35:22'),(70,'Falkland  Islands (Malvinas)','FK','FLK',238,'2011-06-01 01:35:22'),(71,'Micronesia, Federated States of','FM','FSM',583,'2011-06-01 01:35:22'),(72,'Faroe Islands','FO','FRO',234,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (73,'France','FR','FRA',250,'2011-06-01 01:35:22'),(74,'Gabon','GA','GAB',266,'2011-06-01 01:35:22'),(75,'United Kingdom','GB','GBR',826,'2011-06-01 01:35:22'),(76,'Grenada','GD','GRD',308,'2011-06-01 01:35:22'),(77,'Georgia','GE','GEO',268,'2011-06-01 01:35:22'),(78,'French Guiana','GF','GUF',254,'2011-06-01 01:35:22'),(79,'Ghana','GH','GHA',288,'2011-06-01 01:35:22'),(80,'Gibraltar','GI','GIB',292,'2011-06-01 01:35:22'),(81,'Greenland','GL','GRL',304,'2011-06-01 01:35:22'),(82,'Gambia','GM','GMB',270,'2011-06-01 01:35:22'),(83,'Guinea','GN','GIN',324,'2011-06-01 01:35:22'),(84,'Guadeloupe','GP','GLP',312,'2011-06-01 01:35:22'),(85,'Equatorial Guinea','GQ','GNQ',226,'2011-06-01 01:35:22'),(86,'Greece','GR','GRC',300,'2011-06-01 01:35:22'),(87,'South Georgia and the South Sandwich Islands','GS',NULL,NULL,'2011-06-01 01:35:22'),(88,'Guatemala','GT','GTM',320,'2011-06-01 01:35:22'),(89,'Guam','GU','GUM',316,'2011-06-01 01:35:22'),(90,'Guinea-Bissau','GW','GNB',624,'2011-06-01 01:35:22'),(91,'Guyana','GY','GUY',328,'2011-06-01 01:35:22'),(92,'Hong Kong','HK','HKG',344,'2011-06-01 01:35:22'),(93,'Heard Island and Mcdonald Islands','HM',NULL,NULL,'2011-06-01 01:35:22'),(94,'Honduras','HN','HND',340,'2011-06-01 01:35:22'),(95,'Croatia','HR','HRV',191,'2011-06-01 01:35:22'),(96,'Haiti','HT','HTI',332,'2011-06-01 01:35:22'),(97,'Hungary','HU','HUN',348,'2011-06-01 01:35:22'),(98,'Indonesia','ID','IDN',360,'2011-06-01 01:35:22'),(99,'Ireland','IE','IRL',372,'2011-06-01 01:35:22'),(100,'Israel','IL','ISR',376,'2011-06-01 01:35:22'),(101,'India','IN','IND',356,'2011-06-01 01:35:22'),(102,'British Indian Ocean Territory','IO',NULL,NULL,'2011-06-01 01:35:22'),(103,'Iraq','IQ','IRQ',368,'2011-06-01 01:35:22'),(104,'Iran, Islamic Republic of','IR','IRN',364,'2011-06-01 01:35:22'),(105,'Iceland','IS','ISL',352,'2011-06-01 01:35:22'),(106,'Italy','IT','ITA',380,'2011-06-01 01:35:22'),(107,'Jamaica','JM','JAM',388,'2011-06-01 01:35:22'),(108,'Jordan','JO','JOR',400,'2011-06-01 01:35:22'),(109,'Japan','JP','JPN',392,'2011-06-01 01:35:22'),(110,'Kenya','KE','KEN',404,'2011-06-01 01:35:22'),(111,'Kyrgyzstan','KG','KGZ',417,'2011-06-01 01:35:22'),(112,'Cambodia','KH','KHM',116,'2011-06-01 01:35:22'),(113,'Kiribati','KI','KIR',296,'2011-06-01 01:35:22'),(114,'Comoros','KM','COM',174,'2011-06-01 01:35:22'),(115,'Saint Kitts and Nevis','KN','KNA',659,'2011-06-01 01:35:22'),(116,'Korea, Democratic People\'s Republic of','KP','PRK',408,'2011-06-01 01:35:22'),(117,'Korea, Republic of','KR','KOR',410,'2011-06-01 01:35:22'),(118,'Kuwait','KW','KWT',414,'2011-06-01 01:35:22'),(119,'Cayman Islands','KY','CYM',136,'2011-06-01 01:35:22'),(120,'Kazakhstan','KZ','KAZ',398,'2011-06-01 01:35:22'),(121,'Lao People\'s Democratic Republic','LA','LAO',418,'2011-06-01 01:35:22'),(122,'Lebanon','LB','LBN',422,'2011-06-01 01:35:22'),(123,'Saint Lucia','LC','LCA',662,'2011-06-01 01:35:22'),(124,'Liechtenstein','LI','LIE',438,'2011-06-01 01:35:22'),(125,'Sri Lanka','LK','LKA',144,'2011-06-01 01:35:22'),(126,'Liberia','LR','LBR',430,'2011-06-01 01:35:22'),(127,'Lesotho','LS','LSO',426,'2011-06-01 01:35:22'),(128,'Lithuania','LT','LTU',440,'2011-06-01 01:35:22'),(129,'Luxembourg','LU','LUX',442,'2011-06-01 01:35:22'),(130,'Latvia','LV','LVA',428,'2011-06-01 01:35:22'),(131,'Libyan Arab Jamahiriya','LY','LBY',434,'2011-06-01 01:35:22'),(132,'Morocco','MA','MAR',504,'2011-06-01 01:35:22'),(133,'Monaco','MC','MCO',492,'2011-06-01 01:35:22'),(134,'Moldova, Republic of','MD','MDA',498,'2011-06-01 01:35:22'),(135,'Madagascar','MG','MDG',450,'2011-06-01 01:35:22'),(136,'Marshall Islands','MH','MHL',584,'2011-06-01 01:35:22'),(137,'Macedonia, the Former Yugoslav Republic of','MK','MKD',807,'2011-06-01 01:35:22'),(138,'Mali','ML','MLI',466,'2011-06-01 01:35:22'),(139,'Myanmar','MM','MMR',104,'2011-06-01 01:35:22'),(140,'Mongolia','MN','MNG',496,'2011-06-01 01:35:22'),(141,'Macao','MO','MAC',446,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (142,'Northern Mariana Islands','MP','MNP',580,'2011-06-01 01:35:22'),(143,'Martinique','MQ','MTQ',474,'2011-06-01 01:35:22'),(144,'Mauritania','MR','MRT',478,'2011-06-01 01:35:22'),(145,'Montserrat','MS','MSR',500,'2011-06-01 01:35:22'),(146,'Malta','MT','MLT',470,'2011-06-01 01:35:22'),(147,'Mauritius','MU','MUS',480,'2011-06-01 01:35:22'),(148,'Maldives','MV','MDV',462,'2011-06-01 01:35:22'),(149,'Malawi','MW','MWI',454,'2011-06-01 01:35:22'),(150,'Mexico','MX','MEX',484,'2011-06-01 01:35:22'),(151,'Malaysia','MY','MYS',458,'2011-06-01 01:35:22'),(152,'Mozambique','MZ','MOZ',508,'2011-06-01 01:35:22'),(153,'Namibia','NA','NAM',516,'2011-06-01 01:35:22'),(154,'New Caledonia','NC','NCL',540,'2011-06-01 01:35:22'),(155,'Niger','NE','NER',562,'2011-06-01 01:35:22'),(156,'Norfolk Island','NF','NFK',574,'2011-06-01 01:35:22'),(157,'Nigeria','NG','NGA',566,'2011-06-01 01:35:22'),(158,'Nicaragua','NI','NIC',558,'2011-06-01 01:35:22'),(159,'Netherlands','NL','NLD',528,'2011-06-01 01:35:22'),(160,'Norway','NO','NOR',578,'2011-06-01 01:35:22'),(161,'Nepal','NP','NPL',524,'2011-06-01 01:35:22'),(162,'Nauru','NR','NRU',520,'2011-06-01 01:35:22'),(163,'Niue','NU','NIU',570,'2011-06-01 01:35:22'),(164,'New Zealand','NZ','NZL',554,'2011-06-01 01:35:22'),(165,'Oman','OM','OMN',512,'2011-06-01 01:35:22'),(166,'Panama','PA','PAN',591,'2011-06-01 01:35:22'),(167,'Peru','PE','PER',604,'2011-06-01 01:35:22'),(168,'French Polynesia','PF','PYF',258,'2011-06-01 01:35:22'),(169,'Papua New Guinea','PG','PNG',598,'2011-06-01 01:35:22'),(170,'Philippines','PH','PHL',608,'2011-06-01 01:35:22'),(171,'Pakistan','PK','PAK',586,'2011-06-01 01:35:22'),(172,'Poland','PL','POL',616,'2011-06-01 01:35:22'),(173,'Saint Pierre and Miquelon','PM','SPM',666,'2011-06-01 01:35:22'),(174,'Pitcairn','PN','PCN',612,'2011-06-01 01:35:22'),(175,'Puerto Rico','PR','PRI',630,'2011-06-01 01:35:22'),(176,'Palestinian Territory, Occupied','PS',NULL,NULL,'2011-06-01 01:35:22'),(177,'Portugal','PT','PRT',620,'2011-06-01 01:35:22'),(178,'Palau','PW','PLW',585,'2011-06-01 01:35:22'),(179,'Paraguay','PY','PRY',600,'2011-06-01 01:35:22'),(180,'Qatar','QA','QAT',634,'2011-06-01 01:35:22'),(181,'Reunion','RE','REU',638,'2011-06-01 01:35:22'),(182,'Romania','RO','ROM',642,'2011-06-01 01:35:22'),(183,'Russian Federation','RU','RUS',643,'2011-06-01 01:35:22'),(184,'Rwanda','RW','RWA',646,'2011-06-01 01:35:22'),(185,'Saudi Arabia','SA','SAU',682,'2011-06-01 01:35:22'),(186,'Solomon Islands','SB','SLB',90,'2011-06-01 01:35:22'),(187,'Seychelles','SC','SYC',690,'2011-06-01 01:35:22'),(188,'Sudan','SD','SDN',736,'2011-06-01 01:35:22'),(189,'Sweden','SE','SWE',752,'2011-06-01 01:35:22'),(190,'Singapore','SG','SGP',702,'2011-06-01 01:35:22'),(191,'Saint Helena','SH','SHN',654,'2011-06-01 01:35:22'),(192,'Slovenia','SI','SVN',705,'2011-06-01 01:35:22'),(193,'Svalbard and Jan Mayen','SJ','SJM',744,'2011-06-01 01:35:22'),(194,'Slovakia','SK','SVK',703,'2011-06-01 01:35:22'),(195,'Sierra Leone','SL','SLE',694,'2011-06-01 01:35:22'),(196,'San Marino','SM','SMR',674,'2011-06-01 01:35:22'),(197,'Senegal','SN','SEN',686,'2011-06-01 01:35:22'),(198,'Somalia','SO','SOM',706,'2011-06-01 01:35:22'),(199,'Suriname','SR','SUR',740,'2011-06-01 01:35:22'),(200,'Sao Tome and Principe','ST','STP',678,'2011-06-01 01:35:22'),(201,'El Salvador','SV','SLV',222,'2011-06-01 01:35:22'),(202,'Syrian Arab Republic','SY','SYR',760,'2011-06-01 01:35:22'),(203,'Swaziland','SZ','SWZ',748,'2011-06-01 01:35:22'),(204,'Turks and Caicos Islands','TC','TCA',796,'2011-06-01 01:35:22'),(205,'Chad','TD','TCD',148,'2011-06-01 01:35:22'),(206,'French Southern Territories','TF',NULL,NULL,'2011-06-01 01:35:22'),(207,'Togo','TG','TGO',768,'2011-06-01 01:35:22'),(208,'Thailand','TH','THA',764,'2011-06-01 01:35:22'),(209,'Tajikistan','TJ','TJK',762,'2011-06-01 01:35:22'),(210,'Tokelau','TK','TKL',772,'2011-06-01 01:35:22'),(211,'Timor-Leste','TL',NULL,NULL,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (212,'Turkmenistan','TM','TKM',795,'2011-06-01 01:35:22'),(213,'Tunisia','TN','TUN',788,'2011-06-01 01:35:22'),(214,'Tonga','TO','TON',776,'2011-06-01 01:35:22'),(215,'Turkey','TR','TUR',792,'2011-06-01 01:35:22'),(216,'Trinidad and Tobago','TT','TTO',780,'2011-06-01 01:35:22'),(217,'Tuvalu','TV','TUV',798,'2011-06-01 01:35:22'),(218,'Taiwan, Province of China','TW','TWN',158,'2011-06-01 01:35:22'),(219,'Tanzania, United Republic of','TZ','TZA',834,'2011-06-01 01:35:22'),(220,'Ukraine','UA','UKR',804,'2011-06-01 01:35:22'),(221,'Uganda','UG','UGA',800,'2011-06-01 01:35:22'),(222,'United States Minor Outlying Islands','UM',NULL,NULL,'2011-06-01 01:35:22'),(223,'United States','US','USA',840,'2011-06-01 01:35:22'),(224,'Uruguay','UY','URY',858,'2011-06-01 01:35:22'),(225,'Uzbekistan','UZ','UZB',860,'2011-06-01 01:35:22'),(226,'Holy See (Vatican City State)','VA','VAT',336,'2011-06-01 01:35:22'),(227,'Saint Vincent and the Grenadines','VC','VCT',670,'2011-06-01 01:35:22'),(228,'Venezuela','VE','VEN',862,'2011-06-01 01:35:22'),(229,'Virgin Islands, British','VG','VGB',92,'2011-06-01 01:35:22'),(230,'Virgin Islands,  U.s.','VI','VIR',850,'2011-06-01 01:35:22'),(231,'Viet Nam','VN','VNM',704,'2011-06-01 01:35:22'),(232,'Vanuatu','VU','VUT',548,'2011-06-01 01:35:22'),(233,'Wallis and Futuna','WF','WLF',876,'2011-06-01 01:35:22'),(234,'Samoa','WS','WSM',882,'2011-06-01 01:35:22'),(235,'Yemen','YE','YEM',887,'2011-06-01 01:35:22'),(236,'Mayotte','YT',NULL,NULL,'2011-06-01 01:35:22'),(237,'South Africa','ZA','ZAF',710,'2011-06-01 01:35:22'),(238,'Zambia','ZM','ZMB',894,'2011-06-01 01:35:22'),(239,'Zimbabwe','ZW','ZWE',716,'2011-06-01 01:35:22'),(256,'USA','US','USA',840,'2011-06-01 01:41:38');

INSERT INTO `lkupstateprovince` VALUES (1,256,'Alaska','AK','2011-06-01 01:45:09'),(2,256,'Alabama','AL','2011-06-01 01:45:09'),(3,256,'American Samoa','AS','2011-06-01 01:45:09'),(4,256,'Arizona','AZ','2011-06-01 01:45:09'),(5,256,'Arkansas','AR','2011-06-01 01:45:09'),(6,256,'California','CA','2011-06-01 01:45:09'),(7,256,'Colorado','CO','2011-06-01 01:45:09'),(8,256,'Connecticut','CT','2011-06-01 01:45:09'),(9,256,'Delaware','DE','2011-06-01 01:45:09'),(10,256,'District of Columbia','DC','2011-06-01 01:45:09'),(11,256,'Federated States of Micronesia','FM','2011-06-01 01:45:09'),(12,256,'Florida','FL','2011-06-01 01:45:09'),(13,256,'Georgia','GA','2011-06-01 01:45:09'),(14,256,'Guam','GU','2011-06-01 01:45:09'),(15,256,'Hawaii','HI','2011-06-01 01:45:09'),(16,256,'Idaho','ID','2011-06-01 01:45:09'),(17,256,'Illinois','IL','2011-06-01 01:45:09'),(18,256,'Indiana','IN','2011-06-01 01:45:09'),(19,256,'Iowa','IA','2011-06-01 01:45:09'),(20,256,'Kansas','KS','2011-06-01 01:45:09'),(21,256,'Kentucky','KY','2011-06-01 01:45:09'),(22,256,'Louisiana','LA','2011-06-01 01:45:09'),(23,256,'Maine','ME','2011-06-01 01:45:09'),(24,256,'Marshall Islands','MH','2011-06-01 01:45:09'),(25,256,'Maryland','MD','2011-06-01 01:45:09'),(26,256,'Massachusetts','MA','2011-06-01 01:45:09'),(27,256,'Michigan','MI','2011-06-01 01:45:09'),(28,256,'Minnesota','MN','2011-06-01 01:45:09'),(29,256,'Mississippi','MS','2011-06-01 01:45:09'),(30,256,'Missouri','MO','2011-06-01 01:45:09'),(31,256,'Montana','MT','2011-06-01 01:45:09'),(32,256,'Nebraska','NE','2011-06-01 01:45:09'),(33,256,'Nevada','NV','2011-06-01 01:45:09'),(34,256,'New Hampshire','NH','2011-06-01 01:45:09'),(35,256,'New Jersey','NJ','2011-06-01 01:45:09'),(36,256,'New Mexico','NM','2011-06-01 01:45:09'),(37,256,'New York','NY','2011-06-01 01:45:09'),(38,256,'North Carolina','NC','2011-06-01 01:45:09'),(39,256,'North Dakota','ND','2011-06-01 01:45:09'),(40,256,'Northern Mariana Islands','MP','2011-06-01 01:45:09'),(41,256,'Ohio','OH','2011-06-01 01:45:09'),(42,256,'Oklahoma','OK','2011-06-01 01:45:09'),(43,256,'Oregon','OR','2011-06-01 01:45:09'),(44,256,'Palau','PW','2011-06-01 01:45:09'),(45,256,'Pennsylvania','PA','2011-06-01 01:45:09'),(46,256,'Puerto Rico','PR','2011-06-01 01:45:09'),(47,256,'Rhode Island','RI','2011-06-01 01:45:09'),(48,256,'South Carolina','SC','2011-06-01 01:45:09'),(49,256,'South Dakota','SD','2011-06-01 01:45:09'),(50,256,'Tennessee','TN','2011-06-01 01:45:09'),(51,256,'Texas','TX','2011-06-01 01:45:09'),(52,256,'Utah','UT','2011-06-01 01:45:09'),(53,256,'Vermont','VT','2011-06-01 01:45:09'),(54,256,'Virgin Islands','VI','2011-06-01 01:45:09'),(55,256,'Virginia','VA','2011-06-01 01:45:09'),(56,256,'Washington','WA','2011-06-01 01:45:09'),(57,256,'West Virginia','WV','2011-06-01 01:45:09'),(58,256,'Wisconsin','WI','2011-06-01 01:45:09'),(59,256,'Wyoming','WY','2011-06-01 01:45:09'),(60,256,'Armed Forces Africa','AE','2011-06-01 01:45:09'),(61,256,'Armed Forces Americas (except Canada)','AA','2011-06-01 01:45:09'),(62,256,'Armed Forces Canada','AE','2011-06-01 01:45:09'),(63,256,'Armed Forces Europe','AE','2011-06-01 01:45:09'),(64,256,'Armed Forces Middle East','AE','2011-06-01 01:45:09'),(65,256,'Armed Forces Pacific','AP','2011-06-01 01:45:09'),(128,223,'Alaska','AK','2011-06-01 01:45:18'),(129,223,'Alabama','AL','2011-06-01 01:45:18'),(130,223,'American Samoa','AS','2011-06-01 01:45:18'),(131,223,'Arizona','AZ','2011-06-01 01:45:18'),(132,223,'Arkansas','AR','2011-06-01 01:45:18'),(133,223,'California','CA','2011-06-01 01:45:18'),(134,223,'Colorado','CO','2011-06-01 01:45:18'),(135,223,'Connecticut','CT','2011-06-01 01:45:18'),(136,223,'Delaware','DE','2011-06-01 01:45:18'),(137,223,'District of Columbia','DC','2011-06-01 01:45:18'),(138,223,'Federated States of Micronesia','FM','2011-06-01 01:45:18'),(139,223,'Florida','FL','2011-06-01 01:45:18'),(140,223,'Georgia','GA','2011-06-01 01:45:18'),(141,223,'Guam','GU','2011-06-01 01:45:18');
INSERT INTO `lkupstateprovince` VALUES (142,223,'Hawaii','HI','2011-06-01 01:45:18'),(143,223,'Idaho','ID','2011-06-01 01:45:18'),(144,223,'Illinois','IL','2011-06-01 01:45:18'),(145,223,'Indiana','IN','2011-06-01 01:45:18'),(146,223,'Iowa','IA','2011-06-01 01:45:18'),(147,223,'Kansas','KS','2011-06-01 01:45:18'),(148,223,'Kentucky','KY','2011-06-01 01:45:18'),(149,223,'Louisiana','LA','2011-06-01 01:45:18'),(150,223,'Maine','ME','2011-06-01 01:45:18'),(151,223,'Marshall Islands','MH','2011-06-01 01:45:18'),(152,223,'Maryland','MD','2011-06-01 01:45:18'),(153,223,'Massachusetts','MA','2011-06-01 01:45:18'),(154,223,'Michigan','MI','2011-06-01 01:45:18'),(155,223,'Minnesota','MN','2011-06-01 01:45:18'),(156,223,'Mississippi','MS','2011-06-01 01:45:18'),(157,223,'Missouri','MO','2011-06-01 01:45:18'),(158,223,'Montana','MT','2011-06-01 01:45:18'),(159,223,'Nebraska','NE','2011-06-01 01:45:18'),(160,223,'Nevada','NV','2011-06-01 01:45:18'),(161,223,'New Hampshire','NH','2011-06-01 01:45:18'),(162,223,'New Jersey','NJ','2011-06-01 01:45:18'),(163,223,'New Mexico','NM','2011-06-01 01:45:18'),(164,223,'New York','NY','2011-06-01 01:45:18'),(165,223,'North Carolina','NC','2011-06-01 01:45:18'),(166,223,'North Dakota','ND','2011-06-01 01:45:18'),(167,223,'Northern Mariana Islands','MP','2011-06-01 01:45:18'),(168,223,'Ohio','OH','2011-06-01 01:45:18'),(169,223,'Oklahoma','OK','2011-06-01 01:45:18'),(170,223,'Oregon','OR','2011-06-01 01:45:18'),(171,223,'Palau','PW','2011-06-01 01:45:18'),(172,223,'Pennsylvania','PA','2011-06-01 01:45:18'),(173,223,'Puerto Rico','PR','2011-06-01 01:45:18'),(174,223,'Rhode Island','RI','2011-06-01 01:45:18'),(175,223,'South Carolina','SC','2011-06-01 01:45:18'),(176,223,'South Dakota','SD','2011-06-01 01:45:18'),(177,223,'Tennessee','TN','2011-06-01 01:45:18'),(178,223,'Texas','TX','2011-06-01 01:45:18'),(179,223,'Utah','UT','2011-06-01 01:45:18'),(180,223,'Vermont','VT','2011-06-01 01:45:18'),(181,223,'Virgin Islands','VI','2011-06-01 01:45:18'),(182,223,'Virginia','VA','2011-06-01 01:45:18'),(183,223,'Washington','WA','2011-06-01 01:45:18'),(184,223,'West Virginia','WV','2011-06-01 01:45:18'),(185,223,'Wisconsin','WI','2011-06-01 01:45:18'),(186,223,'Wyoming','WY','2011-06-01 01:45:18'),(187,223,'Armed Forces Africa','AE','2011-06-01 01:45:18'),(188,223,'Armed Forces Americas (except Canada)','AA','2011-06-01 01:45:18'),(189,223,'Armed Forces Canada','AE','2011-06-01 01:45:18'),(190,223,'Armed Forces Europe','AE','2011-06-01 01:45:18'),(191,223,'Armed Forces Middle East','AE','2011-06-01 01:45:18'),(192,223,'Armed Forces Pacific','AP','2011-06-01 01:45:18');

INSERT INTO `lkupcounty` VALUES (1,164,'Suffolk','2011-06-01 02:06:40'),(2,173,'Adjuntas','2011-06-01 02:06:40'),(3,173,'Aguada','2011-06-01 02:06:40'),(4,173,'Aguadilla','2011-06-01 02:06:40'),(5,155,'Mower','2011-06-01 02:06:40'),(6,172,'Susquehanna','2011-06-01 02:06:40'),(7,158,'Glacier','2011-06-01 02:06:40'),(8,179,'Garfield','2011-06-01 02:06:40'),(9,173,'Maricao','2011-06-01 02:06:40'),(10,173,'Anasco','2011-06-01 02:06:40'),(11,173,'Utuado','2011-06-01 02:06:40'),(12,173,'Arecibo','2011-06-01 02:06:40'),(13,173,'Barceloneta','2011-06-01 02:06:40'),(14,173,'Cabo rojo','2011-06-01 02:06:40'),(15,173,'Penuelas','2011-06-01 02:06:40'),(16,173,'Camuy','2011-06-01 02:06:40'),(17,173,'Lares','2011-06-01 02:06:40'),(18,173,'San german','2011-06-01 02:06:40'),(19,173,'Sabana grande','2011-06-01 02:06:40'),(20,173,'Ciales','2011-06-01 02:06:40'),(21,173,'Dorado','2011-06-01 02:06:40'),(22,173,'Guanica','2011-06-01 02:06:40'),(23,173,'Florida','2011-06-01 02:06:40'),(24,173,'Guayanilla','2011-06-01 02:06:40'),(25,173,'Hatillo','2011-06-01 02:06:40'),(26,173,'Hormigueros','2011-06-01 02:06:40'),(27,173,'Isabela','2011-06-01 02:06:40'),(28,173,'Jayuya','2011-06-01 02:06:40'),(29,173,'Lajas','2011-06-01 02:06:40'),(30,173,'Las marias','2011-06-01 02:06:40'),(31,173,'Manati','2011-06-01 02:06:40'),(32,173,'Moca','2011-06-01 02:06:40'),(33,173,'Rincon','2011-06-01 02:06:40'),(34,173,'Quebradillas','2011-06-01 02:06:40'),(35,173,'Mayaguez','2011-06-01 02:06:40'),(36,173,'San sebastian','2011-06-01 02:06:40'),(37,173,'Morovis','2011-06-01 02:06:40'),(38,173,'Vega alta','2011-06-01 02:06:40'),(39,173,'Vega baja','2011-06-01 02:06:40'),(40,173,'Yauco','2011-06-01 02:06:40'),(41,173,'Aguas buenas','2011-06-01 02:06:40'),(42,173,'Guayama','2011-06-01 02:06:40'),(43,173,'Aibonito','2011-06-01 02:06:40'),(44,173,'Maunabo','2011-06-01 02:06:40'),(45,173,'Arroyo','2011-06-01 02:06:40'),(46,173,'Ponce','2011-06-01 02:06:40'),(47,173,'Naguabo','2011-06-01 02:06:40'),(48,173,'Naranjito','2011-06-01 02:06:40'),(49,173,'Orocovis','2011-06-01 02:06:40'),(50,173,'Rio grande','2011-06-01 02:06:40'),(51,173,'Patillas','2011-06-01 02:06:40'),(52,173,'Caguas','2011-06-01 02:06:40'),(53,173,'Canovanas','2011-06-01 02:06:40'),(54,173,'Ceiba','2011-06-01 02:06:40'),(55,173,'Cayey','2011-06-01 02:06:40'),(56,173,'Fajardo','2011-06-01 02:06:40'),(57,173,'Cidra','2011-06-01 02:06:40'),(58,173,'Humacao','2011-06-01 02:06:40'),(59,173,'Salinas','2011-06-01 02:06:40'),(60,173,'San lorenzo','2011-06-01 02:06:40'),(61,173,'Santa isabel','2011-06-01 02:06:40'),(62,173,'Vieques','2011-06-01 02:06:40'),(63,173,'Villalba','2011-06-01 02:06:40'),(64,173,'Yabucoa','2011-06-01 02:06:40'),(65,173,'Coamo','2011-06-01 02:06:40'),(66,173,'Las piedras','2011-06-01 02:06:40'),(67,173,'Loiza','2011-06-01 02:06:40'),(68,173,'Luquillo','2011-06-01 02:06:40'),(69,173,'Culebra','2011-06-01 02:06:40'),(70,173,'Juncos','2011-06-01 02:06:40'),(71,173,'Gurabo','2011-06-01 02:06:40'),(72,173,'Comerio','2011-06-01 02:06:40'),(73,173,'Corozal','2011-06-01 02:06:40'),(74,173,'Barranquitas','2011-06-01 02:06:40'),(75,173,'Juana diaz','2011-06-01 02:06:40'),(76,181,'Saint thomas','2011-06-01 02:06:40'),(77,181,'Saint croix','2011-06-01 02:06:40'),(78,181,'Saint john','2011-06-01 02:06:40'),(79,173,'San juan','2011-06-01 02:06:40'),(80,173,'Bayamon','2011-06-01 02:06:40'),(81,173,'Toa baja','2011-06-01 02:06:40'),(82,173,'Toa alta','2011-06-01 02:06:40'),(83,173,'Catano','2011-06-01 02:06:40'),(84,173,'Guaynabo','2011-06-01 02:06:40'),(85,173,'Trujillo alto','2011-06-01 02:06:40'),(86,173,'Carolina','2011-06-01 02:06:40'),(87,153,'Hampden','2011-06-01 02:06:40'),(88,153,'Hampshire','2011-06-01 02:06:40'),(89,153,'Worcester','2011-06-01 02:06:40'),(90,153,'Berkshire','2011-06-01 02:06:40'),(91,153,'Franklin','2011-06-01 02:06:40'),(92,153,'Middlesex','2011-06-01 02:06:40'),(93,153,'Essex','2011-06-01 02:06:40'),(94,153,'Plymouth','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (95,153,'Norfolk','2011-06-01 02:06:40'),(96,153,'Bristol','2011-06-01 02:06:40'),(97,153,'Suffolk','2011-06-01 02:06:40'),(98,153,'Barnstable','2011-06-01 02:06:40'),(99,153,'Dukes','2011-06-01 02:06:40'),(100,153,'Nantucket','2011-06-01 02:06:40'),(101,174,'Newport','2011-06-01 02:06:40'),(102,174,'Providence','2011-06-01 02:06:40'),(103,174,'Washington','2011-06-01 02:06:40'),(104,174,'Bristol','2011-06-01 02:06:40'),(105,174,'Kent','2011-06-01 02:06:40'),(106,161,'Hillsborough','2011-06-01 02:06:40'),(107,161,'Rockingham','2011-06-01 02:06:40'),(108,161,'Merrimack','2011-06-01 02:06:40'),(109,161,'Grafton','2011-06-01 02:06:40'),(110,161,'Belknap','2011-06-01 02:06:40'),(111,161,'Carroll','2011-06-01 02:06:40'),(112,161,'Sullivan','2011-06-01 02:06:40'),(113,161,'Cheshire','2011-06-01 02:06:40'),(114,161,'Coos','2011-06-01 02:06:40'),(115,161,'Strafford','2011-06-01 02:06:40'),(116,150,'York','2011-06-01 02:06:40'),(117,150,'Cumberland','2011-06-01 02:06:40'),(118,150,'Sagadahoc','2011-06-01 02:06:40'),(119,150,'Oxford','2011-06-01 02:06:40'),(120,150,'Androscoggin','2011-06-01 02:06:40'),(121,150,'Franklin','2011-06-01 02:06:40'),(122,150,'Kennebec','2011-06-01 02:06:40'),(123,150,'Lincoln','2011-06-01 02:06:40'),(124,150,'Waldo','2011-06-01 02:06:40'),(125,150,'Penobscot','2011-06-01 02:06:40'),(126,150,'Piscataquis','2011-06-01 02:06:40'),(127,150,'Hancock','2011-06-01 02:06:40'),(128,150,'Washington','2011-06-01 02:06:40'),(129,150,'Aroostook','2011-06-01 02:06:40'),(130,150,'Somerset','2011-06-01 02:06:40'),(131,150,'Piscataguis','2011-06-01 02:06:40'),(132,150,'Knox','2011-06-01 02:06:40'),(133,180,'Windsor','2011-06-01 02:06:40'),(134,180,'Orange','2011-06-01 02:06:40'),(135,180,'Caledonia','2011-06-01 02:06:40'),(136,180,'Windham','2011-06-01 02:06:40'),(137,180,'Bennington','2011-06-01 02:06:40'),(138,180,'Chittenden','2011-06-01 02:06:40'),(139,180,'Grand isle','2011-06-01 02:06:40'),(140,180,'Franklin','2011-06-01 02:06:40'),(141,180,'Lamoille','2011-06-01 02:06:40'),(142,180,'Addison','2011-06-01 02:06:40'),(143,180,'Washington','2011-06-01 02:06:40'),(144,180,'Rutland','2011-06-01 02:06:40'),(145,180,'Orleans','2011-06-01 02:06:40'),(146,180,'Essex','2011-06-01 02:06:40'),(147,135,'Hartford','2011-06-01 02:06:40'),(148,135,'Litchfield','2011-06-01 02:06:40'),(149,135,'Tolland','2011-06-01 02:06:40'),(150,135,'Windham','2011-06-01 02:06:40'),(151,135,'New london','2011-06-01 02:06:40'),(152,135,'New haven','2011-06-01 02:06:40'),(153,135,'Fairfield','2011-06-01 02:06:40'),(154,135,'Middlesex','2011-06-01 02:06:40'),(155,162,'Middlesex','2011-06-01 02:06:40'),(156,162,'Hudson','2011-06-01 02:06:40'),(157,162,'Essex','2011-06-01 02:06:40'),(158,162,'Morris','2011-06-01 02:06:40'),(159,162,'Bergen','2011-06-01 02:06:40'),(160,162,'Passaic','2011-06-01 02:06:40'),(161,162,'Union','2011-06-01 02:06:40'),(162,162,'Somerset','2011-06-01 02:06:40'),(163,162,'Sussex','2011-06-01 02:06:40'),(164,162,'Monmouth','2011-06-01 02:06:40'),(165,162,'Warren','2011-06-01 02:06:40'),(166,162,'Hunterdon','2011-06-01 02:06:40'),(167,162,'Salem','2011-06-01 02:06:40'),(168,162,'Camden','2011-06-01 02:06:40'),(169,162,'Ocean','2011-06-01 02:06:40'),(170,162,'Burlington','2011-06-01 02:06:40'),(171,162,'Gloucester','2011-06-01 02:06:40'),(172,162,'Atlantic','2011-06-01 02:06:40'),(173,162,'Cape may','2011-06-01 02:06:40'),(174,162,'Cumberland','2011-06-01 02:06:40'),(175,162,'Mercer','2011-06-01 02:06:40'),(176,164,'New york','2011-06-01 02:06:40'),(177,164,'Richmond','2011-06-01 02:06:40'),(178,164,'Bronx','2011-06-01 02:06:40'),(179,164,'Westchester','2011-06-01 02:06:40'),(180,164,'Putnam','2011-06-01 02:06:40'),(181,164,'Rockland','2011-06-01 02:06:40'),(182,164,'Orange','2011-06-01 02:06:40'),(183,164,'Nassau','2011-06-01 02:06:40'),(184,164,'Queens','2011-06-01 02:06:40'),(185,164,'Kings','2011-06-01 02:06:40'),(186,164,'Albany','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (187,164,'Schenectady','2011-06-01 02:06:40'),(188,164,'Montgomery','2011-06-01 02:06:40'),(189,164,'Greene','2011-06-01 02:06:40'),(190,164,'Columbia','2011-06-01 02:06:40'),(191,164,'Rensselaer','2011-06-01 02:06:40'),(192,164,'Saratoga','2011-06-01 02:06:40'),(193,164,'Fulton','2011-06-01 02:06:40'),(194,164,'Schoharie','2011-06-01 02:06:40'),(195,164,'Washington','2011-06-01 02:06:40'),(196,164,'Otsego','2011-06-01 02:06:40'),(197,164,'Hamilton','2011-06-01 02:06:40'),(198,164,'Delaware','2011-06-01 02:06:40'),(199,164,'Ulster','2011-06-01 02:06:40'),(200,164,'Dutchess','2011-06-01 02:06:40'),(201,164,'Sullivan','2011-06-01 02:06:40'),(202,164,'Warren','2011-06-01 02:06:40'),(203,164,'Essex','2011-06-01 02:06:40'),(204,164,'Clinton','2011-06-01 02:06:40'),(205,164,'Franklin','2011-06-01 02:06:40'),(206,164,'Saint lawrence','2011-06-01 02:06:40'),(207,164,'Onondaga','2011-06-01 02:06:40'),(208,164,'Cayuga','2011-06-01 02:06:40'),(209,164,'Oswego','2011-06-01 02:06:40'),(210,164,'Madison','2011-06-01 02:06:40'),(211,164,'Cortland','2011-06-01 02:06:40'),(212,164,'Tompkins','2011-06-01 02:06:40'),(213,164,'Oneida','2011-06-01 02:06:40'),(214,164,'Seneca','2011-06-01 02:06:40'),(215,164,'Chenango','2011-06-01 02:06:40'),(216,164,'Wayne','2011-06-01 02:06:40'),(217,164,'Lewis','2011-06-01 02:06:40'),(218,164,'Herkimer','2011-06-01 02:06:40'),(219,164,'Jefferson','2011-06-01 02:06:40'),(220,164,'Tioga','2011-06-01 02:06:40'),(221,164,'Broome','2011-06-01 02:06:40'),(222,164,'Erie','2011-06-01 02:06:40'),(223,164,'Genesee','2011-06-01 02:06:40'),(224,164,'Niagara','2011-06-01 02:06:40'),(225,164,'Wyoming','2011-06-01 02:06:40'),(226,164,'Allegany','2011-06-01 02:06:40'),(227,164,'Cattaraugus','2011-06-01 02:06:40'),(228,164,'Chautauqua','2011-06-01 02:06:40'),(229,164,'Orleans','2011-06-01 02:06:40'),(230,164,'Monroe','2011-06-01 02:06:40'),(231,164,'Livingston','2011-06-01 02:06:40'),(232,164,'Yates','2011-06-01 02:06:40'),(233,164,'Ontario','2011-06-01 02:06:40'),(234,164,'Steuben','2011-06-01 02:06:40'),(235,164,'Schuyler','2011-06-01 02:06:40'),(236,164,'Chemung','2011-06-01 02:06:40'),(237,172,'Beaver','2011-06-01 02:06:40'),(238,172,'Washington','2011-06-01 02:06:40'),(239,172,'Allegheny','2011-06-01 02:06:40'),(240,172,'Fayette','2011-06-01 02:06:40'),(241,172,'Westmoreland','2011-06-01 02:06:40'),(242,172,'Greene','2011-06-01 02:06:40'),(243,172,'Somerset','2011-06-01 02:06:40'),(244,172,'Bedford','2011-06-01 02:06:40'),(245,172,'Fulton','2011-06-01 02:06:40'),(246,172,'Armstrong','2011-06-01 02:06:40'),(247,172,'Indiana','2011-06-01 02:06:40'),(248,172,'Jefferson','2011-06-01 02:06:40'),(249,172,'Cambria','2011-06-01 02:06:40'),(250,172,'Clearfield','2011-06-01 02:06:40'),(251,172,'Elk','2011-06-01 02:06:40'),(252,172,'Forest','2011-06-01 02:06:40'),(253,172,'Cameron','2011-06-01 02:06:40'),(254,172,'Butler','2011-06-01 02:06:40'),(255,172,'Clarion','2011-06-01 02:06:40'),(256,172,'Lawrence','2011-06-01 02:06:40'),(257,172,'Crawford','2011-06-01 02:06:40'),(258,172,'Mercer','2011-06-01 02:06:40'),(259,172,'Venango','2011-06-01 02:06:40'),(260,172,'Warren','2011-06-01 02:06:40'),(261,172,'Mckean','2011-06-01 02:06:40'),(262,172,'Erie','2011-06-01 02:06:40'),(263,172,'Blair','2011-06-01 02:06:40'),(264,172,'Huntingdon','2011-06-01 02:06:40'),(265,172,'Centre','2011-06-01 02:06:40'),(266,172,'Potter','2011-06-01 02:06:40'),(267,172,'Clinton','2011-06-01 02:06:40'),(268,172,'Tioga','2011-06-01 02:06:40'),(269,172,'Bradford','2011-06-01 02:06:40'),(270,172,'Cumberland','2011-06-01 02:06:40'),(271,172,'Mifflin','2011-06-01 02:06:40'),(272,172,'Lebanon','2011-06-01 02:06:40'),(273,172,'Dauphin','2011-06-01 02:06:40'),(274,172,'Perry','2011-06-01 02:06:40'),(275,172,'Juniata','2011-06-01 02:06:40'),(276,172,'Northumberland','2011-06-01 02:06:40'),(277,172,'York','2011-06-01 02:06:40'),(278,172,'Lancaster','2011-06-01 02:06:40'),(279,172,'Franklin','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (280,172,'Adams','2011-06-01 02:06:40'),(281,172,'Lycoming','2011-06-01 02:06:40'),(282,172,'Sullivan','2011-06-01 02:06:40'),(283,172,'Union','2011-06-01 02:06:40'),(284,172,'Snyder','2011-06-01 02:06:40'),(285,172,'Columbia','2011-06-01 02:06:40'),(286,172,'Montour','2011-06-01 02:06:40'),(287,172,'Schuylkill','2011-06-01 02:06:40'),(288,172,'Northampton','2011-06-01 02:06:40'),(289,172,'Lehigh','2011-06-01 02:06:40'),(290,172,'Carbon','2011-06-01 02:06:40'),(291,172,'Bucks','2011-06-01 02:06:40'),(292,172,'Montgomery','2011-06-01 02:06:40'),(293,172,'Berks','2011-06-01 02:06:40'),(294,172,'Monroe','2011-06-01 02:06:40'),(295,172,'Luzerne','2011-06-01 02:06:40'),(296,172,'Pike','2011-06-01 02:06:40'),(297,172,'Lackawanna','2011-06-01 02:06:40'),(298,172,'Wayne','2011-06-01 02:06:40'),(299,172,'Wyoming','2011-06-01 02:06:40'),(300,172,'Delaware','2011-06-01 02:06:40'),(301,172,'Philadelphia','2011-06-01 02:06:40'),(302,172,'Chester','2011-06-01 02:06:40'),(303,136,'New castle','2011-06-01 02:06:40'),(304,136,'Kent','2011-06-01 02:06:40'),(305,136,'Sussex','2011-06-01 02:06:40'),(306,137,'District of Columbia','2011-06-01 02:06:40'),(307,182,'Loudoun','2011-06-01 02:06:40'),(308,182,'Rappahannock','2011-06-01 02:06:40'),(309,182,'Manassas city','2011-06-01 02:06:40'),(310,182,'Manassas Park City','2011-06-01 02:06:40'),(311,182,'Fauquier','2011-06-01 02:06:40'),(312,182,'Fairfax','2011-06-01 02:06:40'),(313,182,'Prince william','2011-06-01 02:06:40'),(314,152,'Charles','2011-06-01 02:06:40'),(315,152,'Saint marys','2011-06-01 02:06:40'),(316,152,'Prince georges','2011-06-01 02:06:40'),(317,152,'Calvert','2011-06-01 02:06:40'),(318,152,'Howard','2011-06-01 02:06:40'),(319,152,'Anne arundel','2011-06-01 02:06:40'),(320,152,'Montgomery','2011-06-01 02:06:40'),(321,152,'Harford','2011-06-01 02:06:40'),(322,152,'Baltimore','2011-06-01 02:06:40'),(323,152,'Carroll','2011-06-01 02:06:40'),(324,152,'Baltimore city','2011-06-01 02:06:40'),(325,152,'Allegany','2011-06-01 02:06:40'),(326,152,'Garrett','2011-06-01 02:06:40'),(327,152,'Talbot','2011-06-01 02:06:40'),(328,152,'Queen annes','2011-06-01 02:06:40'),(329,152,'Caroline','2011-06-01 02:06:40'),(330,152,'Kent','2011-06-01 02:06:40'),(331,152,'Dorchester','2011-06-01 02:06:40'),(332,152,'Frederick','2011-06-01 02:06:40'),(333,152,'Washington','2011-06-01 02:06:40'),(334,152,'Wicomico','2011-06-01 02:06:40'),(335,152,'Worcester','2011-06-01 02:06:40'),(336,152,'Somerset','2011-06-01 02:06:40'),(337,152,'Cecil','2011-06-01 02:06:40'),(338,182,'Fairfax city','2011-06-01 02:06:40'),(339,182,'Falls Church City','2011-06-01 02:06:40'),(340,182,'Arlington','2011-06-01 02:06:40'),(341,182,'Alexandria city','2011-06-01 02:06:40'),(342,182,'Fredericksburg city','2011-06-01 02:06:40'),(343,182,'Stafford','2011-06-01 02:06:40'),(344,182,'Spotsylvania','2011-06-01 02:06:40'),(345,182,'Caroline','2011-06-01 02:06:40'),(346,182,'Northumberland','2011-06-01 02:06:40'),(347,182,'Orange','2011-06-01 02:06:40'),(348,182,'Essex','2011-06-01 02:06:40'),(349,182,'Westmoreland','2011-06-01 02:06:40'),(350,182,'King george','2011-06-01 02:06:40'),(351,182,'Richmond','2011-06-01 02:06:40'),(352,182,'Lancaster','2011-06-01 02:06:40'),(353,182,'Winchester city','2011-06-01 02:06:40'),(354,182,'Frederick','2011-06-01 02:06:40'),(355,182,'Warren','2011-06-01 02:06:40'),(356,182,'Clarke','2011-06-01 02:06:40'),(357,182,'Shenandoah','2011-06-01 02:06:40'),(358,182,'Page','2011-06-01 02:06:40'),(359,182,'Culpeper','2011-06-01 02:06:40'),(360,182,'Madison','2011-06-01 02:06:40'),(361,182,'Harrisonburg city','2011-06-01 02:06:40'),(362,182,'Rockingham','2011-06-01 02:06:40'),(363,182,'Augusta','2011-06-01 02:06:40'),(364,182,'Albemarle','2011-06-01 02:06:40'),(365,182,'Charlottesville city','2011-06-01 02:06:40'),(366,182,'Nelson','2011-06-01 02:06:40'),(367,182,'Greene','2011-06-01 02:06:40'),(368,182,'Fluvanna','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (369,182,'Waynesboro city','2011-06-01 02:06:40'),(370,182,'Gloucester','2011-06-01 02:06:40'),(371,182,'Amelia','2011-06-01 02:06:40'),(372,182,'Buckingham','2011-06-01 02:06:40'),(373,182,'Hanover','2011-06-01 02:06:40'),(374,182,'King william','2011-06-01 02:06:40'),(375,182,'New kent','2011-06-01 02:06:40'),(376,182,'Goochland','2011-06-01 02:06:40'),(377,182,'Mathews','2011-06-01 02:06:40'),(378,182,'King and queen','2011-06-01 02:06:40'),(379,182,'Louisa','2011-06-01 02:06:40'),(380,182,'Cumberland','2011-06-01 02:06:40'),(381,182,'Charles city','2011-06-01 02:06:40'),(382,182,'Middlesex','2011-06-01 02:06:40'),(383,182,'Henrico','2011-06-01 02:06:40'),(384,182,'James city','2011-06-01 02:06:40'),(385,182,'York','2011-06-01 02:06:40'),(386,182,'Powhatan','2011-06-01 02:06:40'),(387,182,'Chesterfield','2011-06-01 02:06:40'),(388,182,'Richmond city','2011-06-01 02:06:40'),(389,182,'Williamsburg city','2011-06-01 02:06:40'),(390,182,'Accomack','2011-06-01 02:06:40'),(391,182,'Isle of wight','2011-06-01 02:06:40'),(392,182,'Northampton','2011-06-01 02:06:40'),(393,182,'Chesapeake city','2011-06-01 02:06:40'),(394,182,'Suffolk city','2011-06-01 02:06:40'),(395,182,'Virginia beach city','2011-06-01 02:06:40'),(396,182,'Norfolk city','2011-06-01 02:06:40'),(397,182,'Newport news city','2011-06-01 02:06:40'),(398,182,'Hampton city','2011-06-01 02:06:40'),(399,182,'Poquoson city','2011-06-01 02:06:40'),(400,182,'Portsmouth city','2011-06-01 02:06:40'),(401,182,'Prince george','2011-06-01 02:06:40'),(402,182,'Petersburg city','2011-06-01 02:06:40'),(403,182,'Brunswick','2011-06-01 02:06:40'),(404,182,'Dinwiddie','2011-06-01 02:06:40'),(405,182,'Nottoway','2011-06-01 02:06:40'),(406,182,'Southampton','2011-06-01 02:06:40'),(407,182,'Colonial heights city','2011-06-01 02:06:40'),(408,182,'Surry','2011-06-01 02:06:40'),(409,182,'Emporia city','2011-06-01 02:06:40'),(410,182,'Franklin city','2011-06-01 02:06:40'),(411,182,'Hopewell city','2011-06-01 02:06:40'),(412,182,'Sussex','2011-06-01 02:06:40'),(413,182,'Greensville','2011-06-01 02:06:40'),(414,182,'Prince edward','2011-06-01 02:06:40'),(415,182,'Mecklenburg','2011-06-01 02:06:40'),(416,182,'Charlotte','2011-06-01 02:06:40'),(417,182,'Lunenburg','2011-06-01 02:06:40'),(418,182,'Appomattox','2011-06-01 02:06:40'),(419,182,'Roanoke city','2011-06-01 02:06:40'),(420,182,'Roanoke','2011-06-01 02:06:40'),(421,182,'Botetourt','2011-06-01 02:06:40'),(422,182,'Montgomery','2011-06-01 02:06:40'),(423,182,'Patrick','2011-06-01 02:06:40'),(424,182,'Henry','2011-06-01 02:06:40'),(425,182,'Pulaski','2011-06-01 02:06:40'),(426,182,'Franklin','2011-06-01 02:06:40'),(427,182,'Pittsylvania','2011-06-01 02:06:40'),(428,182,'Floyd','2011-06-01 02:06:40'),(429,182,'Giles','2011-06-01 02:06:40'),(430,182,'Bedford','2011-06-01 02:06:40'),(431,182,'Martinsville city','2011-06-01 02:06:40'),(432,182,'Craig','2011-06-01 02:06:40'),(433,182,'Salem','2011-06-01 02:06:40'),(434,182,'Bristol','2011-06-01 02:06:40'),(435,182,'Washington','2011-06-01 02:06:40'),(436,182,'Wise','2011-06-01 02:06:40'),(437,182,'Dickenson','2011-06-01 02:06:40'),(438,182,'Lee','2011-06-01 02:06:40'),(439,182,'Russell','2011-06-01 02:06:40'),(440,182,'Buchanan','2011-06-01 02:06:40'),(441,182,'Scott','2011-06-01 02:06:40'),(442,182,'Norton city','2011-06-01 02:06:40'),(443,182,'Grayson','2011-06-01 02:06:40'),(444,182,'Smyth','2011-06-01 02:06:40'),(445,182,'Wythe','2011-06-01 02:06:40'),(446,182,'Bland','2011-06-01 02:06:40'),(447,182,'Carroll','2011-06-01 02:06:40'),(448,182,'Galax city','2011-06-01 02:06:40'),(449,182,'Tazewell','2011-06-01 02:06:40'),(450,182,'Staunton city','2011-06-01 02:06:40'),(451,182,'Bath','2011-06-01 02:06:40'),(452,182,'Highland','2011-06-01 02:06:40'),(453,182,'Rockbridge','2011-06-01 02:06:40'),(454,182,'Buena vista city','2011-06-01 02:06:40'),(455,182,'Clifton forge city','2011-06-01 02:06:40'),(456,182,'Covington city','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (457,182,'Alleghany','2011-06-01 02:06:40'),(458,182,'Lexington city','2011-06-01 02:06:40'),(459,182,'Lynchburg city','2011-06-01 02:06:40'),(460,182,'Campbell','2011-06-01 02:06:40'),(461,182,'Halifax','2011-06-01 02:06:40'),(462,182,'Amherst','2011-06-01 02:06:40'),(463,182,'Bedford city','2011-06-01 02:06:40'),(464,182,'Danville city','2011-06-01 02:06:40'),(465,184,'Mercer','2011-06-01 02:06:40'),(466,184,'Wyoming','2011-06-01 02:06:40'),(467,184,'Mcdowell','2011-06-01 02:06:40'),(468,184,'Mingo','2011-06-01 02:06:40'),(469,184,'Greenbrier','2011-06-01 02:06:40'),(470,184,'Pocahontas','2011-06-01 02:06:40'),(471,184,'Monroe','2011-06-01 02:06:40'),(472,184,'Summers','2011-06-01 02:06:40'),(473,184,'Fayette','2011-06-01 02:06:40'),(474,184,'Kanawha','2011-06-01 02:06:40'),(475,184,'Roane','2011-06-01 02:06:40'),(476,184,'Raleigh','2011-06-01 02:06:40'),(477,184,'Boone','2011-06-01 02:06:40'),(478,184,'Putnam','2011-06-01 02:06:40'),(479,184,'Clay','2011-06-01 02:06:40'),(480,184,'Logan','2011-06-01 02:06:40'),(481,184,'Nicholas','2011-06-01 02:06:40'),(482,184,'Mason','2011-06-01 02:06:40'),(483,184,'Jackson','2011-06-01 02:06:40'),(484,184,'Calhoun','2011-06-01 02:06:40'),(485,184,'Gilmer','2011-06-01 02:06:40'),(486,184,'Berkeley','2011-06-01 02:06:40'),(487,184,'Jefferson','2011-06-01 02:06:40'),(488,184,'Morgan','2011-06-01 02:06:40'),(489,184,'Hampshire','2011-06-01 02:06:40'),(490,184,'Lincoln','2011-06-01 02:06:40'),(491,184,'Cabell','2011-06-01 02:06:40'),(492,184,'Wayne','2011-06-01 02:06:40'),(493,184,'Ohio','2011-06-01 02:06:40'),(494,184,'Brooke','2011-06-01 02:06:40'),(495,184,'Marshall','2011-06-01 02:06:40'),(496,184,'Hancock','2011-06-01 02:06:40'),(497,184,'Wood','2011-06-01 02:06:40'),(498,184,'Pleasants','2011-06-01 02:06:40'),(499,184,'Wirt','2011-06-01 02:06:40'),(500,184,'Tyler','2011-06-01 02:06:40'),(501,184,'Ritchie','2011-06-01 02:06:40'),(502,184,'Wetzel','2011-06-01 02:06:40'),(503,184,'Upshur','2011-06-01 02:06:40'),(504,184,'Webster','2011-06-01 02:06:40'),(505,184,'Randolph','2011-06-01 02:06:40'),(506,184,'Barbour','2011-06-01 02:06:40'),(507,184,'Tucker','2011-06-01 02:06:40'),(508,184,'Harrison','2011-06-01 02:06:40'),(509,184,'Lewis','2011-06-01 02:06:40'),(510,184,'Braxton','2011-06-01 02:06:40'),(511,184,'Doddridge','2011-06-01 02:06:40'),(512,184,'Taylor','2011-06-01 02:06:40'),(513,184,'Preston','2011-06-01 02:06:40'),(514,184,'Monongalia','2011-06-01 02:06:40'),(515,184,'Marion','2011-06-01 02:06:40'),(516,184,'Grant','2011-06-01 02:06:40'),(517,184,'Mineral','2011-06-01 02:06:40'),(518,184,'Hardy','2011-06-01 02:06:40'),(519,184,'Pendleton','2011-06-01 02:06:40'),(520,165,'Davie','2011-06-01 02:06:40'),(521,165,'Surry','2011-06-01 02:06:40'),(522,165,'Forsyth','2011-06-01 02:06:40'),(523,165,'Yadkin','2011-06-01 02:06:40'),(524,165,'Rowan','2011-06-01 02:06:40'),(525,165,'Stokes','2011-06-01 02:06:40'),(526,165,'Rockingham','2011-06-01 02:06:40'),(527,165,'Alamance','2011-06-01 02:06:40'),(528,165,'Randolph','2011-06-01 02:06:40'),(529,165,'Chatham','2011-06-01 02:06:40'),(530,165,'Montgomery','2011-06-01 02:06:40'),(531,165,'Caswell','2011-06-01 02:06:40'),(532,165,'Guilford','2011-06-01 02:06:40'),(533,165,'Orange','2011-06-01 02:06:40'),(534,165,'Lee','2011-06-01 02:06:40'),(535,165,'Davidson','2011-06-01 02:06:40'),(536,165,'Moore','2011-06-01 02:06:40'),(537,165,'Person','2011-06-01 02:06:40'),(538,165,'Harnett','2011-06-01 02:06:40'),(539,165,'Wake','2011-06-01 02:06:40'),(540,165,'Durham','2011-06-01 02:06:40'),(541,165,'Johnston','2011-06-01 02:06:40'),(542,165,'Granville','2011-06-01 02:06:40'),(543,165,'Franklin','2011-06-01 02:06:40'),(544,165,'Wayne','2011-06-01 02:06:40'),(545,165,'Vance','2011-06-01 02:06:40'),(546,165,'Warren','2011-06-01 02:06:40'),(547,165,'Edgecombe','2011-06-01 02:06:40'),(548,165,'Nash','2011-06-01 02:06:40'),(549,165,'Bertie','2011-06-01 02:06:40'),(550,165,'Beaufort','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (551,165,'Pitt','2011-06-01 02:06:40'),(552,165,'Wilson','2011-06-01 02:06:40'),(553,165,'Hertford','2011-06-01 02:06:40'),(554,165,'Northampton','2011-06-01 02:06:40'),(555,165,'Halifax','2011-06-01 02:06:40'),(556,165,'Hyde','2011-06-01 02:06:40'),(557,165,'Martin','2011-06-01 02:06:40'),(558,165,'Greene','2011-06-01 02:06:40'),(559,165,'Pasquotank','2011-06-01 02:06:40'),(560,165,'Dare','2011-06-01 02:06:40'),(561,165,'Currituck','2011-06-01 02:06:40'),(562,165,'Perquimans','2011-06-01 02:06:40'),(563,165,'Camden','2011-06-01 02:06:40'),(564,165,'Tyrrell','2011-06-01 02:06:40'),(565,165,'Gates','2011-06-01 02:06:40'),(566,165,'Washington','2011-06-01 02:06:40'),(567,165,'Chowan','2011-06-01 02:06:40'),(568,165,'Stanly','2011-06-01 02:06:40'),(569,165,'Gaston','2011-06-01 02:06:40'),(570,165,'Anson','2011-06-01 02:06:40'),(571,165,'Iredell','2011-06-01 02:06:40'),(572,165,'Cleveland','2011-06-01 02:06:40'),(573,165,'Rutherford','2011-06-01 02:06:40'),(574,165,'Cabarrus','2011-06-01 02:06:40'),(575,165,'Mecklenburg','2011-06-01 02:06:40'),(576,165,'Lincoln','2011-06-01 02:06:40'),(577,165,'Union','2011-06-01 02:06:40'),(578,165,'Cumberland','2011-06-01 02:06:40'),(579,165,'Sampson','2011-06-01 02:06:40'),(580,165,'Robeson','2011-06-01 02:06:40'),(581,165,'Bladen','2011-06-01 02:06:40'),(582,165,'Duplin','2011-06-01 02:06:40'),(583,165,'Richmond','2011-06-01 02:06:40'),(584,165,'Scotland','2011-06-01 02:06:40'),(585,165,'Hoke','2011-06-01 02:06:40'),(586,165,'New hanover','2011-06-01 02:06:40'),(587,165,'Brunswick','2011-06-01 02:06:40'),(588,165,'Pender','2011-06-01 02:06:40'),(589,165,'Columbus','2011-06-01 02:06:40'),(590,165,'Onslow','2011-06-01 02:06:40'),(591,165,'Lenoir','2011-06-01 02:06:40'),(592,165,'Pamlico','2011-06-01 02:06:40'),(593,165,'Carteret','2011-06-01 02:06:40'),(594,165,'Craven','2011-06-01 02:06:40'),(595,165,'Jones','2011-06-01 02:06:40'),(596,165,'Catawba','2011-06-01 02:06:40'),(597,165,'Avery','2011-06-01 02:06:40'),(598,165,'Watauga','2011-06-01 02:06:40'),(599,165,'Wilkes','2011-06-01 02:06:40'),(600,165,'Caldwell','2011-06-01 02:06:40'),(601,165,'Burke','2011-06-01 02:06:40'),(602,165,'Ashe','2011-06-01 02:06:40'),(603,165,'Alleghany','2011-06-01 02:06:40'),(604,165,'Alexander','2011-06-01 02:06:40'),(605,165,'Buncombe','2011-06-01 02:06:40'),(606,165,'Swain','2011-06-01 02:06:40'),(607,165,'Mitchell','2011-06-01 02:06:40'),(608,165,'Jackson','2011-06-01 02:06:40'),(609,165,'Transylvania','2011-06-01 02:06:40'),(610,165,'Henderson','2011-06-01 02:06:40'),(611,165,'Yancey','2011-06-01 02:06:40'),(612,165,'Haywood','2011-06-01 02:06:40'),(613,165,'Polk','2011-06-01 02:06:40'),(614,165,'Graham','2011-06-01 02:06:40'),(615,165,'Macon','2011-06-01 02:06:40'),(616,165,'Mcdowell','2011-06-01 02:06:40'),(617,165,'Madison','2011-06-01 02:06:40'),(618,165,'Cherokee','2011-06-01 02:06:40'),(619,165,'Clay','2011-06-01 02:06:40'),(620,175,'Clarendon','2011-06-01 02:06:40'),(621,175,'Richland','2011-06-01 02:06:40'),(622,175,'Bamberg','2011-06-01 02:06:40'),(623,175,'Lexington','2011-06-01 02:06:40'),(624,175,'Kershaw','2011-06-01 02:06:40'),(625,175,'Lee','2011-06-01 02:06:40'),(626,175,'Chester','2011-06-01 02:06:40'),(627,175,'Fairfield','2011-06-01 02:06:40'),(628,175,'Orangeburg','2011-06-01 02:06:40'),(629,175,'Calhoun','2011-06-01 02:06:40'),(630,175,'Union','2011-06-01 02:06:40'),(631,175,'Newberry','2011-06-01 02:06:40'),(632,175,'Sumter','2011-06-01 02:06:40'),(633,175,'Williamsburg','2011-06-01 02:06:40'),(634,175,'Lancaster','2011-06-01 02:06:40'),(635,175,'Darlington','2011-06-01 02:06:40'),(636,175,'Colleton','2011-06-01 02:06:40'),(637,175,'Chesterfield','2011-06-01 02:06:40'),(638,175,'Saluda','2011-06-01 02:06:40'),(639,175,'Florence','2011-06-01 02:06:40'),(640,175,'Aiken','2011-06-01 02:06:40'),(641,175,'Spartanburg','2011-06-01 02:06:40'),(642,175,'Laurens','2011-06-01 02:06:40'),(643,175,'Cherokee','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (644,175,'Charleston','2011-06-01 02:06:40'),(645,175,'Berkeley','2011-06-01 02:06:40'),(646,175,'Dorchester','2011-06-01 02:06:40'),(647,175,'Georgetown','2011-06-01 02:06:40'),(648,175,'Horry','2011-06-01 02:06:40'),(649,175,'Marlboro','2011-06-01 02:06:40'),(650,175,'Marion','2011-06-01 02:06:40'),(651,175,'Dillon','2011-06-01 02:06:40'),(652,175,'Greenville','2011-06-01 02:06:40'),(653,175,'Abbeville','2011-06-01 02:06:40'),(654,175,'Anderson','2011-06-01 02:06:40'),(655,175,'Pickens','2011-06-01 02:06:40'),(656,175,'Oconee','2011-06-01 02:06:40'),(657,175,'Greenwood','2011-06-01 02:06:40'),(658,175,'York','2011-06-01 02:06:40'),(659,175,'Allendale','2011-06-01 02:06:40'),(660,175,'Barnwell','2011-06-01 02:06:40'),(661,175,'Mccormick','2011-06-01 02:06:40'),(662,175,'Edgefield','2011-06-01 02:06:40'),(663,175,'Beaufort','2011-06-01 02:06:40'),(664,175,'Hampton','2011-06-01 02:06:40'),(665,175,'Jasper','2011-06-01 02:06:40'),(666,140,'Dekalb','2011-06-01 02:06:40'),(667,140,'Gwinnett','2011-06-01 02:06:40'),(668,140,'Fulton','2011-06-01 02:06:40'),(669,140,'Cobb','2011-06-01 02:06:40'),(670,140,'Barrow','2011-06-01 02:06:40'),(671,140,'Rockdale','2011-06-01 02:06:40'),(672,140,'Newton','2011-06-01 02:06:40'),(673,140,'Walton','2011-06-01 02:06:40'),(674,140,'Forsyth','2011-06-01 02:06:40'),(675,140,'Jasper','2011-06-01 02:06:40'),(676,140,'Bartow','2011-06-01 02:06:40'),(677,140,'Polk','2011-06-01 02:06:40'),(678,140,'Floyd','2011-06-01 02:06:40'),(679,140,'Cherokee','2011-06-01 02:06:40'),(680,140,'Carroll','2011-06-01 02:06:40'),(681,140,'Haralson','2011-06-01 02:06:40'),(682,140,'Douglas','2011-06-01 02:06:40'),(683,140,'Paulding','2011-06-01 02:06:40'),(684,140,'Gordon','2011-06-01 02:06:40'),(685,140,'Pickens','2011-06-01 02:06:40'),(686,140,'Lamar','2011-06-01 02:06:40'),(687,140,'Fayette','2011-06-01 02:06:40'),(688,140,'Pike','2011-06-01 02:06:40'),(689,140,'Spalding','2011-06-01 02:06:40'),(690,140,'Butts','2011-06-01 02:06:40'),(691,140,'Heard','2011-06-01 02:06:40'),(692,140,'Meriwether','2011-06-01 02:06:40'),(693,140,'Coweta','2011-06-01 02:06:40'),(694,140,'Henry','2011-06-01 02:06:40'),(695,140,'Troup','2011-06-01 02:06:40'),(696,140,'Clayton','2011-06-01 02:06:40'),(697,140,'Upson','2011-06-01 02:06:40'),(698,140,'Emanuel','2011-06-01 02:06:40'),(699,140,'Montgomery','2011-06-01 02:06:40'),(700,140,'Wheeler','2011-06-01 02:06:40'),(701,140,'Jefferson','2011-06-01 02:06:40'),(702,140,'Evans','2011-06-01 02:06:40'),(703,140,'Bulloch','2011-06-01 02:06:40'),(704,140,'Tattnall','2011-06-01 02:06:40'),(705,140,'Screven','2011-06-01 02:06:40'),(706,140,'Burke','2011-06-01 02:06:40'),(707,140,'Toombs','2011-06-01 02:06:40'),(708,140,'Candler','2011-06-01 02:06:40'),(709,140,'Jenkins','2011-06-01 02:06:40'),(710,140,'Laurens','2011-06-01 02:06:40'),(711,140,'Treutlen','2011-06-01 02:06:40'),(712,140,'Hall','2011-06-01 02:06:40'),(713,140,'Habersham','2011-06-01 02:06:40'),(714,140,'Banks','2011-06-01 02:06:40'),(715,140,'Union','2011-06-01 02:06:40'),(716,140,'Fannin','2011-06-01 02:06:40'),(717,140,'Hart','2011-06-01 02:06:40'),(718,140,'Jackson','2011-06-01 02:06:40'),(719,140,'Franklin','2011-06-01 02:06:40'),(720,140,'Gilmer','2011-06-01 02:06:40'),(721,140,'Rabun','2011-06-01 02:06:40'),(722,140,'White','2011-06-01 02:06:40'),(723,140,'Lumpkin','2011-06-01 02:06:40'),(724,140,'Dawson','2011-06-01 02:06:40'),(725,140,'Stephens','2011-06-01 02:06:40'),(726,140,'Towns','2011-06-01 02:06:40'),(727,140,'Clarke','2011-06-01 02:06:40'),(728,140,'Oglethorpe','2011-06-01 02:06:40'),(729,140,'Oconee','2011-06-01 02:06:40'),(730,140,'Morgan','2011-06-01 02:06:40'),(731,140,'Elbert','2011-06-01 02:06:40'),(732,140,'Madison','2011-06-01 02:06:40'),(733,140,'Taliaferro','2011-06-01 02:06:40'),(734,140,'Greene','2011-06-01 02:06:40'),(735,140,'Wilkes','2011-06-01 02:06:40'),(736,140,'Murray','2011-06-01 02:06:40'),(737,140,'Walker','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (738,140,'Whitfield','2011-06-01 02:06:40'),(739,140,'Catoosa','2011-06-01 02:06:40'),(740,140,'Chattooga','2011-06-01 02:06:40'),(741,140,'Dade','2011-06-01 02:06:40'),(742,140,'Columbia','2011-06-01 02:06:40'),(743,140,'Richmond','2011-06-01 02:06:40'),(744,140,'Mcduffie','2011-06-01 02:06:40'),(745,140,'Warren','2011-06-01 02:06:40'),(746,140,'Glascock','2011-06-01 02:06:40'),(747,140,'Lincoln','2011-06-01 02:06:40'),(748,140,'Wilcox','2011-06-01 02:06:40'),(749,140,'Wilkinson','2011-06-01 02:06:40'),(750,140,'Monroe','2011-06-01 02:06:40'),(751,140,'Houston','2011-06-01 02:06:40'),(752,140,'Taylor','2011-06-01 02:06:40'),(753,140,'Dooly','2011-06-01 02:06:40'),(754,140,'Peach','2011-06-01 02:06:40'),(755,140,'Crisp','2011-06-01 02:06:40'),(756,140,'Dodge','2011-06-01 02:06:40'),(757,140,'Bleckley','2011-06-01 02:06:40'),(758,140,'Twiggs','2011-06-01 02:06:40'),(759,140,'Washington','2011-06-01 02:06:40'),(760,140,'Putnam','2011-06-01 02:06:40'),(761,140,'Jones','2011-06-01 02:06:40'),(762,140,'Baldwin','2011-06-01 02:06:40'),(763,140,'Pulaski','2011-06-01 02:06:40'),(764,140,'Telfair','2011-06-01 02:06:40'),(765,140,'Macon','2011-06-01 02:06:40'),(766,140,'Johnson','2011-06-01 02:06:40'),(767,140,'Crawford','2011-06-01 02:06:40'),(768,140,'Hancock','2011-06-01 02:06:40'),(769,140,'Bibb','2011-06-01 02:06:40'),(770,140,'Liberty','2011-06-01 02:06:40'),(771,140,'Chatham','2011-06-01 02:06:40'),(772,140,'Effingham','2011-06-01 02:06:40'),(773,140,'Mcintosh','2011-06-01 02:06:40'),(774,140,'Bryan','2011-06-01 02:06:40'),(775,140,'Long','2011-06-01 02:06:40'),(776,140,'Ware','2011-06-01 02:06:40'),(777,140,'Bacon','2011-06-01 02:06:40'),(778,140,'Coffee','2011-06-01 02:06:40'),(779,140,'Appling','2011-06-01 02:06:40'),(780,140,'Pierce','2011-06-01 02:06:40'),(781,140,'Glynn','2011-06-01 02:06:40'),(782,140,'Jeff davis','2011-06-01 02:06:40'),(783,140,'Charlton','2011-06-01 02:06:40'),(784,140,'Brantley','2011-06-01 02:06:40'),(785,140,'Wayne','2011-06-01 02:06:40'),(786,140,'Camden','2011-06-01 02:06:40'),(787,140,'Decatur','2011-06-01 02:06:40'),(788,140,'Lowndes','2011-06-01 02:06:40'),(789,140,'Cook','2011-06-01 02:06:40'),(790,140,'Berrien','2011-06-01 02:06:40'),(791,140,'Clinch','2011-06-01 02:06:40'),(792,140,'Atkinson','2011-06-01 02:06:40'),(793,140,'Brooks','2011-06-01 02:06:40'),(794,140,'Thomas','2011-06-01 02:06:40'),(795,140,'Lanier','2011-06-01 02:06:40'),(796,140,'Echols','2011-06-01 02:06:40'),(797,140,'Dougherty','2011-06-01 02:06:40'),(798,140,'Sumter','2011-06-01 02:06:40'),(799,140,'Turner','2011-06-01 02:06:40'),(800,140,'Mitchell','2011-06-01 02:06:40'),(801,140,'Colquitt','2011-06-01 02:06:40'),(802,140,'Tift','2011-06-01 02:06:40'),(803,140,'Ben hill','2011-06-01 02:06:40'),(804,140,'Irwin','2011-06-01 02:06:40'),(805,140,'Lee','2011-06-01 02:06:40'),(806,140,'Worth','2011-06-01 02:06:40'),(807,140,'Talbot','2011-06-01 02:06:40'),(808,140,'Marion','2011-06-01 02:06:40'),(809,140,'Harris','2011-06-01 02:06:40'),(810,140,'Chattahoochee','2011-06-01 02:06:40'),(811,140,'Schley','2011-06-01 02:06:40'),(812,140,'Muscogee','2011-06-01 02:06:40'),(813,140,'Stewart','2011-06-01 02:06:40'),(814,140,'Webster','2011-06-01 02:06:40'),(815,139,'Clay','2011-06-01 02:06:40'),(816,139,'Saint johns','2011-06-01 02:06:40'),(817,139,'Putnam','2011-06-01 02:06:40'),(818,139,'Suwannee','2011-06-01 02:06:40'),(819,139,'Nassau','2011-06-01 02:06:40'),(820,139,'Lafayette','2011-06-01 02:06:40'),(821,139,'Columbia','2011-06-01 02:06:40'),(822,139,'Union','2011-06-01 02:06:40'),(823,139,'Baker','2011-06-01 02:06:40'),(824,139,'Bradford','2011-06-01 02:06:40'),(825,139,'Hamilton','2011-06-01 02:06:40'),(826,139,'Madison','2011-06-01 02:06:40'),(827,139,'Duval','2011-06-01 02:06:40'),(828,139,'Lake','2011-06-01 02:06:40'),(829,139,'Volusia','2011-06-01 02:06:40'),(830,139,'Flagler','2011-06-01 02:06:40'),(831,139,'Marion','2011-06-01 02:06:40'),(832,139,'Sumter','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (833,139,'Leon','2011-06-01 02:06:40'),(834,139,'Wakulla','2011-06-01 02:06:40'),(835,139,'Franklin','2011-06-01 02:06:40'),(836,139,'Liberty','2011-06-01 02:06:40'),(837,139,'Gadsden','2011-06-01 02:06:40'),(838,139,'Jefferson','2011-06-01 02:06:40'),(839,139,'Taylor','2011-06-01 02:06:40'),(840,139,'Bay','2011-06-01 02:06:40'),(841,139,'Jackson','2011-06-01 02:06:40'),(842,139,'Calhoun','2011-06-01 02:06:40'),(843,139,'Walton','2011-06-01 02:06:40'),(844,139,'Holmes','2011-06-01 02:06:40'),(845,139,'Washington','2011-06-01 02:06:40'),(846,139,'Gulf','2011-06-01 02:06:40'),(847,139,'Escambia','2011-06-01 02:06:40'),(848,139,'Santa rosa','2011-06-01 02:06:40'),(849,139,'Okaloosa','2011-06-01 02:06:40'),(850,139,'Alachua','2011-06-01 02:06:40'),(851,139,'Gilchrist','2011-06-01 02:06:40'),(852,139,'Levy','2011-06-01 02:06:40'),(853,139,'Dixie','2011-06-01 02:06:40'),(854,139,'Seminole','2011-06-01 02:06:40'),(855,139,'Orange','2011-06-01 02:06:40'),(856,139,'Brevard','2011-06-01 02:06:40'),(857,139,'Indian river','2011-06-01 02:06:40'),(858,139,'Monroe','2011-06-01 02:06:40'),(859,139,'Miami dade','2011-06-01 02:06:40'),(860,139,'Broward','2011-06-01 02:06:40'),(861,139,'Palm beach','2011-06-01 02:06:40'),(862,139,'Hendry','2011-06-01 02:06:40'),(863,139,'Martin','2011-06-01 02:06:40'),(864,139,'Glades','2011-06-01 02:06:40'),(865,139,'Hillsborough','2011-06-01 02:06:40'),(866,139,'Pasco','2011-06-01 02:06:40'),(867,139,'Pinellas','2011-06-01 02:06:40'),(868,139,'Polk','2011-06-01 02:06:40'),(869,139,'Highlands','2011-06-01 02:06:40'),(870,139,'Hardee','2011-06-01 02:06:40'),(871,139,'Osceola','2011-06-01 02:06:40'),(872,139,'Lee','2011-06-01 02:06:40'),(873,139,'Charlotte','2011-06-01 02:06:40'),(874,139,'Collier','2011-06-01 02:06:40'),(875,139,'Manatee','2011-06-01 02:06:40'),(876,139,'Sarasota','2011-06-01 02:06:40'),(877,139,'De soto','2011-06-01 02:06:40'),(878,139,'Citrus','2011-06-01 02:06:40'),(879,139,'Hernando','2011-06-01 02:06:40'),(880,139,'Saint lucie','2011-06-01 02:06:40'),(881,139,'Okeechobee','2011-06-01 02:06:40'),(882,129,'Saint clair','2011-06-01 02:06:40'),(883,129,'Jefferson','2011-06-01 02:06:40'),(884,129,'Shelby','2011-06-01 02:06:40'),(885,129,'Tallapoosa','2011-06-01 02:06:40'),(886,129,'Blount','2011-06-01 02:06:40'),(887,129,'Talladega','2011-06-01 02:06:40'),(888,129,'Marshall','2011-06-01 02:06:40'),(889,129,'Cullman','2011-06-01 02:06:40'),(890,129,'Bibb','2011-06-01 02:06:40'),(891,129,'Walker','2011-06-01 02:06:40'),(892,129,'Chilton','2011-06-01 02:06:40'),(893,129,'Coosa','2011-06-01 02:06:40'),(894,129,'Clay','2011-06-01 02:06:40'),(895,129,'Tuscaloosa','2011-06-01 02:06:40'),(896,129,'Hale','2011-06-01 02:06:40'),(897,129,'Pickens','2011-06-01 02:06:40'),(898,129,'Greene','2011-06-01 02:06:40'),(899,129,'Sumter','2011-06-01 02:06:40'),(900,129,'Winston','2011-06-01 02:06:40'),(901,129,'Fayette','2011-06-01 02:06:40'),(902,129,'Marion','2011-06-01 02:06:40'),(903,129,'Lamar','2011-06-01 02:06:40'),(904,129,'Franklin','2011-06-01 02:06:40'),(905,129,'Morgan','2011-06-01 02:06:40'),(906,129,'Lauderdale','2011-06-01 02:06:40'),(907,129,'Limestone','2011-06-01 02:06:40'),(908,129,'Colbert','2011-06-01 02:06:40'),(909,129,'Lawrence','2011-06-01 02:06:40'),(910,129,'Jackson','2011-06-01 02:06:40'),(911,129,'Madison','2011-06-01 02:06:40'),(912,129,'Etowah','2011-06-01 02:06:40'),(913,129,'Cherokee','2011-06-01 02:06:40'),(914,129,'De kalb','2011-06-01 02:06:40'),(915,129,'Autauga','2011-06-01 02:06:40'),(916,129,'Pike','2011-06-01 02:06:40'),(917,129,'Crenshaw','2011-06-01 02:06:40'),(918,129,'Montgomery','2011-06-01 02:06:40'),(919,129,'Butler','2011-06-01 02:06:40'),(920,129,'Barbour','2011-06-01 02:06:40'),(921,129,'Elmore','2011-06-01 02:06:40'),(922,129,'Bullock','2011-06-01 02:06:40'),(923,129,'Macon','2011-06-01 02:06:40'),(924,129,'Lowndes','2011-06-01 02:06:40'),(925,129,'Covington','2011-06-01 02:06:40'),(926,129,'Calhoun','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (927,129,'Cleburne','2011-06-01 02:06:40'),(928,129,'Randolph','2011-06-01 02:06:40'),(929,129,'Houston','2011-06-01 02:06:40'),(930,129,'Henry','2011-06-01 02:06:40'),(931,129,'Dale','2011-06-01 02:06:40'),(932,129,'Geneva','2011-06-01 02:06:40'),(933,129,'Coffee','2011-06-01 02:06:40'),(934,129,'Conecuh','2011-06-01 02:06:40'),(935,129,'Monroe','2011-06-01 02:06:40'),(936,129,'Escambia','2011-06-01 02:06:40'),(937,129,'Wilcox','2011-06-01 02:06:40'),(938,129,'Clarke','2011-06-01 02:06:40'),(939,129,'Mobile','2011-06-01 02:06:40'),(940,129,'Baldwin','2011-06-01 02:06:40'),(941,129,'Washington','2011-06-01 02:06:40'),(942,129,'Dallas','2011-06-01 02:06:40'),(943,129,'Marengo','2011-06-01 02:06:40'),(944,129,'Perry','2011-06-01 02:06:40'),(945,129,'Lee','2011-06-01 02:06:40'),(946,129,'Russell','2011-06-01 02:06:40'),(947,129,'Chambers','2011-06-01 02:06:40'),(948,129,'Choctaw','2011-06-01 02:06:40'),(949,177,'Robertson','2011-06-01 02:06:40'),(950,177,'Davidson','2011-06-01 02:06:40'),(951,177,'Dekalb','2011-06-01 02:06:40'),(952,177,'Williamson','2011-06-01 02:06:40'),(953,177,'Cheatham','2011-06-01 02:06:40'),(954,177,'Cannon','2011-06-01 02:06:40'),(955,177,'Coffee','2011-06-01 02:06:40'),(956,177,'Marshall','2011-06-01 02:06:40'),(957,177,'Bedford','2011-06-01 02:06:40'),(958,177,'Sumner','2011-06-01 02:06:40'),(959,177,'Stewart','2011-06-01 02:06:40'),(960,177,'Hickman','2011-06-01 02:06:40'),(961,177,'Dickson','2011-06-01 02:06:40'),(962,177,'Smith','2011-06-01 02:06:40'),(963,177,'Rutherford','2011-06-01 02:06:40'),(964,177,'Montgomery','2011-06-01 02:06:40'),(965,177,'Houston','2011-06-01 02:06:40'),(966,177,'Wilson','2011-06-01 02:06:40'),(967,177,'Trousdale','2011-06-01 02:06:40'),(968,177,'Humphreys','2011-06-01 02:06:40'),(969,177,'Macon','2011-06-01 02:06:40'),(970,177,'Perry','2011-06-01 02:06:40'),(971,177,'Warren','2011-06-01 02:06:40'),(972,177,'Lincoln','2011-06-01 02:06:40'),(973,177,'Maury','2011-06-01 02:06:40'),(974,177,'Grundy','2011-06-01 02:06:40'),(975,177,'Hamilton','2011-06-01 02:06:40'),(976,177,'Mcminn','2011-06-01 02:06:40'),(977,177,'Franklin','2011-06-01 02:06:40'),(978,177,'Polk','2011-06-01 02:06:40'),(979,177,'Bradley','2011-06-01 02:06:40'),(980,177,'Monroe','2011-06-01 02:06:40'),(981,177,'Rhea','2011-06-01 02:06:40'),(982,177,'Meigs','2011-06-01 02:06:40'),(983,177,'Sequatchie','2011-06-01 02:06:40'),(984,177,'Marion','2011-06-01 02:06:40'),(985,177,'Moore','2011-06-01 02:06:40'),(986,177,'Bledsoe','2011-06-01 02:06:40'),(987,177,'Shelby','2011-06-01 02:06:40'),(988,177,'Washington','2011-06-01 02:06:40'),(989,177,'Greene','2011-06-01 02:06:40'),(990,177,'Sullivan','2011-06-01 02:06:40'),(991,177,'Johnson','2011-06-01 02:06:40'),(992,177,'Hawkins','2011-06-01 02:06:40'),(993,177,'Carter','2011-06-01 02:06:40'),(994,177,'Unicoi','2011-06-01 02:06:40'),(995,177,'Blount','2011-06-01 02:06:40'),(996,177,'Anderson','2011-06-01 02:06:40'),(997,177,'Claiborne','2011-06-01 02:06:40'),(998,177,'Grainger','2011-06-01 02:06:40'),(999,177,'Cocke','2011-06-01 02:06:40'),(1000,177,'Campbell','2011-06-01 02:06:40'),(1001,177,'Morgan','2011-06-01 02:06:40'),(1002,177,'Knox','2011-06-01 02:06:40'),(1003,177,'Cumberland','2011-06-01 02:06:40'),(1004,177,'Jefferson','2011-06-01 02:06:40'),(1005,177,'Scott','2011-06-01 02:06:40'),(1006,177,'Sevier','2011-06-01 02:06:40'),(1007,177,'Loudon','2011-06-01 02:06:40'),(1008,177,'Roane','2011-06-01 02:06:40'),(1009,177,'Hancock','2011-06-01 02:06:40'),(1010,177,'Hamblen','2011-06-01 02:06:40'),(1011,177,'Union','2011-06-01 02:06:40'),(1012,177,'Crockett','2011-06-01 02:06:40'),(1013,177,'Fayette','2011-06-01 02:06:40'),(1014,177,'Tipton','2011-06-01 02:06:40'),(1015,177,'Dyer','2011-06-01 02:06:40'),(1016,177,'Hardeman','2011-06-01 02:06:40'),(1017,177,'Haywood','2011-06-01 02:06:40'),(1018,177,'Lauderdale','2011-06-01 02:06:40'),(1019,177,'Lake','2011-06-01 02:06:40'),(1020,177,'Carroll','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1021,177,'Benton','2011-06-01 02:06:40'),(1022,177,'Henry','2011-06-01 02:06:40'),(1023,177,'Weakley','2011-06-01 02:06:40'),(1024,177,'Obion','2011-06-01 02:06:40'),(1025,177,'Gibson','2011-06-01 02:06:40'),(1026,177,'Madison','2011-06-01 02:06:40'),(1027,177,'Mcnairy','2011-06-01 02:06:40'),(1028,177,'Decatur','2011-06-01 02:06:40'),(1029,177,'Hardin','2011-06-01 02:06:40'),(1030,177,'Henderson','2011-06-01 02:06:40'),(1031,177,'Chester','2011-06-01 02:06:40'),(1032,177,'Wayne','2011-06-01 02:06:40'),(1033,177,'Giles','2011-06-01 02:06:40'),(1034,177,'Lawrence','2011-06-01 02:06:40'),(1035,177,'Lewis','2011-06-01 02:06:40'),(1036,177,'Putnam','2011-06-01 02:06:40'),(1037,177,'Fentress','2011-06-01 02:06:40'),(1038,177,'Overton','2011-06-01 02:06:40'),(1039,177,'Pickett','2011-06-01 02:06:40'),(1040,177,'Clay','2011-06-01 02:06:40'),(1041,177,'White','2011-06-01 02:06:40'),(1042,177,'Jackson','2011-06-01 02:06:40'),(1043,177,'Van buren','2011-06-01 02:06:40'),(1044,156,'Lafayette','2011-06-01 02:06:40'),(1045,156,'Tate','2011-06-01 02:06:40'),(1046,156,'Benton','2011-06-01 02:06:40'),(1047,156,'Panola','2011-06-01 02:06:40'),(1048,156,'Quitman','2011-06-01 02:06:40'),(1049,156,'Tippah','2011-06-01 02:06:40'),(1050,156,'Marshall','2011-06-01 02:06:40'),(1051,156,'Coahoma','2011-06-01 02:06:40'),(1052,156,'Tunica','2011-06-01 02:06:40'),(1053,156,'Union','2011-06-01 02:06:40'),(1054,156,'De soto','2011-06-01 02:06:40'),(1055,156,'Washington','2011-06-01 02:06:40'),(1056,156,'Bolivar','2011-06-01 02:06:40'),(1057,156,'Sharkey','2011-06-01 02:06:40'),(1058,156,'Sunflower','2011-06-01 02:06:40'),(1059,156,'Issaquena','2011-06-01 02:06:40'),(1060,156,'Humphreys','2011-06-01 02:06:40'),(1061,156,'Lee','2011-06-01 02:06:40'),(1062,156,'Pontotoc','2011-06-01 02:06:40'),(1063,156,'Monroe','2011-06-01 02:06:40'),(1064,156,'Tishomingo','2011-06-01 02:06:40'),(1065,156,'Prentiss','2011-06-01 02:06:40'),(1066,156,'Alcorn','2011-06-01 02:06:40'),(1067,156,'Calhoun','2011-06-01 02:06:40'),(1068,156,'Itawamba','2011-06-01 02:06:40'),(1069,156,'Chickasaw','2011-06-01 02:06:40'),(1070,156,'Grenada','2011-06-01 02:06:40'),(1071,156,'Carroll','2011-06-01 02:06:40'),(1072,156,'Tallahatchie','2011-06-01 02:06:40'),(1073,156,'Yalobusha','2011-06-01 02:06:40'),(1074,156,'Holmes','2011-06-01 02:06:40'),(1075,156,'Montgomery','2011-06-01 02:06:40'),(1076,156,'Leflore','2011-06-01 02:06:40'),(1077,156,'Yazoo','2011-06-01 02:06:40'),(1078,156,'Hinds','2011-06-01 02:06:40'),(1079,156,'Rankin','2011-06-01 02:06:40'),(1080,156,'Simpson','2011-06-01 02:06:40'),(1081,156,'Madison','2011-06-01 02:06:40'),(1082,156,'Leake','2011-06-01 02:06:40'),(1083,156,'Newton','2011-06-01 02:06:40'),(1084,156,'Copiah','2011-06-01 02:06:40'),(1085,156,'Attala','2011-06-01 02:06:40'),(1086,156,'Jefferson','2011-06-01 02:06:40'),(1087,156,'Scott','2011-06-01 02:06:40'),(1088,156,'Claiborne','2011-06-01 02:06:40'),(1089,156,'Smith','2011-06-01 02:06:40'),(1090,156,'Covington','2011-06-01 02:06:40'),(1091,156,'Adams','2011-06-01 02:06:40'),(1092,156,'Lawrence','2011-06-01 02:06:40'),(1093,156,'Warren','2011-06-01 02:06:40'),(1094,156,'Lauderdale','2011-06-01 02:06:40'),(1095,156,'Wayne','2011-06-01 02:06:40'),(1096,156,'Kemper','2011-06-01 02:06:40'),(1097,156,'Clarke','2011-06-01 02:06:40'),(1098,156,'Jasper','2011-06-01 02:06:40'),(1099,156,'Winston','2011-06-01 02:06:40'),(1100,156,'Noxubee','2011-06-01 02:06:40'),(1101,156,'Neshoba','2011-06-01 02:06:40'),(1102,156,'Greene','2011-06-01 02:06:40'),(1103,156,'Forrest','2011-06-01 02:06:40'),(1104,156,'Jefferson davis','2011-06-01 02:06:40'),(1105,156,'Perry','2011-06-01 02:06:40'),(1106,156,'Pearl river','2011-06-01 02:06:40'),(1107,156,'Marion','2011-06-01 02:06:40'),(1108,156,'Jones','2011-06-01 02:06:40'),(1109,156,'George','2011-06-01 02:06:40'),(1110,156,'Lamar','2011-06-01 02:06:40'),(1111,156,'Harrison','2011-06-01 02:06:40'),(1112,156,'Hancock','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1113,156,'Jackson','2011-06-01 02:06:40'),(1114,156,'Stone','2011-06-01 02:06:40'),(1115,156,'Lincoln','2011-06-01 02:06:40'),(1116,156,'Franklin','2011-06-01 02:06:40'),(1117,156,'Wilkinson','2011-06-01 02:06:40'),(1118,156,'Pike','2011-06-01 02:06:40'),(1119,156,'Amite','2011-06-01 02:06:40'),(1120,156,'Walthall','2011-06-01 02:06:40'),(1121,156,'Lowndes','2011-06-01 02:06:40'),(1122,156,'Choctaw','2011-06-01 02:06:40'),(1123,156,'Webster','2011-06-01 02:06:40'),(1124,156,'Clay','2011-06-01 02:06:40'),(1125,156,'Oktibbeha','2011-06-01 02:06:40'),(1126,140,'Calhoun','2011-06-01 02:06:40'),(1127,140,'Early','2011-06-01 02:06:40'),(1128,140,'Clay','2011-06-01 02:06:40'),(1129,140,'Phelps','2011-06-01 02:06:40'),(1130,140,'Terrell','2011-06-01 02:06:40'),(1131,140,'Grady','2011-06-01 02:06:40'),(1132,140,'Seminole','2011-06-01 02:06:40'),(1133,140,'Quitman','2011-06-01 02:06:40'),(1134,140,'Baker','2011-06-01 02:06:40'),(1135,140,'Randolph','2011-06-01 02:06:40'),(1136,148,'Shelby','2011-06-01 02:06:40'),(1137,148,'Nelson','2011-06-01 02:06:40'),(1138,148,'Trimble','2011-06-01 02:06:40'),(1139,148,'Henry','2011-06-01 02:06:40'),(1140,148,'Marion','2011-06-01 02:06:40'),(1141,148,'Oldham','2011-06-01 02:06:40'),(1142,148,'Jefferson','2011-06-01 02:06:40'),(1143,148,'Washington','2011-06-01 02:06:40'),(1144,148,'Spencer','2011-06-01 02:06:40'),(1145,148,'Bullitt','2011-06-01 02:06:40'),(1146,148,'Meade','2011-06-01 02:06:40'),(1147,148,'Breckinridge','2011-06-01 02:06:40'),(1148,148,'Grayson','2011-06-01 02:06:40'),(1149,148,'Hardin','2011-06-01 02:06:40'),(1150,148,'Mercer','2011-06-01 02:06:40'),(1151,148,'Nicholas','2011-06-01 02:06:40'),(1152,148,'Powell','2011-06-01 02:06:40'),(1153,148,'Rowan','2011-06-01 02:06:40'),(1154,148,'Menifee','2011-06-01 02:06:40'),(1155,148,'Scott','2011-06-01 02:06:40'),(1156,148,'Montgomery','2011-06-01 02:06:40'),(1157,148,'Estill','2011-06-01 02:06:40'),(1158,148,'Jessamine','2011-06-01 02:06:40'),(1159,148,'Anderson','2011-06-01 02:06:40'),(1160,148,'Woodford','2011-06-01 02:06:40'),(1161,148,'Bourbon','2011-06-01 02:06:40'),(1162,148,'Owen','2011-06-01 02:06:40'),(1163,148,'Bath','2011-06-01 02:06:40'),(1164,148,'Madison','2011-06-01 02:06:40'),(1165,148,'Clark','2011-06-01 02:06:40'),(1166,148,'Jackson','2011-06-01 02:06:40'),(1167,148,'Rockcastle','2011-06-01 02:06:40'),(1168,148,'Garrard','2011-06-01 02:06:40'),(1169,148,'Lincoln','2011-06-01 02:06:40'),(1170,148,'Boyle','2011-06-01 02:06:40'),(1171,148,'Fayette','2011-06-01 02:06:40'),(1172,148,'Franklin','2011-06-01 02:06:40'),(1173,148,'Whitley','2011-06-01 02:06:40'),(1174,148,'Laurel','2011-06-01 02:06:40'),(1175,148,'Knox','2011-06-01 02:06:40'),(1176,148,'Harlan','2011-06-01 02:06:40'),(1177,148,'Leslie','2011-06-01 02:06:40'),(1178,148,'Bell','2011-06-01 02:06:40'),(1179,148,'Letcher','2011-06-01 02:06:40'),(1180,148,'Clay','2011-06-01 02:06:40'),(1181,148,'Perry','2011-06-01 02:06:40'),(1182,148,'Campbell','2011-06-01 02:06:40'),(1183,148,'Bracken','2011-06-01 02:06:40'),(1184,148,'Harrison','2011-06-01 02:06:40'),(1185,148,'Boone','2011-06-01 02:06:40'),(1186,148,'Pendleton','2011-06-01 02:06:40'),(1187,148,'Carroll','2011-06-01 02:06:40'),(1188,148,'Grant','2011-06-01 02:06:40'),(1189,148,'Kenton','2011-06-01 02:06:40'),(1190,148,'Mason','2011-06-01 02:06:40'),(1191,148,'Fleming','2011-06-01 02:06:40'),(1192,148,'Gallatin','2011-06-01 02:06:40'),(1193,148,'Robertson','2011-06-01 02:06:40'),(1194,148,'Boyd','2011-06-01 02:06:40'),(1195,148,'Greenup','2011-06-01 02:06:40'),(1196,148,'Lawrence','2011-06-01 02:06:40'),(1197,148,'Carter','2011-06-01 02:06:40'),(1198,148,'Lewis','2011-06-01 02:06:40'),(1199,148,'Elliott','2011-06-01 02:06:40'),(1200,148,'Martin','2011-06-01 02:06:40'),(1201,148,'Johnson','2011-06-01 02:06:40'),(1202,148,'Wolfe','2011-06-01 02:06:40'),(1203,148,'Breathitt','2011-06-01 02:06:40'),(1204,148,'Lee','2011-06-01 02:06:40'),(1205,148,'Owsley','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1206,148,'Morgan','2011-06-01 02:06:40'),(1207,148,'Magoffin','2011-06-01 02:06:40'),(1208,148,'Pike','2011-06-01 02:06:40'),(1209,148,'Floyd','2011-06-01 02:06:40'),(1210,148,'Knott','2011-06-01 02:06:40'),(1211,148,'Mccracken','2011-06-01 02:06:40'),(1212,148,'Calloway','2011-06-01 02:06:40'),(1213,148,'Carlisle','2011-06-01 02:06:40'),(1214,148,'Ballard','2011-06-01 02:06:40'),(1215,148,'Marshall','2011-06-01 02:06:40'),(1216,148,'Graves','2011-06-01 02:06:40'),(1217,148,'Livingston','2011-06-01 02:06:40'),(1218,148,'Hickman','2011-06-01 02:06:40'),(1219,148,'Crittenden','2011-06-01 02:06:40'),(1220,148,'Lyon','2011-06-01 02:06:40'),(1221,148,'Fulton','2011-06-01 02:06:40'),(1222,148,'Warren','2011-06-01 02:06:40'),(1223,148,'Allen','2011-06-01 02:06:40'),(1224,148,'Barren','2011-06-01 02:06:40'),(1225,148,'Metcalfe','2011-06-01 02:06:40'),(1226,148,'Monroe','2011-06-01 02:06:40'),(1227,148,'Simpson','2011-06-01 02:06:40'),(1228,148,'Edmonson','2011-06-01 02:06:40'),(1229,148,'Butler','2011-06-01 02:06:40'),(1230,148,'Logan','2011-06-01 02:06:40'),(1231,148,'Todd','2011-06-01 02:06:40'),(1232,148,'Trigg','2011-06-01 02:06:40'),(1233,148,'Christian','2011-06-01 02:06:40'),(1234,148,'Daviess','2011-06-01 02:06:40'),(1235,148,'Ohio','2011-06-01 02:06:40'),(1236,148,'Muhlenberg','2011-06-01 02:06:40'),(1237,148,'Mclean','2011-06-01 02:06:40'),(1238,148,'Hancock','2011-06-01 02:06:40'),(1239,148,'Henderson','2011-06-01 02:06:40'),(1240,148,'Webster','2011-06-01 02:06:40'),(1241,148,'Hopkins','2011-06-01 02:06:40'),(1242,148,'Caldwell','2011-06-01 02:06:40'),(1243,148,'Union','2011-06-01 02:06:40'),(1244,148,'Pulaski','2011-06-01 02:06:40'),(1245,148,'Casey','2011-06-01 02:06:40'),(1246,148,'Clinton','2011-06-01 02:06:40'),(1247,148,'Russell','2011-06-01 02:06:40'),(1248,148,'Mccreary','2011-06-01 02:06:40'),(1249,148,'Wayne','2011-06-01 02:06:40'),(1250,148,'Hart','2011-06-01 02:06:40'),(1251,148,'Adair','2011-06-01 02:06:40'),(1252,148,'Larue','2011-06-01 02:06:40'),(1253,148,'Cumberland','2011-06-01 02:06:40'),(1254,148,'Taylor','2011-06-01 02:06:40'),(1255,148,'Green','2011-06-01 02:06:40'),(1256,168,'Licking','2011-06-01 02:06:40'),(1257,168,'Franklin','2011-06-01 02:06:40'),(1258,168,'Delaware','2011-06-01 02:06:40'),(1259,168,'Knox','2011-06-01 02:06:40'),(1260,168,'Union','2011-06-01 02:06:40'),(1261,168,'Champaign','2011-06-01 02:06:40'),(1262,168,'Clark','2011-06-01 02:06:40'),(1263,168,'Fairfield','2011-06-01 02:06:40'),(1264,168,'Madison','2011-06-01 02:06:40'),(1265,168,'Perry','2011-06-01 02:06:40'),(1266,168,'Ross','2011-06-01 02:06:40'),(1267,168,'Pickaway','2011-06-01 02:06:40'),(1268,168,'Fayette','2011-06-01 02:06:40'),(1269,168,'Hocking','2011-06-01 02:06:40'),(1270,168,'Marion','2011-06-01 02:06:40'),(1271,168,'Logan','2011-06-01 02:06:40'),(1272,168,'Morrow','2011-06-01 02:06:40'),(1273,168,'Wyandot','2011-06-01 02:06:40'),(1274,168,'Hardin','2011-06-01 02:06:40'),(1275,168,'Wood','2011-06-01 02:06:40'),(1276,168,'Sandusky','2011-06-01 02:06:40'),(1277,168,'Ottawa','2011-06-01 02:06:40'),(1278,168,'Lucas','2011-06-01 02:06:40'),(1279,168,'Erie','2011-06-01 02:06:40'),(1280,168,'Williams','2011-06-01 02:06:40'),(1281,168,'Fulton','2011-06-01 02:06:40'),(1282,168,'Henry','2011-06-01 02:06:40'),(1283,168,'Defiance','2011-06-01 02:06:40'),(1284,168,'Muskingum','2011-06-01 02:06:40'),(1285,168,'Noble','2011-06-01 02:06:40'),(1286,168,'Belmont','2011-06-01 02:06:40'),(1287,168,'Monroe','2011-06-01 02:06:40'),(1288,168,'Guernsey','2011-06-01 02:06:40'),(1289,168,'Morgan','2011-06-01 02:06:40'),(1290,168,'Coshocton','2011-06-01 02:06:40'),(1291,168,'Tuscarawas','2011-06-01 02:06:40'),(1292,168,'Jefferson','2011-06-01 02:06:40'),(1293,168,'Harrison','2011-06-01 02:06:40'),(1294,168,'Columbiana','2011-06-01 02:06:40'),(1295,168,'Lorain','2011-06-01 02:06:40'),(1296,168,'Ashtabula','2011-06-01 02:06:40'),(1297,168,'Cuyahoga','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1298,168,'Geauga','2011-06-01 02:06:40'),(1299,168,'Lake','2011-06-01 02:06:40'),(1300,168,'Summit','2011-06-01 02:06:40'),(1301,168,'Portage','2011-06-01 02:06:40'),(1302,168,'Medina','2011-06-01 02:06:40'),(1303,168,'Wayne','2011-06-01 02:06:40'),(1304,168,'Mahoning','2011-06-01 02:06:40'),(1305,168,'Trumbull','2011-06-01 02:06:40'),(1306,168,'Stark','2011-06-01 02:06:40'),(1307,168,'Carroll','2011-06-01 02:06:40'),(1308,168,'Holmes','2011-06-01 02:06:40'),(1309,168,'Seneca','2011-06-01 02:06:40'),(1310,168,'Hancock','2011-06-01 02:06:40'),(1311,168,'Ashland','2011-06-01 02:06:40'),(1312,168,'Huron','2011-06-01 02:06:40'),(1313,168,'Richland','2011-06-01 02:06:40'),(1314,168,'Crawford','2011-06-01 02:06:40'),(1315,168,'Hamilton','2011-06-01 02:06:40'),(1316,168,'Butler','2011-06-01 02:06:40'),(1317,168,'Warren','2011-06-01 02:06:40'),(1318,168,'Preble','2011-06-01 02:06:40'),(1319,168,'Brown','2011-06-01 02:06:40'),(1320,168,'Clermont','2011-06-01 02:06:40'),(1321,168,'Adams','2011-06-01 02:06:40'),(1322,168,'Clinton','2011-06-01 02:06:40'),(1323,168,'Highland','2011-06-01 02:06:40'),(1324,168,'Greene','2011-06-01 02:06:40'),(1325,168,'Shelby','2011-06-01 02:06:40'),(1326,168,'Darke','2011-06-01 02:06:40'),(1327,168,'Miami','2011-06-01 02:06:40'),(1328,168,'Montgomery','2011-06-01 02:06:40'),(1329,168,'Mercer','2011-06-01 02:06:40'),(1330,168,'Pike','2011-06-01 02:06:40'),(1331,168,'Gallia','2011-06-01 02:06:40'),(1332,168,'Lawrence','2011-06-01 02:06:40'),(1333,168,'Jackson','2011-06-01 02:06:40'),(1334,168,'Vinton','2011-06-01 02:06:40'),(1335,168,'Scioto','2011-06-01 02:06:40'),(1336,168,'Athens','2011-06-01 02:06:40'),(1337,168,'Washington','2011-06-01 02:06:40'),(1338,168,'Meigs','2011-06-01 02:06:40'),(1339,168,'Allen','2011-06-01 02:06:40'),(1340,168,'Auglaize','2011-06-01 02:06:40'),(1341,168,'Paulding','2011-06-01 02:06:40'),(1342,168,'Putnam','2011-06-01 02:06:40'),(1343,168,'Van wert','2011-06-01 02:06:40'),(1344,145,'Madison','2011-06-01 02:06:40'),(1345,145,'Hamilton','2011-06-01 02:06:40'),(1346,145,'Clinton','2011-06-01 02:06:40'),(1347,145,'Hancock','2011-06-01 02:06:40'),(1348,145,'Tipton','2011-06-01 02:06:40'),(1349,145,'Boone','2011-06-01 02:06:40'),(1350,145,'Hendricks','2011-06-01 02:06:40'),(1351,145,'Rush','2011-06-01 02:06:40'),(1352,145,'Putnam','2011-06-01 02:06:40'),(1353,145,'Johnson','2011-06-01 02:06:40'),(1354,145,'Marion','2011-06-01 02:06:40'),(1355,145,'Shelby','2011-06-01 02:06:40'),(1356,145,'Morgan','2011-06-01 02:06:40'),(1357,145,'Fayette','2011-06-01 02:06:40'),(1358,145,'Henry','2011-06-01 02:06:40'),(1359,145,'Brown','2011-06-01 02:06:40'),(1360,145,'Porter','2011-06-01 02:06:40'),(1361,145,'Lake','2011-06-01 02:06:40'),(1362,145,'Jasper','2011-06-01 02:06:40'),(1363,145,'La porte','2011-06-01 02:06:40'),(1364,145,'Newton','2011-06-01 02:06:40'),(1365,145,'Starke','2011-06-01 02:06:40'),(1366,145,'Marshall','2011-06-01 02:06:40'),(1367,145,'Kosciusko','2011-06-01 02:06:40'),(1368,145,'Elkhart','2011-06-01 02:06:40'),(1369,145,'St joseph','2011-06-01 02:06:40'),(1370,145,'Lagrange','2011-06-01 02:06:40'),(1371,145,'Noble','2011-06-01 02:06:40'),(1372,145,'Huntington','2011-06-01 02:06:40'),(1373,145,'Steuben','2011-06-01 02:06:40'),(1374,145,'Allen','2011-06-01 02:06:40'),(1375,145,'De kalb','2011-06-01 02:06:40'),(1376,145,'Adams','2011-06-01 02:06:40'),(1377,145,'Wells','2011-06-01 02:06:40'),(1378,145,'Whitley','2011-06-01 02:06:40'),(1379,145,'Howard','2011-06-01 02:06:40'),(1380,145,'Fulton','2011-06-01 02:06:40'),(1381,145,'Miami','2011-06-01 02:06:40'),(1382,145,'Carroll','2011-06-01 02:06:40'),(1383,145,'Grant','2011-06-01 02:06:40'),(1384,145,'Cass','2011-06-01 02:06:40'),(1385,145,'Wabash','2011-06-01 02:06:40'),(1386,145,'Pulaski','2011-06-01 02:06:40'),(1387,145,'Dearborn','2011-06-01 02:06:40'),(1388,145,'Union','2011-06-01 02:06:40'),(1389,145,'Ripley','2011-06-01 02:06:40'),(1390,145,'Franklin','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1391,145,'Switzerland','2011-06-01 02:06:40'),(1392,145,'Ohio','2011-06-01 02:06:40'),(1393,145,'Scott','2011-06-01 02:06:40'),(1394,145,'Clark','2011-06-01 02:06:40'),(1395,145,'Harrison','2011-06-01 02:06:40'),(1396,145,'Washington','2011-06-01 02:06:40'),(1397,145,'Crawford','2011-06-01 02:06:40'),(1398,145,'Floyd','2011-06-01 02:06:40'),(1399,145,'Bartholomew','2011-06-01 02:06:40'),(1400,145,'Jackson','2011-06-01 02:06:40'),(1401,145,'Jennings','2011-06-01 02:06:40'),(1402,145,'Jefferson','2011-06-01 02:06:40'),(1403,145,'Decatur','2011-06-01 02:06:40'),(1404,145,'Delaware','2011-06-01 02:06:40'),(1405,145,'Wayne','2011-06-01 02:06:40'),(1406,145,'Jay','2011-06-01 02:06:40'),(1407,145,'Randolph','2011-06-01 02:06:40'),(1408,145,'Blackford','2011-06-01 02:06:40'),(1409,145,'Monroe','2011-06-01 02:06:40'),(1410,145,'Lawrence','2011-06-01 02:06:40'),(1411,145,'Greene','2011-06-01 02:06:40'),(1412,145,'Owen','2011-06-01 02:06:40'),(1413,145,'Orange','2011-06-01 02:06:40'),(1414,145,'Daviess','2011-06-01 02:06:40'),(1415,145,'Knox','2011-06-01 02:06:40'),(1416,145,'Dubois','2011-06-01 02:06:40'),(1417,145,'Perry','2011-06-01 02:06:40'),(1418,145,'Martin','2011-06-01 02:06:40'),(1419,145,'Spencer','2011-06-01 02:06:40'),(1420,145,'Pike','2011-06-01 02:06:40'),(1421,145,'Warrick','2011-06-01 02:06:40'),(1422,145,'Posey','2011-06-01 02:06:40'),(1423,145,'Vanderburgh','2011-06-01 02:06:40'),(1424,145,'Gibson','2011-06-01 02:06:40'),(1425,145,'Vigo','2011-06-01 02:06:40'),(1426,145,'Parke','2011-06-01 02:06:40'),(1427,145,'Vermillion','2011-06-01 02:06:40'),(1428,145,'Clay','2011-06-01 02:06:40'),(1429,145,'Sullivan','2011-06-01 02:06:40'),(1430,145,'Tippecanoe','2011-06-01 02:06:40'),(1431,145,'Montgomery','2011-06-01 02:06:40'),(1432,145,'Benton','2011-06-01 02:06:40'),(1433,145,'Fountain','2011-06-01 02:06:40'),(1434,145,'White','2011-06-01 02:06:40'),(1435,145,'Warren','2011-06-01 02:06:40'),(1436,154,'Saint clair','2011-06-01 02:06:40'),(1437,154,'Lapeer','2011-06-01 02:06:40'),(1438,154,'Macomb','2011-06-01 02:06:40'),(1439,154,'Oakland','2011-06-01 02:06:40'),(1440,154,'Wayne','2011-06-01 02:06:40'),(1441,154,'Washtenaw','2011-06-01 02:06:40'),(1442,154,'Monroe','2011-06-01 02:06:40'),(1443,154,'Livingston','2011-06-01 02:06:40'),(1444,154,'Sanilac','2011-06-01 02:06:40'),(1445,154,'Genesee','2011-06-01 02:06:40'),(1446,154,'Huron','2011-06-01 02:06:40'),(1447,154,'Shiawassee','2011-06-01 02:06:40'),(1448,154,'Saginaw','2011-06-01 02:06:40'),(1449,154,'Tuscola','2011-06-01 02:06:40'),(1450,154,'Ogemaw','2011-06-01 02:06:40'),(1451,154,'Bay','2011-06-01 02:06:40'),(1452,154,'Gladwin','2011-06-01 02:06:40'),(1453,154,'Gratiot','2011-06-01 02:06:40'),(1454,154,'Clare','2011-06-01 02:06:40'),(1455,154,'Midland','2011-06-01 02:06:40'),(1456,154,'Oscoda','2011-06-01 02:06:40'),(1457,154,'Roscommon','2011-06-01 02:06:40'),(1458,154,'Arenac','2011-06-01 02:06:40'),(1459,154,'Alcona','2011-06-01 02:06:40'),(1460,154,'Iosco','2011-06-01 02:06:40'),(1461,154,'Isabella','2011-06-01 02:06:40'),(1462,154,'Ingham','2011-06-01 02:06:40'),(1463,154,'Clinton','2011-06-01 02:06:40'),(1464,154,'Ionia','2011-06-01 02:06:40'),(1465,154,'Montcalm','2011-06-01 02:06:40'),(1466,154,'Eaton','2011-06-01 02:06:40'),(1467,154,'Barry','2011-06-01 02:06:40'),(1468,154,'Kalamazoo','2011-06-01 02:06:40'),(1469,154,'Allegan','2011-06-01 02:06:40'),(1470,154,'Calhoun','2011-06-01 02:06:40'),(1471,154,'Van buren','2011-06-01 02:06:40'),(1472,154,'Berrien','2011-06-01 02:06:40'),(1473,154,'Branch','2011-06-01 02:06:40'),(1474,154,'Saint joseph','2011-06-01 02:06:40'),(1475,154,'Cass','2011-06-01 02:06:40'),(1476,154,'Jackson','2011-06-01 02:06:40'),(1477,154,'Lenawee','2011-06-01 02:06:40'),(1478,154,'Hillsdale','2011-06-01 02:06:40'),(1479,154,'Kent','2011-06-01 02:06:40'),(1480,154,'Muskegon','2011-06-01 02:06:40'),(1481,154,'Lake','2011-06-01 02:06:40'),(1482,154,'Mecosta','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1483,154,'Newaygo','2011-06-01 02:06:40'),(1484,154,'Ottawa','2011-06-01 02:06:40'),(1485,154,'Mason','2011-06-01 02:06:40'),(1486,154,'Oceana','2011-06-01 02:06:40'),(1487,154,'Wexford','2011-06-01 02:06:40'),(1488,154,'Grand traverse','2011-06-01 02:06:40'),(1489,154,'Antrim','2011-06-01 02:06:40'),(1490,154,'Manistee','2011-06-01 02:06:40'),(1491,154,'Benzie','2011-06-01 02:06:40'),(1492,154,'Leelanau','2011-06-01 02:06:40'),(1493,154,'Osceola','2011-06-01 02:06:40'),(1494,154,'Missaukee','2011-06-01 02:06:40'),(1495,154,'Kalkaska','2011-06-01 02:06:40'),(1496,154,'Cheboygan','2011-06-01 02:06:40'),(1497,154,'Emmet','2011-06-01 02:06:40'),(1498,154,'Alpena','2011-06-01 02:06:40'),(1499,154,'Montmorency','2011-06-01 02:06:40'),(1500,154,'Chippewa','2011-06-01 02:06:40'),(1501,154,'Charlevoix','2011-06-01 02:06:40'),(1502,154,'Mackinac','2011-06-01 02:06:40'),(1503,154,'Otsego','2011-06-01 02:06:40'),(1504,154,'Crawford','2011-06-01 02:06:40'),(1505,154,'Presque isle','2011-06-01 02:06:40'),(1506,154,'Dickinson','2011-06-01 02:06:40'),(1507,154,'Keweenaw','2011-06-01 02:06:40'),(1508,154,'Alger','2011-06-01 02:06:40'),(1509,154,'Delta','2011-06-01 02:06:40'),(1510,154,'Marquette','2011-06-01 02:06:40'),(1511,154,'Menominee','2011-06-01 02:06:40'),(1512,154,'Schoolcraft','2011-06-01 02:06:40'),(1513,154,'Luce','2011-06-01 02:06:40'),(1514,154,'Iron','2011-06-01 02:06:40'),(1515,154,'Houghton','2011-06-01 02:06:40'),(1516,154,'Baraga','2011-06-01 02:06:40'),(1517,154,'Ontonagon','2011-06-01 02:06:40'),(1518,154,'Gogebic','2011-06-01 02:06:40'),(1519,146,'Warren','2011-06-01 02:06:40'),(1520,146,'Adair','2011-06-01 02:06:40'),(1521,146,'Dallas','2011-06-01 02:06:40'),(1522,146,'Marshall','2011-06-01 02:06:40'),(1523,146,'Hardin','2011-06-01 02:06:40'),(1524,146,'Polk','2011-06-01 02:06:40'),(1525,146,'Wayne','2011-06-01 02:06:40'),(1526,146,'Story','2011-06-01 02:06:40'),(1527,146,'Cass','2011-06-01 02:06:40'),(1528,146,'Audubon','2011-06-01 02:06:40'),(1529,146,'Guthrie','2011-06-01 02:06:40'),(1530,146,'Mahaska','2011-06-01 02:06:40'),(1531,146,'Jasper','2011-06-01 02:06:40'),(1532,146,'Boone','2011-06-01 02:06:40'),(1533,146,'Madison','2011-06-01 02:06:40'),(1534,146,'Hamilton','2011-06-01 02:06:40'),(1535,146,'Franklin','2011-06-01 02:06:40'),(1536,146,'Marion','2011-06-01 02:06:40'),(1537,146,'Lucas','2011-06-01 02:06:40'),(1538,146,'Greene','2011-06-01 02:06:40'),(1539,146,'Carroll','2011-06-01 02:06:40'),(1540,146,'Decatur','2011-06-01 02:06:40'),(1541,146,'Wright','2011-06-01 02:06:40'),(1542,146,'Ringgold','2011-06-01 02:06:40'),(1543,146,'Keokuk','2011-06-01 02:06:40'),(1544,146,'Poweshiek','2011-06-01 02:06:40'),(1545,146,'Union','2011-06-01 02:06:40'),(1546,146,'Monroe','2011-06-01 02:06:40'),(1547,146,'Tama','2011-06-01 02:06:40'),(1548,146,'Clarke','2011-06-01 02:06:40'),(1549,146,'Cerro gordo','2011-06-01 02:06:40'),(1550,146,'Hancock','2011-06-01 02:06:40'),(1551,146,'Winnebago','2011-06-01 02:06:40'),(1552,146,'Mitchell','2011-06-01 02:06:40'),(1553,146,'Worth','2011-06-01 02:06:40'),(1554,146,'Floyd','2011-06-01 02:06:40'),(1555,146,'Kossuth','2011-06-01 02:06:40'),(1556,146,'Howard','2011-06-01 02:06:40'),(1557,146,'Webster','2011-06-01 02:06:40'),(1558,146,'Buena vista','2011-06-01 02:06:40'),(1559,146,'Emmet','2011-06-01 02:06:40'),(1560,146,'Palo alto','2011-06-01 02:06:40'),(1561,146,'Humboldt','2011-06-01 02:06:40'),(1562,146,'Sac','2011-06-01 02:06:40'),(1563,146,'Calhoun','2011-06-01 02:06:40'),(1564,146,'Pocahontas','2011-06-01 02:06:40'),(1565,146,'Butler','2011-06-01 02:06:40'),(1566,146,'Chickasaw','2011-06-01 02:06:40'),(1567,146,'Fayette','2011-06-01 02:06:40'),(1568,146,'Buchanan','2011-06-01 02:06:40'),(1569,146,'Grundy','2011-06-01 02:06:40'),(1570,146,'Black hawk','2011-06-01 02:06:40'),(1571,146,'Bremer','2011-06-01 02:06:40'),(1572,146,'Delaware','2011-06-01 02:06:40'),(1573,146,'Taylor','2011-06-01 02:06:40'),(1574,146,'Adams','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1575,146,'Montgomery','2011-06-01 02:06:40'),(1576,146,'Plymouth','2011-06-01 02:06:40'),(1577,146,'Sioux','2011-06-01 02:06:40'),(1578,146,'Woodbury','2011-06-01 02:06:40'),(1579,146,'Cherokee','2011-06-01 02:06:40'),(1580,146,'Ida','2011-06-01 02:06:40'),(1581,146,'Obrien','2011-06-01 02:06:40'),(1582,146,'Monona','2011-06-01 02:06:40'),(1583,146,'Clay','2011-06-01 02:06:40'),(1584,146,'Lyon','2011-06-01 02:06:40'),(1585,146,'Osceola','2011-06-01 02:06:40'),(1586,146,'Dickinson','2011-06-01 02:06:40'),(1587,146,'Crawford','2011-06-01 02:06:40'),(1588,146,'Shelby','2011-06-01 02:06:40'),(1589,146,'Pottawattamie','2011-06-01 02:06:40'),(1590,146,'Harrison','2011-06-01 02:06:40'),(1591,146,'Mills','2011-06-01 02:06:40'),(1592,146,'Page','2011-06-01 02:06:40'),(1593,146,'Fremont','2011-06-01 02:06:40'),(1594,146,'Dubuque','2011-06-01 02:06:40'),(1595,146,'Jackson','2011-06-01 02:06:40'),(1596,146,'Clinton','2011-06-01 02:06:40'),(1597,146,'Clayton','2011-06-01 02:06:40'),(1598,146,'Winneshiek','2011-06-01 02:06:40'),(1599,146,'Allamakee','2011-06-01 02:06:40'),(1600,146,'Washington','2011-06-01 02:06:40'),(1601,146,'Linn','2011-06-01 02:06:40'),(1602,146,'Iowa','2011-06-01 02:06:40'),(1603,146,'Jones','2011-06-01 02:06:40'),(1604,146,'Benton','2011-06-01 02:06:40'),(1605,146,'Cedar','2011-06-01 02:06:40'),(1606,146,'Johnson','2011-06-01 02:06:40'),(1607,146,'Wapello','2011-06-01 02:06:40'),(1608,146,'Jefferson','2011-06-01 02:06:40'),(1609,146,'Van buren','2011-06-01 02:06:40'),(1610,146,'Davis','2011-06-01 02:06:40'),(1611,146,'Appanoose','2011-06-01 02:06:40'),(1612,146,'Des moines','2011-06-01 02:06:40'),(1613,146,'Lee','2011-06-01 02:06:40'),(1614,146,'Henry','2011-06-01 02:06:40'),(1615,146,'Louisa','2011-06-01 02:06:40'),(1616,146,'Muscatine','2011-06-01 02:06:40'),(1617,146,'Scott','2011-06-01 02:06:40'),(1618,185,'Sheboygan','2011-06-01 02:06:40'),(1619,185,'Washington','2011-06-01 02:06:40'),(1620,185,'Dodge','2011-06-01 02:06:40'),(1621,185,'Ozaukee','2011-06-01 02:06:40'),(1622,185,'Waukesha','2011-06-01 02:06:40'),(1623,185,'Fond du lac','2011-06-01 02:06:40'),(1624,185,'Calumet','2011-06-01 02:06:40'),(1625,185,'Manitowoc','2011-06-01 02:06:40'),(1626,185,'Jefferson','2011-06-01 02:06:40'),(1627,185,'Kenosha','2011-06-01 02:06:40'),(1628,185,'Racine','2011-06-01 02:06:40'),(1629,185,'Milwaukee','2011-06-01 02:06:40'),(1630,185,'Walworth','2011-06-01 02:06:40'),(1631,185,'Rock','2011-06-01 02:06:40'),(1632,185,'Green','2011-06-01 02:06:40'),(1633,185,'Iowa','2011-06-01 02:06:40'),(1634,185,'Lafayette','2011-06-01 02:06:40'),(1635,185,'Dane','2011-06-01 02:06:40'),(1636,185,'Grant','2011-06-01 02:06:40'),(1637,185,'Richland','2011-06-01 02:06:40'),(1638,185,'Columbia','2011-06-01 02:06:40'),(1639,185,'Sauk','2011-06-01 02:06:40'),(1640,185,'Crawford','2011-06-01 02:06:40'),(1641,185,'Adams','2011-06-01 02:06:40'),(1642,185,'Marquette','2011-06-01 02:06:40'),(1643,185,'Green lake','2011-06-01 02:06:40'),(1644,185,'Juneau','2011-06-01 02:06:40'),(1645,185,'Polk','2011-06-01 02:06:40'),(1646,185,'Saint croix','2011-06-01 02:06:40'),(1647,185,'Pierce','2011-06-01 02:06:40'),(1648,185,'Oconto','2011-06-01 02:06:40'),(1649,185,'Marinette','2011-06-01 02:06:40'),(1650,185,'Forest','2011-06-01 02:06:40'),(1651,185,'Outagamie','2011-06-01 02:06:40'),(1652,185,'Shawano','2011-06-01 02:06:40'),(1653,185,'Brown','2011-06-01 02:06:40'),(1654,185,'Florence','2011-06-01 02:06:40'),(1655,185,'Menominee','2011-06-01 02:06:40'),(1656,185,'Kewaunee','2011-06-01 02:06:40'),(1657,185,'Door','2011-06-01 02:06:40'),(1658,185,'Marathon','2011-06-01 02:06:40'),(1659,185,'Wood','2011-06-01 02:06:40'),(1660,185,'Clark','2011-06-01 02:06:40'),(1661,185,'Portage','2011-06-01 02:06:40'),(1662,185,'Langlade','2011-06-01 02:06:40'),(1663,185,'Taylor','2011-06-01 02:06:40'),(1664,185,'Lincoln','2011-06-01 02:06:40'),(1665,185,'Price','2011-06-01 02:06:40'),(1666,185,'Oneida','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1667,185,'Vilas','2011-06-01 02:06:40'),(1668,185,'Ashland','2011-06-01 02:06:40'),(1669,185,'Iron','2011-06-01 02:06:40'),(1670,185,'Rusk','2011-06-01 02:06:40'),(1671,185,'La crosse','2011-06-01 02:06:40'),(1672,185,'Buffalo','2011-06-01 02:06:40'),(1673,185,'Jackson','2011-06-01 02:06:40'),(1674,185,'Trempealeau','2011-06-01 02:06:40'),(1675,185,'Monroe','2011-06-01 02:06:40'),(1676,185,'Vernon','2011-06-01 02:06:40'),(1677,185,'Eau claire','2011-06-01 02:06:40'),(1678,185,'Pepin','2011-06-01 02:06:40'),(1679,185,'Chippewa','2011-06-01 02:06:40'),(1680,185,'Dunn','2011-06-01 02:06:40'),(1681,185,'Barron','2011-06-01 02:06:40'),(1682,185,'Washburn','2011-06-01 02:06:40'),(1683,185,'Bayfield','2011-06-01 02:06:40'),(1684,185,'Douglas','2011-06-01 02:06:40'),(1685,185,'Sawyer','2011-06-01 02:06:40'),(1686,185,'Burnett','2011-06-01 02:06:40'),(1687,185,'Winnebago','2011-06-01 02:06:40'),(1688,185,'Waupaca','2011-06-01 02:06:40'),(1689,185,'Waushara','2011-06-01 02:06:40'),(1690,155,'Washington','2011-06-01 02:06:40'),(1691,155,'Chisago','2011-06-01 02:06:40'),(1692,155,'Anoka','2011-06-01 02:06:40'),(1693,155,'Isanti','2011-06-01 02:06:40'),(1694,155,'Pine','2011-06-01 02:06:40'),(1695,155,'Goodhue','2011-06-01 02:06:40'),(1696,155,'Dakota','2011-06-01 02:06:40'),(1697,155,'Rice','2011-06-01 02:06:40'),(1698,155,'Scott','2011-06-01 02:06:40'),(1699,155,'Wabasha','2011-06-01 02:06:40'),(1700,155,'Steele','2011-06-01 02:06:40'),(1701,155,'Kanabec','2011-06-01 02:06:40'),(1702,155,'Ramsey','2011-06-01 02:06:40'),(1703,155,'Hennepin','2011-06-01 02:06:40'),(1704,155,'Wright','2011-06-01 02:06:40'),(1705,155,'Sibley','2011-06-01 02:06:40'),(1706,155,'Sherburne','2011-06-01 02:06:40'),(1707,155,'Renville','2011-06-01 02:06:40'),(1708,155,'Mcleod','2011-06-01 02:06:40'),(1709,155,'Carver','2011-06-01 02:06:40'),(1710,155,'Meeker','2011-06-01 02:06:40'),(1711,155,'Stearns','2011-06-01 02:06:40'),(1712,155,'Mille lacs','2011-06-01 02:06:40'),(1713,155,'Lake','2011-06-01 02:06:40'),(1714,155,'Saint louis','2011-06-01 02:06:40'),(1715,155,'Cook','2011-06-01 02:06:40'),(1716,155,'Carlton','2011-06-01 02:06:40'),(1717,155,'Itasca','2011-06-01 02:06:40'),(1718,155,'Aitkin','2011-06-01 02:06:40'),(1719,155,'Olmsted','2011-06-01 02:06:40'),(1720,155,'Winona','2011-06-01 02:06:40'),(1721,155,'Houston','2011-06-01 02:06:40'),(1722,155,'Fillmore','2011-06-01 02:06:40'),(1723,155,'Dodge','2011-06-01 02:06:40'),(1724,155,'Blue earth','2011-06-01 02:06:40'),(1725,155,'Nicollet','2011-06-01 02:06:40'),(1726,155,'Freeborn','2011-06-01 02:06:40'),(1727,155,'Faribault','2011-06-01 02:06:40'),(1728,155,'Le sueur','2011-06-01 02:06:40'),(1729,155,'Brown','2011-06-01 02:06:40'),(1730,155,'Watonwan','2011-06-01 02:06:40'),(1731,155,'Martin','2011-06-01 02:06:40'),(1732,155,'Waseca','2011-06-01 02:06:40'),(1733,155,'Redwood','2011-06-01 02:06:40'),(1734,155,'Cottonwood','2011-06-01 02:06:40'),(1735,155,'Nobles','2011-06-01 02:06:40'),(1736,155,'Jackson','2011-06-01 02:06:40'),(1737,155,'Lincoln','2011-06-01 02:06:40'),(1738,155,'Murray','2011-06-01 02:06:40'),(1739,155,'Lyon','2011-06-01 02:06:40'),(1740,155,'Rock','2011-06-01 02:06:40'),(1741,155,'Pipestone','2011-06-01 02:06:40'),(1742,155,'Kandiyohi','2011-06-01 02:06:40'),(1743,155,'Stevens','2011-06-01 02:06:40'),(1744,155,'Swift','2011-06-01 02:06:40'),(1745,155,'Big stone','2011-06-01 02:06:40'),(1746,155,'Lac qui parle','2011-06-01 02:06:40'),(1747,155,'Traverse','2011-06-01 02:06:40'),(1748,155,'Yellow medicine','2011-06-01 02:06:40'),(1749,155,'Chippewa','2011-06-01 02:06:40'),(1750,155,'Grant','2011-06-01 02:06:40'),(1751,155,'Douglas','2011-06-01 02:06:40'),(1752,155,'Morrison','2011-06-01 02:06:40'),(1753,155,'Todd','2011-06-01 02:06:40'),(1754,155,'Pope','2011-06-01 02:06:40'),(1755,155,'Otter tail','2011-06-01 02:06:40'),(1756,155,'Benton','2011-06-01 02:06:40'),(1757,155,'Crow wing','2011-06-01 02:06:40'),(1758,155,'Cass','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1759,155,'Hubbard','2011-06-01 02:06:40'),(1760,155,'Wadena','2011-06-01 02:06:40'),(1761,155,'Becker','2011-06-01 02:06:40'),(1762,155,'Norman','2011-06-01 02:06:40'),(1763,155,'Clay','2011-06-01 02:06:40'),(1764,155,'Mahnomen','2011-06-01 02:06:40'),(1765,155,'Polk','2011-06-01 02:06:40'),(1766,155,'Wilkin','2011-06-01 02:06:40'),(1767,155,'Beltrami','2011-06-01 02:06:40'),(1768,155,'Clearwater','2011-06-01 02:06:40'),(1769,155,'Lake of the woods','2011-06-01 02:06:40'),(1770,155,'Koochiching','2011-06-01 02:06:40'),(1771,155,'Roseau','2011-06-01 02:06:40'),(1772,155,'Pennington','2011-06-01 02:06:40'),(1773,155,'Marshall','2011-06-01 02:06:40'),(1774,155,'Red lake','2011-06-01 02:06:40'),(1775,155,'Kittson','2011-06-01 02:06:40'),(1776,176,'Union','2011-06-01 02:06:40'),(1777,176,'Brookings','2011-06-01 02:06:40'),(1778,176,'Minnehaha','2011-06-01 02:06:40'),(1779,176,'Clay','2011-06-01 02:06:40'),(1780,176,'Mccook','2011-06-01 02:06:40'),(1781,176,'Lincoln','2011-06-01 02:06:40'),(1782,176,'Turner','2011-06-01 02:06:40'),(1783,176,'Lake','2011-06-01 02:06:40'),(1784,176,'Moody','2011-06-01 02:06:40'),(1785,176,'Hutchinson','2011-06-01 02:06:40'),(1786,176,'Yankton','2011-06-01 02:06:40'),(1787,176,'Kingsbury','2011-06-01 02:06:40'),(1788,176,'Bon homme','2011-06-01 02:06:40'),(1789,176,'Codington','2011-06-01 02:06:40'),(1790,176,'Deuel','2011-06-01 02:06:40'),(1791,176,'Grant','2011-06-01 02:06:40'),(1792,176,'Clark','2011-06-01 02:06:40'),(1793,176,'Day','2011-06-01 02:06:40'),(1794,176,'Hamlin','2011-06-01 02:06:40'),(1795,176,'Roberts','2011-06-01 02:06:40'),(1796,176,'Marshall','2011-06-01 02:06:40'),(1797,176,'Davison','2011-06-01 02:06:40'),(1798,176,'Hanson','2011-06-01 02:06:40'),(1799,176,'Jerauld','2011-06-01 02:06:40'),(1800,176,'Douglas','2011-06-01 02:06:40'),(1801,176,'Sanborn','2011-06-01 02:06:40'),(1802,176,'Gregory','2011-06-01 02:06:40'),(1803,176,'Miner','2011-06-01 02:06:40'),(1804,176,'Beadle','2011-06-01 02:06:40'),(1805,176,'Brule','2011-06-01 02:06:40'),(1806,176,'Charles mix','2011-06-01 02:06:40'),(1807,176,'Buffalo','2011-06-01 02:06:40'),(1808,176,'Hyde','2011-06-01 02:06:40'),(1809,176,'Hand','2011-06-01 02:06:40'),(1810,176,'Lyman','2011-06-01 02:06:40'),(1811,176,'Aurora','2011-06-01 02:06:40'),(1812,176,'Brown','2011-06-01 02:06:40'),(1813,176,'Walworth','2011-06-01 02:06:40'),(1814,176,'Spink','2011-06-01 02:06:40'),(1815,176,'Edmunds','2011-06-01 02:06:40'),(1816,176,'Faulk','2011-06-01 02:06:40'),(1817,176,'Mcpherson','2011-06-01 02:06:40'),(1818,176,'Potter','2011-06-01 02:06:40'),(1819,176,'Hughes','2011-06-01 02:06:40'),(1820,176,'Sully','2011-06-01 02:06:40'),(1821,176,'Jackson','2011-06-01 02:06:40'),(1822,176,'Tripp','2011-06-01 02:06:40'),(1823,176,'Jones','2011-06-01 02:06:40'),(1824,176,'Stanley','2011-06-01 02:06:40'),(1825,176,'Bennett','2011-06-01 02:06:40'),(1826,176,'Haakon','2011-06-01 02:06:40'),(1827,176,'Todd','2011-06-01 02:06:40'),(1828,176,'Mellette','2011-06-01 02:06:40'),(1829,176,'Perkins','2011-06-01 02:06:40'),(1830,176,'Corson','2011-06-01 02:06:40'),(1831,176,'Ziebach','2011-06-01 02:06:40'),(1832,176,'Dewey','2011-06-01 02:06:40'),(1833,176,'Meade','2011-06-01 02:06:40'),(1834,176,'Campbell','2011-06-01 02:06:40'),(1835,176,'Harding','2011-06-01 02:06:40'),(1836,176,'Pennington','2011-06-01 02:06:40'),(1837,176,'Shannon','2011-06-01 02:06:40'),(1838,176,'Butte','2011-06-01 02:06:40'),(1839,176,'Custer','2011-06-01 02:06:40'),(1840,176,'Lawrence','2011-06-01 02:06:40'),(1841,176,'Fall river','2011-06-01 02:06:40'),(1842,166,'Richland','2011-06-01 02:06:40'),(1843,166,'Cass','2011-06-01 02:06:40'),(1844,166,'Traill','2011-06-01 02:06:40'),(1845,166,'Sargent','2011-06-01 02:06:40'),(1846,166,'Ransom','2011-06-01 02:06:40'),(1847,166,'Barnes','2011-06-01 02:06:40'),(1848,166,'Steele','2011-06-01 02:06:40'),(1849,166,'Grand forks','2011-06-01 02:06:40'),(1850,166,'Walsh','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1851,166,'Nelson','2011-06-01 02:06:40'),(1852,166,'Pembina','2011-06-01 02:06:40'),(1853,166,'Cavalier','2011-06-01 02:06:40'),(1854,166,'Ramsey','2011-06-01 02:06:40'),(1855,166,'Rolette','2011-06-01 02:06:40'),(1856,166,'Pierce','2011-06-01 02:06:40'),(1857,166,'Towner','2011-06-01 02:06:40'),(1858,166,'Bottineau','2011-06-01 02:06:40'),(1859,166,'Wells','2011-06-01 02:06:40'),(1860,166,'Benson','2011-06-01 02:06:40'),(1861,166,'Eddy','2011-06-01 02:06:40'),(1862,166,'Stutsman','2011-06-01 02:06:40'),(1863,166,'Mcintosh','2011-06-01 02:06:40'),(1864,166,'Lamoure','2011-06-01 02:06:40'),(1865,166,'Griggs','2011-06-01 02:06:40'),(1866,166,'Foster','2011-06-01 02:06:40'),(1867,166,'Kidder','2011-06-01 02:06:40'),(1868,166,'Sheridan','2011-06-01 02:06:40'),(1869,166,'Dickey','2011-06-01 02:06:40'),(1870,166,'Logan','2011-06-01 02:06:40'),(1871,166,'Burleigh','2011-06-01 02:06:40'),(1872,166,'Morton','2011-06-01 02:06:40'),(1873,166,'Mercer','2011-06-01 02:06:40'),(1874,166,'Emmons','2011-06-01 02:06:40'),(1875,166,'Sioux','2011-06-01 02:06:40'),(1876,166,'Grant','2011-06-01 02:06:40'),(1877,166,'Oliver','2011-06-01 02:06:40'),(1878,166,'Mclean','2011-06-01 02:06:40'),(1879,166,'Stark','2011-06-01 02:06:40'),(1880,166,'Slope','2011-06-01 02:06:40'),(1881,166,'Golden valley','2011-06-01 02:06:40'),(1882,166,'Bowman','2011-06-01 02:06:40'),(1883,166,'Dunn','2011-06-01 02:06:40'),(1884,166,'Billings','2011-06-01 02:06:40'),(1885,166,'Mckenzie','2011-06-01 02:06:40'),(1886,166,'Adams','2011-06-01 02:06:40'),(1887,166,'Hettinger','2011-06-01 02:06:40'),(1888,166,'Ward','2011-06-01 02:06:40'),(1889,166,'Mchenry','2011-06-01 02:06:40'),(1890,166,'Burke','2011-06-01 02:06:40'),(1891,166,'Divide','2011-06-01 02:06:40'),(1892,166,'Renville','2011-06-01 02:06:40'),(1893,166,'Williams','2011-06-01 02:06:40'),(1894,166,'Mountrail','2011-06-01 02:06:40'),(1895,158,'Stillwater','2011-06-01 02:06:40'),(1896,158,'Yellowstone','2011-06-01 02:06:40'),(1897,158,'Rosebud','2011-06-01 02:06:40'),(1898,158,'Carbon','2011-06-01 02:06:40'),(1899,158,'Treasure','2011-06-01 02:06:40'),(1900,158,'Sweet grass','2011-06-01 02:06:40'),(1901,158,'Big horn','2011-06-01 02:06:40'),(1902,158,'Park','2011-06-01 02:06:40'),(1903,158,'Fergus','2011-06-01 02:06:40'),(1904,158,'Wheatland','2011-06-01 02:06:40'),(1905,158,'Golden valley','2011-06-01 02:06:40'),(1906,158,'Meagher','2011-06-01 02:06:40'),(1907,158,'Musselshell','2011-06-01 02:06:40'),(1908,158,'Garfield','2011-06-01 02:06:40'),(1909,158,'Powder river','2011-06-01 02:06:40'),(1910,158,'Petroleum','2011-06-01 02:06:40'),(1911,158,'Roosevelt','2011-06-01 02:06:40'),(1912,158,'Sheridan','2011-06-01 02:06:40'),(1913,158,'Mccone','2011-06-01 02:06:40'),(1914,158,'Richland','2011-06-01 02:06:40'),(1915,158,'Daniels','2011-06-01 02:06:40'),(1916,158,'Valley','2011-06-01 02:06:40'),(1917,158,'Dawson','2011-06-01 02:06:40'),(1918,158,'Phillips','2011-06-01 02:06:40'),(1919,158,'Custer','2011-06-01 02:06:40'),(1920,158,'Carter','2011-06-01 02:06:40'),(1921,158,'Fallon','2011-06-01 02:06:40'),(1922,158,'Prairie','2011-06-01 02:06:40'),(1923,158,'Wibaux','2011-06-01 02:06:40'),(1924,158,'Cascade','2011-06-01 02:06:40'),(1925,158,'Lewis and clark','2011-06-01 02:06:40'),(1926,158,'Pondera','2011-06-01 02:06:40'),(1927,158,'Teton','2011-06-01 02:06:40'),(1928,158,'Chouteau','2011-06-01 02:06:40'),(1929,158,'Toole','2011-06-01 02:06:40'),(1930,158,'Judith basin','2011-06-01 02:06:40'),(1931,158,'Liberty','2011-06-01 02:06:40'),(1932,158,'Hill','2011-06-01 02:06:40'),(1933,158,'Blaine','2011-06-01 02:06:40'),(1934,158,'Jefferson','2011-06-01 02:06:40'),(1935,158,'Broadwater','2011-06-01 02:06:40'),(1936,158,'Silver bow','2011-06-01 02:06:40'),(1937,158,'Madison','2011-06-01 02:06:40'),(1938,158,'Deer lodge','2011-06-01 02:06:40'),(1939,158,'Powell','2011-06-01 02:06:40'),(1940,158,'Gallatin','2011-06-01 02:06:40'),(1941,158,'Beaverhead','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (1942,158,'Missoula','2011-06-01 02:06:40'),(1943,158,'Mineral','2011-06-01 02:06:40'),(1944,158,'Lake','2011-06-01 02:06:40'),(1945,158,'Ravalli','2011-06-01 02:06:40'),(1946,158,'Sanders','2011-06-01 02:06:40'),(1947,158,'Granite','2011-06-01 02:06:40'),(1948,158,'Flathead','2011-06-01 02:06:40'),(1949,158,'Lincoln','2011-06-01 02:06:40'),(1950,144,'Mchenry','2011-06-01 02:06:40'),(1951,144,'Lake','2011-06-01 02:06:40'),(1952,144,'Cook','2011-06-01 02:06:40'),(1953,144,'Du page','2011-06-01 02:06:40'),(1954,144,'Kane','2011-06-01 02:06:40'),(1955,144,'De kalb','2011-06-01 02:06:40'),(1956,144,'Ogle','2011-06-01 02:06:40'),(1957,144,'Will','2011-06-01 02:06:40'),(1958,144,'Grundy','2011-06-01 02:06:40'),(1959,144,'Livingston','2011-06-01 02:06:40'),(1960,144,'La salle','2011-06-01 02:06:40'),(1961,144,'Kendall','2011-06-01 02:06:40'),(1962,144,'Lee','2011-06-01 02:06:40'),(1963,144,'Kankakee','2011-06-01 02:06:40'),(1964,144,'Iroquois','2011-06-01 02:06:40'),(1965,144,'Ford','2011-06-01 02:06:40'),(1966,144,'Vermilion','2011-06-01 02:06:40'),(1967,144,'Champaign','2011-06-01 02:06:40'),(1968,144,'Jo daviess','2011-06-01 02:06:40'),(1969,144,'Boone','2011-06-01 02:06:40'),(1970,144,'Stephenson','2011-06-01 02:06:40'),(1971,144,'Carroll','2011-06-01 02:06:40'),(1972,144,'Winnebago','2011-06-01 02:06:40'),(1973,144,'Whiteside','2011-06-01 02:06:40'),(1974,144,'Rock island','2011-06-01 02:06:40'),(1975,144,'Mercer','2011-06-01 02:06:40'),(1976,144,'Henry','2011-06-01 02:06:40'),(1977,144,'Bureau','2011-06-01 02:06:40'),(1978,144,'Putnam','2011-06-01 02:06:40'),(1979,144,'Marshall','2011-06-01 02:06:40'),(1980,144,'Knox','2011-06-01 02:06:40'),(1981,144,'Mcdonough','2011-06-01 02:06:40'),(1982,144,'Fulton','2011-06-01 02:06:40'),(1983,144,'Warren','2011-06-01 02:06:40'),(1984,144,'Henderson','2011-06-01 02:06:40'),(1985,144,'Stark','2011-06-01 02:06:40'),(1986,144,'Hancock','2011-06-01 02:06:40'),(1987,144,'Peoria','2011-06-01 02:06:40'),(1988,144,'Schuyler','2011-06-01 02:06:40'),(1989,144,'Woodford','2011-06-01 02:06:40'),(1990,144,'Mason','2011-06-01 02:06:40'),(1991,144,'Tazewell','2011-06-01 02:06:40'),(1992,144,'Mclean','2011-06-01 02:06:40'),(1993,144,'Logan','2011-06-01 02:06:40'),(1994,144,'Dewitt','2011-06-01 02:06:40'),(1995,144,'Macon','2011-06-01 02:06:40'),(1996,144,'Piatt','2011-06-01 02:06:40'),(1997,144,'Douglas','2011-06-01 02:06:40'),(1998,144,'Coles','2011-06-01 02:06:40'),(1999,144,'Moultrie','2011-06-01 02:06:40'),(2000,144,'Edgar','2011-06-01 02:06:40'),(2001,144,'Shelby','2011-06-01 02:06:40'),(2002,144,'Madison','2011-06-01 02:06:40'),(2003,144,'Calhoun','2011-06-01 02:06:40'),(2004,144,'Macoupin','2011-06-01 02:06:40'),(2005,144,'Fayette','2011-06-01 02:06:40'),(2006,144,'Jersey','2011-06-01 02:06:40'),(2007,144,'Montgomery','2011-06-01 02:06:40'),(2008,144,'Greene','2011-06-01 02:06:40'),(2009,144,'Bond','2011-06-01 02:06:40'),(2010,144,'Saint clair','2011-06-01 02:06:40'),(2011,144,'Christian','2011-06-01 02:06:40'),(2012,144,'Washington','2011-06-01 02:06:40'),(2013,144,'Clinton','2011-06-01 02:06:40'),(2014,144,'Randolph','2011-06-01 02:06:40'),(2015,144,'Monroe','2011-06-01 02:06:40'),(2016,144,'Perry','2011-06-01 02:06:40'),(2017,144,'Adams','2011-06-01 02:06:40'),(2018,144,'Pike','2011-06-01 02:06:40'),(2019,144,'Brown','2011-06-01 02:06:40'),(2020,144,'Effingham','2011-06-01 02:06:40'),(2021,144,'Wabash','2011-06-01 02:06:40'),(2022,144,'Crawford','2011-06-01 02:06:40'),(2023,144,'Lawrence','2011-06-01 02:06:40'),(2024,144,'Richland','2011-06-01 02:06:40'),(2025,144,'Clark','2011-06-01 02:06:40'),(2026,144,'Cumberland','2011-06-01 02:06:40'),(2027,144,'Jasper','2011-06-01 02:06:40'),(2028,144,'Clay','2011-06-01 02:06:40'),(2029,144,'Wayne','2011-06-01 02:06:40'),(2030,144,'Edwards','2011-06-01 02:06:40'),(2031,144,'Sangamon','2011-06-01 02:06:40'),(2032,144,'Morgan','2011-06-01 02:06:40'),(2033,144,'Scott','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2034,144,'Cass','2011-06-01 02:06:40'),(2035,144,'Menard','2011-06-01 02:06:40'),(2036,144,'Marion','2011-06-01 02:06:40'),(2037,144,'Franklin','2011-06-01 02:06:40'),(2038,144,'Jefferson','2011-06-01 02:06:40'),(2039,144,'Hamilton','2011-06-01 02:06:40'),(2040,144,'White','2011-06-01 02:06:40'),(2041,144,'Williamson','2011-06-01 02:06:40'),(2042,144,'Gallatin','2011-06-01 02:06:40'),(2043,144,'Jackson','2011-06-01 02:06:40'),(2044,144,'Union','2011-06-01 02:06:40'),(2045,144,'Johnson','2011-06-01 02:06:40'),(2046,144,'Massac','2011-06-01 02:06:40'),(2047,144,'Alexander','2011-06-01 02:06:40'),(2048,144,'Saline','2011-06-01 02:06:40'),(2049,144,'Hardin','2011-06-01 02:06:40'),(2050,144,'Pope','2011-06-01 02:06:40'),(2051,144,'Pulaski','2011-06-01 02:06:40'),(2052,157,'Saint louis','2011-06-01 02:06:40'),(2053,157,'Jefferson','2011-06-01 02:06:40'),(2054,157,'Franklin','2011-06-01 02:06:40'),(2055,157,'Saint francois','2011-06-01 02:06:40'),(2056,157,'Washington','2011-06-01 02:06:40'),(2057,157,'Gasconade','2011-06-01 02:06:40'),(2058,157,'Saint louis city','2011-06-01 02:06:40'),(2059,157,'Saint charles','2011-06-01 02:06:40'),(2060,157,'Pike','2011-06-01 02:06:40'),(2061,157,'Montgomery','2011-06-01 02:06:40'),(2062,157,'Warren','2011-06-01 02:06:40'),(2063,157,'Lincoln','2011-06-01 02:06:40'),(2064,157,'Audrain','2011-06-01 02:06:40'),(2065,157,'Callaway','2011-06-01 02:06:40'),(2066,157,'Marion','2011-06-01 02:06:40'),(2067,157,'Clark','2011-06-01 02:06:40'),(2068,157,'Macon','2011-06-01 02:06:40'),(2069,157,'Scotland','2011-06-01 02:06:40'),(2070,157,'Shelby','2011-06-01 02:06:40'),(2071,157,'Lewis','2011-06-01 02:06:40'),(2072,157,'Ralls','2011-06-01 02:06:40'),(2073,157,'Knox','2011-06-01 02:06:40'),(2074,157,'Monroe','2011-06-01 02:06:40'),(2075,157,'Adair','2011-06-01 02:06:40'),(2076,157,'Schuyler','2011-06-01 02:06:40'),(2077,157,'Sullivan','2011-06-01 02:06:40'),(2078,157,'Putnam','2011-06-01 02:06:40'),(2079,157,'Linn','2011-06-01 02:06:40'),(2080,157,'Iron','2011-06-01 02:06:40'),(2081,157,'Reynolds','2011-06-01 02:06:40'),(2082,157,'Sainte genevieve','2011-06-01 02:06:40'),(2083,157,'Wayne','2011-06-01 02:06:40'),(2084,157,'Madison','2011-06-01 02:06:40'),(2085,157,'Bollinger','2011-06-01 02:06:40'),(2086,157,'Cape girardeau','2011-06-01 02:06:40'),(2087,157,'Stoddard','2011-06-01 02:06:40'),(2088,157,'Perry','2011-06-01 02:06:40'),(2089,157,'Scott','2011-06-01 02:06:40'),(2090,157,'Mississippi','2011-06-01 02:06:40'),(2091,157,'Dunklin','2011-06-01 02:06:40'),(2092,157,'Pemiscot','2011-06-01 02:06:40'),(2093,157,'New madrid','2011-06-01 02:06:40'),(2094,157,'Butler','2011-06-01 02:06:40'),(2095,157,'Ripley','2011-06-01 02:06:40'),(2096,157,'Carter','2011-06-01 02:06:40'),(2097,157,'Lafayette','2011-06-01 02:06:40'),(2098,157,'Cass','2011-06-01 02:06:40'),(2099,157,'Jackson','2011-06-01 02:06:40'),(2100,157,'Ray','2011-06-01 02:06:40'),(2101,157,'Platte','2011-06-01 02:06:40'),(2102,157,'Johnson','2011-06-01 02:06:40'),(2103,157,'Clay','2011-06-01 02:06:40'),(2104,157,'Buchanan','2011-06-01 02:06:40'),(2105,157,'Gentry','2011-06-01 02:06:40'),(2106,157,'Worth','2011-06-01 02:06:40'),(2107,157,'Andrew','2011-06-01 02:06:40'),(2108,157,'Dekalb','2011-06-01 02:06:40'),(2109,157,'Nodaway','2011-06-01 02:06:40'),(2110,157,'Harrison','2011-06-01 02:06:40'),(2111,157,'Clinton','2011-06-01 02:06:40'),(2112,157,'Holt','2011-06-01 02:06:40'),(2113,157,'Atchison','2011-06-01 02:06:40'),(2114,157,'Livingston','2011-06-01 02:06:40'),(2115,157,'Daviess','2011-06-01 02:06:40'),(2116,157,'Carroll','2011-06-01 02:06:40'),(2117,157,'Caldwell','2011-06-01 02:06:40'),(2118,157,'Grundy','2011-06-01 02:06:40'),(2119,157,'Chariton','2011-06-01 02:06:40'),(2120,157,'Mercer','2011-06-01 02:06:40'),(2121,157,'Bates','2011-06-01 02:06:40'),(2122,157,'Saint clair','2011-06-01 02:06:40'),(2123,157,'Henry','2011-06-01 02:06:40'),(2124,157,'Vernon','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2125,157,'Cedar','2011-06-01 02:06:40'),(2126,157,'Barton','2011-06-01 02:06:40'),(2127,157,'Jasper','2011-06-01 02:06:40'),(2128,157,'Mcdonald','2011-06-01 02:06:40'),(2129,157,'Newton','2011-06-01 02:06:40'),(2130,157,'Barry','2011-06-01 02:06:40'),(2131,157,'Osage','2011-06-01 02:06:40'),(2132,157,'Boone','2011-06-01 02:06:40'),(2133,157,'Morgan','2011-06-01 02:06:40'),(2134,157,'Maries','2011-06-01 02:06:40'),(2135,157,'Miller','2011-06-01 02:06:40'),(2136,157,'Moniteau','2011-06-01 02:06:40'),(2137,157,'Camden','2011-06-01 02:06:40'),(2138,157,'Cole','2011-06-01 02:06:40'),(2139,157,'Cooper','2011-06-01 02:06:40'),(2140,157,'Howard','2011-06-01 02:06:40'),(2141,157,'Randolph','2011-06-01 02:06:40'),(2142,157,'Pettis','2011-06-01 02:06:40'),(2143,157,'Saline','2011-06-01 02:06:40'),(2144,157,'Benton','2011-06-01 02:06:40'),(2145,157,'Phelps','2011-06-01 02:06:40'),(2146,157,'Shannon','2011-06-01 02:06:40'),(2147,157,'Dent','2011-06-01 02:06:40'),(2148,157,'Crawford','2011-06-01 02:06:40'),(2149,157,'Texas','2011-06-01 02:06:40'),(2150,157,'Pulaski','2011-06-01 02:06:40'),(2151,157,'Laclede','2011-06-01 02:06:40'),(2152,157,'Howell','2011-06-01 02:06:40'),(2153,157,'Dallas','2011-06-01 02:06:40'),(2154,157,'Polk','2011-06-01 02:06:40'),(2155,157,'Dade','2011-06-01 02:06:40'),(2156,157,'Greene','2011-06-01 02:06:40'),(2157,157,'Lawrence','2011-06-01 02:06:40'),(2158,157,'Oregon','2011-06-01 02:06:40'),(2159,157,'Douglas','2011-06-01 02:06:40'),(2160,157,'Ozark','2011-06-01 02:06:40'),(2161,157,'Christian','2011-06-01 02:06:40'),(2162,157,'Stone','2011-06-01 02:06:40'),(2163,157,'Taney','2011-06-01 02:06:40'),(2164,157,'Hickory','2011-06-01 02:06:40'),(2165,157,'Webster','2011-06-01 02:06:40'),(2166,157,'Wright','2011-06-01 02:06:40'),(2167,147,'Atchison','2011-06-01 02:06:40'),(2168,147,'Douglas','2011-06-01 02:06:40'),(2169,147,'Leavenworth','2011-06-01 02:06:40'),(2170,147,'Doniphan','2011-06-01 02:06:40'),(2171,147,'Linn','2011-06-01 02:06:40'),(2172,147,'Wyandotte','2011-06-01 02:06:40'),(2173,147,'Miami','2011-06-01 02:06:40'),(2174,147,'Anderson','2011-06-01 02:06:40'),(2175,147,'Johnson','2011-06-01 02:06:40'),(2176,147,'Franklin','2011-06-01 02:06:40'),(2177,147,'Jefferson','2011-06-01 02:06:40'),(2178,147,'Wabaunsee','2011-06-01 02:06:40'),(2179,147,'Shawnee','2011-06-01 02:06:40'),(2180,147,'Marshall','2011-06-01 02:06:40'),(2181,147,'Nemaha','2011-06-01 02:06:40'),(2182,147,'Pottawatomie','2011-06-01 02:06:40'),(2183,147,'Osage','2011-06-01 02:06:40'),(2184,147,'Jackson','2011-06-01 02:06:40'),(2185,147,'Brown','2011-06-01 02:06:40'),(2186,147,'Geary','2011-06-01 02:06:40'),(2187,147,'Riley','2011-06-01 02:06:40'),(2188,147,'Bourbon','2011-06-01 02:06:40'),(2189,147,'Wilson','2011-06-01 02:06:40'),(2190,147,'Crawford','2011-06-01 02:06:40'),(2191,147,'Cherokee','2011-06-01 02:06:40'),(2192,147,'Neosho','2011-06-01 02:06:40'),(2193,147,'Allen','2011-06-01 02:06:40'),(2194,147,'Woodson','2011-06-01 02:06:40'),(2195,147,'Lyon','2011-06-01 02:06:40'),(2196,147,'Morris','2011-06-01 02:06:40'),(2197,147,'Coffey','2011-06-01 02:06:40'),(2198,147,'Marion','2011-06-01 02:06:40'),(2199,147,'Butler','2011-06-01 02:06:40'),(2200,147,'Chase','2011-06-01 02:06:40'),(2201,147,'Greenwood','2011-06-01 02:06:40'),(2202,147,'Cloud','2011-06-01 02:06:40'),(2203,147,'Republic','2011-06-01 02:06:40'),(2204,147,'Smith','2011-06-01 02:06:40'),(2205,147,'Washington','2011-06-01 02:06:40'),(2206,147,'Jewell','2011-06-01 02:06:40'),(2207,147,'Sedgwick','2011-06-01 02:06:40'),(2208,147,'Harper','2011-06-01 02:06:40'),(2209,147,'Sumner','2011-06-01 02:06:40'),(2210,147,'Cowley','2011-06-01 02:06:40'),(2211,147,'Harvey','2011-06-01 02:06:40'),(2212,147,'Pratt','2011-06-01 02:06:40'),(2213,147,'Chautauqua','2011-06-01 02:06:40'),(2214,147,'Comanche','2011-06-01 02:06:40'),(2215,147,'Kingman','2011-06-01 02:06:40'),(2216,147,'Kiowa','2011-06-01 02:06:40'),(2217,147,'Barber','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2218,147,'Mcpherson','2011-06-01 02:06:40'),(2219,147,'Montgomery','2011-06-01 02:06:40'),(2220,147,'Labette','2011-06-01 02:06:40'),(2221,147,'Elk','2011-06-01 02:06:40'),(2222,147,'Saline','2011-06-01 02:06:40'),(2223,147,'Dickinson','2011-06-01 02:06:40'),(2224,147,'Lincoln','2011-06-01 02:06:40'),(2225,147,'Mitchell','2011-06-01 02:06:40'),(2226,147,'Ottawa','2011-06-01 02:06:40'),(2227,147,'Rice','2011-06-01 02:06:40'),(2228,147,'Clay','2011-06-01 02:06:40'),(2229,147,'Osborne','2011-06-01 02:06:40'),(2230,147,'Ellsworth','2011-06-01 02:06:40'),(2231,147,'Reno','2011-06-01 02:06:40'),(2232,147,'Barton','2011-06-01 02:06:40'),(2233,147,'Rush','2011-06-01 02:06:40'),(2234,147,'Ness','2011-06-01 02:06:40'),(2235,147,'Edwards','2011-06-01 02:06:40'),(2236,147,'Pawnee','2011-06-01 02:06:40'),(2237,147,'Stafford','2011-06-01 02:06:40'),(2238,147,'Ellis','2011-06-01 02:06:40'),(2239,147,'Phillips','2011-06-01 02:06:40'),(2240,147,'Norton','2011-06-01 02:06:40'),(2241,147,'Graham','2011-06-01 02:06:40'),(2242,147,'Russell','2011-06-01 02:06:40'),(2243,147,'Trego','2011-06-01 02:06:40'),(2244,147,'Rooks','2011-06-01 02:06:40'),(2245,147,'Decatur','2011-06-01 02:06:40'),(2246,147,'Thomas','2011-06-01 02:06:40'),(2247,147,'Rawlins','2011-06-01 02:06:40'),(2248,147,'Cheyenne','2011-06-01 02:06:40'),(2249,147,'Sherman','2011-06-01 02:06:40'),(2250,147,'Gove','2011-06-01 02:06:40'),(2251,147,'Sheridan','2011-06-01 02:06:40'),(2252,147,'Logan','2011-06-01 02:06:40'),(2253,147,'Wallace','2011-06-01 02:06:40'),(2254,147,'Ford','2011-06-01 02:06:40'),(2255,147,'Clark','2011-06-01 02:06:40'),(2256,147,'Gray','2011-06-01 02:06:40'),(2257,147,'Hamilton','2011-06-01 02:06:40'),(2258,147,'Kearny','2011-06-01 02:06:40'),(2259,147,'Lane','2011-06-01 02:06:40'),(2260,147,'Meade','2011-06-01 02:06:40'),(2261,147,'Finney','2011-06-01 02:06:40'),(2262,147,'Hodgeman','2011-06-01 02:06:40'),(2263,147,'Stanton','2011-06-01 02:06:40'),(2264,147,'Seward','2011-06-01 02:06:40'),(2265,147,'Wichita','2011-06-01 02:06:40'),(2266,147,'Haskell','2011-06-01 02:06:40'),(2267,147,'Scott','2011-06-01 02:06:40'),(2268,147,'Greeley','2011-06-01 02:06:40'),(2269,147,'Grant','2011-06-01 02:06:40'),(2270,147,'Morton','2011-06-01 02:06:40'),(2271,147,'Stevens','2011-06-01 02:06:40'),(2272,159,'Butler','2011-06-01 02:06:40'),(2273,159,'Washington','2011-06-01 02:06:40'),(2274,159,'Saunders','2011-06-01 02:06:40'),(2275,159,'Cuming','2011-06-01 02:06:40'),(2276,159,'Sarpy','2011-06-01 02:06:40'),(2277,159,'Douglas','2011-06-01 02:06:40'),(2278,159,'Cass','2011-06-01 02:06:40'),(2279,159,'Burt','2011-06-01 02:06:40'),(2280,159,'Dodge','2011-06-01 02:06:40'),(2281,159,'Dakota','2011-06-01 02:06:40'),(2282,159,'Thurston','2011-06-01 02:06:40'),(2283,159,'Gage','2011-06-01 02:06:40'),(2284,159,'Thayer','2011-06-01 02:06:40'),(2285,159,'Nemaha','2011-06-01 02:06:40'),(2286,159,'Seward','2011-06-01 02:06:40'),(2287,159,'York','2011-06-01 02:06:40'),(2288,159,'Lancaster','2011-06-01 02:06:40'),(2289,159,'Pawnee','2011-06-01 02:06:40'),(2290,159,'Otoe','2011-06-01 02:06:40'),(2291,159,'Johnson','2011-06-01 02:06:40'),(2292,159,'Saline','2011-06-01 02:06:40'),(2293,159,'Richardson','2011-06-01 02:06:40'),(2294,159,'Jefferson','2011-06-01 02:06:40'),(2295,159,'Fillmore','2011-06-01 02:06:40'),(2296,159,'Clay','2011-06-01 02:06:40'),(2297,159,'Platte','2011-06-01 02:06:40'),(2298,159,'Boone','2011-06-01 02:06:40'),(2299,159,'Wheeler','2011-06-01 02:06:40'),(2300,159,'Nance','2011-06-01 02:06:40'),(2301,159,'Merrick','2011-06-01 02:06:40'),(2302,159,'Colfax','2011-06-01 02:06:40'),(2303,159,'Antelope','2011-06-01 02:06:40'),(2304,159,'Polk','2011-06-01 02:06:40'),(2305,159,'Greeley','2011-06-01 02:06:40'),(2306,159,'Madison','2011-06-01 02:06:40'),(2307,159,'Dixon','2011-06-01 02:06:40'),(2308,159,'Holt','2011-06-01 02:06:40'),(2309,159,'Rock','2011-06-01 02:06:40'),(2310,159,'Cedar','2011-06-01 02:06:40'),(2311,159,'Knox','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2312,159,'Boyd','2011-06-01 02:06:40'),(2313,159,'Wayne','2011-06-01 02:06:40'),(2314,159,'Pierce','2011-06-01 02:06:40'),(2315,159,'Keya paha','2011-06-01 02:06:40'),(2316,159,'Stanton','2011-06-01 02:06:40'),(2317,159,'Hall','2011-06-01 02:06:40'),(2318,159,'Buffalo','2011-06-01 02:06:40'),(2319,159,'Custer','2011-06-01 02:06:40'),(2320,159,'Valley','2011-06-01 02:06:40'),(2321,159,'Sherman','2011-06-01 02:06:40'),(2322,159,'Hamilton','2011-06-01 02:06:40'),(2323,159,'Howard','2011-06-01 02:06:40'),(2324,159,'Blaine','2011-06-01 02:06:40'),(2325,159,'Garfield','2011-06-01 02:06:40'),(2326,159,'Dawson','2011-06-01 02:06:40'),(2327,159,'Loup','2011-06-01 02:06:40'),(2328,159,'Adams','2011-06-01 02:06:40'),(2329,159,'Harlan','2011-06-01 02:06:40'),(2330,159,'Furnas','2011-06-01 02:06:40'),(2331,159,'Phelps','2011-06-01 02:06:40'),(2332,159,'Kearney','2011-06-01 02:06:40'),(2333,159,'Webster','2011-06-01 02:06:40'),(2334,159,'Franklin','2011-06-01 02:06:40'),(2335,159,'Gosper','2011-06-01 02:06:40'),(2336,159,'Nuckolls','2011-06-01 02:06:40'),(2337,159,'Red willow','2011-06-01 02:06:40'),(2338,159,'Dundy','2011-06-01 02:06:40'),(2339,159,'Chase','2011-06-01 02:06:40'),(2340,159,'Hitchcock','2011-06-01 02:06:40'),(2341,159,'Frontier','2011-06-01 02:06:40'),(2342,159,'Hayes','2011-06-01 02:06:40'),(2343,159,'Lincoln','2011-06-01 02:06:40'),(2344,159,'Arthur','2011-06-01 02:06:40'),(2345,159,'Deuel','2011-06-01 02:06:40'),(2346,159,'Morrill','2011-06-01 02:06:40'),(2347,159,'Keith','2011-06-01 02:06:40'),(2348,159,'Kimball','2011-06-01 02:06:40'),(2349,159,'Cheyenne','2011-06-01 02:06:40'),(2350,159,'Perkins','2011-06-01 02:06:40'),(2351,159,'Cherry','2011-06-01 02:06:40'),(2352,159,'Thomas','2011-06-01 02:06:40'),(2353,159,'Garden','2011-06-01 02:06:40'),(2354,159,'Hooker','2011-06-01 02:06:40'),(2355,159,'Logan','2011-06-01 02:06:40'),(2356,159,'Mcpherson','2011-06-01 02:06:40'),(2357,159,'Brown','2011-06-01 02:06:40'),(2358,159,'Box butte','2011-06-01 02:06:40'),(2359,159,'Grant','2011-06-01 02:06:40'),(2360,159,'Sheridan','2011-06-01 02:06:40'),(2361,159,'Dawes','2011-06-01 02:06:40'),(2362,159,'Scotts bluff','2011-06-01 02:06:40'),(2363,159,'Banner','2011-06-01 02:06:40'),(2364,159,'Sioux','2011-06-01 02:06:40'),(2365,149,'Jefferson','2011-06-01 02:06:40'),(2366,149,'Saint charles','2011-06-01 02:06:40'),(2367,149,'Saint bernard','2011-06-01 02:06:40'),(2368,149,'Plaquemines','2011-06-01 02:06:40'),(2369,149,'St john the baptist','2011-06-01 02:06:40'),(2370,149,'Saint james','2011-06-01 02:06:40'),(2371,149,'Orleans','2011-06-01 02:06:40'),(2372,149,'Lafourche','2011-06-01 02:06:40'),(2373,149,'Assumption','2011-06-01 02:06:40'),(2374,149,'Saint mary','2011-06-01 02:06:40'),(2375,149,'Terrebonne','2011-06-01 02:06:40'),(2376,149,'Ascension','2011-06-01 02:06:40'),(2377,149,'Tangipahoa','2011-06-01 02:06:40'),(2378,149,'Saint tammany','2011-06-01 02:06:40'),(2379,149,'Washington','2011-06-01 02:06:40'),(2380,149,'Saint helena','2011-06-01 02:06:40'),(2381,149,'Livingston','2011-06-01 02:06:40'),(2382,149,'Lafayette','2011-06-01 02:06:40'),(2383,149,'Vermilion','2011-06-01 02:06:40'),(2384,149,'Saint landry','2011-06-01 02:06:40'),(2385,149,'Iberia','2011-06-01 02:06:40'),(2386,149,'Evangeline','2011-06-01 02:06:40'),(2387,149,'Acadia','2011-06-01 02:06:40'),(2388,149,'Saint martin','2011-06-01 02:06:40'),(2389,149,'Jefferson davis','2011-06-01 02:06:40'),(2390,149,'Calcasieu','2011-06-01 02:06:40'),(2391,149,'Cameron','2011-06-01 02:06:40'),(2392,149,'Beauregard','2011-06-01 02:06:40'),(2393,149,'Allen','2011-06-01 02:06:40'),(2394,149,'Vernon','2011-06-01 02:06:40'),(2395,149,'East baton rouge','2011-06-01 02:06:40'),(2396,149,'West baton rouge','2011-06-01 02:06:40'),(2397,149,'West feliciana','2011-06-01 02:06:40'),(2398,149,'Pointe coupee','2011-06-01 02:06:40'),(2399,149,'Iberville','2011-06-01 02:06:40'),(2400,149,'East feliciana','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2401,149,'Bienville','2011-06-01 02:06:40'),(2402,149,'Natchitoches','2011-06-01 02:06:40'),(2403,149,'Claiborne','2011-06-01 02:06:40'),(2404,149,'Caddo','2011-06-01 02:06:40'),(2405,149,'Bossier','2011-06-01 02:06:40'),(2406,149,'Webster','2011-06-01 02:06:40'),(2407,149,'Red river','2011-06-01 02:06:40'),(2408,149,'De soto','2011-06-01 02:06:40'),(2409,149,'Sabine','2011-06-01 02:06:40'),(2410,149,'Ouachita','2011-06-01 02:06:40'),(2411,149,'Richland','2011-06-01 02:06:40'),(2412,149,'Franklin','2011-06-01 02:06:40'),(2413,149,'Morehouse','2011-06-01 02:06:40'),(2414,149,'Union','2011-06-01 02:06:40'),(2415,149,'Jackson','2011-06-01 02:06:40'),(2416,149,'Lincoln','2011-06-01 02:06:40'),(2417,149,'Madison','2011-06-01 02:06:40'),(2418,149,'West carroll','2011-06-01 02:06:40'),(2419,149,'East carroll','2011-06-01 02:06:40'),(2420,149,'Rapides','2011-06-01 02:06:40'),(2421,149,'Concordia','2011-06-01 02:06:40'),(2422,149,'Avoyelles','2011-06-01 02:06:40'),(2423,149,'Catahoula','2011-06-01 02:06:40'),(2424,149,'La salle','2011-06-01 02:06:40'),(2425,149,'Tensas','2011-06-01 02:06:40'),(2426,149,'Winn','2011-06-01 02:06:40'),(2427,149,'Grant','2011-06-01 02:06:40'),(2428,149,'Caldwell','2011-06-01 02:06:40'),(2429,132,'Jefferson','2011-06-01 02:06:40'),(2430,132,'Desha','2011-06-01 02:06:40'),(2431,132,'Bradley','2011-06-01 02:06:40'),(2432,132,'Ashley','2011-06-01 02:06:40'),(2433,132,'Chicot','2011-06-01 02:06:40'),(2434,132,'Lincoln','2011-06-01 02:06:40'),(2435,132,'Cleveland','2011-06-01 02:06:40'),(2436,132,'Drew','2011-06-01 02:06:40'),(2437,132,'Ouachita','2011-06-01 02:06:40'),(2438,132,'Clark','2011-06-01 02:06:40'),(2439,132,'Nevada','2011-06-01 02:06:40'),(2440,132,'Union','2011-06-01 02:06:40'),(2441,132,'Dallas','2011-06-01 02:06:40'),(2442,132,'Columbia','2011-06-01 02:06:40'),(2443,132,'Calhoun','2011-06-01 02:06:40'),(2444,132,'Hempstead','2011-06-01 02:06:40'),(2445,132,'Little river','2011-06-01 02:06:40'),(2446,132,'Sevier','2011-06-01 02:06:40'),(2447,132,'Lafayette','2011-06-01 02:06:40'),(2448,132,'Howard','2011-06-01 02:06:40'),(2449,132,'Miller','2011-06-01 02:06:40'),(2450,132,'Garland','2011-06-01 02:06:40'),(2451,132,'Pike','2011-06-01 02:06:40'),(2452,132,'Hot spring','2011-06-01 02:06:40'),(2453,132,'Polk','2011-06-01 02:06:40'),(2454,132,'Montgomery','2011-06-01 02:06:40'),(2455,132,'Perry','2011-06-01 02:06:40'),(2456,132,'Pulaski','2011-06-01 02:06:40'),(2457,132,'Arkansas','2011-06-01 02:06:40'),(2458,132,'Jackson','2011-06-01 02:06:40'),(2459,132,'Woodruff','2011-06-01 02:06:40'),(2460,132,'Lonoke','2011-06-01 02:06:40'),(2461,132,'White','2011-06-01 02:06:40'),(2462,132,'Saline','2011-06-01 02:06:40'),(2463,132,'Van buren','2011-06-01 02:06:40'),(2464,132,'Prairie','2011-06-01 02:06:40'),(2465,132,'Monroe','2011-06-01 02:06:40'),(2466,132,'Conway','2011-06-01 02:06:40'),(2467,132,'Faulkner','2011-06-01 02:06:40'),(2468,132,'Cleburne','2011-06-01 02:06:40'),(2469,132,'Stone','2011-06-01 02:06:40'),(2470,132,'Grant','2011-06-01 02:06:40'),(2471,132,'Independence','2011-06-01 02:06:40'),(2472,132,'Crittenden','2011-06-01 02:06:40'),(2473,132,'Mississippi','2011-06-01 02:06:40'),(2474,132,'Lee','2011-06-01 02:06:40'),(2475,132,'Phillips','2011-06-01 02:06:40'),(2476,132,'Saint francis','2011-06-01 02:06:40'),(2477,132,'Cross','2011-06-01 02:06:40'),(2478,132,'Poinsett','2011-06-01 02:06:40'),(2479,132,'Craighead','2011-06-01 02:06:40'),(2480,132,'Lawrence','2011-06-01 02:06:40'),(2481,132,'Greene','2011-06-01 02:06:40'),(2482,132,'Randolph','2011-06-01 02:06:40'),(2483,132,'Clay','2011-06-01 02:06:40'),(2484,132,'Sharp','2011-06-01 02:06:40'),(2485,132,'Izard','2011-06-01 02:06:40'),(2486,132,'Fulton','2011-06-01 02:06:40'),(2487,132,'Baxter','2011-06-01 02:06:40'),(2488,132,'Boone','2011-06-01 02:06:40'),(2489,132,'Carroll','2011-06-01 02:06:40'),(2490,132,'Marion','2011-06-01 02:06:40'),(2491,132,'Newton','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2492,132,'Searcy','2011-06-01 02:06:40'),(2493,132,'Pope','2011-06-01 02:06:40'),(2494,132,'Washington','2011-06-01 02:06:40'),(2495,132,'Benton','2011-06-01 02:06:40'),(2496,132,'Madison','2011-06-01 02:06:40'),(2497,132,'Franklin','2011-06-01 02:06:40'),(2498,132,'Yell','2011-06-01 02:06:40'),(2499,132,'Logan','2011-06-01 02:06:40'),(2500,132,'Johnson','2011-06-01 02:06:40'),(2501,132,'Scott','2011-06-01 02:06:40'),(2502,132,'Sebastian','2011-06-01 02:06:40'),(2503,132,'Crawford','2011-06-01 02:06:40'),(2504,169,'Caddo','2011-06-01 02:06:40'),(2505,169,'Grady','2011-06-01 02:06:40'),(2506,169,'Oklahoma','2011-06-01 02:06:40'),(2507,169,'Mcclain','2011-06-01 02:06:40'),(2508,169,'Stephens','2011-06-01 02:06:40'),(2509,169,'Canadian','2011-06-01 02:06:40'),(2510,169,'Kingfisher','2011-06-01 02:06:40'),(2511,169,'Cleveland','2011-06-01 02:06:40'),(2512,169,'Washita','2011-06-01 02:06:40'),(2513,169,'Logan','2011-06-01 02:06:40'),(2514,169,'Murray','2011-06-01 02:06:40'),(2515,169,'Blaine','2011-06-01 02:06:40'),(2516,169,'Kiowa','2011-06-01 02:06:40'),(2517,169,'Garvin','2011-06-01 02:06:40'),(2518,169,'Noble','2011-06-01 02:06:40'),(2519,169,'Custer','2011-06-01 02:06:40'),(2520,178,'Travis','2011-06-01 02:06:40'),(2521,169,'Carter','2011-06-01 02:06:40'),(2522,169,'Love','2011-06-01 02:06:40'),(2523,169,'Johnston','2011-06-01 02:06:40'),(2524,169,'Marshall','2011-06-01 02:06:40'),(2525,169,'Bryan','2011-06-01 02:06:40'),(2526,169,'Jefferson','2011-06-01 02:06:40'),(2527,169,'Comanche','2011-06-01 02:06:40'),(2528,169,'Jackson','2011-06-01 02:06:40'),(2529,169,'Tillman','2011-06-01 02:06:40'),(2530,169,'Cotton','2011-06-01 02:06:40'),(2531,169,'Harmon','2011-06-01 02:06:40'),(2532,169,'Greer','2011-06-01 02:06:40'),(2533,169,'Beckham','2011-06-01 02:06:40'),(2534,169,'Roger mills','2011-06-01 02:06:40'),(2535,169,'Dewey','2011-06-01 02:06:40'),(2536,169,'Garfield','2011-06-01 02:06:40'),(2537,169,'Alfalfa','2011-06-01 02:06:40'),(2538,169,'Woods','2011-06-01 02:06:40'),(2539,169,'Major','2011-06-01 02:06:40'),(2540,169,'Grant','2011-06-01 02:06:40'),(2541,169,'Woodward','2011-06-01 02:06:40'),(2542,169,'Ellis','2011-06-01 02:06:40'),(2543,169,'Harper','2011-06-01 02:06:40'),(2544,169,'Beaver','2011-06-01 02:06:40'),(2545,169,'Texas','2011-06-01 02:06:40'),(2546,169,'Cimarron','2011-06-01 02:06:40'),(2547,169,'Osage','2011-06-01 02:06:40'),(2548,169,'Washington','2011-06-01 02:06:40'),(2549,169,'Tulsa','2011-06-01 02:06:40'),(2550,169,'Creek','2011-06-01 02:06:40'),(2551,169,'Wagoner','2011-06-01 02:06:40'),(2552,169,'Rogers','2011-06-01 02:06:40'),(2553,169,'Pawnee','2011-06-01 02:06:40'),(2554,169,'Payne','2011-06-01 02:06:40'),(2555,169,'Lincoln','2011-06-01 02:06:40'),(2556,169,'Nowata','2011-06-01 02:06:40'),(2557,169,'Craig','2011-06-01 02:06:40'),(2558,169,'Mayes','2011-06-01 02:06:40'),(2559,169,'Ottawa','2011-06-01 02:06:40'),(2560,169,'Delaware','2011-06-01 02:06:40'),(2561,169,'Muskogee','2011-06-01 02:06:40'),(2562,169,'Okmulgee','2011-06-01 02:06:40'),(2563,169,'Pittsburg','2011-06-01 02:06:40'),(2564,169,'Mcintosh','2011-06-01 02:06:40'),(2565,169,'Cherokee','2011-06-01 02:06:40'),(2566,169,'Sequoyah','2011-06-01 02:06:40'),(2567,169,'Haskell','2011-06-01 02:06:40'),(2568,169,'Adair','2011-06-01 02:06:40'),(2569,169,'Pushmataha','2011-06-01 02:06:40'),(2570,169,'Atoka','2011-06-01 02:06:40'),(2571,169,'Hughes','2011-06-01 02:06:40'),(2572,169,'Coal','2011-06-01 02:06:40'),(2573,169,'Latimer','2011-06-01 02:06:40'),(2574,169,'Le flore','2011-06-01 02:06:40'),(2575,169,'Kay','2011-06-01 02:06:40'),(2576,169,'Mccurtain','2011-06-01 02:06:40'),(2577,169,'Choctaw','2011-06-01 02:06:40'),(2578,169,'Pottawatomie','2011-06-01 02:06:40'),(2579,169,'Seminole','2011-06-01 02:06:40'),(2580,169,'Pontotoc','2011-06-01 02:06:40'),(2581,169,'Okfuskee','2011-06-01 02:06:40'),(2582,178,'Dallas','2011-06-01 02:06:40'),(2583,178,'Collin','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2584,178,'Denton','2011-06-01 02:06:40'),(2585,178,'Grayson','2011-06-01 02:06:40'),(2586,178,'Rockwall','2011-06-01 02:06:40'),(2587,178,'Ellis','2011-06-01 02:06:40'),(2588,178,'Navarro','2011-06-01 02:06:40'),(2589,178,'Van zandt','2011-06-01 02:06:40'),(2590,178,'Kaufman','2011-06-01 02:06:40'),(2591,178,'Henderson','2011-06-01 02:06:40'),(2592,178,'Hunt','2011-06-01 02:06:40'),(2593,178,'Wood','2011-06-01 02:06:40'),(2594,178,'Lamar','2011-06-01 02:06:40'),(2595,178,'Red river','2011-06-01 02:06:40'),(2596,178,'Fannin','2011-06-01 02:06:40'),(2597,178,'Delta','2011-06-01 02:06:40'),(2598,178,'Hopkins','2011-06-01 02:06:40'),(2599,178,'Rains','2011-06-01 02:06:40'),(2600,178,'Camp','2011-06-01 02:06:40'),(2601,178,'Titus','2011-06-01 02:06:40'),(2602,178,'Franklin','2011-06-01 02:06:40'),(2603,178,'Bowie','2011-06-01 02:06:40'),(2604,178,'Cass','2011-06-01 02:06:40'),(2605,178,'Marion','2011-06-01 02:06:40'),(2606,178,'Morris','2011-06-01 02:06:40'),(2607,178,'Gregg','2011-06-01 02:06:40'),(2608,178,'Panola','2011-06-01 02:06:40'),(2609,178,'Upshur','2011-06-01 02:06:40'),(2610,178,'Harrison','2011-06-01 02:06:40'),(2611,178,'Rusk','2011-06-01 02:06:40'),(2612,178,'Smith','2011-06-01 02:06:40'),(2613,178,'Cherokee','2011-06-01 02:06:40'),(2614,178,'Nacogdoches','2011-06-01 02:06:40'),(2615,178,'Anderson','2011-06-01 02:06:40'),(2616,178,'Leon','2011-06-01 02:06:40'),(2617,178,'Trinity','2011-06-01 02:06:40'),(2618,178,'Houston','2011-06-01 02:06:40'),(2619,178,'Freestone','2011-06-01 02:06:40'),(2620,178,'Madison','2011-06-01 02:06:40'),(2621,178,'Angelina','2011-06-01 02:06:40'),(2622,178,'Newton','2011-06-01 02:06:40'),(2623,178,'San augustine','2011-06-01 02:06:40'),(2624,178,'Sabine','2011-06-01 02:06:40'),(2625,178,'Polk','2011-06-01 02:06:40'),(2626,178,'Shelby','2011-06-01 02:06:40'),(2627,178,'Tyler','2011-06-01 02:06:40'),(2628,178,'Jasper','2011-06-01 02:06:40'),(2629,178,'Tarrant','2011-06-01 02:06:40'),(2630,178,'Parker','2011-06-01 02:06:40'),(2631,178,'Johnson','2011-06-01 02:06:40'),(2632,178,'Wise','2011-06-01 02:06:40'),(2633,178,'Hood','2011-06-01 02:06:40'),(2634,178,'Somervell','2011-06-01 02:06:40'),(2635,178,'Hill','2011-06-01 02:06:40'),(2636,178,'Palo pinto','2011-06-01 02:06:40'),(2637,178,'Clay','2011-06-01 02:06:40'),(2638,178,'Montague','2011-06-01 02:06:40'),(2639,178,'Cooke','2011-06-01 02:06:40'),(2640,178,'Wichita','2011-06-01 02:06:40'),(2641,178,'Archer','2011-06-01 02:06:40'),(2642,178,'Knox','2011-06-01 02:06:40'),(2643,178,'Wilbarger','2011-06-01 02:06:40'),(2644,178,'Young','2011-06-01 02:06:40'),(2645,178,'Baylor','2011-06-01 02:06:40'),(2646,178,'Haskell','2011-06-01 02:06:40'),(2647,178,'Erath','2011-06-01 02:06:40'),(2648,178,'Stephens','2011-06-01 02:06:40'),(2649,178,'Jack','2011-06-01 02:06:40'),(2650,178,'Shackelford','2011-06-01 02:06:40'),(2651,178,'Brown','2011-06-01 02:06:40'),(2652,178,'Eastland','2011-06-01 02:06:40'),(2653,178,'Hamilton','2011-06-01 02:06:40'),(2654,178,'Comanche','2011-06-01 02:06:40'),(2655,178,'Callahan','2011-06-01 02:06:40'),(2656,178,'Throckmorton','2011-06-01 02:06:40'),(2657,178,'Bell','2011-06-01 02:06:40'),(2658,178,'Milam','2011-06-01 02:06:40'),(2659,178,'Coryell','2011-06-01 02:06:40'),(2660,178,'Mclennan','2011-06-01 02:06:40'),(2661,178,'Williamson','2011-06-01 02:06:40'),(2662,178,'Lampasas','2011-06-01 02:06:40'),(2663,178,'Falls','2011-06-01 02:06:40'),(2664,178,'Robertson','2011-06-01 02:06:40'),(2665,178,'Bosque','2011-06-01 02:06:40'),(2666,178,'Limestone','2011-06-01 02:06:40'),(2667,178,'Mason','2011-06-01 02:06:40'),(2668,178,'Runnels','2011-06-01 02:06:40'),(2669,178,'Mcculloch','2011-06-01 02:06:40'),(2670,178,'Coleman','2011-06-01 02:06:40'),(2671,178,'Llano','2011-06-01 02:06:40'),(2672,178,'San saba','2011-06-01 02:06:40'),(2673,178,'Concho','2011-06-01 02:06:40'),(2674,178,'Menard','2011-06-01 02:06:40'),(2675,178,'Mills','2011-06-01 02:06:40'),(2676,178,'Kimble','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2677,178,'Edwards','2011-06-01 02:06:40'),(2678,178,'Tom green','2011-06-01 02:06:40'),(2679,178,'Irion','2011-06-01 02:06:40'),(2680,178,'Reagan','2011-06-01 02:06:40'),(2681,178,'Coke','2011-06-01 02:06:40'),(2682,178,'Schleicher','2011-06-01 02:06:40'),(2683,178,'Crockett','2011-06-01 02:06:40'),(2684,178,'Sutton','2011-06-01 02:06:40'),(2685,178,'Sterling','2011-06-01 02:06:40'),(2686,178,'Harris','2011-06-01 02:06:40'),(2687,178,'Montgomery','2011-06-01 02:06:40'),(2688,178,'Walker','2011-06-01 02:06:40'),(2689,178,'Liberty','2011-06-01 02:06:40'),(2690,178,'San jacinto','2011-06-01 02:06:40'),(2691,178,'Grimes','2011-06-01 02:06:40'),(2692,178,'Hardin','2011-06-01 02:06:40'),(2693,178,'Matagorda','2011-06-01 02:06:40'),(2694,178,'Fort bend','2011-06-01 02:06:40'),(2695,178,'Colorado','2011-06-01 02:06:40'),(2696,178,'Austin','2011-06-01 02:06:40'),(2697,178,'Wharton','2011-06-01 02:06:40'),(2698,178,'Brazoria','2011-06-01 02:06:40'),(2699,178,'Waller','2011-06-01 02:06:40'),(2700,178,'Washington','2011-06-01 02:06:40'),(2701,178,'Galveston','2011-06-01 02:06:40'),(2702,178,'Chambers','2011-06-01 02:06:40'),(2703,178,'Orange','2011-06-01 02:06:40'),(2704,178,'Jefferson','2011-06-01 02:06:40'),(2705,178,'Brazos','2011-06-01 02:06:40'),(2706,178,'Burleson','2011-06-01 02:06:40'),(2707,178,'Lee','2011-06-01 02:06:40'),(2708,178,'Victoria','2011-06-01 02:06:40'),(2709,178,'Refugio','2011-06-01 02:06:40'),(2710,178,'De witt','2011-06-01 02:06:40'),(2711,178,'Jackson','2011-06-01 02:06:40'),(2712,178,'Goliad','2011-06-01 02:06:40'),(2713,178,'Lavaca','2011-06-01 02:06:40'),(2714,178,'Calhoun','2011-06-01 02:06:40'),(2715,178,'La salle','2011-06-01 02:06:40'),(2716,178,'Bexar','2011-06-01 02:06:40'),(2717,178,'Bandera','2011-06-01 02:06:40'),(2718,178,'Kendall','2011-06-01 02:06:40'),(2719,178,'Frio','2011-06-01 02:06:40'),(2720,178,'Mcmullen','2011-06-01 02:06:40'),(2721,178,'Atascosa','2011-06-01 02:06:40'),(2722,178,'Medina','2011-06-01 02:06:40'),(2723,178,'Kerr','2011-06-01 02:06:40'),(2724,178,'Live oak','2011-06-01 02:06:40'),(2725,178,'Webb','2011-06-01 02:06:40'),(2726,178,'Zapata','2011-06-01 02:06:40'),(2727,178,'Comal','2011-06-01 02:06:40'),(2728,178,'Bee','2011-06-01 02:06:40'),(2729,178,'Guadalupe','2011-06-01 02:06:40'),(2730,178,'Karnes','2011-06-01 02:06:40'),(2731,178,'Wilson','2011-06-01 02:06:40'),(2732,178,'Gonzales','2011-06-01 02:06:40'),(2733,178,'Nueces','2011-06-01 02:06:40'),(2734,178,'Jim wells','2011-06-01 02:06:40'),(2735,178,'San patricio','2011-06-01 02:06:40'),(2736,178,'Kenedy','2011-06-01 02:06:40'),(2737,178,'Duval','2011-06-01 02:06:40'),(2738,178,'Brooks','2011-06-01 02:06:40'),(2739,178,'Aransas','2011-06-01 02:06:40'),(2740,178,'Jim hogg','2011-06-01 02:06:40'),(2741,178,'Kleberg','2011-06-01 02:06:40'),(2742,178,'Hidalgo','2011-06-01 02:06:40'),(2743,178,'Cameron','2011-06-01 02:06:40'),(2744,178,'Starr','2011-06-01 02:06:40'),(2745,178,'Willacy','2011-06-01 02:06:40'),(2746,178,'Bastrop','2011-06-01 02:06:40'),(2747,178,'Burnet','2011-06-01 02:06:40'),(2748,178,'Blanco','2011-06-01 02:06:40'),(2749,178,'Hays','2011-06-01 02:06:40'),(2750,178,'Caldwell','2011-06-01 02:06:40'),(2751,178,'Gillespie','2011-06-01 02:06:40'),(2752,178,'Uvalde','2011-06-01 02:06:40'),(2753,178,'Dimmit','2011-06-01 02:06:40'),(2754,178,'Zavala','2011-06-01 02:06:40'),(2755,178,'Kinney','2011-06-01 02:06:40'),(2756,178,'Real','2011-06-01 02:06:40'),(2757,178,'Val verde','2011-06-01 02:06:40'),(2758,178,'Terrell','2011-06-01 02:06:40'),(2759,178,'Maverick','2011-06-01 02:06:40'),(2760,178,'Fayette','2011-06-01 02:06:40'),(2761,178,'Oldham','2011-06-01 02:06:40'),(2762,178,'Gray','2011-06-01 02:06:40'),(2763,178,'Wheeler','2011-06-01 02:06:40'),(2764,178,'Lipscomb','2011-06-01 02:06:40'),(2765,178,'Hutchinson','2011-06-01 02:06:40'),(2766,178,'Parmer','2011-06-01 02:06:40'),(2767,178,'Potter','2011-06-01 02:06:40'),(2768,178,'Moore','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2769,178,'Hemphill','2011-06-01 02:06:40'),(2770,178,'Randall','2011-06-01 02:06:40'),(2771,178,'Hartley','2011-06-01 02:06:40'),(2772,178,'Armstrong','2011-06-01 02:06:40'),(2773,178,'Hale','2011-06-01 02:06:40'),(2774,178,'Dallam','2011-06-01 02:06:40'),(2775,178,'Deaf smith','2011-06-01 02:06:40'),(2776,178,'Castro','2011-06-01 02:06:40'),(2777,178,'Lamb','2011-06-01 02:06:40'),(2778,178,'Ochiltree','2011-06-01 02:06:40'),(2779,178,'Carson','2011-06-01 02:06:40'),(2780,178,'Hansford','2011-06-01 02:06:40'),(2781,178,'Swisher','2011-06-01 02:06:40'),(2782,178,'Roberts','2011-06-01 02:06:40'),(2783,178,'Collingsworth','2011-06-01 02:06:40'),(2784,178,'Sherman','2011-06-01 02:06:40'),(2785,178,'Childress','2011-06-01 02:06:40'),(2786,178,'Dickens','2011-06-01 02:06:40'),(2787,178,'Floyd','2011-06-01 02:06:40'),(2788,178,'Cottle','2011-06-01 02:06:40'),(2789,178,'Hardeman','2011-06-01 02:06:40'),(2790,178,'Donley','2011-06-01 02:06:40'),(2791,178,'Foard','2011-06-01 02:06:40'),(2792,178,'Hall','2011-06-01 02:06:40'),(2793,178,'Motley','2011-06-01 02:06:40'),(2794,178,'King','2011-06-01 02:06:40'),(2795,178,'Briscoe','2011-06-01 02:06:40'),(2796,178,'Hockley','2011-06-01 02:06:40'),(2797,178,'Cochran','2011-06-01 02:06:40'),(2798,178,'Terry','2011-06-01 02:06:40'),(2799,178,'Bailey','2011-06-01 02:06:40'),(2800,178,'Crosby','2011-06-01 02:06:40'),(2801,178,'Yoakum','2011-06-01 02:06:40'),(2802,178,'Lubbock','2011-06-01 02:06:40'),(2803,178,'Garza','2011-06-01 02:06:40'),(2804,178,'Dawson','2011-06-01 02:06:40'),(2805,178,'Gaines','2011-06-01 02:06:40'),(2806,178,'Lynn','2011-06-01 02:06:40'),(2807,178,'Jones','2011-06-01 02:06:40'),(2808,178,'Stonewall','2011-06-01 02:06:40'),(2809,178,'Nolan','2011-06-01 02:06:40'),(2810,178,'Taylor','2011-06-01 02:06:40'),(2811,178,'Howard','2011-06-01 02:06:40'),(2812,178,'Mitchell','2011-06-01 02:06:40'),(2813,178,'Scurry','2011-06-01 02:06:40'),(2814,178,'Kent','2011-06-01 02:06:40'),(2815,178,'Fisher','2011-06-01 02:06:40'),(2816,178,'Midland','2011-06-01 02:06:40'),(2817,178,'Andrews','2011-06-01 02:06:40'),(2818,178,'Reeves','2011-06-01 02:06:40'),(2819,178,'Ward','2011-06-01 02:06:40'),(2820,178,'Pecos','2011-06-01 02:06:40'),(2821,178,'Crane','2011-06-01 02:06:40'),(2822,178,'Jeff davis','2011-06-01 02:06:40'),(2823,178,'Borden','2011-06-01 02:06:40'),(2824,178,'Glasscock','2011-06-01 02:06:40'),(2825,178,'Ector','2011-06-01 02:06:40'),(2826,178,'Winkler','2011-06-01 02:06:40'),(2827,178,'Martin','2011-06-01 02:06:40'),(2828,178,'Upton','2011-06-01 02:06:40'),(2829,178,'Loving','2011-06-01 02:06:40'),(2830,178,'El paso','2011-06-01 02:06:40'),(2831,178,'Brewster','2011-06-01 02:06:40'),(2832,178,'Hudspeth','2011-06-01 02:06:40'),(2833,178,'Presidio','2011-06-01 02:06:40'),(2834,178,'Culberson','2011-06-01 02:06:40'),(2835,134,'Jefferson','2011-06-01 02:06:40'),(2836,134,'Arapahoe','2011-06-01 02:06:40'),(2837,134,'Adams','2011-06-01 02:06:40'),(2838,134,'Boulder','2011-06-01 02:06:40'),(2839,134,'Elbert','2011-06-01 02:06:40'),(2840,134,'Douglas','2011-06-01 02:06:40'),(2841,134,'Denver','2011-06-01 02:06:40'),(2842,134,'El paso','2011-06-01 02:06:40'),(2843,134,'Park','2011-06-01 02:06:40'),(2844,134,'Gilpin','2011-06-01 02:06:40'),(2845,134,'Eagle','2011-06-01 02:06:40'),(2846,134,'Summit','2011-06-01 02:06:40'),(2847,134,'Routt','2011-06-01 02:06:40'),(2848,134,'Lake','2011-06-01 02:06:40'),(2849,134,'Jackson','2011-06-01 02:06:40'),(2850,134,'Clear creek','2011-06-01 02:06:40'),(2851,134,'Grand','2011-06-01 02:06:40'),(2852,134,'Weld','2011-06-01 02:06:40'),(2853,134,'Larimer','2011-06-01 02:06:40'),(2854,134,'Morgan','2011-06-01 02:06:40'),(2855,134,'Washington','2011-06-01 02:06:40'),(2856,134,'Phillips','2011-06-01 02:06:40'),(2857,134,'Logan','2011-06-01 02:06:40'),(2858,134,'Yuma','2011-06-01 02:06:40'),(2859,134,'Sedgwick','2011-06-01 02:06:40'),(2860,134,'Cheyenne','2011-06-01 02:06:40'),(2861,134,'Lincoln','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2862,134,'Kit carson','2011-06-01 02:06:40'),(2863,134,'Teller','2011-06-01 02:06:40'),(2864,134,'Mohave','2011-06-01 02:06:40'),(2865,134,'Pueblo','2011-06-01 02:06:40'),(2866,134,'Las animas','2011-06-01 02:06:40'),(2867,134,'Kiowa','2011-06-01 02:06:40'),(2868,134,'Baca','2011-06-01 02:06:40'),(2869,134,'Otero','2011-06-01 02:06:40'),(2870,134,'Crowley','2011-06-01 02:06:40'),(2871,134,'Bent','2011-06-01 02:06:40'),(2872,134,'Huerfano','2011-06-01 02:06:40'),(2873,134,'Prowers','2011-06-01 02:06:40'),(2874,134,'Alamosa','2011-06-01 02:06:40'),(2875,134,'Conejos','2011-06-01 02:06:40'),(2876,134,'Archuleta','2011-06-01 02:06:40'),(2877,134,'La plata','2011-06-01 02:06:40'),(2878,134,'Costilla','2011-06-01 02:06:40'),(2879,134,'Saguache','2011-06-01 02:06:40'),(2880,134,'Mineral','2011-06-01 02:06:40'),(2881,134,'Rio grande','2011-06-01 02:06:40'),(2882,134,'Chaffee','2011-06-01 02:06:40'),(2883,134,'Gunnison','2011-06-01 02:06:40'),(2884,134,'Fremont','2011-06-01 02:06:40'),(2885,134,'Montrose','2011-06-01 02:06:40'),(2886,134,'Hinsdale','2011-06-01 02:06:40'),(2887,134,'Custer','2011-06-01 02:06:40'),(2888,134,'Dolores','2011-06-01 02:06:40'),(2889,134,'Montezuma','2011-06-01 02:06:40'),(2890,134,'San miguel','2011-06-01 02:06:40'),(2891,134,'Delta','2011-06-01 02:06:40'),(2892,134,'Ouray','2011-06-01 02:06:40'),(2893,134,'San juan','2011-06-01 02:06:40'),(2894,134,'Mesa','2011-06-01 02:06:40'),(2895,134,'Garfield','2011-06-01 02:06:40'),(2896,134,'Moffat','2011-06-01 02:06:40'),(2897,134,'Pitkin','2011-06-01 02:06:40'),(2898,134,'Rio blanco','2011-06-01 02:06:40'),(2899,186,'Laramie','2011-06-01 02:06:40'),(2900,186,'Albany','2011-06-01 02:06:40'),(2901,186,'Park','2011-06-01 02:06:40'),(2902,186,'Platte','2011-06-01 02:06:40'),(2903,186,'Goshen','2011-06-01 02:06:40'),(2904,186,'Niobrara','2011-06-01 02:06:40'),(2905,186,'Converse','2011-06-01 02:06:40'),(2906,186,'Carbon','2011-06-01 02:06:40'),(2907,186,'Fremont','2011-06-01 02:06:40'),(2908,186,'Sweetwater','2011-06-01 02:06:40'),(2909,186,'Washakie','2011-06-01 02:06:40'),(2910,186,'Big horn','2011-06-01 02:06:40'),(2911,186,'Hot springs','2011-06-01 02:06:40'),(2912,186,'Natrona','2011-06-01 02:06:40'),(2913,186,'Johnson','2011-06-01 02:06:40'),(2914,186,'Weston','2011-06-01 02:06:40'),(2915,186,'Crook','2011-06-01 02:06:40'),(2916,186,'Campbell','2011-06-01 02:06:40'),(2917,186,'Sheridan','2011-06-01 02:06:40'),(2918,186,'Sublette','2011-06-01 02:06:40'),(2919,186,'Uinta','2011-06-01 02:06:40'),(2920,186,'Teton','2011-06-01 02:06:40'),(2921,186,'Lincoln','2011-06-01 02:06:40'),(2922,143,'Bannock','2011-06-01 02:06:40'),(2923,143,'Bingham','2011-06-01 02:06:40'),(2924,143,'Power','2011-06-01 02:06:40'),(2925,143,'Butte','2011-06-01 02:06:40'),(2926,143,'Caribou','2011-06-01 02:06:40'),(2927,143,'Bear lake','2011-06-01 02:06:40'),(2928,143,'Custer','2011-06-01 02:06:40'),(2929,143,'Franklin','2011-06-01 02:06:40'),(2930,143,'Lemhi','2011-06-01 02:06:40'),(2931,143,'Oneida','2011-06-01 02:06:40'),(2932,143,'Twin falls','2011-06-01 02:06:40'),(2933,143,'Cassia','2011-06-01 02:06:40'),(2934,143,'Blaine','2011-06-01 02:06:40'),(2935,143,'Gooding','2011-06-01 02:06:40'),(2936,143,'Camas','2011-06-01 02:06:40'),(2937,143,'Lincoln','2011-06-01 02:06:40'),(2938,143,'Jerome','2011-06-01 02:06:40'),(2939,143,'Minidoka','2011-06-01 02:06:40'),(2940,143,'Bonneville','2011-06-01 02:06:40'),(2941,143,'Fremont','2011-06-01 02:06:40'),(2942,143,'Teton','2011-06-01 02:06:40'),(2943,143,'Clark','2011-06-01 02:06:40'),(2944,143,'Jefferson','2011-06-01 02:06:40'),(2945,143,'Madison','2011-06-01 02:06:40'),(2946,143,'Nez perce','2011-06-01 02:06:40'),(2947,143,'Clearwater','2011-06-01 02:06:40'),(2948,143,'Idaho','2011-06-01 02:06:40'),(2949,143,'Lewis','2011-06-01 02:06:40'),(2950,143,'Latah','2011-06-01 02:06:40'),(2951,143,'Elmore','2011-06-01 02:06:40'),(2952,143,'Boise','2011-06-01 02:06:40'),(2953,143,'Owyhee','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (2954,143,'Canyon','2011-06-01 02:06:40'),(2955,143,'Washington','2011-06-01 02:06:40'),(2956,143,'Valley','2011-06-01 02:06:40'),(2957,143,'Adams','2011-06-01 02:06:40'),(2958,143,'Ada','2011-06-01 02:06:40'),(2959,143,'Gem','2011-06-01 02:06:40'),(2960,143,'Payette','2011-06-01 02:06:40'),(2961,143,'Kootenai','2011-06-01 02:06:40'),(2962,143,'Shoshone','2011-06-01 02:06:40'),(2963,143,'Bonner','2011-06-01 02:06:40'),(2964,143,'Boundary','2011-06-01 02:06:40'),(2965,143,'Benewah','2011-06-01 02:06:40'),(2966,179,'Duchesne','2011-06-01 02:06:40'),(2967,179,'Utah','2011-06-01 02:06:40'),(2968,179,'Salt lake','2011-06-01 02:06:40'),(2969,179,'Uintah','2011-06-01 02:06:40'),(2970,179,'Davis','2011-06-01 02:06:40'),(2971,179,'Summit','2011-06-01 02:06:40'),(2972,179,'Morgan','2011-06-01 02:06:40'),(2973,179,'Tooele','2011-06-01 02:06:40'),(2974,179,'Daggett','2011-06-01 02:06:40'),(2975,179,'Rich','2011-06-01 02:06:40'),(2976,179,'Wasatch','2011-06-01 02:06:40'),(2977,179,'Weber','2011-06-01 02:06:40'),(2978,179,'Box elder','2011-06-01 02:06:40'),(2979,179,'Cache','2011-06-01 02:06:40'),(2980,179,'Carbon','2011-06-01 02:06:40'),(2981,179,'San juan','2011-06-01 02:06:40'),(2982,179,'Emery','2011-06-01 02:06:40'),(2983,179,'Grand','2011-06-01 02:06:40'),(2984,179,'Sevier','2011-06-01 02:06:40'),(2985,179,'Sanpete','2011-06-01 02:06:40'),(2986,179,'Millard','2011-06-01 02:06:40'),(2987,179,'Juab','2011-06-01 02:06:40'),(2988,179,'Kane','2011-06-01 02:06:40'),(2989,179,'Beaver','2011-06-01 02:06:40'),(2990,179,'Iron','2011-06-01 02:06:40'),(2991,179,'Wayne','2011-06-01 02:06:40'),(2992,179,'Washington','2011-06-01 02:06:40'),(2993,179,'Piute','2011-06-01 02:06:40'),(2994,131,'Maricopa','2011-06-01 02:06:40'),(2995,131,'Pinal','2011-06-01 02:06:40'),(2996,131,'Gila','2011-06-01 02:06:40'),(2997,131,'Pima','2011-06-01 02:06:40'),(2998,131,'Yavapai','2011-06-01 02:06:40'),(2999,131,'La Paz','2011-06-01 02:06:40'),(3000,131,'Yuma','2011-06-01 02:06:40'),(3001,131,'Mohave','2011-06-01 02:06:40'),(3002,131,'Graham','2011-06-01 02:06:40'),(3003,131,'Greenlee','2011-06-01 02:06:40'),(3004,131,'Cochise','2011-06-01 02:06:40'),(3005,131,'Santa Cruz','2011-06-01 02:06:40'),(3006,131,'Navajo','2011-06-01 02:06:40'),(3007,131,'Apache','2011-06-01 02:06:40'),(3008,131,'Coconino','2011-06-01 02:06:40'),(3009,163,'Sandoval','2011-06-01 02:06:40'),(3010,163,'Valencia','2011-06-01 02:06:40'),(3011,163,'Cibola','2011-06-01 02:06:40'),(3012,163,'Bernalillo','2011-06-01 02:06:40'),(3013,163,'Torrance','2011-06-01 02:06:40'),(3014,163,'Santa fe','2011-06-01 02:06:40'),(3015,163,'Socorro','2011-06-01 02:06:40'),(3016,163,'Rio arriba','2011-06-01 02:06:40'),(3017,163,'San juan','2011-06-01 02:06:40'),(3018,163,'Mckinley','2011-06-01 02:06:40'),(3019,163,'Taos','2011-06-01 02:06:40'),(3020,163,'San miguel','2011-06-01 02:06:40'),(3021,163,'Los alamos','2011-06-01 02:06:40'),(3022,163,'Colfax','2011-06-01 02:06:40'),(3023,163,'Guadalupe','2011-06-01 02:06:40'),(3024,163,'Mora','2011-06-01 02:06:40'),(3025,163,'Harding','2011-06-01 02:06:40'),(3026,163,'Catron','2011-06-01 02:06:40'),(3027,163,'Sierra','2011-06-01 02:06:40'),(3028,163,'Dona ana','2011-06-01 02:06:40'),(3029,163,'Hidalgo','2011-06-01 02:06:40'),(3030,163,'Grant','2011-06-01 02:06:40'),(3031,163,'Luna','2011-06-01 02:06:40'),(3032,163,'Curry','2011-06-01 02:06:40'),(3033,163,'Roosevelt','2011-06-01 02:06:40'),(3034,163,'Lea','2011-06-01 02:06:40'),(3035,163,'De baca','2011-06-01 02:06:40'),(3036,163,'Quay','2011-06-01 02:06:40'),(3037,163,'Chaves','2011-06-01 02:06:40'),(3038,163,'Eddy','2011-06-01 02:06:40'),(3039,163,'Lincoln','2011-06-01 02:06:40'),(3040,163,'Otero','2011-06-01 02:06:40'),(3041,163,'Union','2011-06-01 02:06:40'),(3042,160,'Clark','2011-06-01 02:06:40'),(3043,160,'Lincoln','2011-06-01 02:06:40'),(3044,160,'Nye','2011-06-01 02:06:40'),(3045,160,'Esmeralda','2011-06-01 02:06:40'),(3046,160,'White pine','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (3047,160,'Lander','2011-06-01 02:06:40'),(3048,160,'Eureka','2011-06-01 02:06:40'),(3049,160,'Washoe','2011-06-01 02:06:40'),(3050,160,'Lyon','2011-06-01 02:06:40'),(3051,160,'Humboldt','2011-06-01 02:06:40'),(3052,160,'Churchill','2011-06-01 02:06:40'),(3053,160,'Douglas','2011-06-01 02:06:40'),(3054,160,'Mineral','2011-06-01 02:06:40'),(3055,160,'Pershing','2011-06-01 02:06:40'),(3056,160,'Storey','2011-06-01 02:06:40'),(3057,160,'Carson city','2011-06-01 02:06:40'),(3058,160,'Elko','2011-06-01 02:06:40'),(3059,133,'Los angeles','2011-06-01 02:06:40'),(3060,133,'Orange','2011-06-01 02:06:40'),(3061,133,'Ventura','2011-06-01 02:06:40'),(3062,133,'San bernardino','2011-06-01 02:06:40'),(3063,133,'Riverside','2011-06-01 02:06:40'),(3064,133,'San diego','2011-06-01 02:06:40'),(3065,133,'Imperial','2011-06-01 02:06:40'),(3066,133,'Inyo','2011-06-01 02:06:40'),(3067,133,'Santa barbara','2011-06-01 02:06:40'),(3068,133,'Tulare','2011-06-01 02:06:40'),(3069,133,'Kings','2011-06-01 02:06:40'),(3070,133,'Kern','2011-06-01 02:06:40'),(3071,133,'Fresno','2011-06-01 02:06:40'),(3072,133,'San luis obispo','2011-06-01 02:06:40'),(3073,133,'Monterey','2011-06-01 02:06:40'),(3074,133,'Mono','2011-06-01 02:06:40'),(3075,133,'Madera','2011-06-01 02:06:40'),(3076,133,'Merced','2011-06-01 02:06:40'),(3077,133,'Mariposa','2011-06-01 02:06:40'),(3078,133,'San mateo','2011-06-01 02:06:40'),(3079,133,'Santa clara','2011-06-01 02:06:40'),(3080,133,'San francisco','2011-06-01 02:06:40'),(3081,133,'Sacramento','2011-06-01 02:06:40'),(3082,133,'Alameda','2011-06-01 02:06:40'),(3083,133,'Napa','2011-06-01 02:06:40'),(3084,133,'Contra costa','2011-06-01 02:06:40'),(3085,133,'Solano','2011-06-01 02:06:40'),(3086,133,'Marin','2011-06-01 02:06:40'),(3087,133,'Sonoma','2011-06-01 02:06:40'),(3088,133,'Santa cruz','2011-06-01 02:06:40'),(3089,133,'San benito','2011-06-01 02:06:40'),(3090,133,'San joaquin','2011-06-01 02:06:40'),(3091,133,'Calaveras','2011-06-01 02:06:40'),(3092,133,'Tuolumne','2011-06-01 02:06:40'),(3093,133,'Stanislaus','2011-06-01 02:06:40'),(3094,133,'Mendocino','2011-06-01 02:06:40'),(3095,133,'Lake','2011-06-01 02:06:40'),(3096,133,'Humboldt','2011-06-01 02:06:40'),(3097,133,'Trinity','2011-06-01 02:06:40'),(3098,133,'Del norte','2011-06-01 02:06:40'),(3099,133,'Siskiyou','2011-06-01 02:06:40'),(3100,133,'Amador','2011-06-01 02:06:40'),(3101,133,'Placer','2011-06-01 02:06:40'),(3102,133,'Yolo','2011-06-01 02:06:40'),(3103,133,'El dorado','2011-06-01 02:06:40'),(3104,133,'Alpine','2011-06-01 02:06:40'),(3105,133,'Sutter','2011-06-01 02:06:40'),(3106,133,'Yuba','2011-06-01 02:06:40'),(3107,133,'Nevada','2011-06-01 02:06:40'),(3108,133,'Sierra','2011-06-01 02:06:40'),(3109,133,'Colusa','2011-06-01 02:06:40'),(3110,133,'Glenn','2011-06-01 02:06:40'),(3111,133,'Butte','2011-06-01 02:06:40'),(3112,133,'Plumas','2011-06-01 02:06:40'),(3113,133,'Shasta','2011-06-01 02:06:40'),(3114,133,'Modoc','2011-06-01 02:06:40'),(3115,133,'Lassen','2011-06-01 02:06:40'),(3116,133,'Tehama','2011-06-01 02:06:40'),(3117,142,'Honolulu','2011-06-01 02:06:40'),(3118,142,'Kauai','2011-06-01 02:06:40'),(3119,142,'Hawaii','2011-06-01 02:06:40'),(3120,142,'Maui','2011-06-01 02:06:40'),(3121,130,'American samoa','2011-06-01 02:06:40'),(3122,141,'Guam','2011-06-01 02:06:40'),(3123,171,'Palau','2011-06-01 02:06:40'),(3124,138,'Federated states of micro','2011-06-01 02:06:40'),(3125,167,'Northern mariana islands','2011-06-01 02:06:40'),(3126,151,'Marshall islands','2011-06-01 02:06:40'),(3127,170,'Wasco','2011-06-01 02:06:40'),(3128,170,'Marion','2011-06-01 02:06:40'),(3129,170,'Clackamas','2011-06-01 02:06:40'),(3130,170,'Washington','2011-06-01 02:06:40'),(3131,170,'Multnomah','2011-06-01 02:06:40'),(3132,170,'Hood river','2011-06-01 02:06:40'),(3133,170,'Columbia','2011-06-01 02:06:40'),(3134,170,'Sherman','2011-06-01 02:06:40'),(3135,170,'Yamhill','2011-06-01 02:06:40'),(3136,170,'Clatsop','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (3137,170,'Tillamook','2011-06-01 02:06:40'),(3138,170,'Polk','2011-06-01 02:06:40'),(3139,170,'Linn','2011-06-01 02:06:40'),(3140,170,'Benton','2011-06-01 02:06:40'),(3141,170,'Lincoln','2011-06-01 02:06:40'),(3142,170,'Lane','2011-06-01 02:06:40'),(3143,170,'Curry','2011-06-01 02:06:40'),(3144,170,'Coos','2011-06-01 02:06:40'),(3145,170,'Douglas','2011-06-01 02:06:40'),(3146,170,'Klamath','2011-06-01 02:06:40'),(3147,170,'Josephine','2011-06-01 02:06:40'),(3148,170,'Jackson','2011-06-01 02:06:40'),(3149,170,'Lake','2011-06-01 02:06:40'),(3150,170,'Deschutes','2011-06-01 02:06:40'),(3151,170,'Harney','2011-06-01 02:06:40'),(3152,170,'Jefferson','2011-06-01 02:06:40'),(3153,170,'Wheeler','2011-06-01 02:06:40'),(3154,170,'Crook','2011-06-01 02:06:40'),(3155,170,'Umatilla','2011-06-01 02:06:40'),(3156,170,'Gilliam','2011-06-01 02:06:40'),(3157,170,'Baker','2011-06-01 02:06:40'),(3158,170,'Grant','2011-06-01 02:06:40'),(3159,170,'Morrow','2011-06-01 02:06:40'),(3160,170,'Union','2011-06-01 02:06:40'),(3161,170,'Wallowa','2011-06-01 02:06:40'),(3162,170,'Malheur','2011-06-01 02:06:40'),(3163,183,'King','2011-06-01 02:06:40'),(3164,183,'Snohomish','2011-06-01 02:06:40'),(3165,183,'Kitsap','2011-06-01 02:06:40'),(3166,183,'Whatcom','2011-06-01 02:06:40'),(3167,183,'Skagit','2011-06-01 02:06:40'),(3168,183,'San juan','2011-06-01 02:06:40'),(3169,183,'Island','2011-06-01 02:06:40'),(3170,183,'Pierce','2011-06-01 02:06:40'),(3171,183,'Clallam','2011-06-01 02:06:40'),(3172,183,'Jefferson','2011-06-01 02:06:40'),(3173,183,'Lewis','2011-06-01 02:06:40'),(3174,183,'Thurston','2011-06-01 02:06:40'),(3175,183,'Grays harbor','2011-06-01 02:06:40'),(3176,183,'Mason','2011-06-01 02:06:40'),(3177,183,'Pacific','2011-06-01 02:06:40'),(3178,183,'Cowlitz','2011-06-01 02:06:40'),(3179,183,'Clark','2011-06-01 02:06:40'),(3180,183,'Klickitat','2011-06-01 02:06:40'),(3181,183,'Skamania','2011-06-01 02:06:40'),(3182,183,'Wahkiakum','2011-06-01 02:06:40'),(3183,183,'Chelan','2011-06-01 02:06:40'),(3184,183,'Douglas','2011-06-01 02:06:40'),(3185,183,'Okanogan','2011-06-01 02:06:40'),(3186,183,'Grant','2011-06-01 02:06:40'),(3187,183,'Yakima','2011-06-01 02:06:40'),(3188,183,'Kittitas','2011-06-01 02:06:40'),(3189,183,'Spokane','2011-06-01 02:06:40'),(3190,183,'Lincoln','2011-06-01 02:06:40'),(3191,183,'Stevens','2011-06-01 02:06:40'),(3192,183,'Whitman','2011-06-01 02:06:40'),(3193,183,'Adams','2011-06-01 02:06:40'),(3194,183,'Ferry','2011-06-01 02:06:40'),(3195,183,'Pend oreille','2011-06-01 02:06:40'),(3196,183,'Franklin','2011-06-01 02:06:40'),(3197,183,'Benton','2011-06-01 02:06:40'),(3198,183,'Walla walla','2011-06-01 02:06:40'),(3199,183,'Columbia','2011-06-01 02:06:40'),(3200,183,'Garfield','2011-06-01 02:06:40'),(3201,183,'Asotin','2011-06-01 02:06:40'),(3202,128,'Anchorage','2011-06-01 02:06:40'),(3203,128,'Bethel','2011-06-01 02:06:40'),(3204,128,'Aleutians west','2011-06-01 02:06:40'),(3205,128,'Lake and peninsula','2011-06-01 02:06:40'),(3206,128,'Kodiak island','2011-06-01 02:06:40'),(3207,128,'Aleutians east','2011-06-01 02:06:40'),(3208,128,'Wade hampton','2011-06-01 02:06:40'),(3209,128,'Dillingham','2011-06-01 02:06:40'),(3210,128,'Kenai peninsula','2011-06-01 02:06:40'),(3211,128,'Yukon koyukuk','2011-06-01 02:06:40'),(3212,128,'Valdez cordova','2011-06-01 02:06:40'),(3213,128,'Matanuska susitna','2011-06-01 02:06:40'),(3214,128,'Bristol bay','2011-06-01 02:06:40'),(3215,128,'Nome','2011-06-01 02:06:40'),(3216,128,'Yakutat','2011-06-01 02:06:40'),(3217,128,'Fairbanks north star','2011-06-01 02:06:40'),(3218,128,'Denali','2011-06-01 02:06:40'),(3219,128,'North slope','2011-06-01 02:06:40'),(3220,128,'Northwest arctic','2011-06-01 02:06:40'),(3221,128,'Southeast fairbanks','2011-06-01 02:06:40'),(3222,128,'Juneau','2011-06-01 02:06:40'),(3223,128,'Skagway hoonah angoon','2011-06-01 02:06:40'),(3224,128,'Haines','2011-06-01 02:06:40'),(3225,128,'Wrangell petersburg','2011-06-01 02:06:40');
INSERT INTO `lkupcounty` VALUES (3226,128,'Sitka','2011-06-01 02:06:40'),(3227,128,'Ketchikan gateway','2011-06-01 02:06:40'),(3228,128,'Prince wales ketchikan','2011-06-01 02:06:40');

INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (1, "English", "en", "eng");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (2, "German", "de", "deu");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (3, "French", "fr", "fra");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (4, "Dutch", "nl", "nld");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (5, "Italian", "it", "ita");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (6, "Spanish", "es", "spa");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (7, "Polish", "pl", "pol");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (8, "Russian", "ru", "rus");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (9, "Japanese", "ja", "jpn");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (10, "Portuguese", "pt", "por");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (11, "Swedish", "sv", "swe");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (12, "Chinese", "zh", "zho");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (13, "Catalan", "ca", "cat");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (14, "Ukrainian", "uk", "ukr");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (15, "Norwegian Bokmål", "no", "nob");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (16, "Finnish", "fi", "fin");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (17, "Vietnamese", "vi", "vie");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (18, "Czech", "cs", "ces");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (19, "Hungarian", "hu", "hun");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (20, "Korean", "ko", "kor");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (21, "Indonesian", "id", "ind");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (22, "Turkish", "tr", "tur");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (23, "Romanian", "ro", "ron");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (24, "Persian (Farsi)", "fa", "fas");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (25, "Arabic", "ar", "ara");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (26, "Danish", "da", "dan");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (27, "Esperanto", "eo", "epo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (28, "Serbian", "sr", "srp");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (29, "Lithuanian", "lt", "lit");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (30, "Slovak", "sk", "slk");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (31, "Malay", "ms", "msa");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (32, "Hebrew (modern)", "he", "heb");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (33, "Bulgarian", "bg", "bul");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (34, "Slovene", "sl", "slv");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (35, "Volapük", "vo", "vol");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (36, "Kazakh", "kk", "kaz");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (38, "Basque", "eu", "eus");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (39, "Croatian", "hr", "hrv");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (40, "Hindi", "hi", "hin");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (41, "Estonian", "et", "est");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (42, "Azerbaijani", "az", "aze");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (43, "Galician", "gl", "glg");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (45, "Norwegian Nynorsk", "nn", "nno");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (46, "Thai", "th", "tha");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (48, "Greek (modern)", "el", "ell");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (50, "Latin", "la", "lat");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (51, "Occitan", "oc", "oci");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (52, "Tagalog", "tl", "tgl");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (53, "Haitian, Haitian Creole", "ht", "hat");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (54, "Macedonian", "mk", "mkd");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (55, "Georgian", "ka", "kat");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (57, "Telugu", "te", "tel");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (60, "Tamil", "ta", "tam");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (62, "Breton", "br", "bre");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (63, "Latvian", "lv", "lav");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (64, "Javanese", "jv", "jav");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (65, "Albanian", "sq", "sqi");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (66, "Belarusian", "be", "bel");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (67, "Marathi (Marāṭhī)", "mr", "mar");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (68, "Welsh", "cy", "cym");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (69, "Luxembourgish, Letzeburgesch", "lb", "ltz");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (70, "Icelandic", "is", "isl");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (71, "Bosnian", "bs", "bos");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (72, "Yoruba", "yo", "yor");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (73, "Malagasy", "mg", "mlg");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (74, "Aragonese", "an", "arg");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (77, "Western Frisian", "fy", "fry");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (78, "Bengali, Bangla", "bn", "ben");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (79, "Ido", "io", "ido");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (80, "Swahili", "sw", "swa");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (81, "Gujarati", "gu", "guj");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (82, "Malayalam", "ml", "mal");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (84, "Afrikaans", "af", "afr");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (87, "Urdu", "ur", "urd");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (88, "Kurdish", "ku", "kur");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (90, "Armenian", "hy", "hye");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (91, "Quechua", "qu", "que");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (92, "Sundanese", "su", "sun");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (93, "Nepali", "ne", "nep");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (96, "Tatar", "tt", "tat");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (98, "Irish", "ga", "gle");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (99, "Chuvash", "cv", "chv");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (101, "Walloon", "wa", "wln");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (102, "Amharic", "am", "amh");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (103, "Kannada", "kn", "kan");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (106, "Burmese", "my", "mya");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (107, "Interlingua", "ia", "ina");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (108, "Abkhaz", "ab", "abk");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (109, "Afar", "aa", "aar");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (110, "Akan", "ak", "aka");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (111, "Assamese", "as", "asm");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (112, "Avaric", "av", "ava");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (113, "Avestan", "ae", "ave");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (114, "Aymara", "ay", "aym");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (115, "Bambara", "bm", "bam");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (116, "Bashkir", "ba", "bak");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (117, "Bihari", "bh", "bih");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (118, "Bislama", "bi", "bis");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (119, "Chamorro", "ch", "cha");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (120, "Chechen", "ce", "che");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (121, "Chichewa, Chewa, Nyanja", "ny", "nya");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (122, "Cornish", "kw", "cor");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (123, "Corsican", "co", "cos");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (124, "Cree", "cr", "cre");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (125, "Divehi, Dhivehi, Maldivian", "dv", "div");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (126, "Dzongkha", "dz", "dzo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (127, "Ewe", "ee", "ewe");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (128, "Faroese", "fo", "fao");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (129, "Fijian", "fj", "fij");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (130, "Fula, Fulah, Pulaar, Pular", "ff", "ful");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (131, "Guaraní", "gn", "grn");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (132, "Hausa", "ha", "hau");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (133, "Herero", "hz", "her");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (134, "Hiri Motu", "ho", "hmo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (135, "Interlingue", "ie", "ile");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (136, "Igbo", "ig", "ibo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (137, "Inupiaq", "ik", "ipk");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (138, "Inuktitut", "iu", "iku");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (139, "Kalaallisut, Greenlandic", "kl", "kal");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (140, "Kanuri", "kr", "kau");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (141, "Kashmiri", "ks", "kas");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (142, "Khmer", "km", "khm");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (143, "Kikuyu, Gikuyu", "ki", "kik");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (144, "Kinyarwanda", "rw", "kin");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (145, "Kyrgyz", "ky", "kir");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (146, "Komi", "kv", "kom");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (147, "Kongo", "kg", "kon");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (148, "Kwanyama, Kuanyama", "kj", "kua");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (149, "Ganda", "lg", "lug");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (150, "Limburgish, Limburgan, Limburger", "li", "lim");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (151, "Lingala", "ln", "lin");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (152, "Lao", "lo", "lao");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (153, "Luba-Katanga", "lu", "lub");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (154, "Manx", "gv", "glv");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (155, "Maltese", "mt", "mlt");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (156, "Māori", "mi", "mri");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (157, "Marshallese", "mh", "mah");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (158, "Mongolian", "mn", "mon");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (159, "Nauruan", "na", "nau");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (160, "Navajo, Navaho", "nv", "nav");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (161, "Northern Ndebele", "nd", "nde");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (162, "Ndonga", "ng", "ndo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (163, "Norwegian", "no", "nor");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (164, "Nuosu", "ii", "iii");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (165, "Southern Ndebele", "nr", "nbl");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (166, "Ojibwe, Ojibwa", "oj", "oji");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (167, "Old Church Slavonic, Church Slavonic, Old Bul", "cu", "chu");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (168, "Oromo", "om", "orm");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (169, "Oriya", "or", "ori");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (170, "Ossetian, Ossetic", "os", "oss");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (171, "(Eastern) Punjabi", "pa", "pan");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (172, "Pāli", "pi", "pli");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (173, "Pashto, Pushto", "ps", "pus");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (174, "Romansh", "rm", "roh");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (175, "Kirundi", "rn", "run");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (176, "Sanskrit (Saṁskṛta)", "sa", "san");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (177, "Sardinian", "sc", "srd");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (178, "Sindhi", "sd", "snd");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (179, "Northern Sami", "se", "sme");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (180, "Samoan", "sm", "smo");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (181, "Sango", "sg", "sag");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (182, "Scottish Gaelic, Gaelic", "gd", "gla");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (183, "Shona", "sn", "sna");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (184, "Sinhalese, Sinhala", "si", "sin");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (185, "Somali", "so", "som");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (186, "Southern Sotho", "st", "sot");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (187, "Swati", "ss", "ssw");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (188, "Tajik", "tg", "tgk");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (189, "Tigrinya", "ti", "tir");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (190, "Tibetan Standard, Tibetan, Central", "bo", "bod");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (191, "Turkmen", "tk", "tuk");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (192, "Tswana", "tn", "tsn");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (193, "Tonga (Tonga Islands)", "to", "ton");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (194, "Tsonga", "ts", "tso");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (195, "Twi", "tw", "twi");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (196, "Tahitian", "ty", "tah");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (197, "Uyghur", "ug", "uig");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (198, "Uzbek", "uz", "uzb");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (199, "Venda", "ve", "ven");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (200, "Wolof", "wo", "wol");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (201, "Xhosa", "xh", "xho");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (202, "Yiddish", "yi", "yid");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (203, "Zhuang, Chuang", "za", "zha");
INSERT INTO `adminlanguages` (`langid`, `langname`, `iso639_1`, `iso639_2`) VALUES (204, "Zulu", "zu", "zul");

INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('HasOrganism','Image shows an organism.','Organism',0);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('HasLabel','Image shows label data.','Label',10);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('HasIDLabel','Image shows an annotation/identification label.','Annotation',20);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('TypedText','Image has typed or printed text.','Typed/Printed',30);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('Handwriting','Image has handwritten label text.','Handwritten',40);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('ShowsHabitat','Field image of habitat.','Habitat',50);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('HasProblem','There is a problem with this image.','QC Problem',60);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('ImageOfAdult','Image contains the adult organism.','Adult',80);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('Diagnostic','Image contains a diagnostic character.','Diagnostic',70);
INSERT into imagetagkey (tagkey,description_en,shortlabel,sortorder) values ('ImageOfImmature','Image contains the immature organism.','Immature',90);

INSERT INTO `referencetype` VALUES ('1', 'Generic', null, 'Title', 'SecondaryTitle', 'PlacePublished', 'Publisher', 'Volume', 'NumberVolumes', 'Number', 'Pages', 'Section', 'TertiaryTitle', 'Edition', 'Date', 'TypeWork', 'ShortTitle', 'AlternativeTitle', 'Isbn_Issn', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('2', 'Journal Article', null, 'Title', 'Periodical Title', null, null, 'Volume', null, 'Issue', 'Pages', null, null, null, 'Date', null, 'Short Title', 'Alt. Jour.', null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('3', 'Book', '1', 'Title', 'Series Title', 'City', 'Publisher', 'Volume', 'No. Vols.', 'Number', 'Pages', null, null, 'Edition', 'Date', null, 'Short Title', null, 'ISBN', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('4', 'Book Section', null, 'Title', 'Book Title', 'City', 'Publisher', 'Volume', 'No. Vols.', 'Number', 'Pages', null, 'Ser. Title', 'Edition', 'Date', null, 'Short Title', null, 'ISBN', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('5', 'Manuscript', null, 'Title', 'Collection Title', 'City', null, null, null, 'Number', 'Pages', null, null, 'Edition', 'Date', 'Type Work', 'Short Title', null, null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('6', 'Edited Book', '1', 'Title', 'Series Title', 'City', 'Publisher', 'Volume', 'No. Vols.', 'Number', 'Pages', null, null, 'Edition', 'Date', null, 'Short Title', null, 'ISBN', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('7', 'Magazine Article', null, 'Title', 'Periodical Title', null, null, 'Volume', null, 'Issue', 'Pages', null, null, null, 'Date', null, 'Short Title', null, null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('8', 'Newspaper Article', null, 'Title', 'Periodical Title', 'City', null, null, null, null, 'Pages', 'Section', null, 'Edition', 'Date', 'Type Art.', 'Short Title', null, null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('9', 'Conference Proceedings', null, 'Title', 'Conf. Name', 'Conf. Loc.', 'Publisher', 'Volume', 'No. Vols.', null, 'Pages', null, 'Ser. Title', 'Edition', 'Date', null, 'Short Title', null, 'ISBN', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('10', 'Thesis', null, 'Title', 'Academic Dept.', 'City', 'University', null, null, null, 'Pages', null, null, null, 'Date', 'Thesis Type', 'Short Title', null, null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('11', 'Report', null, 'Title', null, 'City', 'Institution', null, null, null, 'Pages', null, null, null, 'Date', 'Type Work', 'Short Title', null, 'Rpt. No.', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('12', 'Personal Communication', null, 'Title', null, 'City', 'Publisher', null, null, null, null, null, null, null, 'Date', 'Type Work', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('13', 'Computer Program', null, 'Title', null, 'City', 'Publisher', 'Version', null, null, null, null, null, 'Platform', 'Date', 'Type Work', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('14', 'Electronic Source', null, 'Title', null, null, 'Publisher', 'Access Year', 'Extent', 'Acc. Date', null, null, null, 'Edition', 'Date', 'Medium', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('15', 'Audiovisual Material', null, 'Title', 'Collection Title', 'City', 'Publisher', null, null, 'Number', null, null, null, null, 'Date', 'Type Work', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('16', 'Film or Broadcast', null, 'Title', 'Series Title', 'City', 'Distributor', null, null, null, 'Length', null, null, null, 'Date', 'Medium', 'Short Title', null, 'ISBN', null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('17', 'Artwork', null, 'Title', null, 'City', 'Publisher', null, null, null, null, null, null, null, 'Date', 'Type Work', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('18', 'Map', null, 'Title', null, 'City', 'Publisher', null, null, null, 'Scale', null, null, 'Edition', 'Date', 'Type Work', 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('19', 'Patent', null, 'Title', 'Published Source', 'Country', 'Assignee', 'Volume', 'No. Vols.', 'Issue', 'Pages', null, null, null, 'Date', null, 'Short Title', null, 'Pat. No.', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('20', 'Hearing', null, 'Title', 'Committee', 'City', 'Publisher', null, null, 'Doc. No.', 'Pages', null, 'Leg. Boby', 'Session', 'Date', null, 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('21', 'Bill', null, 'Title', 'Code', null, null, 'Code Volume', null, 'Bill No.', 'Pages', 'Section', 'Leg. Boby', 'Session', 'Date', null, 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('22', 'Statute', null, 'Title', 'Code', null, null, 'Code Number', null, 'Law No.', '1st Pg.', 'Section', null, 'Session', 'Date', null, 'Short Title', null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('23', 'Case', null, 'Title', null, null, 'Court', 'Reporter Vol.', null, null, null, null, null, null, 'Date', null, null, null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('24', 'Figure', null, 'Title', 'Source Program', null, null, null, '-', null, null, null, null, null, 'Date', null, null, null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('25', 'Chart or Table', null, 'Title', 'Source Program', null, null, null, null, null, null, null, null, null, 'Date', null, null, null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('26', 'Equation', null, 'Title', 'Source Program', null, null, 'Volume', null, 'Number', null, null, null, null, 'Date', null, null, null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('27', 'Book Series', '1', 'Title', null, 'City', 'Publisher', null, 'No. Vols.', null, 'Pages', null, null, 'Edition', 'Date', null, null, null, 'ISBN', 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('28', 'Determination', null, 'Title', null, null, 'Institution', null, null, null, null, null, null, null, 'Date', null, null, null, null, null, null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('29', 'Sub-Reference', null, 'Title', null, null, null, null, null, null, 'Pages', null, null, null, 'Date', null, null, null, null, 'Figures', null, '2014-06-17 00:27:12');
INSERT INTO `referencetype` VALUES ('30', 'Periodical', '1', 'Title', null, 'City', null, 'Volume', null, 'Issue', null, null, null, 'Edition', 'Date', null, 'Short Title', 'Alt. Jour.', null, null, null, '2014-10-30 21:34:44');
INSERT INTO `referencetype` VALUES ('31', 'Web Page', null, 'Title', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2014-10-30 21:37:12');

INSERT INTO `paleochronostratigraphy` VALUES ('1', 'Hadean', null, null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('2', 'Archean', null, null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('3', 'Archean', 'Eoarchean', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('4', 'Archean', 'Paleoarchean', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('5', 'Archean', 'Mesoarchean', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('6', 'Archean', 'Neoarchean', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('7', 'Proterozoic', null, null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('8', 'Proterozoic', 'Paleoproterozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('9', 'Proterozoic', 'Paleoproterozoic', 'Siderian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('10', 'Proterozoic', 'Paleoproterozoic', 'Rhyacian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('11', 'Proterozoic', 'Paleoproterozoic', 'Orosirian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('12', 'Proterozoic', 'Paleoproterozoic', 'Statherian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('13', 'Proterozoic', 'Mesoproterozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('14', 'Proterozoic', 'Mesoproterozoic', 'Calymmian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('15', 'Proterozoic', 'Mesoproterozoic', 'Ectasian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('16', 'Proterozoic', 'Mesoproterozoic', 'Stenian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('17', 'Proterozoic', 'Neoproterozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('18', 'Proterozoic', 'Neoproterozoic', 'Tonian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('19', 'Proterozoic', 'Neoproterozoic', 'Gryogenian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('20', 'Proterozoic', 'Neoproterozoic', 'Ediacaran', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('21', 'Phanerozoic', null, null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('22', 'Phanerozoic', 'Paleozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('23', 'Phanerozoic', 'Paleozoic', 'Cambrian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('24', 'Phanerozoic', 'Paleozoic', 'Cambrian', 'Lower Cambrian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('25', 'Phanerozoic', 'Paleozoic', 'Cambrian', 'Middle Cambrian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('26', 'Phanerozoic', 'Paleozoic', 'Cambrian', 'Upper Cambrian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('27', 'Phanerozoic', 'Paleozoic', 'Ordovician', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('28', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Lower Ordovician', null);
INSERT INTO `paleochronostratigraphy` VALUES ('29', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Lower Ordovician', 'Tremadocian');
INSERT INTO `paleochronostratigraphy` VALUES ('30', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Lower Ordovician', 'Floian');
INSERT INTO `paleochronostratigraphy` VALUES ('31', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Middle Ordovician', null);
INSERT INTO `paleochronostratigraphy` VALUES ('32', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Middle Ordovician', 'Dapingian');
INSERT INTO `paleochronostratigraphy` VALUES ('33', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Middle Ordovician', 'Darriwilian');
INSERT INTO `paleochronostratigraphy` VALUES ('34', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Upper Ordivician', null);
INSERT INTO `paleochronostratigraphy` VALUES ('35', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Upper Ordivician', 'Sandbian');
INSERT INTO `paleochronostratigraphy` VALUES ('36', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Upper Ordivician', 'Katian');
INSERT INTO `paleochronostratigraphy` VALUES ('37', 'Phanerozoic', 'Paleozoic', 'Ordovician', 'Upper Ordivician', 'Hirnantian');
INSERT INTO `paleochronostratigraphy` VALUES ('38', 'Phanerozoic', 'Paleozoic', 'Silurian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('39', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Llandovery', null);
INSERT INTO `paleochronostratigraphy` VALUES ('40', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Llandovery', 'Rhuddanian');
INSERT INTO `paleochronostratigraphy` VALUES ('41', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Llandovery', 'Aeronian');
INSERT INTO `paleochronostratigraphy` VALUES ('42', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Llandovery', 'Telychian');
INSERT INTO `paleochronostratigraphy` VALUES ('43', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Wenlock', null);
INSERT INTO `paleochronostratigraphy` VALUES ('44', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Wenlock', 'Sheinwoodian');
INSERT INTO `paleochronostratigraphy` VALUES ('45', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Wenlock', 'Homerian');
INSERT INTO `paleochronostratigraphy` VALUES ('46', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Ludlow', null);
INSERT INTO `paleochronostratigraphy` VALUES ('47', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Ludlow', 'Gorstian');
INSERT INTO `paleochronostratigraphy` VALUES ('48', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Ludlow', 'Ludfordian');
INSERT INTO `paleochronostratigraphy` VALUES ('49', 'Phanerozoic', 'Paleozoic', 'Silurian', 'Pridoli', null);
INSERT INTO `paleochronostratigraphy` VALUES ('50', 'Phanerozoic', 'Paleozoic', 'Devonian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('51', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Lower Devonian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('52', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Lower Devonian', 'Lochkovian');
INSERT INTO `paleochronostratigraphy` VALUES ('53', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Lower Devonian', 'Pragian');
INSERT INTO `paleochronostratigraphy` VALUES ('54', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Lower Devonian', 'Emsian');
INSERT INTO `paleochronostratigraphy` VALUES ('55', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Middle Devonian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('56', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Middle Devonian', 'Eifelian');
INSERT INTO `paleochronostratigraphy` VALUES ('57', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Middle Devonian', 'Givetian');
INSERT INTO `paleochronostratigraphy` VALUES ('58', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Upper Devonian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('59', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Upper Devonian', 'Frasnian');
INSERT INTO `paleochronostratigraphy` VALUES ('60', 'Phanerozoic', 'Paleozoic', 'Devonian', 'Upper Devonian', 'Famennian');
INSERT INTO `paleochronostratigraphy` VALUES ('61', 'Phanerozoic', 'Paleozoic', 'Carboniferous', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('62', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Mississippian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('63', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Mississippian', 'Lower Mississippian');
INSERT INTO `paleochronostratigraphy` VALUES ('64', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Mississippian', 'Middle Mississippian');
INSERT INTO `paleochronostratigraphy` VALUES ('65', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Mississippian', 'Upper Mississippian');
INSERT INTO `paleochronostratigraphy` VALUES ('66', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Pennsylvanian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('67', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Pennsylvanian', 'Lower Pennsylvanian');
INSERT INTO `paleochronostratigraphy` VALUES ('68', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Pennsylvanian', 'Middle Pennsylvanian');
INSERT INTO `paleochronostratigraphy` VALUES ('69', 'Phanerozoic', 'Paleozoic', 'Carboniferous', 'Pennsylvanian', 'Upper Pennsylvanian');
INSERT INTO `paleochronostratigraphy` VALUES ('70', 'Phanerozoic', 'Paleozoic', 'Permian', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('71', 'Phanerozoic', 'Paleozoic', 'Permian', 'Cisuralian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('72', 'Phanerozoic', 'Paleozoic', 'Permian', 'Cisuralian', 'Asselian');
INSERT INTO `paleochronostratigraphy` VALUES ('73', 'Phanerozoic', 'Paleozoic', 'Permian', 'Cisuralian', 'Sakmarian');
INSERT INTO `paleochronostratigraphy` VALUES ('74', 'Phanerozoic', 'Paleozoic', 'Permian', 'Cisuralian', 'Artinskian');
INSERT INTO `paleochronostratigraphy` VALUES ('75', 'Phanerozoic', 'Paleozoic', 'Permian', 'Cisuralian', 'Kungurian');
INSERT INTO `paleochronostratigraphy` VALUES ('76', 'Phanerozoic', 'Paleozoic', 'Permian', 'Guadalupian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('77', 'Phanerozoic', 'Paleozoic', 'Permian', 'Guadalupian', 'Roadian');
INSERT INTO `paleochronostratigraphy` VALUES ('78', 'Phanerozoic', 'Paleozoic', 'Permian', 'Guadalupian', 'Wordian');
INSERT INTO `paleochronostratigraphy` VALUES ('79', 'Phanerozoic', 'Paleozoic', 'Permian', 'Guadalupian', 'Capitanian');
INSERT INTO `paleochronostratigraphy` VALUES ('80', 'Phanerozoic', 'Paleozoic', 'Permian', 'Lopingian', null);
INSERT INTO `paleochronostratigraphy` VALUES ('81', 'Phanerozoic', 'Paleozoic', 'Permian', 'Lopingian', 'Wuchiapingian');
INSERT INTO `paleochronostratigraphy` VALUES ('82', 'Phanerozoic', 'Paleozoic', 'Permian', 'Lopingian', 'Changhsingian');
INSERT INTO `paleochronostratigraphy` VALUES ('83', 'Phanerozoic', 'Mesozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('84', 'Phanerozoic', 'Mesozoic', 'Triassic', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('85', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Lower Triassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('86', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Lower Triassic', 'Induan');
INSERT INTO `paleochronostratigraphy` VALUES ('87', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Lower Triassic', 'Olenekian');
INSERT INTO `paleochronostratigraphy` VALUES ('88', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Middle Triassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('89', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Middle Triassic', 'Anisian');
INSERT INTO `paleochronostratigraphy` VALUES ('90', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Middle Triassic', 'Ladinian');
INSERT INTO `paleochronostratigraphy` VALUES ('91', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Upper Triassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('92', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Upper Triassic', 'Carnian');
INSERT INTO `paleochronostratigraphy` VALUES ('93', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Upper Triassic', 'Norian');
INSERT INTO `paleochronostratigraphy` VALUES ('94', 'Phanerozoic', 'Mesozoic', 'Triassic', 'Upper Triassic', 'Rhaetian');
INSERT INTO `paleochronostratigraphy` VALUES ('95', 'Phanerozoic', 'Mesozoic', 'Jurassic', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('96', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Lower Jurassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('97', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Lower Jurassic', 'Hettangian');
INSERT INTO `paleochronostratigraphy` VALUES ('98', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Lower Jurassic', 'Sinemurian');
INSERT INTO `paleochronostratigraphy` VALUES ('99', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Lower Jurassic', 'Pliensbachian');
INSERT INTO `paleochronostratigraphy` VALUES ('100', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Lower Jurassic', 'Toarcian');
INSERT INTO `paleochronostratigraphy` VALUES ('101', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Middle Jurassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('102', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Middle Jurassic', 'Aalenian');
INSERT INTO `paleochronostratigraphy` VALUES ('103', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Middle Jurassic', 'Bajocian');
INSERT INTO `paleochronostratigraphy` VALUES ('104', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Middle Jurassic', 'Bathonian');
INSERT INTO `paleochronostratigraphy` VALUES ('105', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Middle Jurassic', 'Callovian');
INSERT INTO `paleochronostratigraphy` VALUES ('106', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Upper Jurassic', null);
INSERT INTO `paleochronostratigraphy` VALUES ('107', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Upper Jurassic', 'Oxfordian');
INSERT INTO `paleochronostratigraphy` VALUES ('108', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Upper Jurassic', 'Kimmeridgian');
INSERT INTO `paleochronostratigraphy` VALUES ('109', 'Phanerozoic', 'Mesozoic', 'Jurassic', 'Upper Jurassic', 'Tithonian');
INSERT INTO `paleochronostratigraphy` VALUES ('110', 'Phanerozoic', 'Mesozoic', 'Cretaceous', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('111', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', null);
INSERT INTO `paleochronostratigraphy` VALUES ('112', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Berriasian');
INSERT INTO `paleochronostratigraphy` VALUES ('113', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Valanginian');
INSERT INTO `paleochronostratigraphy` VALUES ('114', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Hauterivian');
INSERT INTO `paleochronostratigraphy` VALUES ('115', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Barremian');
INSERT INTO `paleochronostratigraphy` VALUES ('116', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Aptian');
INSERT INTO `paleochronostratigraphy` VALUES ('117', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Lower Cretaceous', 'Albian');
INSERT INTO `paleochronostratigraphy` VALUES ('118', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', null);
INSERT INTO `paleochronostratigraphy` VALUES ('119', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Cenomanian');
INSERT INTO `paleochronostratigraphy` VALUES ('120', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Turonian');
INSERT INTO `paleochronostratigraphy` VALUES ('121', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Coniacian');
INSERT INTO `paleochronostratigraphy` VALUES ('122', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Santonian');
INSERT INTO `paleochronostratigraphy` VALUES ('123', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Campanian');
INSERT INTO `paleochronostratigraphy` VALUES ('124', 'Phanerozoic', 'Mesozoic', 'Cretaceous', 'Upper Cretaceous', 'Maastrichtian');
INSERT INTO `paleochronostratigraphy` VALUES ('125', 'Phanerozoic', 'Cenozoic', null, null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('126', 'Phanerozoic', 'Cenozoic', 'Paleogene', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('127', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Paleocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('128', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Paleocene', 'Danian');
INSERT INTO `paleochronostratigraphy` VALUES ('129', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Paleocene', 'Selandian');
INSERT INTO `paleochronostratigraphy` VALUES ('130', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Paleocene', 'Thanetian');
INSERT INTO `paleochronostratigraphy` VALUES ('131', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Eocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('132', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Eocene', 'Ypresian');
INSERT INTO `paleochronostratigraphy` VALUES ('133', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Eocene', 'Lutetian');
INSERT INTO `paleochronostratigraphy` VALUES ('134', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Eocene', 'Bartonian');
INSERT INTO `paleochronostratigraphy` VALUES ('135', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Eocene', 'Priabonian');
INSERT INTO `paleochronostratigraphy` VALUES ('136', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Oligocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('137', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Oligocene', 'Rupelian');
INSERT INTO `paleochronostratigraphy` VALUES ('138', 'Phanerozoic', 'Cenozoic', 'Paleogene', 'Oligocene', 'Chattian');
INSERT INTO `paleochronostratigraphy` VALUES ('139', 'Phanerozoic', 'Cenozoic', 'Neogene', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('140', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('141', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Aquitanian');
INSERT INTO `paleochronostratigraphy` VALUES ('142', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Burdigalian');
INSERT INTO `paleochronostratigraphy` VALUES ('143', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Langhian');
INSERT INTO `paleochronostratigraphy` VALUES ('144', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Serravallian');
INSERT INTO `paleochronostratigraphy` VALUES ('145', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Tortonian');
INSERT INTO `paleochronostratigraphy` VALUES ('146', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Miocene', 'Messinian');
INSERT INTO `paleochronostratigraphy` VALUES ('147', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Pliocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('148', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Pliocene', 'Zanclean');
INSERT INTO `paleochronostratigraphy` VALUES ('149', 'Phanerozoic', 'Cenozoic', 'Neogene', 'Pliocene', 'Piacenzian');
INSERT INTO `paleochronostratigraphy` VALUES ('150', 'Phanerozoic', 'Cenozoic', 'Quaternary', null, null);
INSERT INTO `paleochronostratigraphy` VALUES ('151', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Pleistocene', null);
INSERT INTO `paleochronostratigraphy` VALUES ('152', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Pleistocene', 'Gelasian');
INSERT INTO `paleochronostratigraphy` VALUES ('153', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Pleistocene', 'Calabrian');
INSERT INTO `paleochronostratigraphy` VALUES ('154', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Pleistocene', 'Middle Pleistocene');
INSERT INTO `paleochronostratigraphy` VALUES ('155', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Pleistocene', 'Upper Pleistocene');
INSERT INTO `paleochronostratigraphy` VALUES ('156', 'Phanerozoic', 'Cenozoic', 'Quaternary', 'Holocene', null);

INSERT INTO users(uid,firstname,lastname,username,password,state,country,email,validated) VALUES (1,'General','Administrator','admin',SHA2('admin', 224),'NA','NA','NA',1);
INSERT INTO userroles(uid,role) VALUES (1,'SuperAdmin');

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

INSERT INTO `taxonkingdoms` VALUES (1, 'Bacteria');
INSERT INTO `taxonkingdoms` VALUES (2, 'Protozoa');
INSERT INTO `taxonkingdoms` VALUES (3, 'Plantae');
INSERT INTO `taxonkingdoms` VALUES (4, 'Fungi');
INSERT INTO `taxonkingdoms` VALUES (5, 'Animalia');
INSERT INTO `taxonkingdoms` VALUES (6, 'Chromista');
INSERT INTO `taxonkingdoms` VALUES (7, 'Archaea');
INSERT INTO `taxonkingdoms` VALUES (100, 'Unknown');

INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (1, 0, 1, 'Organism', 'Organism', 1, 0);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (2, 1, 10, 'Bacteria', 'Bacteria', 2, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (3, 2, 10, 'Protozoa', 'Protozoa', 3, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (4, 3, 10, 'Plantae', 'Plantae', 4, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (5, 4, 10, 'Fungi', 'Fungi', 5, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (6, 5, 10, 'Animalia', 'Animalia', 6, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (7, 6, 10, 'Chromista', 'Chromista', 7, 1);
INSERT INTO `taxa` (`TID`, `kingdomId`, `RankId`, `SciName`, `UnitName1`, `tidaccepted`, `parenttid`) VALUES (8, 7, 10, 'Archaea', 'Archaea', 8, 1);

SET FOREIGN_KEY_CHECKS = 1;
