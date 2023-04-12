<?php
class DwcArchiverOccurrence{

	public static function getOccurrenceArr($schemaType, $extended): array
    {
        if($schemaType === 'pensoft'){
        	$occurFieldArr['Taxon_Local_ID'] = 'v.tid AS Taxon_Local_ID';
        }
        else{
        	$occurFieldArr['id'] = 'o.occid';
        }
        $occurTermArr['institutionCode'] = 'https://dwc.tdwg.org/list/#dwc_institutionCode';
        $occurFieldArr['institutionCode'] = 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode';
        $occurTermArr['collectionCode'] = 'https://dwc.tdwg.org/list/#dwc_collectionCode';
        $occurFieldArr['collectionCode'] = 'IFNULL(o.collectionCode,c.collectionCode) AS collectionCode';
        $occurTermArr['collectionID'] = 'https://dwc.tdwg.org/list/#dwc_collectionID';
        $occurFieldArr['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
        $occurTermArr['ownerInstitutionCode'] = 'https://dwc.tdwg.org/list/#dwc_ownerInstitutionCode';
        $occurFieldArr['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
        $occurTermArr['institutionID'] = 'https://dwc.tdwg.org/list/#dwc_institutionID';
        $occurFieldArr['institutionID'] = 'o.institutionID';
        $occurTermArr['datasetID'] = 'https://dwc.tdwg.org/list/#dwc_datasetID';
        $occurFieldArr['datasetID'] = 'o.datasetID';
        $occurTermArr['basisOfRecord'] = 'https://dwc.tdwg.org/list/#dwc_basisOfRecord';
        $occurFieldArr['basisOfRecord'] = 'o.basisOfRecord';
        $occurTermArr['occurrenceID'] = 'https://dwc.tdwg.org/list/#dwc_occurrenceID';
        $occurFieldArr['occurrenceID'] = 'o.occurrenceID';
        $occurTermArr['catalogNumber'] = 'https://dwc.tdwg.org/list/#dwc_catalogNumber';
        $occurFieldArr['catalogNumber'] = 'o.catalogNumber';
        $occurTermArr['otherCatalogNumbers'] = 'https://dwc.tdwg.org/list/#dwc_otherCatalogNumbers';
        $occurFieldArr['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
        $occurTermArr['kingdom'] = 'https://dwc.tdwg.org/list/#dwc_kingdom';
        $occurFieldArr['kingdom'] = '';
        $occurTermArr['phylum'] = 'https://dwc.tdwg.org/list/#dwc_phylum';
        $occurFieldArr['phylum'] = '';
        $occurTermArr['class'] = 'https://dwc.tdwg.org/list/#dwc_class';
        $occurFieldArr['class'] = '';
        $occurTermArr['order'] = 'https://dwc.tdwg.org/list/#dwc_order';
        $occurFieldArr['order'] = '';
        $occurTermArr['family'] = 'https://dwc.tdwg.org/list/#dwc_family';
        $occurFieldArr['family'] = 'o.family';
        $occurTermArr['scientificName'] = 'https://dwc.tdwg.org/list/#dwc_scientificName';
        $occurFieldArr['scientificName'] = 'o.sciname AS scientificName';
        $occurTermArr['taxonID'] = 'https://dwc.tdwg.org/list/#dwc_taxonID';
        $occurFieldArr['taxonID'] = 't.tidaccepted as taxonID';
        $occurTermArr['scientificNameAuthorship'] = 'https://dwc.tdwg.org/list/#dwc_scientificNameAuthorship';
        $occurFieldArr['scientificNameAuthorship'] = 'IFNULL(t.author,o.scientificNameAuthorship) AS scientificNameAuthorship';
        $occurTermArr['genus'] = 'https://dwc.tdwg.org/list/#dwc_genus';
        $occurFieldArr['genus'] = 'IF(t.rankid >= 180,CONCAT_WS(" ",t.unitind1,t.unitname1),NULL) AS genus';
        $occurTermArr['specificEpithet'] = 'https://dwc.tdwg.org/list/#dwc_specificEpithet';
        $occurFieldArr['specificEpithet'] = 'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet';
        $occurTermArr['infraspecificEpithet'] = 'https://dwc.tdwg.org/list/#dwc_infraspecificEpithet';
        $occurFieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $occurTermArr['taxonRank'] = 'https://dwc.tdwg.org/list/#dwc_taxonRank';
        $occurFieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $occurTermArr['identifiedBy'] = 'https://dwc.tdwg.org/list/#dwc_identifiedBy';
        $occurFieldArr['identifiedBy'] = 'o.identifiedBy';
        $occurTermArr['dateIdentified'] = 'https://dwc.tdwg.org/list/#dwc_dateIdentified';
        $occurFieldArr['dateIdentified'] = 'o.dateIdentified';
        $occurTermArr['identificationReferences'] = 'https://dwc.tdwg.org/list/#dwc_identificationReferences';
        $occurFieldArr['identificationReferences'] = 'o.identificationReferences';
        $occurTermArr['identificationRemarks'] = 'https://dwc.tdwg.org/list/#dwc_identificationRemarks';
        $occurFieldArr['identificationRemarks'] = 'o.identificationRemarks';
        $occurTermArr['taxonRemarks'] = 'https://dwc.tdwg.org/list/#dwc_taxonRemarks';
        $occurFieldArr['taxonRemarks'] = 'o.taxonRemarks';
        $occurTermArr['identificationQualifier'] = 'https://dwc.tdwg.org/list/#dwc_identificationQualifier';
        $occurFieldArr['identificationQualifier'] = 'o.identificationQualifier';
        $occurTermArr['typeStatus'] = 'https://dwc.tdwg.org/list/#dwc_typeStatus';
        $occurFieldArr['typeStatus'] = 'o.typeStatus';
        $occurTermArr['recordedBy'] = 'https://dwc.tdwg.org/list/#dwc_recordedBy';
        $occurFieldArr['recordedBy'] = 'o.recordedBy';
        $occurTermArr['recordNumber'] = 'https://dwc.tdwg.org/list/#dwc_recordNumber';
        $occurFieldArr['recordNumber'] = 'o.recordNumber';
        $occurTermArr['eventDate'] = 'https://dwc.tdwg.org/list/#dwc_eventDate';
        $occurFieldArr['eventDate'] = 'o.eventDate';
        $occurTermArr['year'] = 'https://dwc.tdwg.org/list/#dwc_year';
        $occurFieldArr['year'] = 'o.year';
        $occurTermArr['month'] = 'https://dwc.tdwg.org/list/#dwc_month';
        $occurFieldArr['month'] = 'o.month';
        $occurTermArr['day'] = 'https://dwc.tdwg.org/list/#dwc_day';
        $occurFieldArr['day'] = 'o.day';
        $occurTermArr['startDayOfYear'] = 'https://dwc.tdwg.org/list/#dwc_startDayOfYear';
        $occurFieldArr['startDayOfYear'] = 'o.startDayOfYear';
        $occurTermArr['endDayOfYear'] = 'https://dwc.tdwg.org/list/#dwc_endDayOfYear';
        $occurFieldArr['endDayOfYear'] = 'o.endDayOfYear';
        $occurTermArr['verbatimEventDate'] = 'https://dwc.tdwg.org/list/#dwc_verbatimEventDate';
        $occurFieldArr['verbatimEventDate'] = 'o.verbatimEventDate';
        $occurTermArr['occurrenceRemarks'] = 'https://dwc.tdwg.org/list/#dwc_occurrenceRemarks';
        $occurFieldArr['occurrenceRemarks'] = 'o.occurrenceRemarks';
        $occurTermArr['habitat'] = 'https://dwc.tdwg.org/list/#dwc_habitat';
        $occurFieldArr['habitat'] = 'o.habitat';
        $occurTermArr['fieldNumber'] = 'https://dwc.tdwg.org/list/#dwc_fieldNumber';
        $occurFieldArr['fieldNumber'] = 'o.fieldNumber';
        $occurTermArr['fieldNotes'] = 'https://dwc.tdwg.org/list/#dwc_fieldNotes';
        $occurFieldArr['fieldNotes'] = 'o.fieldNotes';
        $occurTermArr['samplingProtocol'] = 'https://dwc.tdwg.org/list/#dwc_samplingProtocol';
        $occurFieldArr['samplingProtocol'] = 'o.samplingProtocol';
        $occurTermArr['samplingEffort'] = 'https://dwc.tdwg.org/list/#dwc_samplingEffort';
        $occurFieldArr['samplingEffort'] = 'o.samplingEffort';
        $occurTermArr['eventID'] = 'https://dwc.tdwg.org/list/#dwc_eventID';
        $occurFieldArr['eventID'] = 'o.eventID';
        $occurTermArr['informationWithheld'] = 'https://dwc.tdwg.org/list/#dwc_informationWithheld';
        $occurFieldArr['informationWithheld'] = 'o.informationWithheld';
        $occurTermArr['dataGeneralizations'] = 'https://dwc.tdwg.org/list/#dwc_dataGeneralizations';
        $occurFieldArr['dataGeneralizations'] = 'o.dataGeneralizations';
        $occurTermArr['dynamicProperties'] = 'https://dwc.tdwg.org/list/#dwc_dynamicProperties';
        $occurFieldArr['dynamicProperties'] = 'o.dynamicProperties';
        $occurTermArr['associatedOccurrences'] = 'https://dwc.tdwg.org/list/#dwc_associatedOccurrences';
        $occurFieldArr['associatedOccurrences'] = 'o.associatedOccurrences';
        $occurTermArr['associatedTaxa'] = 'https://dwc.tdwg.org/list/#dwc_associatedTaxa';
        $occurFieldArr['associatedTaxa'] = 'o.associatedTaxa';
        $occurTermArr['reproductiveCondition'] = 'https://dwc.tdwg.org/list/#dwc_reproductiveCondition';
        $occurFieldArr['reproductiveCondition'] = 'o.reproductiveCondition';
        $occurTermArr['establishmentMeans'] = 'https://dwc.tdwg.org/list/#dwc_establishmentMeans';
        $occurFieldArr['establishmentMeans'] = 'o.establishmentMeans';
        $occurTermArr['lifeStage'] = 'https://dwc.tdwg.org/list/#dwc_lifeStage';
        $occurFieldArr['lifeStage'] = 'o.lifeStage';
        $occurTermArr['sex'] = 'https://dwc.tdwg.org/list/#dwc_sex';
        $occurFieldArr['sex'] = 'o.sex';
        $occurTermArr['behavior'] = 'https://dwc.tdwg.org/list/#dwc_behavior';
        $occurFieldArr['behavior'] = 'o.behavior';
        $occurTermArr['individualCount'] = 'https://dwc.tdwg.org/list/#dwc_individualCount';
        $occurFieldArr['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
        $occurTermArr['preparations'] = 'https://dwc.tdwg.org/list/#dwc_preparations';
        $occurFieldArr['preparations'] = 'o.preparations';
        $occurTermArr['locationID'] = 'https://dwc.tdwg.org/list/#dwc_locationID';
        $occurFieldArr['locationID'] = 'o.locationID';
        $occurTermArr['waterBody'] = 'https://dwc.tdwg.org/list/#dwc_waterBody';
        $occurFieldArr['waterBody'] = 'o.waterBody';
        $occurTermArr['country'] = 'https://dwc.tdwg.org/list/#dwc_country';
        $occurFieldArr['country'] = 'o.country';
        $occurTermArr['stateProvince'] = 'https://dwc.tdwg.org/list/#dwc_stateProvince';
        $occurFieldArr['stateProvince'] = 'o.stateProvince';
        $occurTermArr['county'] = 'https://dwc.tdwg.org/list/#dwc_county';
        $occurFieldArr['county'] = 'o.county';
        $occurTermArr['municipality'] = 'https://dwc.tdwg.org/list/#dwc_municipality';
        $occurFieldArr['municipality'] = 'o.municipality';
        $occurTermArr['locality'] = 'https://dwc.tdwg.org/list/#dwc_locality';
        $occurFieldArr['locality'] = 'o.locality';
        $occurTermArr['locationRemarks'] = 'https://dwc.tdwg.org/list/#dwc_locationRemarks';
        $occurFieldArr['locationRemarks'] = 'o.locationremarks';
        $occurTermArr['decimalLatitude'] = 'https://dwc.tdwg.org/list/#dwc_decimalLatitude';
        $occurFieldArr['decimalLatitude'] = 'o.decimalLatitude';
        $occurTermArr['decimalLongitude'] = 'https://dwc.tdwg.org/list/#dwc_decimalLongitude';
        $occurFieldArr['decimalLongitude'] = 'o.decimalLongitude';
        $occurTermArr['geodeticDatum'] = 'https://dwc.tdwg.org/list/#dwc_geodeticDatum';
        $occurFieldArr['geodeticDatum'] = 'o.geodeticDatum';
        $occurTermArr['coordinateUncertaintyInMeters'] = 'https://dwc.tdwg.org/list/#dwc_coordinateUncertaintyInMeters';
        $occurFieldArr['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
        $occurTermArr['coordinatePrecision'] = 'https://dwc.tdwg.org/list/#dwc_coordinatePrecision';
        $occurFieldArr['coordinatePrecision'] = 'o.coordinatePrecision';
        $occurTermArr['verbatimCoordinateSystem'] = 'https://dwc.tdwg.org/list/#dwc_verbatimCoordinateSystem';
        $occurFieldArr['verbatimCoordinateSystem'] = 'o.verbatimCoordinateSystem';
        $occurTermArr['verbatimCoordinates'] = 'https://dwc.tdwg.org/list/#dwc_verbatimCoordinates';
        $occurFieldArr['verbatimCoordinates'] = 'o.verbatimCoordinates';
        $occurTermArr['georeferencedBy'] = 'https://dwc.tdwg.org/list/#dwc_georeferencedBy';
        $occurFieldArr['georeferencedBy'] = 'o.georeferencedBy';
        $occurTermArr['georeferenceProtocol'] = 'https://dwc.tdwg.org/list/#dwc_georeferenceProtocol';
        $occurFieldArr['georeferenceProtocol'] = 'o.georeferenceProtocol';
        $occurTermArr['georeferenceSources'] = 'https://dwc.tdwg.org/list/#dwc_georeferenceSources';
        $occurFieldArr['georeferenceSources'] = 'o.georeferenceSources';
        $occurTermArr['georeferenceVerificationStatus'] = 'https://dwc.tdwg.org/list/#dwc_georeferenceVerificationStatus';
        $occurFieldArr['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
        $occurTermArr['georeferenceRemarks'] = 'https://dwc.tdwg.org/list/#dwc_georeferenceRemarks';
        $occurFieldArr['georeferenceRemarks'] = 'o.georeferenceRemarks';
        $occurTermArr['minimumElevationInMeters'] = 'https://dwc.tdwg.org/list/#dwc_minimumElevationInMeters';
        $occurFieldArr['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
        $occurTermArr['maximumElevationInMeters'] = 'https://dwc.tdwg.org/list/#dwc_maximumElevationInMeters';
        $occurFieldArr['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
        $occurTermArr['minimumDepthInMeters'] = 'https://dwc.tdwg.org/list/#dwc_minimumDepthInMeters';
        $occurFieldArr['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
        $occurTermArr['maximumDepthInMeters'] = 'https://dwc.tdwg.org/list/#dwc_maximumDepthInMeters';
        $occurFieldArr['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
        $occurTermArr['verbatimDepth'] = 'https://dwc.tdwg.org/list/#dwc_verbatimDepth';
        $occurFieldArr['verbatimDepth'] = 'o.verbatimDepth';
        $occurTermArr['verbatimElevation'] = 'https://dwc.tdwg.org/list/#dwc_verbatimElevation';
        $occurFieldArr['verbatimElevation'] = 'o.verbatimElevation';
        $occurTermArr['disposition'] = 'https://dwc.tdwg.org/list/#dwc_disposition';
        $occurFieldArr['disposition'] = 'o.disposition';
        $occurTermArr['language'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#language';
        $occurFieldArr['language'] = 'o.language';
        $occurTermArr['modified'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#modified';
        $occurFieldArr['modified'] = 'IFNULL(o.modified,o.datelastmodified) AS modified';
        $occurTermArr['rights'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/elements11/rights/';
        $occurFieldArr['rights'] = 'c.rights';
        $occurTermArr['rightsHolder'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#rightsHolder';
        $occurFieldArr['rightsHolder'] = 'c.rightsHolder';
        $occurTermArr['accessRights'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#accessRights';
        $occurFieldArr['accessRights'] = 'c.accessRights';
        $occurTermArr['references'] = 'https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#references';
        $occurFieldArr['references'] = '';
        $occurTermArr['recordId'] = 'https://biokic.github.io/symbiota-docs/editor/edit/fields/';
        $occurFieldArr['recordId'] = 'g.guid AS recordId';
        $occurTermArr['collId'] = '';
        $occurFieldArr['collId'] = 'c.collid';
        $occurTermArr['sourcePrimaryKey-dbpk'] = '';
        $occurFieldArr['sourcePrimaryKey-dbpk'] = 'o.dbpk';
        $occurTermArr['recordedByID'] = '';
        $occurFieldArr['recordedByID'] = 'o.recordedById';
        $occurTermArr['associatedCollectors'] = '';
        $occurFieldArr['associatedCollectors'] = 'o.associatedCollectors';
        $occurTermArr['substrate'] = '';
        $occurFieldArr['substrate'] = 'o.substrate';
        $occurTermArr['verbatimAttributes'] = '';
        $occurFieldArr['verbatimAttributes'] = 'o.verbatimAttributes';
        $occurTermArr['cultivationStatus'] = '';
        $occurFieldArr['cultivationStatus'] = 'o.cultivationStatus';
        $occurTermArr['localitySecurity'] = '';
        $occurFieldArr['localitySecurity'] = 'o.localitySecurity';
        $occurTermArr['localitySecurityReason'] = '';
        $occurFieldArr['localitySecurityReason'] = 'o.localitySecurityReason';
        $occurTermArr['footprintWKT'] = 'https://dwc.tdwg.org/list/#dwc_footprintWKT';
        $occurFieldArr['footprintWKT'] = 'o.footprintWKT';
        $occurTermArr['storageLocation'] = '';
        $occurFieldArr['storageLocation'] = 'o.storageLocation';
        $occurTermArr['genericcolumn1'] = '';
        $occurFieldArr['genericcolumn1'] = 'o.genericcolumn1';
        $occurTermArr['genericcolumn2'] = '';
        $occurFieldArr['genericcolumn2'] = 'o.genericcolumn2';
        $occurTermArr['observerUid'] = '';
        $occurFieldArr['observerUid'] = 'o.observeruid';
        $occurTermArr['processingStatus'] = '';
        $occurFieldArr['processingStatus'] = 'o.processingstatus';
        $occurTermArr['recordEnteredBy'] = '';
        $occurFieldArr['recordEnteredBy'] = 'o.recordEnteredBy';
        $occurTermArr['duplicateQuantity'] = '';
        $occurFieldArr['duplicateQuantity'] = 'o.duplicateQuantity';
        $occurTermArr['labelProject'] = '';
        $occurFieldArr['labelProject'] = 'o.labelProject';
        $occurTermArr['dynamicFields'] = '';
        $occurFieldArr['dynamicFields'] = 'o.dynamicFields';
        $occurTermArr['dateEntered'] = '';
        $occurFieldArr['dateEntered'] = 'o.dateEntered';
        $occurTermArr['dateLastModified'] = 'https://dwc.tdwg.org/list/#dwc_dateLastModified';
        $occurFieldArr['dateLastModified'] = 'o.datelastmodified';
		if($schemaType === 'pensoft'){
			$occurFieldArr['occid'] = 'o.occid';
		}

		$occurrenceFieldArr['terms'] = self::trimOccurrenceBySchemaType($occurTermArr, $schemaType, $extended);
		$occurFieldArr = self::trimOccurrenceBySchemaType($occurFieldArr, $schemaType, $extended);
		if($schemaType === 'dwc' || $schemaType === 'pensoft'){
			$occurFieldArr['recordedBy'] = 'CONCAT_WS("; ",o.recordedBy,o.associatedCollectors) AS recordedBy';
			$occurFieldArr['occurrenceRemarks'] = 'CONCAT_WS("; ",o.occurrenceRemarks,o.verbatimAttributes) AS occurrenceRemarks';
			$occurFieldArr['habitat'] = 'CONCAT_WS("; ",o.habitat, o.substrate) AS habitat';
		}
		$occurrenceFieldArr['fields'] = $occurFieldArr;
		return $occurrenceFieldArr;
	}

	private static function trimOccurrenceBySchemaType($occurArr, $schemaType, $extended): array
    {
		$retArr = array();
		if($schemaType === 'dwc' || $schemaType === 'pensoft'){
			$trimArr = array('sourcePrimaryKey-dbpk','recordedByID','associatedCollectors','associatedCollectors','substrate',
                'verbatimAttributes','cultivationStatus','localitySecurityReason','footprintWKT','storageLocation',
                'genericcolumn1','genericcolumn2','observerUid','processingStatus','recordEnteredBy','duplicateQuantity','labelProject',
                'dynamicFields','dateEntered','dateLastModified');
			$retArr = array_diff_key($occurArr,array_flip($trimArr));
		}
		elseif($schemaType === 'native'){
			$trimArr = array();
			if(!$extended){
				$trimArr = array('collectionID','rights','rightsHolder','accessRights','genericcolumn1','genericcolumn2',
					'storageLocation','observerUid','processingStatus','duplicateQuantity','dateEntered','dateLastModified');
			}
			$retArr = array_diff_key($occurArr,array_flip($trimArr));
		}
		elseif($schemaType === 'backup'){
			$trimArr = array();
			$retArr = array_diff_key($occurArr,array_flip($trimArr));
		}
		elseif($schemaType === 'coge'){
			$targetArr = array('id','basisOfRecord','institutionCode','collectionCode','catalogNumber','occurrenceID','family','scientificName','scientificNameAuthorship',
				'kingdom','phylum','class','order','genus','specificEpithet','infraSpecificEpithet',
				'recordedBy','recordNumber','eventDate','year','month','day','fieldNumber','country','stateProvince','county','municipality',
				'locality','localitySecurity','geodeticDatum','decimalLatitude','decimalLongitude','verbatimCoordinates',
				'minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','maximumDepthInMeters','minimumDepthInMeters',
				'sex','occurrenceRemarks','preparationType','individualCount','dateEntered','dateLastModified','recordId','references','collId');
			$retArr = array_intersect_key($occurArr,array_flip($targetArr));
		}
		return $retArr;
	}

	public static function getSqlOccurrences($fieldArr, $conditionSql, $tableJoinStr, $fullSql): string
    {
		$sql = '';
		if($conditionSql){
			if($fullSql){
				$sqlFrag = '';
				foreach($fieldArr as $fieldName => $colName){
					if($colName){
						$sqlFrag .= ', '.$colName;
					}
					else{
						$sqlFrag .= ', "" AS t_'.$fieldName;
					}
				}
				$sql = 'SELECT DISTINCT '.trim($sqlFrag,', ');
			}
			$sql .= ' FROM omcollections AS c INNER JOIN omoccurrences AS o ON c.collid = o.collid '.
				'INNER JOIN guidoccurrences AS g ON o.occid = g.occid '.
				'LEFT JOIN taxa AS t ON o.tid = t.TID ';
			$sql .= $tableJoinStr.$conditionSql;
			if($fullSql) {
                $sql .= ' ORDER BY c.collid ';
            }
			//echo '<div>'.$sql.'</div>'; exit;
		}
		return $sql;
	}
}
