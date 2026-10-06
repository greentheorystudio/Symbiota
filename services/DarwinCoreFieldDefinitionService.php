<?php
class DarwinCoreFieldDefinitionService {

    public static function getDeterminationArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['identifiedBy'] = 'https://dwc.tdwg.org/terms/#dwc:identifiedBy';
        $fieldArr['identifiedBy'] = 'd.identifiedBy';
        $termArr['identifiedByID'] = 'https://dwc.tdwg.org/terms/#dwc:identifiedByID';
        $fieldArr['identifiedByID'] = 'd.idbyid';
        $termArr['dateIdentified'] = 'https://dwc.tdwg.org/terms/#dwc:dateIdentified';
        $fieldArr['dateIdentified'] = 'd.dateIdentified';
        $termArr['identificationQualifier'] = 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier';
        $fieldArr['identificationQualifier'] = 'd.identificationQualifier';
        $termArr['scientificName'] = 'https://dwc.tdwg.org/terms/#dwc:scientificName';
        $fieldArr['scientificName'] = 'd.sciName AS scientificName';
        $termArr['tidAccepted'] = 'tidAccepted';
        $fieldArr['tidAccepted'] = 't.tidaccepted';
        $termArr['identificationIsCurrent'] = 'identificationIsCurrent';
        $fieldArr['identificationIsCurrent'] = 'd.iscurrent';
        $termArr['scientificNameAuthorship'] = 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship';
        $fieldArr['scientificNameAuthorship'] = 'd.scientificNameAuthorship';
        $termArr['genus'] = 'https://dwc.tdwg.org/terms/#dwc:genus';
        $fieldArr['genus'] = 'CONCAT_WS(" ", t.unitind1, t.unitname1) AS genus';
        $termArr['specificEpithet'] = 'https://dwc.tdwg.org/terms/#dwc:specificEpithet';
        $fieldArr['specificEpithet'] = 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificEpithet';
        $termArr['taxonRank'] = 'https://dwc.tdwg.org/terms/#dwc:taxonRank';
        $fieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $termArr['infraspecificEpithet'] = 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet';
        $fieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $termArr['identificationReferences'] = 'https://dwc.tdwg.org/terms/#dwc:identificationReferences';
        $fieldArr['identificationReferences'] = 'd.identificationReferences';
        $termArr['identificationRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks';
        $fieldArr['identificationRemarks'] = 'd.identificationRemarks';
        $termArr['recordId'] = 'recordId';
        $fieldArr['recordId'] = 'g.guid AS recordId';
        $termArr['modified'] = 'https://dwc.tdwg.org/terms/#dcterms:modified';
        $fieldArr['modified'] = 'd.initialTimeStamp AS modified';
        $termArr['collId'] = 'collId';
        $fieldArr['collId'] = 'c.collid';
        $termArr['localitySecurity'] = 'localitySecurity';
        $fieldArr['localitySecurity'] = 'o.localitySecurity';
        $retArr['terms'] = self::trimDeterminationBySchemaType($termArr, $schemaType);
        $retArr['fields'] = self::trimDeterminationBySchemaType($fieldArr, $schemaType);
        return $retArr;
    }

