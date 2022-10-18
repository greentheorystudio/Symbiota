<?php
class DwcArchiverAttribute{

	public static function getFieldArr(): array
    {
		$fieldArr['coreid'] = 'o.occid';
		$termArr['measurementType'] = 'https://dwc.tdwg.org/terms/#measurementType';
		$fieldArr['measurementType'] = 'm.traitname';
		$termArr['measurementTypeID'] = 'http://rs.iobis.org/obis/terms/measurementTypeID';
		$fieldArr['measurementTypeID'] = 'm.refurl AS measurementTypeID';
		$termArr['measurementValue'] = 'https://dwc.tdwg.org/terms/#measurementValue';
		$fieldArr['measurementValue'] = 's.statename';
		$termArr['measurementValueID'] = 'http://rs.iobis.org/obis/terms/measurementValueID';
		$fieldArr['measurementValueID'] = 's.refurl AS measurementValueID';
		$termArr['measurementUnit'] = 'https://dwc.tdwg.org/terms/#measurementUnit';
		$fieldArr['measurementUnit'] = 'm.units';
		$termArr['measurementDeterminedDate'] = 'https://dwc.tdwg.org/terms/#measurementDeterminedDate';
		$fieldArr['measurementDeterminedDate'] = 'DATE_FORMAT(IFNULL(a.datelastmodified,a.initialtimestamp), "%Y-%m-%dT%TZ") AS detDate';
		$termArr['measurementDeterminedBy'] = 'https://dwc.tdwg.org/terms/#measurementDeterminedBy';
		$fieldArr['measurementDeterminedBy'] = 'u.username';
		$termArr['measurementRemarks'] = 'https://dwc.tdwg.org/terms/#measurementRemarks';
		$fieldArr['measurementRemarks'] = 'a.notes';
		
		$retArr['terms'] = $termArr;
		$retArr['fields'] = $fieldArr;
		return $retArr;
	}

	public static function getSql($fieldArr, $conditionSql): string
    {
		$sql = ''; 
		if($fieldArr && $conditionSql){
			$sqlFrag = '';
			foreach($fieldArr as $fieldName => $colName){
				if($colName) {
                    $sqlFrag .= ', ' . $colName;
                }
			}
			$sql = 'SELECT '.trim($sqlFrag,', ').
				' FROM tmtraits m INNER JOIN tmstates s ON m.traitid = s.traitid '.
				'INNER JOIN tmattributes a ON s.stateid = a.stateid '.
				'INNER JOIN users u ON a.createduid = u.uid '.
				'INNER JOIN omoccurrences o ON a.occid = o.occid ';
			if(strpos($conditionSql,'v.clid')){
				$sql .= 'LEFT JOIN fmvouchers v ON o.occid = v.occid ';
			}
			if(strpos($conditionSql,'p.point')){
				$sql .= 'LEFT JOIN omoccurpoints p ON o.occid = p.occid ';
			}
			if(strpos($conditionSql,'MATCH(f.recordedby)') || strpos($conditionSql,'MATCH(f.locality)')){
				$sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
			}
			$sql .= $conditionSql;
			$sql .= ' ORDER BY o.occid ';
			//echo '<div>'.$sql.'</div>'; exit;
		}
		return $sql;
	}
}
