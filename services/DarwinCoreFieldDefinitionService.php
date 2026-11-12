<?php
class DarwinCoreFieldDefinitionService {

    public static function getDeterminationArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('label' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifiedby'] = array('label' => 'identifiedBy', 'sql' => 'd.identifiedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identifiedBy');
        $fieldArr['dateidentified'] = array('label' => 'dateIdentified', 'sql' => 'd.dateidentified', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dateIdentified');
        $fieldArr['identificationqualifier'] = array('label' => 'identificationQualifier', 'sql' => 'd.identificationqualifier', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier');
        $fieldArr['scientificname'] = array('label' => 'scientificName', 'sql' => 'd.sciname AS scientificname', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificName');
        $fieldArr['scientificnameauthorship'] = array('label' => 'scientificNameAuthorship', 'sql' => 'd.scientificnameauthorship', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship');
        $fieldArr['genus'] = array('label' => 'genus', 'sql' => 'CONCAT_WS(" ", t.unitind1, t.unitname1) AS genus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:genus');
        $fieldArr['specificepithet'] = array('label' => 'specificEpithet', 'sql' => 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:specificEpithet');
        $fieldArr['taxonrank'] = array('label' => 'taxonRank', 'sql' => 't.unitind3 AS taxonrank', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRank');
        $fieldArr['infraspecificepithet'] = array('label' => 'infraspecificEpithet', 'sql' => 't.unitname3 AS infraspecificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet');
        $fieldArr['identificationreferences'] = array('label' => 'identificationReferences', 'sql' => 'd.identificationreferences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationReferences');
        $fieldArr['identificationremarks'] = array('label' => 'identificationRemarks', 'sql' => 'd.identificationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks');
        $fieldArr['modified'] = array('label' => 'modified', 'sql' => 'd.initialtimestamp AS modified', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:modified');
        $fieldArr['providermanagedid'] = array('label' => 'providerManagedID', 'sql' => 'd.detid AS providermanagedid', 'definition' => 'http://rs.tdwg.org/ac/terms/providerManagedID');
        return $fieldArr;
    }

    public static function getImageArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('label' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifier'] = array('label' => 'identifier', 'sql' => 'IFNULL(i.originalurl, i.url) as identifier', 'definition' => 'http://purl.org/dc/terms/identifier');
        $fieldArr['accessuri'] = array('label' => 'accessURI', 'sql' => 'IFNULL(NULLIF(i.originalurl, ""), i.url) as accessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/accessURI');
        $fieldArr['thumbnailaccessuri'] = array('label' => 'thumbnailAccessURI', 'sql' => 'i.thumbnailurl as thumbnailaccessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI');
        $fieldArr['goodqualityaccessuri'] = array('label' => 'goodQualityAccessURI', 'sql' => 'i.url as goodqualityaccessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI');
        $fieldArr['rights'] = array('label' => 'rights', 'sql' => 'c.rights', 'definition' => 'http://purl.org/dc/terms/rights');
        $fieldArr['owner'] = array('label' => 'Owner', 'sql' => 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS owner', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/Owner');
        $fieldArr['creator'] = array('label' => 'creator', 'sql' => 'i.photographer AS creator', 'definition' => 'http://purl.org/dc/elements/1.1/creator');
        $fieldArr['usageterms'] = array('label' => 'UsageTerms', 'sql' => 'i.copyright AS usageterms', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/UsageTerms');
        $fieldArr['webstatement'] = array('label' => 'WebStatement', 'sql' => 'c.accessrights AS webstatement', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/WebStatement');
        $fieldArr['caption'] = array('label' => 'caption', 'sql' => 'i.caption', 'definition' => 'http://rs.tdwg.org/ac/terms/caption');
        $fieldArr['comments'] = array('label' => 'comments', 'sql' => 'i.notes AS comments', 'definition' => 'http://rs.tdwg.org/ac/terms/comments');
        $fieldArr['metadatadate'] = array('label' => 'MetadataDate', 'sql' => 'i.initialtimestamp AS metadatadate', 'definition' => 'http://ns.adobe.com/xap/1.0/MetadataDate');
        $fieldArr['format'] = array('label' => 'format', 'sql' => 'i.format', 'definition' => 'http://purl.org/dc/terms/format');
        $fieldArr['associatedspecimenreference'] = array('label' => 'associatedSpecimenReference', 'sql' => '"" AS associatedspecimenreference', 'definition' => 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference');
        $fieldArr['type'] = array('label' => 'type', 'sql' => '"" AS type', 'definition' => 'http://purl.org/dc/terms/type');
        $fieldArr['metadatalanguage'] = array('label' => 'metadataLanguage', 'sql' => '"" AS metadatalanguage', 'definition' => 'http://rs.tdwg.org/ac/terms/metadataLanguage');
        $fieldArr['providermanagedid'] = array('label' => 'providerManagedID', 'sql' => 'i.imgid AS providermanagedid', 'definition' => 'http://rs.tdwg.org/ac/terms/providerManagedID');
        return $fieldArr;
    }

    public static function getMeasurementOrFactArr($core): array
    {
        $fieldArr = array();
        if($core === 'event'){
            $fieldArr['coreid'] = array('label' => 'eventCoreId', 'sql' => 'e.guid AS coreid', 'definition' => '');
        }
        elseif($core === 'location'){
            $fieldArr['coreid'] = array('label' => 'locationCoreId', 'sql' => 'l.guid AS coreid', 'definition' => '');
        }
        else{
            $fieldArr['occurrencecoreid'] = array('label' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        }
        $fieldArr['measurementtype'] = array('label' => 'measurementType', 'sql' => 'm.field AS measurementtype', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementType');
        $fieldArr['measurementvalue'] = array('label' => 'measurementValue', 'sql' => 'm.datavalue AS measurementvalue', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementValue');
        $fieldArr['measurementunit'] = array('label' => 'measurementUnit', 'sql' => '"" AS measurementunit', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementUnit');
        $fieldArr['measurementaccuracy'] = array('label' => 'measurementAccuracy', 'sql' => '"" AS measurementaccuracy', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementAccuracy');
        $fieldArr['measurementmethod'] = array('label' => 'measurementMethod', 'sql' => '"" AS measurementmethod', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementMethod');
        $fieldArr['measurementremarks'] = array('label' => 'measurementRemarks', 'sql' => '"" AS measurementremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementRemarks');
        $fieldArr['measurementdetermineddate'] = array('label' => 'measurementDeterminedDate', 'sql' => 'DATE_FORMAT(m.initialtimestamp, "%Y-%m-%dT%TZ") AS measurementdetermineddate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedDate');
        $fieldArr['measurementdeterminedby'] = array('label' => 'measurementDeterminedBy', 'sql' => 'm.enteredby AS measurementdeterminedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedBy');
        $fieldArr['providermanagedid'] = array('label' => 'providerManagedID', 'sql' => 'm.mofid AS providermanagedid', 'definition' => 'http://rs.tdwg.org/ac/terms/providerManagedID');
        return $fieldArr;
    }

    public static function getMediaArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('label' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifier'] = array('label' => 'identifier', 'sql' => 'm.accessuri as identifier', 'definition' => 'http://purl.org/dc/terms/identifier');
        $fieldArr['accessuri'] = array('label' => 'accessURI', 'sql' => 'm.accessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/accessURI');
        $fieldArr['thumbnailaccessuri'] = array('label' => 'thumbnailAccessURI', 'sql' => '"" as thumbnailaccessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI');
        $fieldArr['goodqualityaccessuri'] = array('label' => 'goodQualityAccessURI', 'sql' => 'm.accessuri as goodqualityaccessuri', 'definition' => 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI');
        $fieldArr['rights'] = array('label' => 'rights', 'sql' => 'c.rights', 'definition' => 'http://purl.org/dc/terms/rights');
        $fieldArr['owner'] = array('label' => 'Owner', 'sql' => 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS owner', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/Owner');
        $fieldArr['creator'] = array('label' => 'creator', 'sql' => 'm.creator', 'definition' => 'http://purl.org/dc/elements/1.1/creator');
        $fieldArr['usageterms'] = array('label' => 'UsageTerms', 'sql' => 'm.usageterms', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/UsageTerms');
        $fieldArr['webstatement'] = array('label' => 'WebStatement', 'sql' => 'c.accessrights AS webstatement', 'definition' => 'http://ns.adobe.com/xap/1.0/rights/WebStatement');
        $fieldArr['caption'] = array('label' => 'caption', 'sql' => 'm.title AS caption', 'definition' => 'http://rs.tdwg.org/ac/terms/caption');
        $fieldArr['comments'] = array('label' => 'comments', 'sql' => 'm.description AS comments', 'definition' => 'http://rs.tdwg.org/ac/terms/comments');
        $fieldArr['metadatadate'] = array('label' => 'MetadataDate', 'sql' => 'm.initialtimestamp AS metadatadate', 'definition' => 'http://ns.adobe.com/xap/1.0/MetadataDate');
        $fieldArr['format'] = array('label' => 'format', 'sql' => 'm.format', 'definition' => 'http://purl.org/dc/terms/format');
        $fieldArr['associatedspecimenreference'] = array('label' => 'associatedSpecimenReference', 'sql' => '"" as associatedspecimenreference', 'definition' => 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference');
        $fieldArr['type'] = array('label' => 'type', 'sql' => 'm.type', 'definition' => 'http://purl.org/dc/terms/type');
        $fieldArr['metadatalanguage'] = array('label' => 'metadataLanguage', 'sql' => '"" as metadatalanguage', 'definition' => 'http://rs.tdwg.org/ac/terms/metadataLanguage');
        $fieldArr['providermanagedid'] = array('label' => 'providerManagedID', 'sql' => 'm.mediaid AS providermanagedid', 'definition' => 'http://rs.tdwg.org/ac/terms/providerManagedID');
        return $fieldArr;
    }

    public static function getOccurrenceArr(): array
    {
        $occurFieldArr = array();
        $occurFieldArr['occurrencecoreid'] = array('label' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $occurFieldArr['institutioncode'] = array('label' => 'institutionCode', 'sql' => 'IFNULL(o.institutioncode, c.institutioncode) AS institutioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:institutionCode');
        $occurFieldArr['collectioncode'] = array('label' => 'collectionCode', 'sql' => 'IFNULL(o.collectioncode, c.collectioncode) AS collectioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:collectionCode');
        $occurFieldArr['collectionid'] = array('label' => 'collectionID', 'sql' => 'IFNULL(o.collectionid, c.collectionguid) AS collectionid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:collectionID');
        $occurFieldArr['ownerinstitutioncode'] = array('label' => 'ownerInstitutionCode', 'sql' => 'o.ownerinstitutioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:ownerInstitutionCode');
        $occurFieldArr['institutionid'] = array('label' => 'institutionID', 'sql' => 'o.institutionid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:institutionID');
        $occurFieldArr['datasetid'] = array('label' => 'datasetID', 'sql' => 'o.datasetid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:datasetID');
        $occurFieldArr['basisofrecord'] = array('label' => 'basisOfRecord', 'sql' => 'o.basisofrecord', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:basisOfRecord');
        $occurFieldArr['occurrenceid'] = array('label' => 'occurrenceID', 'sql' => 'IFNULL(o.occurrenceid, o.guid) AS occurrenceid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:occurrenceID');
        $occurFieldArr['catalognumber'] = array('label' => 'catalogNumber', 'sql' => 'o.catalognumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:catalogNumber');
        $occurFieldArr['othercatalognumbers'] = array('label' => 'otherCatalogNumbers', 'sql' => 'o.othercatalognumbers', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:otherCatalogNumbers');
        $occurFieldArr['kingdom'] = array('label' => 'kingdom', 'sql' => '"" AS t_kingdom', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:kingdom');
        $occurFieldArr['phylum'] = array('label' => 'phylum', 'sql' => '"" AS t_phylum', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:phylum');
        $occurFieldArr['class'] = array('label' => 'class', 'sql' => '"" AS t_class', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:class');
        $occurFieldArr['order'] = array('label' => 'order', 'sql' => '"" AS t_order', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:order');
        $occurFieldArr['family'] = array('label' => 'family', 'sql' => 'o.family', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:family');
        $occurFieldArr['scientificname'] = array('label' => 'scientificName', 'sql' => 'o.sciname AS scientificname', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificName');
        $occurFieldArr['taxonid'] = array('label' => 'taxonID', 'sql' => 'o.tid AS taxonid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonID');
        $occurFieldArr['scientificnameauthorship'] = array('label' => 'scientificNameAuthorship', 'sql' => 'IFNULL(t.author, o.scientificnameauthorship) AS scientificnameauthorship', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship');
        $occurFieldArr['genus'] = array('label' => 'genus', 'sql' => 'IF(t.rankid >= 180, CONCAT_WS(" ", t.unitind1, t.unitname1), NULL) AS genus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:genus');
        $occurFieldArr['specificepithet'] = array('label' => 'specificEpithet', 'sql' => 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:specificEpithet');
        $occurFieldArr['infraspecificepithet'] = array('label' => 'infraspecificEpithet', 'sql' => 't.unitname3 AS infraspecificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet');
        $occurFieldArr['taxonrank'] = array('label' => 'taxonRank', 'sql' => 't.unitind3 AS taxonrank', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRank');
        $occurFieldArr['identifiedby'] = array('label' => 'identifiedBy', 'sql' => 'o.identifiedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identifiedBy');
        $occurFieldArr['dateidentified'] = array('label' => 'dateIdentified', 'sql' => 'o.dateidentified', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dateIdentified');
        $occurFieldArr['identificationreferences'] = array('label' => 'identificationReferences', 'sql' => 'o.identificationreferences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationReferences');
        $occurFieldArr['identificationremarks'] = array('label' => 'identificationRemarks', 'sql' => 'o.identificationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks');
        $occurFieldArr['taxonremarks'] = array('label' => 'taxonRemarks', 'sql' => 'o.taxonremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRemarks');
        $occurFieldArr['identificationqualifier'] = array('label' => 'identificationQualifier', 'sql' => 'o.identificationqualifier', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier');
        $occurFieldArr['typestatus'] = array('label' => 'typeStatus', 'sql' => 'o.typestatus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:typeStatus');
        $occurFieldArr['recordedby'] = array('label' => 'recordedBy', 'sql' => 'CONCAT_WS("; ", o.recordedby, o.associatedcollectors) AS recordedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:recordedBy');
        $occurFieldArr['recordnumber'] = array('label' => 'recordNumber', 'sql' => 'o.recordnumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:recordNumber');
        $occurFieldArr['eventdate'] = array('label' => 'eventDate', 'sql' => 'o.eventdate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:eventDate');
        $occurFieldArr['year'] = array('label' => 'year', 'sql' => 'o.`year`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:year');
        $occurFieldArr['month'] = array('label' => 'month', 'sql' => 'o.`month`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:month');
        $occurFieldArr['day'] = array('label' => 'day', 'sql' => 'o.`day`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:day');
        $occurFieldArr['startdayofyear'] = array('label' => 'startDayOfYear', 'sql' => 'o.startdayofyear', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:startDayOfYear');
        $occurFieldArr['enddayofyear'] = array('label' => 'endDayOfYear', 'sql' => 'o.enddayofyear', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:endDayOfYear');
        $occurFieldArr['verbatimeventdate'] = array('label' => 'verbatimEventDate', 'sql' => 'o.verbatimeventdate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimEventDate');
        $occurFieldArr['occurrenceremarks'] = array('label' => 'occurrenceRemarks', 'sql' => 'CONCAT_WS("; ", o.occurrenceremarks, o.verbatimattributes) AS occurrenceremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:occurrenceRemarks');
        $occurFieldArr['habitat'] = array('label' => 'habitat', 'sql' => 'CONCAT_WS("; ", o.habitat, o.substrate) AS habitat', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:habitat');
        $occurFieldArr['fieldnumber'] = array('label' => 'fieldNumber', 'sql' => 'o.fieldnumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:fieldNumber');
        $occurFieldArr['fieldnotes'] = array('label' => 'fieldNotes', 'sql' => 'o.fieldnotes', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:fieldNotes');
        $occurFieldArr['samplingprotocol'] = array('label' => 'samplingProtocol', 'sql' => 'o.samplingprotocol', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:samplingProtocol');
        $occurFieldArr['samplingeffort'] = array('label' => 'samplingEffort', 'sql' => 'o.samplingeffort', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:samplingEffort');
        $occurFieldArr['eventid'] = array('label' => 'eventID', 'sql' => 'o.eventid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:eventID');
        $occurFieldArr['informationwithheld'] = array('label' => 'informationWithheld', 'sql' => 'o.informationwithheld', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:informationWithheld');
        $occurFieldArr['datageneralizations'] = array('label' => 'dataGeneralizations', 'sql' => 'o.datageneralizations', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dataGeneralizations');
        $occurFieldArr['dynamicproperties'] = array('label' => 'dynamicProperties', 'sql' => 'o.dynamicproperties', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dynamicProperties');
        $occurFieldArr['associatedoccurrences'] = array('label' => 'associatedOccurrences', 'sql' => 'o.associatedoccurrences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:associatedOccurrences');
        $occurFieldArr['associatedtaxa'] = array('label' => 'associatedTaxa', 'sql' => 'o.associatedtaxa', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:associatedTaxa');
        $occurFieldArr['reproductivecondition'] = array('label' => 'reproductiveCondition', 'sql' => 'o.reproductivecondition', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:reproductiveCondition');
        $occurFieldArr['establishmentmeans'] = array('label' => 'establishmentMeans', 'sql' => 'o.establishmentmeans', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:establishmentMeans');
        $occurFieldArr['lifestage'] = array('label' => 'lifeStage', 'sql' => 'o.lifestage', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:lifeStage');
        $occurFieldArr['sex'] = array('label' => 'sex', 'sql' => 'o.sex', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:sex');
        $occurFieldArr['behavior'] = array('label' => 'behavior', 'sql' => 'o.behavior', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:behavior');
        $occurFieldArr['individualcount'] = array('label' => 'individualCount', 'sql' => 'o.individualcount', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:individualCount');
        $occurFieldArr['preparations'] = array('label' => 'preparations', 'sql' => 'o.preparations', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:preparations');
        $occurFieldArr['locationid'] = array('label' => 'locationID', 'sql' => 'o.locationid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locationID');
        $occurFieldArr['waterbody'] = array('label' => 'waterBody', 'sql' => 'o.waterbody', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:waterBody');
        $occurFieldArr['country'] = array('label' => 'country', 'sql' => 'o.country', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:country');
        $occurFieldArr['stateprovince'] = array('label' => 'stateProvince', 'sql' => 'o.stateprovince', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:stateProvince');
        $occurFieldArr['county'] = array('label' => 'county', 'sql' => 'o.county', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:county');
        $occurFieldArr['municipality'] = array('label' => 'municipality', 'sql' => 'o.municipality', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:municipality');
        $occurFieldArr['locality'] = array('label' => 'locality', 'sql' => 'o.locality', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locality');
        $occurFieldArr['locationremarks'] = array('label' => 'locationRemarks', 'sql' => 'o.locationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locationRemarks');
        $occurFieldArr['decimallatitude'] = array('label' => 'decimalLatitude', 'sql' => 'o.decimallatitude', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:decimalLatitude');
        $occurFieldArr['decimallongitude'] = array('label' => 'decimalLongitude', 'sql' => 'o.decimallongitude', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:decimalLongitude');
        $occurFieldArr['geodeticdatum'] = array('label' => 'geodeticDatum', 'sql' => 'o.geodeticdatum', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:geodeticDatum');
        $occurFieldArr['coordinateuncertaintyinmeters'] = array('label' => 'coordinateUncertaintyInMeters', 'sql' => 'o.coordinateuncertaintyinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:coordinateUncertaintyInMeters');
        $occurFieldArr['coordinateprecision'] = array('label' => 'coordinatePrecision', 'sql' => 'o.coordinateprecision', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:coordinatePrecision');
        $occurFieldArr['verbatimcoordinatesystem'] = array('label' => 'verbatimCoordinateSystem', 'sql' => 'o.verbatimcoordinatesystem', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinateSystem');
        $occurFieldArr['verbatimcoordinates'] = array('label' => 'verbatimCoordinates', 'sql' => 'o.verbatimcoordinates', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinates');
        $occurFieldArr['georeferencedby'] = array('label' => 'georeferencedBy', 'sql' => 'o.georeferencedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferencedBy');
        $occurFieldArr['georeferenceprotocol'] = array('label' => 'georeferenceProtocol', 'sql' => 'o.georeferenceprotocol', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceProtocol');
        $occurFieldArr['georeferencesources'] = array('label' => 'georeferenceSources', 'sql' => 'o.georeferencesources', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceSources');
        $occurFieldArr['georeferenceverificationstatus'] = array('label' => 'georeferenceVerificationStatus', 'sql' => 'o.georeferenceverificationstatus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceVerificationStatus');
        $occurFieldArr['georeferenceremarks'] = array('label' => 'georeferenceRemarks', 'sql' => 'o.georeferenceremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceRemarks');
        $occurFieldArr['minimumelevationinmeters'] = array('label' => 'minimumElevationInMeters', 'sql' => 'o.minimumelevationinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:minimumElevationInMeters');
        $occurFieldArr['maximumelevationinmeters'] = array('label' => 'maximumElevationInMeters', 'sql' => 'o.maximumelevationinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:maximumElevationInMeters');
        $occurFieldArr['minimumdepthinmeters'] = array('label' => 'minimumDepthInMeters', 'sql' => 'o.minimumdepthinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:minimumDepthInMeters');
        $occurFieldArr['maximumdepthinmeters'] = array('label' => 'maximumDepthInMeters', 'sql' => 'o.maximumdepthinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:maximumDepthInMeters');
        $occurFieldArr['verbatimdepth'] = array('label' => 'verbatimDepth', 'sql' => 'o.verbatimdepth', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimDepth');
        $occurFieldArr['verbatimelevation'] = array('label' => 'verbatimElevation', 'sql' => 'o.verbatimelevation', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimElevation');
        $occurFieldArr['disposition'] = array('label' => 'disposition', 'sql' => 'o.disposition', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:disposition');
        $occurFieldArr['language'] = array('label' => 'language', 'sql' => 'o.`language`', 'definition' => 'https://dwc.tdwg.org/terms/#dc:language');
        $occurFieldArr['modified'] = array('label' => 'modified', 'sql' => 'IFNULL(o.modified, o.datelastmodified) AS modified', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:modified');
        $occurFieldArr['license'] = array('label' => 'license', 'sql' => 'c.rights AS license', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:license');
        $occurFieldArr['rightsholder'] = array('label' => 'rightsHolder', 'sql' => 'c.rightsholder', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:rightsHolder');
        $occurFieldArr['accessrights'] = array('label' => 'accessRights', 'sql' => 'c.accessrights', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:accessRights');
        $occurFieldArr['references'] = array('label' => 'references', 'sql' => '"" AS t_references', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:references');
        $occurFieldArr['footprintwkt'] = array('label' => 'footprintWKT', 'sql' => 'o.footprintwkt', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:footprintWKT');
        $occurFieldArr['datelastmodified'] = array('label' => 'dateLastModified', 'sql' => 'o.datelastmodified', 'definition' => 'https://dwc.tdwg.org/list/#dwc_dateLastModified');
        $occurFieldArr['providermanagedid'] = array('label' => 'providerManagedID', 'sql' => 'o.occid AS providermanagedid', 'definition' => 'http://rs.tdwg.org/ac/terms/providerManagedID');
        return $occurFieldArr;
    }
}