    public static function getImageArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
        $fieldArr['identifier'] = 'IFNULL(i.originalurl, i.url) as identifier';
        $termArr['accessURI'] = 'http://rs.tdwg.org/ac/terms/accessURI';
        $fieldArr['accessURI'] = 'IFNULL(NULLIF(i.originalurl, ""), i.url) as accessURI';
        $termArr['thumbnailAccessURI'] = 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI';
        $fieldArr['thumbnailAccessURI'] = 'i.thumbnailurl as thumbnailAccessURI';
        $termArr['goodQualityAccessURI'] = 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI';
        $fieldArr['goodQualityAccessURI'] = 'i.url as goodQualityAccessURI';
        $termArr['rights'] = 'http://purl.org/dc/terms/rights';
        $fieldArr['rights'] = 'c.rights';
        $termArr['Owner'] = 'http://ns.adobe.com/xap/1.0/rights/Owner';
        $fieldArr['Owner'] = 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS owner';
        $termArr['creator'] = 'http://purl.org/dc/elements/1.1/creator';
        $fieldArr['creator'] = 'i.photographer AS creator';
        $termArr['UsageTerms'] = 'http://ns.adobe.com/xap/1.0/rights/UsageTerms';
        $fieldArr['UsageTerms'] = 'i.copyright AS usageterms';
        $termArr['WebStatement'] = 'http://ns.adobe.com/xap/1.0/rights/WebStatement';
        $fieldArr['WebStatement'] = 'c.accessrights AS webstatement';
        $termArr['caption'] = 'http://rs.tdwg.org/ac/terms/caption';
        $fieldArr['caption'] = 'i.caption';
        $termArr['comments'] = 'http://rs.tdwg.org/ac/terms/comments';
        $fieldArr['comments'] = 'i.notes';
        $termArr['providerManagedID'] = 'http://rs.tdwg.org/ac/terms/providerManagedID';
        $fieldArr['providerManagedID'] = 'i.imgid AS providermanagedid';
        $termArr['MetadataDate'] = 'http://ns.adobe.com/xap/1.0/MetadataDate';
        $fieldArr['MetadataDate'] = 'i.initialtimestamp AS metadatadate';
        $termArr['format'] = 'http://purl.org/dc/terms/format';
        $fieldArr['format'] = 'i.format';
        $termArr['associatedSpecimenReference'] = 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference';
        $fieldArr['associatedSpecimenReference'] = '"" AS associatedSpecimenReference';
        $termArr['type'] = 'http://purl.org/dc/terms/type';
        $fieldArr['type'] = '"" AS type';
        $termArr['metadataLanguage'] = 'http://rs.tdwg.org/ac/terms/metadataLanguage';
        $fieldArr['metadataLanguage'] = '"" AS metadataLanguage';
        $termArr['collId'] = 'collId';
        $fieldArr['collId'] = 'c.collid';
        $termArr['localitySecurity'] = 'localitySecurity';
        $fieldArr['localitySecurity'] = 'o.localitySecurity';
        if($schemaType === 'backup') {
            $fieldArr['rights'] = 'i.copyright';
        }
        $retArr['terms'] = self::trimMediaBySchemaType($termArr, $schemaType);
        $retArr['fields'] = self::trimMediaBySchemaType($fieldArr, $schemaType);
        return $retArr;
    }

    public static function getMeasurementOrFactArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['eventID'] = 'eventID';
        $fieldArr['eventID'] = 'o.eventID';
        $termArr['measurementType'] = 'https://dwc.tdwg.org/terms/#dwc:measurementType';
        $fieldArr['measurementType'] = 'm.field AS measurementType';
        $termArr['measurementValue'] = 'https://dwc.tdwg.org/terms/#dwc:measurementValue';
        $fieldArr['measurementValue'] = 'm.datavalue AS measurementValue';
        $termArr['measurementUnit'] = 'https://dwc.tdwg.org/terms/#dwc:measurementUnit';
        $fieldArr['measurementUnit'] = '"" AS measurementUnit';
        $termArr['measurementAccuracy'] = 'https://dwc.tdwg.org/terms/#dwc:measurementAccuracy';
        $fieldArr['measurementAccuracy'] = '"" AS measurementAccuracy';
        $termArr['measurementMethod'] = 'https://dwc.tdwg.org/terms/#dwc:measurementMethod';
        $fieldArr['measurementMethod'] = '"" AS measurementMethod';
        $termArr['measurementRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:measurementRemarks';
        $fieldArr['measurementRemarks'] = '"" AS measurementRemarks';
        $termArr['measurementDeterminedDate'] = 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedDate';
        $fieldArr['measurementDeterminedDate'] = 'DATE_FORMAT(m.initialtimestamp, "%Y-%m-%dT%TZ") AS measurementDeterminedDate';
        $termArr['measurementDeterminedBy'] = 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedBy';
        $fieldArr['measurementDeterminedBy'] = 'm.enteredBy AS measurementDeterminedBy';
        $termArr['collId'] = 'collId';
        $fieldArr['collId'] = 'c.collid';
        $termArr['localitySecurity'] = 'localitySecurity';
        $fieldArr['localitySecurity'] = 'o.localitySecurity';
        $retArr['terms'] = self::trimMediaBySchemaType($termArr, $schemaType);
        $retArr['fields'] = self::trimMediaBySchemaType($fieldArr, $schemaType);
        return $retArr;
    }

    public static function getMediaArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
        $fieldArr['identifier'] = 'm.accessuri as identifier';
        $termArr['accessURI'] = 'http://rs.tdwg.org/ac/terms/accessURI';
        $fieldArr['accessURI'] = 'm.accessuri as accessURI';
        $termArr['thumbnailAccessURI'] = 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI';
        $fieldArr['thumbnailAccessURI'] = '"" as thumbnailAccessURI';
        $termArr['goodQualityAccessURI'] = 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI';
        $fieldArr['goodQualityAccessURI'] = 'm.accessuri as goodQualityAccessURI';
        $termArr['rights'] = 'http://purl.org/dc/terms/rights';
        $fieldArr['rights'] = 'c.rights';
        $termArr['Owner'] = 'http://ns.adobe.com/xap/1.0/rights/Owner';
        $fieldArr['Owner'] = 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS owner';
        $termArr['creator'] = 'http://purl.org/dc/elements/1.1/creator';
        $fieldArr['creator'] = 'm.creator';
        $termArr['UsageTerms'] = 'http://ns.adobe.com/xap/1.0/rights/UsageTerms';
        $fieldArr['UsageTerms'] = 'm.usageterms AS usageterms';
        $termArr['WebStatement'] = 'http://ns.adobe.com/xap/1.0/rights/WebStatement';
        $fieldArr['WebStatement'] = 'c.accessrights AS webstatement';
        $termArr['caption'] = 'http://rs.tdwg.org/ac/terms/caption';
        $fieldArr['caption'] = 'm.title AS caption';
        $termArr['comments'] = 'http://rs.tdwg.org/ac/terms/comments';
        $fieldArr['comments'] = 'm.description AS notes';
        $termArr['providerManagedID'] = 'http://rs.tdwg.org/ac/terms/providerManagedID';
        $fieldArr['providerManagedID'] = 'm.mediaid AS providermanagedid';
        $termArr['MetadataDate'] = 'http://ns.adobe.com/xap/1.0/MetadataDate';
        $fieldArr['MetadataDate'] = 'm.initialtimestamp AS metadatadate';
        $termArr['format'] = 'http://purl.org/dc/terms/format';
        $fieldArr['format'] = 'm.format';
        $termArr['associatedSpecimenReference'] = 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference';
        $fieldArr['associatedSpecimenReference'] = '"" as associatedSpecimenReference';
        $termArr['type'] = 'http://purl.org/dc/terms/type';
        $fieldArr['type'] = 'm.type';
        $termArr['metadataLanguage'] = 'http://rs.tdwg.org/ac/terms/metadataLanguage';
        $fieldArr['metadataLanguage'] = '"" as metadataLanguage';
        $termArr['collId'] = 'collId';
        $fieldArr['collId'] = 'c.collid';
        $termArr['localitySecurity'] = 'localitySecurity';
        $fieldArr['localitySecurity'] = 'o.localitySecurity';
        $retArr['terms'] = self::trimMediaBySchemaType($termArr, $schemaType);
        $retArr['fields'] = self::trimMediaBySchemaType($fieldArr, $schemaType);
        return $retArr;
    }

    public static function getOccurrenceArr($schemaType): array
    {
        $occurFieldArr['id'] = 'o.occid';
        $occurTermArr['institutionCode'] = 'https://dwc.tdwg.org/terms/#dwc:institutionCode';
        $occurFieldArr['institutionCode'] = 'IFNULL(o.institutionCode, c.institutionCode) AS institutionCode';
        $occurTermArr['collectionCode'] = 'https://dwc.tdwg.org/terms/#dwc:collectionCode';
        $occurFieldArr['collectionCode'] = 'IFNULL(o.collectionCode, c.collectionCode) AS collectionCode';
        $occurTermArr['collectionID'] = 'https://dwc.tdwg.org/terms/#dwc:collectionID';
        $occurFieldArr['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
        $occurTermArr['ownerInstitutionCode'] = 'https://dwc.tdwg.org/terms/#dwc:ownerInstitutionCode';
        $occurFieldArr['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
        $occurTermArr['institutionID'] = 'https://dwc.tdwg.org/terms/#dwc:institutionID';
        $occurFieldArr['institutionID'] = 'o.institutionID';
        $occurTermArr['datasetID'] = 'https://dwc.tdwg.org/terms/#dwc:datasetID';
        $occurFieldArr['datasetID'] = 'o.datasetID';
        $occurTermArr['basisOfRecord'] = 'https://dwc.tdwg.org/terms/#dwc:basisOfRecord';
        $occurFieldArr['basisOfRecord'] = 'o.basisOfRecord';
        $occurTermArr['occurrenceID'] = 'https://dwc.tdwg.org/terms/#dwc:occurrenceID';
        $occurFieldArr['occurrenceID'] = 'o.occurrenceID';
        $occurTermArr['catalogNumber'] = 'https://dwc.tdwg.org/terms/#dwc:catalogNumber';
        $occurFieldArr['catalogNumber'] = 'o.catalogNumber';
        $occurTermArr['otherCatalogNumbers'] = 'https://dwc.tdwg.org/terms/#dwc:otherCatalogNumbers';
        $occurFieldArr['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
        $occurTermArr['kingdom'] = 'https://dwc.tdwg.org/terms/#dwc:kingdom';
        $occurFieldArr['kingdom'] = '';
        $occurTermArr['phylum'] = 'https://dwc.tdwg.org/terms/#dwc:phylum';
        $occurFieldArr['phylum'] = '';
        $occurTermArr['class'] = 'https://dwc.tdwg.org/terms/#dwc:class';
        $occurFieldArr['class'] = '';
        $occurTermArr['order'] = 'https://dwc.tdwg.org/terms/#dwc:order';
        $occurFieldArr['order'] = '';
        $occurTermArr['family'] = 'https://dwc.tdwg.org/terms/#dwc:family';
        $occurFieldArr['family'] = 'o.family';
        $occurTermArr['scientificName'] = 'https://dwc.tdwg.org/terms/#dwc:scientificName';
        $occurFieldArr['scientificName'] = 'o.sciname AS scientificName';
        $occurTermArr['taxonID'] = 'https://dwc.tdwg.org/terms/#dwc:taxonID';
        $occurFieldArr['taxonID'] = 't.tidaccepted as taxonID';
        $occurTermArr['scientificNameAuthorship'] = 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship';
        $occurFieldArr['scientificNameAuthorship'] = 'IFNULL(t.author, o.scientificNameAuthorship) AS scientificNameAuthorship';
        $occurTermArr['genus'] = 'https://dwc.tdwg.org/terms/#dwc:genus';
        $occurFieldArr['genus'] = 'IF(t.rankid >= 180, CONCAT_WS(" ", t.unitind1, t.unitname1), NULL) AS genus';
        $occurTermArr['specificEpithet'] = 'https://dwc.tdwg.org/terms/#dwc:specificEpithet';
        $occurFieldArr['specificEpithet'] = 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificEpithet';
        $occurTermArr['infraspecificEpithet'] = 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet';
        $occurFieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $occurTermArr['taxonRank'] = 'https://dwc.tdwg.org/terms/#dwc:taxonRank';
        $occurFieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $occurTermArr['identifiedBy'] = 'https://dwc.tdwg.org/terms/#dwc:identifiedBy';
        $occurFieldArr['identifiedBy'] = 'o.identifiedBy';
        $occurTermArr['dateIdentified'] = 'https://dwc.tdwg.org/terms/#dwc:dateIdentified';
        $occurFieldArr['dateIdentified'] = 'o.dateIdentified';
        $occurTermArr['identificationReferences'] = 'https://dwc.tdwg.org/terms/#dwc:identificationReferences';
        $occurFieldArr['identificationReferences'] = 'o.identificationReferences';
        $occurTermArr['identificationRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks';
        $occurFieldArr['identificationRemarks'] = 'o.identificationRemarks';
        $occurTermArr['taxonRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:taxonRemarks';
        $occurFieldArr['taxonRemarks'] = 'o.taxonRemarks';
        $occurTermArr['identificationQualifier'] = 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier';
        $occurFieldArr['identificationQualifier'] = 'o.identificationQualifier';
        $occurTermArr['typeStatus'] = 'https://dwc.tdwg.org/terms/#dwc:typeStatus';
        $occurFieldArr['typeStatus'] = 'o.typeStatus';
        $occurTermArr['recordedBy'] = 'https://dwc.tdwg.org/terms/#dwc:recordedBy';
        $occurFieldArr['recordedBy'] = 'o.recordedBy';
        $occurTermArr['recordNumber'] = 'https://dwc.tdwg.org/terms/#dwc:recordNumber';
        $occurFieldArr['recordNumber'] = 'o.recordNumber';
        $occurTermArr['eventDate'] = 'https://dwc.tdwg.org/terms/#dwc:eventDate';
        $occurFieldArr['eventDate'] = 'o.eventDate';
        $occurTermArr['year'] = 'https://dwc.tdwg.org/terms/#dwc:year';
        $occurFieldArr['year'] = 'o.`year`';
        $occurTermArr['month'] = 'https://dwc.tdwg.org/terms/#dwc:month';
        $occurFieldArr['month'] = 'o.`month`';
        $occurTermArr['day'] = 'https://dwc.tdwg.org/terms/#dwc:day';
        $occurFieldArr['day'] = 'o.`day`';
        $occurTermArr['startDayOfYear'] = 'https://dwc.tdwg.org/terms/#dwc:startDayOfYear';
        $occurFieldArr['startDayOfYear'] = 'o.startDayOfYear';
        $occurTermArr['endDayOfYear'] = 'https://dwc.tdwg.org/terms/#dwc:endDayOfYear';
        $occurFieldArr['endDayOfYear'] = 'o.endDayOfYear';
        $occurTermArr['verbatimEventDate'] = 'https://dwc.tdwg.org/terms/#dwc:verbatimEventDate';
        $occurFieldArr['verbatimEventDate'] = 'o.verbatimEventDate';
        $occurTermArr['occurrenceRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:occurrenceRemarks';
        $occurFieldArr['occurrenceRemarks'] = 'o.occurrenceRemarks';
        $occurTermArr['habitat'] = 'https://dwc.tdwg.org/terms/#dwc:habitat';
        $occurFieldArr['habitat'] = 'o.habitat';
        $occurTermArr['fieldNumber'] = 'https://dwc.tdwg.org/terms/#dwc:fieldNumber';
        $occurFieldArr['fieldNumber'] = 'o.fieldNumber';
        $occurTermArr['fieldNotes'] = 'https://dwc.tdwg.org/terms/#dwc:fieldNotes';
        $occurFieldArr['fieldNotes'] = 'o.fieldNotes';
        $occurTermArr['samplingProtocol'] = 'https://dwc.tdwg.org/terms/#dwc:samplingProtocol';
        $occurFieldArr['samplingProtocol'] = 'o.samplingProtocol';
        $occurTermArr['samplingEffort'] = 'https://dwc.tdwg.org/terms/#dwc:samplingEffort';
        $occurFieldArr['samplingEffort'] = 'o.samplingEffort';
        $occurTermArr['eventID'] = 'https://dwc.tdwg.org/terms/#dwc:eventID';
        $occurFieldArr['eventID'] = 'o.eventID';
        $occurTermArr['informationWithheld'] = 'https://dwc.tdwg.org/terms/#dwc:informationWithheld';
        $occurFieldArr['informationWithheld'] = 'o.informationWithheld';
        $occurTermArr['dataGeneralizations'] = 'https://dwc.tdwg.org/terms/#dwc:dataGeneralizations';
        $occurFieldArr['dataGeneralizations'] = 'o.dataGeneralizations';
        $occurTermArr['dynamicProperties'] = 'https://dwc.tdwg.org/terms/#dwc:dynamicProperties';
        $occurFieldArr['dynamicProperties'] = 'o.dynamicProperties';
        $occurTermArr['associatedOccurrences'] = 'https://dwc.tdwg.org/terms/#dwc:associatedOccurrences';
        $occurFieldArr['associatedOccurrences'] = 'o.associatedOccurrences';
        $occurTermArr['associatedTaxa'] = 'https://dwc.tdwg.org/terms/#dwc:associatedTaxa';
        $occurFieldArr['associatedTaxa'] = 'o.associatedTaxa';
        $occurTermArr['reproductiveCondition'] = 'https://dwc.tdwg.org/terms/#dwc:reproductiveCondition';
        $occurFieldArr['reproductiveCondition'] = 'o.reproductiveCondition';
        $occurTermArr['establishmentMeans'] = 'https://dwc.tdwg.org/terms/#dwc:establishmentMeans';
        $occurFieldArr['establishmentMeans'] = 'o.establishmentMeans';
        $occurTermArr['lifeStage'] = 'https://dwc.tdwg.org/terms/#dwc:lifeStage';
        $occurFieldArr['lifeStage'] = 'o.lifeStage';
        $occurTermArr['sex'] = 'https://dwc.tdwg.org/terms/#dwc:sex';
        $occurFieldArr['sex'] = 'o.sex';
        $occurTermArr['behavior'] = 'https://dwc.tdwg.org/terms/#dwc:behavior';
        $occurFieldArr['behavior'] = 'o.behavior';
        $occurTermArr['individualCount'] = 'https://dwc.tdwg.org/terms/#dwc:individualCount';
        $occurFieldArr['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
        $occurTermArr['preparations'] = 'https://dwc.tdwg.org/terms/#dwc:preparations';
        $occurFieldArr['preparations'] = 'o.preparations';
        $occurTermArr['locationID'] = 'https://dwc.tdwg.org/terms/#dwc:locationID';
        $occurFieldArr['locationID'] = 'o.locationID';
        $occurTermArr['waterBody'] = 'https://dwc.tdwg.org/terms/#dwc:waterBody';
        $occurFieldArr['waterBody'] = 'o.waterBody';
        $occurTermArr['country'] = 'https://dwc.tdwg.org/terms/#dwc:country';
        $occurFieldArr['country'] = 'o.country';
        $occurTermArr['stateProvince'] = 'https://dwc.tdwg.org/terms/#dwc:stateProvince';
        $occurFieldArr['stateProvince'] = 'o.stateProvince';
        $occurTermArr['county'] = 'https://dwc.tdwg.org/terms/#dwc:county';
        $occurFieldArr['county'] = 'o.county';
        $occurTermArr['municipality'] = 'https://dwc.tdwg.org/terms/#dwc:municipality';
        $occurFieldArr['municipality'] = 'o.municipality';
        $occurTermArr['locality'] = 'https://dwc.tdwg.org/terms/#dwc:locality';
        $occurFieldArr['locality'] = 'o.locality';
        $occurTermArr['locationRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:locationRemarks';
        $occurFieldArr['locationRemarks'] = 'o.locationremarks';
        $occurTermArr['decimalLatitude'] = 'https://dwc.tdwg.org/terms/#dwc:decimalLatitude';
        $occurFieldArr['decimalLatitude'] = 'o.decimalLatitude';
        $occurTermArr['decimalLongitude'] = 'https://dwc.tdwg.org/terms/#dwc:decimalLongitude';
        $occurFieldArr['decimalLongitude'] = 'o.decimalLongitude';
        $occurTermArr['geodeticDatum'] = 'https://dwc.tdwg.org/terms/#dwc:geodeticDatum';
        $occurFieldArr['geodeticDatum'] = 'o.geodeticDatum';
        $occurTermArr['coordinateUncertaintyInMeters'] = 'https://dwc.tdwg.org/terms/#dwc:coordinateUncertaintyInMeters';
        $occurFieldArr['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
        $occurTermArr['coordinatePrecision'] = 'https://dwc.tdwg.org/terms/#dwc:coordinatePrecision';
        $occurFieldArr['coordinatePrecision'] = 'o.coordinatePrecision';
        $occurTermArr['verbatimCoordinateSystem'] = 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinateSystem';
        $occurFieldArr['verbatimCoordinateSystem'] = 'o.verbatimCoordinateSystem';
        $occurTermArr['verbatimCoordinates'] = 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinates';
        $occurFieldArr['verbatimCoordinates'] = 'o.verbatimCoordinates';
        $occurTermArr['georeferencedBy'] = 'https://dwc.tdwg.org/terms/#dwc:georeferencedBy';
        $occurFieldArr['georeferencedBy'] = 'o.georeferencedBy';
        $occurTermArr['georeferenceProtocol'] = 'https://dwc.tdwg.org/terms/#dwc:georeferenceProtocol';
        $occurFieldArr['georeferenceProtocol'] = 'o.georeferenceProtocol';
        $occurTermArr['georeferenceSources'] = 'https://dwc.tdwg.org/terms/#dwc:georeferenceSources';
        $occurFieldArr['georeferenceSources'] = 'o.georeferenceSources';
        $occurTermArr['georeferenceVerificationStatus'] = 'https://dwc.tdwg.org/terms/#dwc:georeferenceVerificationStatus';
        $occurFieldArr['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
        $occurTermArr['georeferenceRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:georeferenceRemarks';
        $occurFieldArr['georeferenceRemarks'] = 'o.georeferenceRemarks';
        $occurTermArr['minimumElevationInMeters'] = 'https://dwc.tdwg.org/terms/#dwc:minimumElevationInMeters';
        $occurFieldArr['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
        $occurTermArr['maximumElevationInMeters'] = 'https://dwc.tdwg.org/terms/#dwc:maximumElevationInMeters';
        $occurFieldArr['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
        $occurTermArr['minimumDepthInMeters'] = 'https://dwc.tdwg.org/terms/#dwc:minimumDepthInMeters';
        $occurFieldArr['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
        $occurTermArr['maximumDepthInMeters'] = 'https://dwc.tdwg.org/terms/#dwc:maximumDepthInMeters';
        $occurFieldArr['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
        $occurTermArr['verbatimDepth'] = 'https://dwc.tdwg.org/terms/#dwc:verbatimDepth';
        $occurFieldArr['verbatimDepth'] = 'o.verbatimDepth';
        $occurTermArr['verbatimElevation'] = 'https://dwc.tdwg.org/terms/#dwc:verbatimElevation';
        $occurFieldArr['verbatimElevation'] = 'o.verbatimElevation';
        $occurTermArr['disposition'] = 'https://dwc.tdwg.org/terms/#dwc:disposition';
        $occurFieldArr['disposition'] = 'o.disposition';
        $occurTermArr['language'] = 'https://dwc.tdwg.org/terms/#dc:language';
        $occurFieldArr['language'] = 'o.`language`';
        $occurTermArr['modified'] = 'https://dwc.tdwg.org/terms/#dcterms:modified';
        $occurFieldArr['modified'] = 'IFNULL(o.modified, o.datelastmodified) AS modified';
        $occurTermArr['license'] = 'https://dwc.tdwg.org/terms/#dcterms:license';
        $occurFieldArr['license'] = 'c.rights';
        $occurTermArr['rightsHolder'] = 'https://dwc.tdwg.org/terms/#dcterms:rightsHolder';
        $occurFieldArr['rightsHolder'] = 'c.rightsHolder';
        $occurTermArr['accessRights'] = 'https://dwc.tdwg.org/terms/#dcterms:accessRights';
        $occurFieldArr['accessRights'] = 'c.accessRights';
        $occurTermArr['references'] = 'https://dwc.tdwg.org/terms/#dcterms:references';
        $occurFieldArr['references'] = '';
        $occurTermArr['recordId'] = 'https://symbiota.org/terms/recordID';
        $occurFieldArr['recordId'] = 'g.guid AS recordId';
        $occurTermArr['collId'] = 'collId';
        $occurFieldArr['collId'] = 'c.collid';
        $occurTermArr['sourcePrimaryKey-dbpk'] = 'dbpk';
        $occurFieldArr['sourcePrimaryKey-dbpk'] = 'o.dbpk';
        $occurTermArr['recordedByID'] = 'recordedByID';
        $occurFieldArr['recordedByID'] = 'o.recordedById';
        $occurTermArr['associatedCollectors'] = 'associatedCollectors';
        $occurFieldArr['associatedCollectors'] = 'o.associatedCollectors';
        $occurTermArr['substrate'] = 'substrate';
        $occurFieldArr['substrate'] = 'o.substrate';
        $occurTermArr['verbatimAttributes'] = 'verbatimAttributes';
        $occurFieldArr['verbatimAttributes'] = 'o.verbatimAttributes';
        $occurTermArr['cultivationStatus'] = 'cultivationStatus';
        $occurFieldArr['cultivationStatus'] = 'o.cultivationStatus';
        $occurTermArr['localitySecurity'] = 'localitySecurity';
        $occurFieldArr['localitySecurity'] = 'o.localitySecurity';
        $occurTermArr['localitySecurityReason'] = 'localitySecurityReason';
        $occurFieldArr['localitySecurityReason'] = 'o.localitySecurityReason';
        $occurTermArr['footprintWKT'] = 'https://dwc.tdwg.org/terms/#dwc:footprintWKT';
        $occurFieldArr['footprintWKT'] = 'o.footprintWKT';
        $occurTermArr['storageLocation'] = 'storageLocation';
        $occurFieldArr['storageLocation'] = 'o.storageLocation';
        $occurTermArr['processingStatus'] = 'processingStatus';
        $occurFieldArr['processingStatus'] = 'o.processingstatus';
        $occurTermArr['recordEnteredBy'] = 'recordEnteredBy';
        $occurFieldArr['recordEnteredBy'] = 'o.recordEnteredBy';
        $occurTermArr['duplicateQuantity'] = 'duplicateQuantity';
        $occurFieldArr['duplicateQuantity'] = 'o.duplicateQuantity';
        $occurTermArr['labelProject'] = 'labelProject';
        $occurFieldArr['labelProject'] = 'o.labelProject';
        $occurTermArr['dynamicFields'] = 'dynamicFields';
        $occurFieldArr['dynamicFields'] = 'o.dynamicFields';
        $occurTermArr['dateEntered'] = 'dateEntered';
        $occurFieldArr['dateEntered'] = 'o.dateEntered';
        $occurTermArr['dateLastModified'] = 'https://dwc.tdwg.org/list/#dwc_dateLastModified';
        $occurFieldArr['dateLastModified'] = 'o.datelastmodified';
        $occurrenceFieldArr['terms'] = self::trimOccurrenceBySchemaType($occurTermArr, $schemaType);
        $occurFieldArr = self::trimOccurrenceBySchemaType($occurFieldArr, $schemaType);
        if($schemaType === 'dwc'){
            $occurFieldArr['recordedBy'] = 'CONCAT_WS("; ", o.recordedBy, o.associatedCollectors) AS recordedBy';
            $occurFieldArr['occurrenceRemarks'] = 'CONCAT_WS("; ", o.occurrenceRemarks, o.verbatimAttributes) AS occurrenceRemarks';
            $occurFieldArr['habitat'] = 'CONCAT_WS("; ", o.habitat, o.substrate) AS habitat';
        }
        $occurrenceFieldArr['fields'] = $occurFieldArr;
        return $occurrenceFieldArr;
    }

    public static function trimDeterminationBySchemaType($detArr, $schemaType): array
    {
        $trimArr = array();
        if($schemaType === 'dwc'){
            $trimArr[] = 'identifiedByID';
            $trimArr[] = 'tidAccepted';
            $trimArr[] = 'identificationIsCurrent';
        }
        elseif($schemaType === 'native'){
            $trimArr[] = 'identifiedByID';
            $trimArr[] = 'tidAccepted';
        }
        return array_diff_key($detArr, array_flip($trimArr));
    }

    public static function trimMediaBySchemaType($imageArr, $schemaType): array
    {
        $trimArr = array();
        if($schemaType === 'backup'){
            $trimArr = array('Owner', 'UsageTerms', 'WebStatement');
        }
        return array_diff_key($imageArr, array_flip($trimArr));
    }

    public static function trimOccurrenceBySchemaType($occurArr, $schemaType): array
    {
        $retArr = array();
        if($schemaType === 'dwc'){
            $trimArr = array('sourcePrimaryKey-dbpk', 'recordedByID', 'associatedCollectors', 'associatedCollectors', 'substrate',
                'verbatimAttributes', 'cultivationStatus', 'localitySecurityReason', 'storageLocation',
                'processingStatus', 'recordEnteredBy', 'duplicateQuantity', 'labelProject', 'dynamicFields', 'dateEntered', 'dateLastModified');
            $retArr = array_diff_key($occurArr, array_flip($trimArr));
        }
        elseif($schemaType === 'native'){
            $trimArr = array();
            $retArr = array_diff_key($occurArr, array_flip($trimArr));
        }
        elseif($schemaType === 'backup'){
            $trimArr = array();
            $retArr = array_diff_key($occurArr, array_flip($trimArr));
        }
        return $retArr;
    }
}
