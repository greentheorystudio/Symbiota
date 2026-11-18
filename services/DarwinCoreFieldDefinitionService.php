<?php
class DarwinCoreFieldDefinitionService {

    public static function getDeterminationArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('header' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifiedby'] = array('header' => 'identifiedBy', 'sql' => 'd.identifiedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identifiedBy');
        $fieldArr['dateidentified'] = array('header' => 'dateIdentified', 'sql' => 'd.dateidentified', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dateIdentified');
        $fieldArr['identificationqualifier'] = array('header' => 'identificationQualifier', 'sql' => 'd.identificationqualifier', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier');
        $fieldArr['scientificname'] = array('header' => 'scientificName', 'sql' => 'd.sciname AS scientificname', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificName');
        $fieldArr['scientificnameauthorship'] = array('header' => 'scientificNameAuthorship', 'sql' => 'd.scientificnameauthorship', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship');
        $fieldArr['genus'] = array('header' => 'genus', 'sql' => 'CONCAT_WS(" ", t.unitind1, t.unitname1) AS genus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:genus');
        $fieldArr['specificepithet'] = array('header' => 'specificEpithet', 'sql' => 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:specificEpithet');
        $fieldArr['taxonrank'] = array('header' => 'taxonRank', 'sql' => 't.unitind3 AS taxonrank', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRank');
        $fieldArr['infraspecificepithet'] = array('header' => 'infraspecificEpithet', 'sql' => 't.unitname3 AS infraspecificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet');
        $fieldArr['identificationreferences'] = array('header' => 'identificationReferences', 'sql' => 'd.identificationreferences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationReferences');
        $fieldArr['identificationremarks'] = array('header' => 'identificationRemarks', 'sql' => 'd.identificationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks');
        $fieldArr['modified'] = array('header' => 'modified', 'sql' => 'd.initialtimestamp AS modified', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:modified');
        $fieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'd.detid AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $fieldArr;
    }

    public static function getGeneticArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('header' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['targetgene'] = array('header' => 'targetGene', 'sql' => 'g.targetgene', 'definition' => 'https://genomicsstandardsconsortium.github.io/mixs/0000044');
        $fieldArr['targetsubfragment'] = array('header' => 'targetSubfragment', 'sql' => 'g.targetsubfragment', 'definition' => 'https://genomicsstandardsconsortium.github.io/mixs/0000045');
        $fieldArr['dnasequence'] = array('header' => 'dnaSequence', 'sql' => 'g.dnasequence', 'definition' => 'https://rs.gbif.org/extension/gbif/1.0/dna_derived_data_2024-07-11.xml#DNA_sequence');
        $fieldArr['url'] = array('header' => 'url', 'sql' => 'g.url AS scientificname', 'definition' => 'https://genomicsstandardsconsortium.github.io/mixs/0000091');
        $fieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'g.idoccurgenetic AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $fieldArr;
    }

    public static function getImageArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('header' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifier'] = array('header' => 'identifier', 'sql' => 'IFNULL(i.originalurl, i.url) as identifier', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#identifier');
        $fieldArr['rights'] = array('header' => 'rights', 'sql' => 'c.rights', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#rights');
        $fieldArr['rightsholder'] = array('header' => 'rightsHolder', 'sql' => 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS rightsholder', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#rightsHolder');
        $fieldArr['creator'] = array('header' => 'creator', 'sql' => 'i.photographer AS creator', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#creator');
        $fieldArr['accessrights'] = array('header' => 'accessRights', 'sql' => 'c.accessrights', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#accessRights');
        $fieldArr['title'] = array('header' => 'title', 'sql' => '"" AS title', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#title');
        $fieldArr['caption'] = array('header' => 'caption', 'sql' => 'i.caption', 'definition' => 'https://ac.tdwg.org/termlist/#ac_caption');
        $fieldArr['comments'] = array('header' => 'comments', 'sql' => 'i.notes AS comments', 'definition' => 'https://ac.tdwg.org/termlist/#ac_comments');
        $fieldArr['description'] = array('header' => 'description', 'sql' => '"" AS description', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#description');
        $fieldArr['format'] = array('header' => 'format', 'sql' => 'i.format', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#format');
        $fieldArr['associatedspecimenreference'] = array('header' => 'associatedSpecimenReference', 'sql' => '"" AS associatedspecimenreference', 'definition' => 'https://ac.tdwg.org/termlist/#ac_associatedSpecimenReference');
        $fieldArr['metadatalanguage'] = array('header' => 'metadataLanguage', 'sql' => '"" AS metadatalanguage', 'definition' => 'https://ac.tdwg.org/termlist/#ac_metadataLanguage');
        $fieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'i.imgid AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $fieldArr;
    }

    public static function getMeasurementOrFactArr($core): array
    {
        $fieldArr = array();
        if($core === 'event'){
            $fieldArr['coreid'] = array('header' => 'eventCoreId', 'sql' => 'e.guid AS coreid', 'definition' => '');
        }
        elseif($core === 'location'){
            $fieldArr['coreid'] = array('header' => 'locationCoreId', 'sql' => 'l.guid AS coreid', 'definition' => '');
        }
        else{
            $fieldArr['occurrencecoreid'] = array('header' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        }
        $fieldArr['measurementtype'] = array('header' => 'measurementType', 'sql' => 'm.field AS measurementtype', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementType');
        $fieldArr['measurementvalue'] = array('header' => 'measurementValue', 'sql' => 'm.datavalue AS measurementvalue', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementValue');
        $fieldArr['measurementunit'] = array('header' => 'measurementUnit', 'sql' => '"" AS measurementunit', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementUnit');
        $fieldArr['measurementaccuracy'] = array('header' => 'measurementAccuracy', 'sql' => '"" AS measurementaccuracy', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementAccuracy');
        $fieldArr['measurementmethod'] = array('header' => 'measurementMethod', 'sql' => '"" AS measurementmethod', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementMethod');
        $fieldArr['measurementremarks'] = array('header' => 'measurementRemarks', 'sql' => '"" AS measurementremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementRemarks');
        $fieldArr['measurementdetermineddate'] = array('header' => 'measurementDeterminedDate', 'sql' => 'DATE_FORMAT(m.initialtimestamp, "%Y-%m-%dT%TZ") AS measurementdetermineddate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedDate');
        $fieldArr['measurementdeterminedby'] = array('header' => 'measurementDeterminedBy', 'sql' => 'm.enteredby AS measurementdeterminedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:measurementDeterminedBy');
        $fieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'm.mofid AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $fieldArr;
    }

    public static function getMediaArr(): array
    {
        $fieldArr = array();
        $fieldArr['occurrencecoreid'] = array('header' => 'occurrenceCoreID', 'sql' => 'o.guid AS occurrencecoreid', 'definition' => '');
        $fieldArr['identifier'] = array('header' => 'identifier', 'sql' => 'm.accessuri as identifier', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#identifier');
        $fieldArr['rights'] = array('header' => 'rights', 'sql' => 'c.rights', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#rights');
        $fieldArr['rightsholder'] = array('header' => 'rightsHolder', 'sql' => 'IFNULL(c.rightsholder, CONCAT(c.collectionname, " (", CONCAT_WS("-", c.institutioncode, c.collectioncode), ")")) AS rightsholder', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#rightsHolder');
        $fieldArr['creator'] = array('header' => 'creator', 'sql' => 'm.creator', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#creator');
        $fieldArr['accessrights'] = array('header' => 'accessRights', 'sql' => 'c.accessrights', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#accessRights');
        $fieldArr['title'] = array('header' => 'title', 'sql' => 'm.title', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#title');
        $fieldArr['caption'] = array('header' => 'caption', 'sql' => '"" AS caption', 'definition' => 'https://ac.tdwg.org/termlist/#ac_caption');
        $fieldArr['comments'] = array('header' => 'comments', 'sql' => '"" AS comments', 'definition' => 'https://ac.tdwg.org/termlist/#ac_comments');
        $fieldArr['description'] = array('header' => 'description', 'sql' => 'm.description', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#description');
        $fieldArr['format'] = array('header' => 'format', 'sql' => 'm.format', 'definition' => 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#format');
        $fieldArr['associatedspecimenreference'] = array('header' => 'associatedSpecimenReference', 'sql' => '"" as associatedspecimenreference', 'definition' => 'https://ac.tdwg.org/termlist/#ac_associatedSpecimenReference');
        $fieldArr['metadatalanguage'] = array('header' => 'metadataLanguage', 'sql' => '"" as metadatalanguage', 'definition' => 'https://ac.tdwg.org/termlist/#ac_metadataLanguage');
        $fieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'm.mediaid AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $fieldArr;
    }

    public static function getOccurrenceArr(): array
    {
        $occurFieldArr = array();
        $occurFieldArr['id'] = array('header' => 'id', 'sql' => 'o.guid AS id', 'definition' => '');
        $occurFieldArr['institutioncode'] = array('header' => 'institutionCode', 'sql' => 'IFNULL(o.institutioncode, c.institutioncode) AS institutioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:institutionCode');
        $occurFieldArr['collectioncode'] = array('header' => 'collectionCode', 'sql' => 'IFNULL(o.collectioncode, c.collectioncode) AS collectioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:collectionCode');
        $occurFieldArr['collectionid'] = array('header' => 'collectionID', 'sql' => 'IFNULL(o.collectionid, c.collectionguid) AS collectionid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:collectionID');
        $occurFieldArr['ownerinstitutioncode'] = array('header' => 'ownerInstitutionCode', 'sql' => 'o.ownerinstitutioncode', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:ownerInstitutionCode');
        $occurFieldArr['institutionid'] = array('header' => 'institutionID', 'sql' => 'o.institutionid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:institutionID');
        $occurFieldArr['datasetid'] = array('header' => 'datasetID', 'sql' => 'o.datasetid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:datasetID');
        $occurFieldArr['basisofrecord'] = array('header' => 'basisOfRecord', 'sql' => 'o.basisofrecord', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:basisOfRecord');
        $occurFieldArr['occurrenceid'] = array('header' => 'occurrenceID', 'sql' => 'IFNULL(o.occurrenceid, o.guid) AS occurrenceid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:occurrenceID');
        $occurFieldArr['catalognumber'] = array('header' => 'catalogNumber', 'sql' => 'o.catalognumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:catalogNumber');
        $occurFieldArr['othercatalognumbers'] = array('header' => 'otherCatalogNumbers', 'sql' => 'o.othercatalognumbers', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:otherCatalogNumbers');
        $occurFieldArr['kingdom'] = array('header' => 'kingdom', 'sql' => '"" AS t_kingdom', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:kingdom');
        $occurFieldArr['phylum'] = array('header' => 'phylum', 'sql' => '"" AS t_phylum', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:phylum');
        $occurFieldArr['class'] = array('header' => 'class', 'sql' => '"" AS t_class', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:class');
        $occurFieldArr['order'] = array('header' => 'order', 'sql' => '"" AS t_order', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:order');
        $occurFieldArr['family'] = array('header' => 'family', 'sql' => 'o.family', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:family');
        $occurFieldArr['scientificname'] = array('header' => 'scientificName', 'sql' => 'o.sciname AS scientificname', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificName');
        $occurFieldArr['taxonid'] = array('header' => 'taxonID', 'sql' => 'o.tid AS taxonid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonID');
        $occurFieldArr['scientificnameauthorship'] = array('header' => 'scientificNameAuthorship', 'sql' => 'IFNULL(t.author, o.scientificnameauthorship) AS scientificnameauthorship', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:scientificNameAuthorship');
        $occurFieldArr['genus'] = array('header' => 'genus', 'sql' => 'IF(t.rankid >= 180, CONCAT_WS(" ", t.unitind1, t.unitname1), NULL) AS genus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:genus');
        $occurFieldArr['specificepithet'] = array('header' => 'specificEpithet', 'sql' => 'CONCAT_WS(" ", t.unitind2, t.unitname2) AS specificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:specificEpithet');
        $occurFieldArr['infraspecificepithet'] = array('header' => 'infraspecificEpithet', 'sql' => 't.unitname3 AS infraspecificepithet', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:infraspecificEpithet');
        $occurFieldArr['taxonrank'] = array('header' => 'taxonRank', 'sql' => 't.unitind3 AS taxonrank', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRank');
        $occurFieldArr['identifiedby'] = array('header' => 'identifiedBy', 'sql' => 'o.identifiedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identifiedBy');
        $occurFieldArr['dateidentified'] = array('header' => 'dateIdentified', 'sql' => 'o.dateidentified', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dateIdentified');
        $occurFieldArr['identificationreferences'] = array('header' => 'identificationReferences', 'sql' => 'o.identificationreferences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationReferences');
        $occurFieldArr['identificationremarks'] = array('header' => 'identificationRemarks', 'sql' => 'o.identificationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationRemarks');
        $occurFieldArr['taxonremarks'] = array('header' => 'taxonRemarks', 'sql' => 'o.taxonremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:taxonRemarks');
        $occurFieldArr['identificationqualifier'] = array('header' => 'identificationQualifier', 'sql' => 'o.identificationqualifier', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:identificationQualifier');
        $occurFieldArr['typestatus'] = array('header' => 'typeStatus', 'sql' => 'o.typestatus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:typeStatus');
        $occurFieldArr['recordedby'] = array('header' => 'recordedBy', 'sql' => 'CONCAT_WS("; ", o.recordedby, o.associatedcollectors) AS recordedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:recordedBy');
        $occurFieldArr['recordnumber'] = array('header' => 'recordNumber', 'sql' => 'o.recordnumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:recordNumber');
        $occurFieldArr['eventdate'] = array('header' => 'eventDate', 'sql' => 'o.eventdate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:eventDate');
        $occurFieldArr['year'] = array('header' => 'year', 'sql' => 'o.`year`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:year');
        $occurFieldArr['month'] = array('header' => 'month', 'sql' => 'o.`month`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:month');
        $occurFieldArr['day'] = array('header' => 'day', 'sql' => 'o.`day`', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:day');
        $occurFieldArr['startdayofyear'] = array('header' => 'startDayOfYear', 'sql' => 'o.startdayofyear', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:startDayOfYear');
        $occurFieldArr['enddayofyear'] = array('header' => 'endDayOfYear', 'sql' => 'o.enddayofyear', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:endDayOfYear');
        $occurFieldArr['verbatimeventdate'] = array('header' => 'verbatimEventDate', 'sql' => 'o.verbatimeventdate', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimEventDate');
        $occurFieldArr['occurrenceremarks'] = array('header' => 'occurrenceRemarks', 'sql' => 'CONCAT_WS("; ", o.occurrenceremarks, o.verbatimattributes) AS occurrenceremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:occurrenceRemarks');
        $occurFieldArr['habitat'] = array('header' => 'habitat', 'sql' => 'CONCAT_WS("; ", o.habitat, o.substrate) AS habitat', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:habitat');
        $occurFieldArr['fieldnumber'] = array('header' => 'fieldNumber', 'sql' => 'o.fieldnumber', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:fieldNumber');
        $occurFieldArr['fieldnotes'] = array('header' => 'fieldNotes', 'sql' => 'o.fieldnotes', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:fieldNotes');
        $occurFieldArr['samplingprotocol'] = array('header' => 'samplingProtocol', 'sql' => 'o.samplingprotocol', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:samplingProtocol');
        $occurFieldArr['samplingeffort'] = array('header' => 'samplingEffort', 'sql' => 'o.samplingeffort', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:samplingEffort');
        $occurFieldArr['eventid'] = array('header' => 'eventID', 'sql' => 'o.eventid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:eventID');
        $occurFieldArr['informationwithheld'] = array('header' => 'informationWithheld', 'sql' => 'o.informationwithheld', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:informationWithheld');
        $occurFieldArr['datageneralizations'] = array('header' => 'dataGeneralizations', 'sql' => 'o.datageneralizations', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dataGeneralizations');
        $occurFieldArr['dynamicproperties'] = array('header' => 'dynamicProperties', 'sql' => 'o.dynamicproperties', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:dynamicProperties');
        $occurFieldArr['associatedoccurrences'] = array('header' => 'associatedOccurrences', 'sql' => 'o.associatedoccurrences', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:associatedOccurrences');
        $occurFieldArr['associatedtaxa'] = array('header' => 'associatedTaxa', 'sql' => 'o.associatedtaxa', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:associatedTaxa');
        $occurFieldArr['reproductivecondition'] = array('header' => 'reproductiveCondition', 'sql' => 'o.reproductivecondition', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:reproductiveCondition');
        $occurFieldArr['establishmentmeans'] = array('header' => 'establishmentMeans', 'sql' => 'o.establishmentmeans', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:establishmentMeans');
        $occurFieldArr['lifestage'] = array('header' => 'lifeStage', 'sql' => 'o.lifestage', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:lifeStage');
        $occurFieldArr['sex'] = array('header' => 'sex', 'sql' => 'o.sex', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:sex');
        $occurFieldArr['behavior'] = array('header' => 'behavior', 'sql' => 'o.behavior', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:behavior');
        $occurFieldArr['individualcount'] = array('header' => 'individualCount', 'sql' => 'o.individualcount', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:individualCount');
        $occurFieldArr['preparations'] = array('header' => 'preparations', 'sql' => 'o.preparations', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:preparations');
        $occurFieldArr['locationid'] = array('header' => 'locationID', 'sql' => 'o.locationid', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locationID');
        $occurFieldArr['waterbody'] = array('header' => 'waterBody', 'sql' => 'o.waterbody', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:waterBody');
        $occurFieldArr['country'] = array('header' => 'country', 'sql' => 'o.country', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:country');
        $occurFieldArr['stateprovince'] = array('header' => 'stateProvince', 'sql' => 'o.stateprovince', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:stateProvince');
        $occurFieldArr['county'] = array('header' => 'county', 'sql' => 'o.county', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:county');
        $occurFieldArr['municipality'] = array('header' => 'municipality', 'sql' => 'o.municipality', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:municipality');
        $occurFieldArr['locality'] = array('header' => 'locality', 'sql' => 'o.locality', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locality');
        $occurFieldArr['locationremarks'] = array('header' => 'locationRemarks', 'sql' => 'o.locationremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:locationRemarks');
        $occurFieldArr['decimallatitude'] = array('header' => 'decimalLatitude', 'sql' => 'o.decimallatitude', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:decimalLatitude');
        $occurFieldArr['decimallongitude'] = array('header' => 'decimalLongitude', 'sql' => 'o.decimallongitude', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:decimalLongitude');
        $occurFieldArr['geodeticdatum'] = array('header' => 'geodeticDatum', 'sql' => 'o.geodeticdatum', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:geodeticDatum');
        $occurFieldArr['coordinateuncertaintyinmeters'] = array('header' => 'coordinateUncertaintyInMeters', 'sql' => 'o.coordinateuncertaintyinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:coordinateUncertaintyInMeters');
        $occurFieldArr['coordinateprecision'] = array('header' => 'coordinatePrecision', 'sql' => 'o.coordinateprecision', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:coordinatePrecision');
        $occurFieldArr['verbatimcoordinatesystem'] = array('header' => 'verbatimCoordinateSystem', 'sql' => 'o.verbatimcoordinatesystem', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinateSystem');
        $occurFieldArr['verbatimcoordinates'] = array('header' => 'verbatimCoordinates', 'sql' => 'o.verbatimcoordinates', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimCoordinates');
        $occurFieldArr['georeferencedby'] = array('header' => 'georeferencedBy', 'sql' => 'o.georeferencedby', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferencedBy');
        $occurFieldArr['georeferenceprotocol'] = array('header' => 'georeferenceProtocol', 'sql' => 'o.georeferenceprotocol', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceProtocol');
        $occurFieldArr['georeferencesources'] = array('header' => 'georeferenceSources', 'sql' => 'o.georeferencesources', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceSources');
        $occurFieldArr['georeferenceverificationstatus'] = array('header' => 'georeferenceVerificationStatus', 'sql' => 'o.georeferenceverificationstatus', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceVerificationStatus');
        $occurFieldArr['georeferenceremarks'] = array('header' => 'georeferenceRemarks', 'sql' => 'o.georeferenceremarks', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:georeferenceRemarks');
        $occurFieldArr['minimumelevationinmeters'] = array('header' => 'minimumElevationInMeters', 'sql' => 'o.minimumelevationinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:minimumElevationInMeters');
        $occurFieldArr['maximumelevationinmeters'] = array('header' => 'maximumElevationInMeters', 'sql' => 'o.maximumelevationinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:maximumElevationInMeters');
        $occurFieldArr['minimumdepthinmeters'] = array('header' => 'minimumDepthInMeters', 'sql' => 'o.minimumdepthinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:minimumDepthInMeters');
        $occurFieldArr['maximumdepthinmeters'] = array('header' => 'maximumDepthInMeters', 'sql' => 'o.maximumdepthinmeters', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:maximumDepthInMeters');
        $occurFieldArr['verbatimdepth'] = array('header' => 'verbatimDepth', 'sql' => 'o.verbatimdepth', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimDepth');
        $occurFieldArr['verbatimelevation'] = array('header' => 'verbatimElevation', 'sql' => 'o.verbatimelevation', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:verbatimElevation');
        $occurFieldArr['disposition'] = array('header' => 'disposition', 'sql' => 'o.disposition', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:disposition');
        $occurFieldArr['language'] = array('header' => 'language', 'sql' => 'o.`language`', 'definition' => 'https://dwc.tdwg.org/terms/#dc:language');
        $occurFieldArr['modified'] = array('header' => 'modified', 'sql' => 'IFNULL(o.modified, o.datelastmodified) AS modified', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:modified');
        $occurFieldArr['license'] = array('header' => 'license', 'sql' => 'c.rights AS license', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:license');
        $occurFieldArr['rightsholder'] = array('header' => 'rightsHolder', 'sql' => 'c.rightsholder', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:rightsHolder');
        $occurFieldArr['accessrights'] = array('header' => 'accessRights', 'sql' => 'c.accessrights', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:accessRights');
        $occurFieldArr['references'] = array('header' => 'references', 'sql' => '"" AS t_references', 'definition' => 'https://dwc.tdwg.org/terms/#dcterms:references');
        $occurFieldArr['footprintwkt'] = array('header' => 'footprintWKT', 'sql' => 'o.footprintwkt', 'definition' => 'https://dwc.tdwg.org/terms/#dwc:footprintWKT');
        $occurFieldArr['datelastmodified'] = array('header' => 'dateLastModified', 'sql' => 'o.datelastmodified', 'definition' => 'https://dwc.tdwg.org/list/#dwc_dateLastModified');
        $occurFieldArr['providermanagedid'] = array('header' => 'providerManagedID', 'sql' => 'o.occid AS providermanagedid', 'definition' => 'https://ac.tdwg.org/termlist/#ac_providerManagedID');
        return $occurFieldArr;
    }
}
