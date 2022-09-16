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
        $occurTermArr['collId'] = '';
        $occurFieldArr['collId'] = 'c.collid';
        $occurTermArr['sourcePrimaryKey-dbpk'] = '';
        $occurFieldArr['sourcePrimaryKey-dbpk'] = 'o.dbpk';
        $occurTermArr['basisOfRecord'] = 'https://dwc.tdwg.org/terms/#basisOfRecord';
        $occurFieldArr['basisOfRecord'] = 'o.basisOfRecord';
        $occurTermArr['occurrenceID'] = 'https://dwc.tdwg.org/terms/#occurrenceID';
        $occurFieldArr['occurrenceID'] = 'o.occurrenceID';
        $occurTermArr['catalogNumber'] = 'https://dwc.tdwg.org/terms/#catalogNumber';
        $occurFieldArr['catalogNumber'] = 'o.catalogNumber';
        $occurTermArr['otherCatalogNumbers'] = 'https://dwc.tdwg.org/terms/#otherCatalogNumbers';
        $occurFieldArr['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
        $occurTermArr['ownerInstitutionCode'] = 'https://dwc.tdwg.org/terms/#ownerInstitutionCode';
        $occurFieldArr['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
        $occurTermArr['institutionID'] = 'https://dwc.tdwg.org/terms/#institutionID';
        $occurFieldArr['institutionID'] = 'o.institutionID';
        $occurTermArr['collectionID'] = 'https://dwc.tdwg.org/terms/#collectionID';
        $occurFieldArr['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
        $occurTermArr['datasetID'] = 'https://dwc.tdwg.org/terms/#datasetID';
        $occurFieldArr['datasetID'] = 'o.datasetID';
        $occurTermArr['institutionCode'] = 'https://dwc.tdwg.org/terms/#institutionCode';
        $occurFieldArr['institutionCode'] = 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode';
        $occurTermArr['collectionCode'] = 'https://dwc.tdwg.org/terms/#collectionCode';
        $occurFieldArr['collectionCode'] = 'IFNULL(o.collectionCode,c.collectionCode) AS collectionCode';
        $occurTermArr['kingdom'] = 'https://dwc.tdwg.org/terms/#kingdom';
        $occurFieldArr['kingdom'] = '';
        $occurTermArr['phylum'] = 'https://dwc.tdwg.org/terms/#phylum';
        $occurFieldArr['phylum'] = '';
        $occurTermArr['class'] = 'https://dwc.tdwg.org/terms/#class';
        $occurFieldArr['class'] = '';
        $occurTermArr['order'] = 'https://dwc.tdwg.org/terms/#order';
        $occurFieldArr['order'] = '';
        $occurTermArr['family'] = 'https://dwc.tdwg.org/terms/#family';
        $occurFieldArr['family'] = 'o.family';
        $occurTermArr['scientificName'] = 'https://dwc.tdwg.org/terms/#scientificName';
        $occurFieldArr['scientificName'] = 'o.sciname AS scientificName';
        $occurTermArr['taxonID'] = 'https://dwc.tdwg.org/terms/#taxonID';
        $occurFieldArr['taxonID'] = 'o.tidinterpreted as taxonID';
        $occurTermArr['genus'] = 'https://dwc.tdwg.org/terms/#genus';
        $occurFieldArr['genus'] = 'IF(t.rankid >= 180,CONCAT_WS(" ",t.unitind1,t.unitname1),NULL) AS genus';
        $occurTermArr['specificEpithet'] = 'https://dwc.tdwg.org/terms/#specificEpithet';
        $occurFieldArr['specificEpithet'] = 'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet';
        $occurTermArr['taxonRank'] = 'https://dwc.tdwg.org/terms/#taxonRank';
        $occurFieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
        $occurTermArr['infraspecificEpithet'] = 'https://dwc.tdwg.org/terms/#infraspecificEpithet';
        $occurFieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
        $occurTermArr['scientificNameAuthorship'] = 'https://dwc.tdwg.org/terms/#scientificNameAuthorship';
        $occurFieldArr['scientificNameAuthorship'] = 'IFNULL(t.author,o.scientificNameAuthorship) AS scientificNameAuthorship';
        $occurTermArr['taxonRemarks'] = 'https://dwc.tdwg.org/terms/#taxonRemarks';
        $occurFieldArr['taxonRemarks'] = 'o.taxonRemarks';
        $occurTermArr['identifiedBy'] = 'https://dwc.tdwg.org/terms/#identifiedBy';
        $occurFieldArr['identifiedBy'] = 'o.identifiedBy';
        $occurTermArr['dateIdentified'] = 'https://dwc.tdwg.org/terms/#dateIdentified';
        $occurFieldArr['dateIdentified'] = 'o.dateIdentified';
        $occurTermArr['identificationReferences'] = 'https://dwc.tdwg.org/terms/#identificationReferences';
        $occurFieldArr['identificationReferences'] = 'o.identificationReferences';
        $occurTermArr['identificationRemarks'] = 'https://dwc.tdwg.org/terms/#identificationRemarks';
        $occurFieldArr['identificationRemarks'] = 'o.identificationRemarks';
        $occurTermArr['identificationQualifier'] = 'https://dwc.tdwg.org/terms/#identificationQualifier';
        $occurFieldArr['identificationQualifier'] = 'o.identificationQualifier';
        $occurTermArr['typeStatus'] = 'https://dwc.tdwg.org/terms/#typeStatus';
        $occurFieldArr['typeStatus'] = 'o.typeStatus';
        $occurTermArr['recordedBy'] = 'https://dwc.tdwg.org/terms/#recordedBy';
        $occurFieldArr['recordedBy'] = 'o.recordedBy';
        $occurTermArr['recordNumber'] = 'https://dwc.tdwg.org/terms/#recordNumber';
        $occurFieldArr['recordNumber'] = 'o.recordNumber';
        $occurTermArr['recordedByID'] = '';
        $occurFieldArr['recordedByID'] = 'o.recordedById';
        $occurTermArr['associatedCollectors'] = '';
        $occurFieldArr['associatedCollectors'] = 'o.associatedCollectors';
        $occurTermArr['eventDate'] = 'https://dwc.tdwg.org/terms/#eventDate';
        $occurFieldArr['eventDate'] = 'o.eventDate';
        $occurTermArr['year'] = 'https://dwc.tdwg.org/terms/#year';
        $occurFieldArr['year'] = 'o.year';
        $occurTermArr['month'] = 'https://dwc.tdwg.org/terms/#month';
        $occurFieldArr['month'] = 'o.month';
        $occurTermArr['day'] = 'https://dwc.tdwg.org/terms/#day';
        $occurFieldArr['day'] = 'o.day';
        $occurTermArr['startDayOfYear'] = 'https://dwc.tdwg.org/terms/#startDayOfYear';
        $occurFieldArr['startDayOfYear'] = 'o.startDayOfYear';
        $occurTermArr['endDayOfYear'] = 'https://dwc.tdwg.org/terms/#endDayOfYear';
        $occurFieldArr['endDayOfYear'] = 'o.endDayOfYear';
        $occurTermArr['verbatimEventDate'] = 'https://dwc.tdwg.org/terms/#verbatimEventDate';
        $occurFieldArr['verbatimEventDate'] = 'o.verbatimEventDate';
        $occurTermArr['habitat'] = 'https://dwc.tdwg.org/terms/#habitat';
        $occurFieldArr['habitat'] = 'o.habitat';
        $occurTermArr['substrate'] = '';
        $occurFieldArr['substrate'] = 'o.substrate';
        $occurTermArr['fieldNotes'] = 'https://dwc.tdwg.org/terms/#fieldNotes';
        $occurFieldArr['fieldNotes'] = 'o.fieldNotes';
        $occurTermArr['fieldNumber'] = 'https://dwc.tdwg.org/terms/#fieldNumber';
        $occurFieldArr['fieldNumber'] = 'o.fieldNumber';
        $occurTermArr['eventID'] = 'https://dwc.tdwg.org/terms/#eventID';
        $occurFieldArr['eventID'] = 'o.eventID';
        $occurTermArr['occurrenceRemarks'] = 'https://dwc.tdwg.org/terms/#occurrenceRemarks';
        $occurFieldArr['occurrenceRemarks'] = 'o.occurrenceRemarks';
        $occurTermArr['informationWithheld'] = 'https://dwc.tdwg.org/terms/#informationWithheld';
        $occurFieldArr['informationWithheld'] = 'o.informationWithheld';
        $occurTermArr['dataGeneralizations'] = 'https://dwc.tdwg.org/terms/#dataGeneralizations';
        $occurFieldArr['dataGeneralizations'] = 'o.dataGeneralizations';
        $occurTermArr['associatedOccurrences'] = 'https://dwc.tdwg.org/terms/#associatedOccurrences';
        $occurFieldArr['associatedOccurrences'] = 'o.associatedOccurrences';
        $occurTermArr['associatedTaxa'] = 'https://dwc.tdwg.org/terms/#associatedTaxa';
        $occurFieldArr['associatedTaxa'] = 'o.associatedTaxa';
        $occurTermArr['dynamicProperties'] = 'https://dwc.tdwg.org/terms/#dynamicProperties';
        $occurFieldArr['dynamicProperties'] = 'o.dynamicProperties';
        $occurTermArr['verbatimAttributes'] = '';
        $occurFieldArr['verbatimAttributes'] = 'o.verbatimAttributes';
        $occurTermArr['behavior'] = 'https://dwc.tdwg.org/terms/#behavior';
        $occurFieldArr['behavior'] = 'o.behavior';
        $occurTermArr['reproductiveCondition'] = 'https://dwc.tdwg.org/terms/#reproductiveCondition';
        $occurFieldArr['reproductiveCondition'] = 'o.reproductiveCondition';
        $occurTermArr['cultivationStatus'] = '';
        $occurFieldArr['cultivationStatus'] = 'o.cultivationStatus';
        $occurTermArr['establishmentMeans'] = 'https://dwc.tdwg.org/terms/#establishmentMeans';
        $occurFieldArr['establishmentMeans'] = 'o.establishmentMeans';
        $occurTermArr['lifeStage'] = 'https://dwc.tdwg.org/terms/#lifeStage';
        $occurFieldArr['lifeStage'] = 'o.lifeStage';
        $occurTermArr['sex'] = 'https://dwc.tdwg.org/terms/#sex';
        $occurFieldArr['sex'] = 'o.sex';
        $occurTermArr['individualCount'] = 'https://dwc.tdwg.org/terms/#individualCount';
        $occurFieldArr['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
        $occurTermArr['samplingProtocol'] = 'https://dwc.tdwg.org/terms/#samplingProtocol';
        $occurFieldArr['samplingProtocol'] = 'o.samplingProtocol';
        $occurTermArr['samplingEffort'] = 'https://dwc.tdwg.org/terms/#samplingEffort';
        $occurFieldArr['samplingEffort'] = 'o.samplingEffort';
        $occurTermArr['preparations'] = 'https://dwc.tdwg.org/terms/#preparations';
        $occurFieldArr['preparations'] = 'o.preparations';
        $occurTermArr['locationID'] = 'https://dwc.tdwg.org/terms/#locationID';
        $occurFieldArr['locationID'] = 'o.locationID';
        $occurTermArr['waterBody'] = 'https://dwc.tdwg.org/terms/#waterBody';
        $occurFieldArr['waterBody'] = 'o.waterBody';
        $occurTermArr['country'] = 'https://dwc.tdwg.org/terms/#country';
        $occurFieldArr['country'] = 'o.country';
        $occurTermArr['stateProvince'] = 'https://dwc.tdwg.org/terms/#stateProvince';
        $occurFieldArr['stateProvince'] = 'o.stateProvince';
        $occurTermArr['county'] = 'https://dwc.tdwg.org/terms/#county';
        $occurFieldArr['county'] = 'o.county';
        $occurTermArr['municipality'] = 'https://dwc.tdwg.org/terms/#municipality';
        $occurFieldArr['municipality'] = 'o.municipality';
        $occurTermArr['locality'] = 'https://dwc.tdwg.org/terms/#locality';
        $occurFieldArr['locality'] = 'o.locality';
        $occurTermArr['localitySecurity'] = '';
        $occurFieldArr['localitySecurity'] = 'o.localitySecurity';
        $occurTermArr['localitySecurityReason'] = '';
        $occurFieldArr['localitySecurityReason'] = 'o.localitySecurityReason';
        $occurTermArr['decimalLatitude'] = 'https://dwc.tdwg.org/terms/#decimalLatitude';
        $occurFieldArr['decimalLatitude'] = 'o.decimalLatitude';
        $occurTermArr['decimalLongitude'] = 'https://dwc.tdwg.org/terms/#decimalLongitude';
        $occurFieldArr['decimalLongitude'] = 'o.decimalLongitude';
        $occurTermArr['geodeticDatum'] = 'https://dwc.tdwg.org/terms/#geodeticDatum';
        $occurFieldArr['geodeticDatum'] = 'o.geodeticDatum';
        $occurTermArr['coordinateUncertaintyInMeters'] = 'https://dwc.tdwg.org/terms/#coordinateUncertaintyInMeters';
        $occurFieldArr['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
        $occurTermArr['footprintWKT'] = 'https://dwc.tdwg.org/terms/#footprintWKT';
        $occurFieldArr['footprintWKT'] = 'o.footprintWKT';
        $occurTermArr['coordinatePrecision'] = 'https://dwc.tdwg.org/terms/#coordinatePrecision';
        $occurFieldArr['coordinatePrecision'] = 'o.coordinatePrecision';
        $occurTermArr['locationRemarks'] = 'https://dwc.tdwg.org/terms/#locationRemarks';
        $occurFieldArr['locationRemarks'] = 'o.locationremarks';
        $occurTermArr['verbatimCoordinates'] = 'https://dwc.tdwg.org/terms/#verbatimCoordinates';
        $occurFieldArr['verbatimCoordinates'] = 'o.verbatimCoordinates';
        $occurTermArr['verbatimCoordinateSystem'] = 'https://dwc.tdwg.org/terms/#verbatimCoordinateSystem';
        $occurFieldArr['verbatimCoordinateSystem'] = 'o.verbatimCoordinateSystem';
        $occurTermArr['georeferencedBy'] = 'https://dwc.tdwg.org/terms/#georeferencedBy';
        $occurFieldArr['georeferencedBy'] = 'o.georeferencedBy';
        $occurTermArr['georeferenceProtocol'] = 'https://dwc.tdwg.org/terms/#georeferenceProtocol';
        $occurFieldArr['georeferenceProtocol'] = 'o.georeferenceProtocol';
        $occurTermArr['georeferenceSources'] = 'https://dwc.tdwg.org/terms/#georeferenceSources';
        $occurFieldArr['georeferenceSources'] = 'o.georeferenceSources';
        $occurTermArr['georeferenceVerificationStatus'] = 'https://dwc.tdwg.org/terms/#georeferenceVerificationStatus';
        $occurFieldArr['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
        $occurTermArr['georeferenceRemarks'] = 'https://dwc.tdwg.org/terms/#georeferenceRemarks';
        $occurFieldArr['georeferenceRemarks'] = 'o.georeferenceRemarks';
        $occurTermArr['minimumElevationInMeters'] = 'https://dwc.tdwg.org/terms/#minimumElevationInMeters';
        $occurFieldArr['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
        $occurTermArr['maximumElevationInMeters'] = 'https://dwc.tdwg.org/terms/#maximumElevationInMeters';
        $occurFieldArr['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
        $occurTermArr['verbatimElevation'] = 'https://dwc.tdwg.org/terms/#verbatimElevation';
        $occurFieldArr['verbatimElevation'] = 'o.verbatimElevation';
        $occurTermArr['minimumDepthInMeters'] = 'https://dwc.tdwg.org/terms/#minimumDepthInMeters';
        $occurFieldArr['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
        $occurTermArr['maximumDepthInMeters'] = 'https://dwc.tdwg.org/terms/#maximumDepthInMeters';
        $occurFieldArr['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
        $occurTermArr['verbatimDepth'] = 'https://dwc.tdwg.org/terms/#verbatimDepth';
        $occurFieldArr['verbatimDepth'] = 'o.verbatimDepth';
        $occurTermArr['disposition'] = 'https://dwc.tdwg.org/terms/#disposition';
        $occurFieldArr['disposition'] = 'o.disposition';
        $occurTermArr['storageLocation'] = '';
        $occurFieldArr['storageLocation'] = 'o.storageLocation';
        $occurTermArr['genericcolumn1'] = '';
        $occurFieldArr['genericcolumn1'] = 'o.genericcolumn1';
        $occurTermArr['genericcolumn2'] = '';
        $occurFieldArr['genericcolumn2'] = 'o.genericcolumn2';
        $occurTermArr['modified'] = '';
        $occurFieldArr['modified'] = 'IFNULL(o.modified,o.datelastmodified) AS modified';
        $occurTermArr['language'] = '';
        $occurFieldArr['language'] = 'o.language';
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
        $occurTermArr['dateLastModified'] = 'https://dwc.tdwg.org/terms/#dateLastModified';
        $occurFieldArr['dateLastModified'] = 'o.datelastmodified';
		$occurTermArr['rights'] = '';
		$occurFieldArr['rights'] = 'c.rights';
		$occurTermArr['rightsHolder'] = 'https://dwc.tdwg.org/terms/#rightsHolder';
		$occurFieldArr['rightsHolder'] = 'c.rightsHolder';
		$occurTermArr['accessRights'] = 'https://dwc.tdwg.org/terms/#accessRights';
		$occurFieldArr['accessRights'] = 'c.accessRights';
		$occurTermArr['recordId'] = '';
		$occurFieldArr['recordId'] = 'g.guid AS recordId';
		$occurTermArr['references'] = 'https://dwc.tdwg.org/terms/#references';
		$occurFieldArr['references'] = '';
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
			$trimArr = array('recordedByID','associatedCollectors','substrate','verbatimAttributes','cultivationStatus',
				'localitySecurityReason','genericcolumn1','genericcolumn2','storageLocation','observerUid','processingStatus',
				'duplicateQuantity','dateEntered','dateLastModified','sourcePrimaryKey-dbpk','host','labelProject','dynamicFields');
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
			$sql .= ' FROM (omcollections c INNER JOIN omoccurrences o ON c.collid = o.collid) '.
				'INNER JOIN guidoccurrences g ON o.occid = g.occid '.
				'LEFT JOIN taxa t ON o.tidinterpreted = t.TID ';
			$sql .= $tableJoinStr.$conditionSql;
			if($fullSql) {
                $sql .= ' ORDER BY c.collid ';
            }
			//echo '<div>'.$sql.'</div>'; exit;
		}
		return $sql;
	}
}
