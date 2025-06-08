<?php
class DarwinCoreFieldDefinitionService {

    public static function getDeterminationArr($schemaType): array
    {
        $fieldArr['coreid'] = 'o.occid';
        $termArr['identifiedBy'] = 'http://rs.tdwg.org/dwc/terms/identifiedBy';
        $fieldArr['identifiedBy'] = 'd.identifiedBy';
        $termArr['identifiedByID'] = 'identifiedByID';
        $fieldArr['identifiedByID'] = 'd.idbyid';
        $termArr['dateIdentified'] = 'http://rs.tdwg.org/dwc/terms/dateIdentified';
        $fieldArr['dateIdentified'] = 'd.dateIdentified';
        $termArr['identificationQualifier'] = 'http://rs.tdwg.org/dwc/terms/identificationQualifier';
        $fieldArr['identificationQualifier'] = 'd.identificationQualifier';
        $termArr['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
        $fieldArr['scientificName'] = 'd.sciName AS scientificName';
        $termArr['tidAccepted'] = 'tidAccepted';
        $fieldArr['tidAccepted'] = 't.tidaccepted';
        $termArr['identificationIsCurrent'] = 'identificationIsCurrent';
        $fieldArr['identificationIsCurrent'] = 'd.iscurrent';
        $termArr['scientificNameAuthorship'] = 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship';
        $fieldArr['scientificNameAuthorship'] = 'd.scientificNameAuthorship';
        $termArr['genus'] = 'http://rs.tdwg.org/dwc/terms/genus';
        $fieldArr['genus'] = 'CONCAT_WS(" ", t.unitind1, t.unitname1) AS genus';
        $termArr['specificEpithet'] = 'http://rs.tdwg.org/dwc/terms/specificEpithet';
        $fieldArr['specificEpithet'] = 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificEpithet';
        $termArr['taxonRank'] = 'http://rs.tdwg.org/dwc/terms/taxonRank';
        $fieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $termArr['infraspecificEpithet'] = 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet';
        $fieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $termArr['identificationReferences'] = 'http://rs.tdwg.org/dwc/terms/identificationReferences';
        $fieldArr['identificationReferences'] = 'd.identificationReferences';
        $termArr['identificationRemarks'] = 'http://rs.tdwg.org/dwc/terms/identificationRemarks';
        $fieldArr['identificationRemarks'] = 'd.identificationRemarks';
        $termArr['recordId'] = 'http://portal.idigbio.org/terms/recordID';
        $fieldArr['recordId'] = 'g.guid AS recordId';
        $termArr['modified'] = 'http://purl.org/dc/terms/modified';
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
        $termArr['eventID'] = 'http://rs.tdwg.org/dwc/terms/eventID';
        $fieldArr['eventID'] = 'o.eventID';
        $termArr['measurementType'] = 'http://rs.tdwg.org/dwc/terms/measurementType';
        $fieldArr['measurementType'] = 'm.field AS measurementType';
        $termArr['measurementValue'] = 'http://rs.tdwg.org/dwc/terms/measurementValue';
        $fieldArr['measurementValue'] = 'm.datavalue AS measurementValue';
        $termArr['measurementUnit'] = 'http://rs.tdwg.org/dwc/terms/measurementUnit';
        $fieldArr['measurementUnit'] = '"" AS measurementUnit';
        $termArr['measurementAccuracy'] = 'http://rs.tdwg.org/dwc/terms/measurementAccuracy';
        $fieldArr['measurementAccuracy'] = '"" AS measurementAccuracy';
        $termArr['measurementMethod'] = 'http://rs.tdwg.org/dwc/terms/measurementMethod';
        $fieldArr['measurementMethod'] = '"" AS measurementMethod';
        $termArr['measurementRemarks'] = 'http://rs.tdwg.org/dwc/terms/measurementRemarks';
        $fieldArr['measurementRemarks'] = '"" AS measurementRemarks';
        $termArr['measurementDeterminedDate'] = 'http://rs.tdwg.org/dwc/terms/measurementDeterminedDate';
        $fieldArr['measurementDeterminedDate'] = 'DATE_FORMAT(m.initialtimestamp, "%Y-%m-%dT%TZ") AS measurementDeterminedDate';
        $termArr['measurementDeterminedBy'] = 'http://rs.tdwg.org/dwc/terms/measurementDeterminedBy';
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
        $occurTermArr['institutionCode'] = 'http://rs.tdwg.org/dwc/terms/institutionCode';
        $occurFieldArr['institutionCode'] = 'IFNULL(o.institutionCode, c.institutionCode) AS institutionCode';
        $occurTermArr['collectionCode'] = 'http://rs.tdwg.org/dwc/terms/collectionCode';
        $occurFieldArr['collectionCode'] = 'IFNULL(o.collectionCode, c.collectionCode) AS collectionCode';
        $occurTermArr['collectionID'] = 'http://rs.tdwg.org/dwc/terms/collectionID';
        $occurFieldArr['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
        $occurTermArr['ownerInstitutionCode'] = 'http://rs.tdwg.org/dwc/terms/ownerInstitutionCode';
        $occurFieldArr['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
        $occurTermArr['institutionID'] = 'http://rs.tdwg.org/dwc/terms/institutionID';
        $occurFieldArr['institutionID'] = 'o.institutionID';
        $occurTermArr['datasetID'] = 'http://rs.tdwg.org/dwc/terms/datasetID';
        $occurFieldArr['datasetID'] = 'o.datasetID';
        $occurTermArr['basisOfRecord'] = 'http://rs.tdwg.org/dwc/terms/basisOfRecord';
        $occurFieldArr['basisOfRecord'] = 'o.basisOfRecord';
        $occurTermArr['occurrenceID'] = 'http://rs.tdwg.org/dwc/terms/occurrenceID';
        $occurFieldArr['occurrenceID'] = 'o.occurrenceID';
        $occurTermArr['catalogNumber'] = 'http://rs.tdwg.org/dwc/terms/catalogNumber';
        $occurFieldArr['catalogNumber'] = 'o.catalogNumber';
        $occurTermArr['otherCatalogNumbers'] = 'http://rs.tdwg.org/dwc/terms/otherCatalogNumbers';
        $occurFieldArr['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
        $occurTermArr['kingdom'] = 'http://rs.tdwg.org/dwc/terms/kingdom';
        $occurFieldArr['kingdom'] = '';
        $occurTermArr['phylum'] = 'http://rs.tdwg.org/dwc/terms/phylum';
        $occurFieldArr['phylum'] = '';
        $occurTermArr['class'] = 'http://rs.tdwg.org/dwc/terms/class';
        $occurFieldArr['class'] = '';
        $occurTermArr['order'] = 'http://rs.tdwg.org/dwc/terms/order';
        $occurFieldArr['order'] = '';
        $occurTermArr['family'] = 'http://rs.tdwg.org/dwc/terms/family';
        $occurFieldArr['family'] = 'o.family';
        $occurTermArr['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
        $occurFieldArr['scientificName'] = 'o.sciname AS scientificName';
        $occurTermArr['taxonID'] = 'http://rs.tdwg.org/dwc/terms/taxonID';
        $occurFieldArr['taxonID'] = 't.tidaccepted as taxonID';
        $occurTermArr['scientificNameAuthorship'] = 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship';
        $occurFieldArr['scientificNameAuthorship'] = 'IFNULL(t.author, o.scientificNameAuthorship) AS scientificNameAuthorship';
        $occurTermArr['genus'] = 'http://rs.tdwg.org/dwc/terms/genus';
        $occurFieldArr['genus'] = 'IF(t.rankid >= 180, CONCAT_WS(" ", t.unitind1, t.unitname1), NULL) AS genus';
        $occurTermArr['specificEpithet'] = 'http://rs.tdwg.org/dwc/terms/specificEpithet';
        $occurFieldArr['specificEpithet'] = 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificEpithet';
        $occurTermArr['infraspecificEpithet'] = 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet';
        $occurFieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $occurTermArr['taxonRank'] = 'http://rs.tdwg.org/dwc/terms/taxonRank';
        $occurFieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $occurTermArr['identifiedBy'] = 'http://rs.tdwg.org/dwc/terms/identifiedBy';
        $occurFieldArr['identifiedBy'] = 'o.identifiedBy';
        $occurTermArr['dateIdentified'] = 'http://rs.tdwg.org/dwc/terms/dateIdentified';
        $occurFieldArr['dateIdentified'] = 'o.dateIdentified';
        $occurTermArr['identificationReferences'] = 'http://rs.tdwg.org/dwc/terms/identificationReferences';
        $occurFieldArr['identificationReferences'] = 'o.identificationReferences';
        $occurTermArr['identificationRemarks'] = 'http://rs.tdwg.org/dwc/terms/identificationRemarks';
        $occurFieldArr['identificationRemarks'] = 'o.identificationRemarks';
        $occurTermArr['taxonRemarks'] = 'http://rs.tdwg.org/dwc/terms/taxonRemarks';
        $occurFieldArr['taxonRemarks'] = 'o.taxonRemarks';
        $occurTermArr['identificationQualifier'] = 'http://rs.tdwg.org/dwc/terms/identificationQualifier';
        $occurFieldArr['identificationQualifier'] = 'o.identificationQualifier';
        $occurTermArr['typeStatus'] = 'http://rs.tdwg.org/dwc/terms/typeStatus';
        $occurFieldArr['typeStatus'] = 'o.typeStatus';
        $occurTermArr['recordedBy'] = 'http://rs.tdwg.org/dwc/terms/recordedBy';
        $occurFieldArr['recordedBy'] = 'o.recordedBy';
        $occurTermArr['recordNumber'] = 'http://rs.tdwg.org/dwc/terms/recordNumber';
        $occurFieldArr['recordNumber'] = 'o.recordNumber';
        $occurTermArr['eventDate'] = 'http://rs.tdwg.org/dwc/terms/eventDate';
        $occurFieldArr['eventDate'] = 'o.eventDate';
        $occurTermArr['year'] = 'http://rs.tdwg.org/dwc/terms/year';
        $occurFieldArr['year'] = 'o.`year`';
        $occurTermArr['month'] = 'http://rs.tdwg.org/dwc/terms/month';
        $occurFieldArr['month'] = 'o.`month`';
        $occurTermArr['day'] = 'http://rs.tdwg.org/dwc/terms/day';
        $occurFieldArr['day'] = 'o.`day`';
        $occurTermArr['startDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/startDayOfYear';
        $occurFieldArr['startDayOfYear'] = 'o.startDayOfYear';
        $occurTermArr['endDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/endDayOfYear';
        $occurFieldArr['endDayOfYear'] = 'o.endDayOfYear';
        $occurTermArr['verbatimEventDate'] = 'http://rs.tdwg.org/dwc/terms/verbatimEventDate';
        $occurFieldArr['verbatimEventDate'] = 'o.verbatimEventDate';
        $occurTermArr['occurrenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/occurrenceRemarks';
        $occurFieldArr['occurrenceRemarks'] = 'o.occurrenceRemarks';
        $occurTermArr['habitat'] = 'http://rs.tdwg.org/dwc/terms/habitat';
        $occurFieldArr['habitat'] = 'o.habitat';
        $occurTermArr['fieldNumber'] = 'http://rs.tdwg.org/dwc/terms/fieldNumber';
        $occurFieldArr['fieldNumber'] = 'o.fieldNumber';
        $occurTermArr['fieldNotes'] = 'http://rs.tdwg.org/dwc/terms/fieldNotes';
        $occurFieldArr['fieldNotes'] = 'o.fieldNotes';
        $occurTermArr['samplingProtocol'] = 'http://rs.tdwg.org/dwc/terms/samplingProtocol';
        $occurFieldArr['samplingProtocol'] = 'o.samplingProtocol';
        $occurTermArr['samplingEffort'] = 'http://rs.tdwg.org/dwc/terms/samplingEffort';
        $occurFieldArr['samplingEffort'] = 'o.samplingEffort';
        $occurTermArr['eventID'] = 'http://rs.tdwg.org/dwc/terms/eventID';
        $occurFieldArr['eventID'] = 'o.eventID';
        $occurTermArr['informationWithheld'] = 'http://rs.tdwg.org/dwc/terms/informationWithheld';
        $occurFieldArr['informationWithheld'] = 'o.informationWithheld';
        $occurTermArr['dataGeneralizations'] = 'http://rs.tdwg.org/dwc/terms/dataGeneralizations';
        $occurFieldArr['dataGeneralizations'] = 'o.dataGeneralizations';
        $occurTermArr['dynamicProperties'] = 'http://rs.tdwg.org/dwc/terms/dynamicProperties';
        $occurFieldArr['dynamicProperties'] = 'o.dynamicProperties';
        $occurTermArr['associatedOccurrences'] = 'http://rs.tdwg.org/dwc/terms/associatedOccurrences';
        $occurFieldArr['associatedOccurrences'] = 'o.associatedOccurrences';
        $occurTermArr['associatedTaxa'] = 'http://rs.tdwg.org/dwc/terms/associatedTaxa';
        $occurFieldArr['associatedTaxa'] = 'o.associatedTaxa';
        $occurTermArr['reproductiveCondition'] = 'http://rs.tdwg.org/dwc/terms/reproductiveCondition';
        $occurFieldArr['reproductiveCondition'] = 'o.reproductiveCondition';
        $occurTermArr['establishmentMeans'] = 'http://rs.tdwg.org/dwc/terms/establishmentMeans';
        $occurFieldArr['establishmentMeans'] = 'o.establishmentMeans';
        $occurTermArr['lifeStage'] = 'http://rs.tdwg.org/dwc/terms/lifeStage';
        $occurFieldArr['lifeStage'] = 'o.lifeStage';
        $occurTermArr['sex'] = 'http://rs.tdwg.org/dwc/terms/sex';
        $occurFieldArr['sex'] = 'o.sex';
        $occurTermArr['behavior'] = 'http://rs.tdwg.org/dwc/terms/behavior';
        $occurFieldArr['behavior'] = 'o.behavior';
        $occurTermArr['individualCount'] = 'http://rs.tdwg.org/dwc/terms/individualCount';
        $occurFieldArr['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
        $occurTermArr['preparations'] = 'http://rs.tdwg.org/dwc/terms/preparations';
        $occurFieldArr['preparations'] = 'o.preparations';
        $occurTermArr['locationID'] = 'http://rs.tdwg.org/dwc/terms/locationID';
        $occurFieldArr['locationID'] = 'o.locationID';
        $occurTermArr['waterBody'] = 'http://rs.tdwg.org/dwc/terms/waterBody';
        $occurFieldArr['waterBody'] = 'o.waterBody';
        $occurTermArr['country'] = 'http://rs.tdwg.org/dwc/terms/country';
        $occurFieldArr['country'] = 'o.country';
        $occurTermArr['stateProvince'] = 'http://rs.tdwg.org/dwc/terms/stateProvince';
        $occurFieldArr['stateProvince'] = 'o.stateProvince';
        $occurTermArr['county'] = 'http://rs.tdwg.org/dwc/terms/county';
        $occurFieldArr['county'] = 'o.county';
        $occurTermArr['municipality'] = 'http://rs.tdwg.org/dwc/terms/municipality';
        $occurFieldArr['municipality'] = 'o.municipality';
        $occurTermArr['locality'] = 'http://rs.tdwg.org/dwc/terms/locality';
        $occurFieldArr['locality'] = 'o.locality';
        $occurTermArr['locationRemarks'] = 'http://rs.tdwg.org/dwc/terms/locationRemarks';
        $occurFieldArr['locationRemarks'] = 'o.locationremarks';
        $occurTermArr['decimalLatitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLatitude';
        $occurFieldArr['decimalLatitude'] = 'o.decimalLatitude';
        $occurTermArr['decimalLongitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLongitude';
        $occurFieldArr['decimalLongitude'] = 'o.decimalLongitude';
        $occurTermArr['geodeticDatum'] = 'http://rs.tdwg.org/dwc/terms/geodeticDatum';
        $occurFieldArr['geodeticDatum'] = 'o.geodeticDatum';
        $occurTermArr['coordinateUncertaintyInMeters'] = 'http://rs.tdwg.org/dwc/terms/coordinateUncertaintyInMeters';
        $occurFieldArr['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
        $occurTermArr['coordinatePrecision'] = 'http://rs.tdwg.org/dwc/terms/coordinatePrecision';
        $occurFieldArr['coordinatePrecision'] = 'o.coordinatePrecision';
        $occurTermArr['verbatimCoordinateSystem'] = 'http://rs.tdwg.org/dwc/terms/verbatimCoordinateSystem';
        $occurFieldArr['verbatimCoordinateSystem'] = 'o.verbatimCoordinateSystem';
        $occurTermArr['verbatimCoordinates'] = 'http://rs.tdwg.org/dwc/terms/verbatimCoordinates';
        $occurFieldArr['verbatimCoordinates'] = 'o.verbatimCoordinates';
        $occurTermArr['georeferencedBy'] = 'http://rs.tdwg.org/dwc/terms/georeferencedBy';
        $occurFieldArr['georeferencedBy'] = 'o.georeferencedBy';
        $occurTermArr['georeferenceProtocol'] = 'http://rs.tdwg.org/dwc/terms/georeferenceProtocol';
        $occurFieldArr['georeferenceProtocol'] = 'o.georeferenceProtocol';
        $occurTermArr['georeferenceSources'] = 'http://rs.tdwg.org/dwc/terms/georeferenceSources';
        $occurFieldArr['georeferenceSources'] = 'o.georeferenceSources';
        $occurTermArr['georeferenceVerificationStatus'] = 'http://rs.tdwg.org/dwc/terms/georeferenceVerificationStatus';
        $occurFieldArr['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
        $occurTermArr['georeferenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/georeferenceRemarks';
        $occurFieldArr['georeferenceRemarks'] = 'o.georeferenceRemarks';
        $occurTermArr['minimumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumElevationInMeters';
        $occurFieldArr['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
        $occurTermArr['maximumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumElevationInMeters';
        $occurFieldArr['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
        $occurTermArr['minimumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumDepthInMeters';
        $occurFieldArr['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
        $occurTermArr['maximumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumDepthInMeters';
        $occurFieldArr['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
        $occurTermArr['verbatimDepth'] = 'http://rs.tdwg.org/dwc/terms/verbatimDepth';
        $occurFieldArr['verbatimDepth'] = 'o.verbatimDepth';
        $occurTermArr['verbatimElevation'] = 'http://rs.tdwg.org/dwc/terms/verbatimElevation';
        $occurFieldArr['verbatimElevation'] = 'o.verbatimElevation';
        $occurTermArr['disposition'] = 'http://rs.tdwg.org/dwc/terms/disposition';
        $occurFieldArr['disposition'] = 'o.disposition';
        $occurTermArr['language'] = 'http://purl.org/dc/terms/language';
        $occurFieldArr['language'] = 'o.`language`';
        $occurTermArr['modified'] = 'http://purl.org/dc/terms/modified';
        $occurFieldArr['modified'] = 'IFNULL(o.modified, o.datelastmodified) AS modified';
        $occurTermArr['rights'] = 'http://purl.org/dc/elements/1.1/rights';
        $occurFieldArr['rights'] = 'c.rights';
        $occurTermArr['rightsHolder'] = 'http://purl.org/dc/terms/rightsHolder';
        $occurFieldArr['rightsHolder'] = 'c.rightsHolder';
        $occurTermArr['accessRights'] = 'http://purl.org/dc/terms/accessRights';
        $occurFieldArr['accessRights'] = 'c.accessRights';
        $occurTermArr['references'] = 'http://purl.org/dc/terms/references';
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
        $occurTermArr['footprintWKT'] = 'https://dwc.tdwg.org/list/footprintWKT';
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
