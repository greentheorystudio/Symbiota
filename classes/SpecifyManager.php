<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceUtilities.php');

class SpecifyManager {

	private $conn;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
    }

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
    }

    public function getSpecifyTotal(): int
    {
        $specifyCnt = 0;
        $sql = 'SELECT COUNT(DISTINCT CollectionObjectID) as cnt '.
            'FROM collectionobject ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $specifyCnt = $r->cnt;
        }
        $rs->close();
        return $specifyCnt;
    }

	public function uploadSpecifyRecords($limit,$index)
	{
		$limitBottom = 0;
		$totalUpload = 0;
		$intervalUpload = 0;
        $collectionObjectIDArr = array();
		if($index > 1){
            $limitBottom = $limit * $index;
        }
		$sql = 'SELECT co.CollectionObjectID, co.GUID, co.AltCatalogNumber, co.FieldNumber, co.CollectingEventID, '.
            'CONCAT_WS(" ",co.Remarks,coa.Remarks) AS occurrenceRemarks '.
			'FROM collectionobject AS co LEFT JOIN collectionobjectattribute AS coa ON co.CollectionObjectAttributeID = coa.CollectionObjectAttributeID '.
            'LIMIT ' . $limitBottom . ',' . $limit;
		$rs = $this->conn->query($sql);
		//echo $sql;
        while($r = $rs->fetch_object()){
			$coreId = $r->CollectionObjectID;
            if($coreId){
                $collectingEventId = $r->CollectingEventID;
                $occurrenceArr = array();
                $determinationArr = array();
                $imageArr = array();
                $collectorStr = '';
                $associatedCollectorStr = '';
                $habitatStr = '';

                $occurrenceArr['occurrenceid'] = $r->GUID;
                $occurrenceArr['catalognumber'] = $r->AltCatalogNumber;
                $occurrenceArr['recordnumber'] = $r->FieldNumber;
                $occurrenceArr['occurrenceremarks'] = $r->occurrenceRemarks;

                $colSql = 'SELECT a.FirstName, a.MiddleInitial, a.LastName, c.IsPrimary '.
                    'FROM collector AS c LEFT JOIN agent AS a ON c.AgentID = a.AgentID '.
                    'WHERE c.CollectingEventID = '.$collectingEventId.' '.
                    'ORDER BY c.OrderNumber ';
                $colRs = $this->conn->query($colSql);
                //echo $colSql;
                while($colR = $colRs->fetch_object()){
                    $primary = (int)$colR->IsPrimary;
                    $collector = $colR->MiddleInitial;
                    if($colR->FirstName){
                        $collector = $colR->FirstName . ($collector ? ' ' . $collector : '');
                    }
                    if($colR->LastName){
                        $collector = $colR->LastName . ($collector ? ', ' . $collector : '');
                    }
                    if($primary === 1){
                        $collectorStr = ($collectorStr ? $collectorStr . ', ' : '') . $collector;
                    }
                    else{
                        $associatedCollectorStr = ($associatedCollectorStr ? $associatedCollectorStr . ', ' : '') . $collector;
                    }
                }
                $colRs->close();

                $occurrenceArr['recordedby'] = $collectorStr;
                $occurrenceArr['associatedcollectors'] = $associatedCollectorStr;

                $colEvSql = 'SELECT ce.LocalityID, ce.StartDate, ce.StartDateVerbatim, ce.VerbatimLocality, ce.Remarks '.
                    'FROM collectingevent AS ce '.
                    'WHERE ce.CollectingEventID = '.$collectingEventId.' ';
                $colEvRs = $this->conn->query($colEvSql);
                while($colEvR = $colEvRs->fetch_object()){
                    $localityId = $colEvR->LocalityID;
                    $occurrenceArr['eventdate'] = (($colEvR->StartDate && $colEvR->StartDate !== '')?$colEvR->StartDate:'0000-00-00');
                    $occurrenceArr['verbatimeventdate'] = $colEvR->StartDateVerbatim;
                    $habitatStr = $colEvR->VerbatimLocality;
                    if($colEvR->Remarks && $colEvR->Remarks !== ''){
                        $habitatStr = ' ' . $colEvR->Remarks;
                    }

                    $locSql = 'SELECT geo1.RankID AS geo1RankId, geo1.`Name` AS geo1Name, geo2.RankID AS geo2RankId, geo2.`Name` AS geo2Name, '.
                        'geo3.RankID AS geo3RankId, geo3.`Name` AS geo3Name, loc.LocalityName, loc.Remarks, loc.Latitude1, loc.Latitude2, '.
                        'loc.Longitude1, loc.Longitude2, ld.Township, ld.TownshipDirection, ld.RangeDesc, ld.RangeDirection, ld.Section, '.
                        'ld.SectionPart, ld.WaterBody, ld.UtmNorthing, ld.UtmEasting, ld.UtmDatum, ld.RangeDirection, ld.Section, loc.Datum, '.
                        'loc.Lat1Text, loc.Lat2Text, loc.Long1Text, loc.Long2Text, loc.MinElevation, loc.MaxElevation, loc.VerbatimElevation, '.
                        'loc.NamedPlace '.
                        'FROM locality AS loc LEFT JOIN localitydetail AS ld ON loc.LocalityID = ld.LocalityID '.
                        'LEFT JOIN geography AS geo1 ON loc.GeographyID = geo1.GeographyID '.
                        'LEFT JOIN geography AS geo2 ON geo1.ParentID = geo2.GeographyID '.
                        'LEFT JOIN geography AS geo3 ON geo2.ParentID = geo3.GeographyID '.
                        'WHERE loc.LocalityID = '.$localityId.' ';
                    $locRs = $this->conn->query($locSql);
                    //echo $locSql;
                    while($locR = $locRs->fetch_object()){
                        if($locR->NamedPlace && $locR->NamedPlace !== ''){
                            $occurrenceArr['locality'] = $locR->NamedPlace . '. ';
                        }
                        if($locR->LocalityName && $locR->LocalityName !== '' && $locR->LocalityName !== 'n/a'){
                            $occurrenceArr['locality'] .= $locR->LocalityName;
                        }
                        $occurrenceArr['locationremarks'] = $locR->Remarks;
                        $occurrenceArr['trstownship'] = $locR->Township . ($locR->TownshipDirection ? ' ' . $locR->TownshipDirection : '');
                        $occurrenceArr['trsrange'] = $locR->RangeDesc . ($locR->RangeDirection ? ' ' . $locR->RangeDirection : '');
                        $occurrenceArr['trssection'] = $locR->Section;
                        $occurrenceArr['trssectiondetails'] = $locR->SectionPart;
                        $occurrenceArr['waterbody'] = $locR->WaterBody;
                        $occurrenceArr['utmnorthing'] = $locR->UtmNorthing;
                        $occurrenceArr['utmeasting'] = $locR->UtmEasting;
                        $occurrenceArr['utmzoning'] = $locR->UtmDatum;
                        $occurrenceArr['geodeticdatum'] = $locR->Datum;
                        $occurrenceArr['minimumelevationinmeters'] = $locR->MinElevation;
                        $occurrenceArr['maximumelevationinmeters'] = $locR->MaxElevation;
                        $occurrenceArr['verbatimelevation'] = $locR->VerbatimElevation;
                        if($locR->Lat1Text){
                            $verbatimLat = $locR->Lat1Text . ($locR->Lat2Text ? ' - ' . $locR->Lat2Text : '');
                            $verbatimLong = $locR->Long1Text . ($locR->Long2Text ? ' - ' . $locR->Long2Text : '');
                            $occurrenceArr['verbatimcoordinates'] = $verbatimLat . ', ' . $verbatimLong;
                        }
                        if(!$locR->Latitude2){
                            $occurrenceArr['decimallatitude'] = $locR->Latitude1;
                            $occurrenceArr['decimallongitude'] = $locR->Longitude1;
                        }
                        if((int)$locR->geo1RankId === 200){
                            $occurrenceArr['country'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 200){
                            $occurrenceArr['country'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 200){
                            $occurrenceArr['country'] = $locR->geo3Name;
                        }
                        if((int)$locR->geo1RankId === 300){
                            $occurrenceArr['stateprovince'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 300){
                            $occurrenceArr['stateprovince'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 300){
                            $occurrenceArr['stateprovince'] = $locR->geo3Name;
                        }
                        if((int)$locR->geo1RankId === 400){
                            $occurrenceArr['county'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 400){
                            $occurrenceArr['county'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 400){
                            $occurrenceArr['county'] = $locR->geo3Name;
                        }
                    }
                    $locRs->close();

                    $geoCSql = 'SELECT MaxUncertaintyEst, Protocol, Source, GeoRefRemarks '.
                        'FROM geocoorddetail '.
                        'WHERE LocalityID = '.$localityId.' '.
                        'ORDER BY TimestampCreated, GeoCoordDetailID DESC LIMIT 1 ';
                    $geoCRs = $this->conn->query($geoCSql);
                    //echo $geoCSql;
                    while($geoCR = $geoCRs->fetch_object()){
                        $occurrenceArr['coordinateuncertaintyinmeters'] = $geoCR->MaxUncertaintyEst;
                        $occurrenceArr['georeferenceprotocol'] = $geoCR->Protocol;
                        $occurrenceArr['georeferencesources'] = $geoCR->Source;
                        $occurrenceArr['georeferenceremarks'] = $geoCR->GeoRefRemarks;
                    }
                    $geoCRs->close();
                }
                $colEvRs->close();

                $occurrenceArr['habitat'] = $habitatStr;

                $detSql = 'SELECT det.DeterminationID, det.DeterminedDate, det.Remarks, det.Qualifier, det.TypeStatusName, det.VarQualifer, det.Text2, '.
                    'det.SubSpQualifier, det.IsCurrent, det.GUID, tx.FullName, tx.Author, ag.LastName, ag.MiddleInitial, ag.FirstName '.
                    'FROM determination AS det LEFT JOIN taxon AS tx ON det.TaxonID = tx.TaxonID '.
                    'LEFT JOIN agent AS ag ON det.DeterminerID = ag.AgentID '.
                    'WHERE det.CollectionObjectID = '.$coreId.' '.
                    'ORDER BY det.DeterminedDate ';
                $detRs = $this->conn->query($detSql);
                while($detR = $detRs->fetch_object()){
                    $qualifier = '';
                    $detCoreId = $detR->DeterminationID;
                    $isCurrent = (int)$detR->IsCurrent;
                    $determiner = $detR->MiddleInitial;
                    if($detR->FirstName){
                        $determiner = $detR->FirstName . ($determiner ? ' ' . $determiner : '');
                    }
                    if($detR->LastName){
                        $determiner = $detR->LastName . ($determiner ? ', ' . $determiner : '');
                    }
                    if($detR->Qualifier){
                        $qualifier = $detR->Qualifier;
                    }
                    if($detR->VarQualifer){
                        $qualifier = $detR->VarQualifer;
                    }
                    if($detR->SubSpQualifier){
                        $qualifier = $detR->SubSpQualifier;
                    }
                    if($detR->TypeStatusName){
                        $occurrenceArr['typestatus'] = ($occurrenceArr['typestatus'] ? $occurrenceArr['typestatus'] . ', ' : '') . $detR->TypeStatusName;
                    }
                    if($isCurrent === 1){
                        $occurrenceArr['scientificname'] = $detR->FullName;
                        $occurrenceArr['scientificnameauthorship'] = $detR->Author;
                        $occurrenceArr['identifiedby'] = $determiner;
                        $occurrenceArr['dateidentified'] = $detR->DeterminedDate;
                        $occurrenceArr['identificationremarks'] = $detR->Remarks;
                        $occurrenceArr['identificationqualifier'] = $qualifier;
                    }
                    $determinationArr[$detCoreId]['scientificname'] = $detR->FullName;
                    $determinationArr[$detCoreId]['scientificnameauthorship'] = $detR->Author;
                    $determinationArr[$detCoreId]['identifiedby'] = ($determiner ?: $occurrenceArr['recordedby']);
                    $determinationArr[$detCoreId]['dateidentified'] = (($detR->Text2 && $detR->Text2 !== '')?$detR->Text2:'N/A');
                    $determinationArr[$detCoreId]['dateidentifiedinterpreted'] = $detR->DeterminedDate;
                    $determinationArr[$detCoreId]['identificationremarks'] = $detR->Remarks;
                    $determinationArr[$detCoreId]['identificationqualifier'] = $qualifier;
                    $determinationArr[$detCoreId]['identificationiscurrent'] = $isCurrent;
                    $determinationArr[$detCoreId]['recordId'] = $detR->GUID;
                }
                $detRs->close();

                $imgSql = 'SELECT att.AttachmentID, att.GUID, att.AttachmentLocation, att.CopyrightHolder, att.License, att.Remarks, '.
                    'att.MimeType '.
                    'FROM collectionobjectattachment AS coa LEFT JOIN attachment AS att ON coa.AttachmentID = att.AttachmentID '.
                    'WHERE coa.CollectionObjectID = '.$coreId.' ';
                $imgRs = $this->conn->query($imgSql);
                while($imgR = $imgRs->fetch_object()){
                    $imgCoreId = $imgR->AttachmentID;
                    $imageArr[$imgCoreId]['identifier'] = $imgR->GUID;
                    $imageArr[$imgCoreId]['accessuri'] = 'https://wisflora.herbarium.wisc.edu/specifyimages/originals/' . $imgR->AttachmentLocation;
                    $imageArr[$imgCoreId]['owner'] = $imgR->CopyrightHolder;
                    $imageArr[$imgCoreId]['usageterms'] = $imgR->License;
                    $imageArr[$imgCoreId]['comments'] = $imgR->Remarks;
                    $imageArr[$imgCoreId]['format'] = $imgR->MimeType;
                }
                $imgRs->close();

                $occurrenceArr = OccurrenceUtilities::occurrenceArrayCleaning($occurrenceArr);

                /*$occinsertsql = 'INSERT INTO omoccurrences(collid,dbpk,basisOfRecord,occurrenceID,catalogNumber,sciname,scientificNameAuthorship,'.
                    'identifiedBy,dateIdentified,identificationRemarks,identificationQualifier,typeStatus,recordedBy,recordNumber,associatedCollectors,'.
                    'eventDate,verbatimEventDate,habitat,occurrenceRemarks,waterBody,country,stateProvince,county,locality,decimalLatitude,'.
                    'decimalLongitude,geodeticDatum,coordinateUncertaintyInMeters,locationRemarks,verbatimCoordinates,georeferenceProtocol,'.
                    'georeferenceSources,georeferenceRemarks,minimumElevationInMeters,maximumElevationInMeters,verbatimElevation) '.
                    'VALUES (1,'.$coreId.',"PreservedSpecimen",'.
                    (isset($occurrenceArr['occurrenceid'])?'"'.$this->conn->real_escape_string($occurrenceArr['occurrenceid']).'"':'NULL').','.
                    (isset($occurrenceArr['catalognumber'])?'"'.$this->conn->real_escape_string($occurrenceArr['catalognumber']).'"':'NULL').','.
                    (isset($occurrenceArr['scientificname'])?'"'.$this->conn->real_escape_string($occurrenceArr['scientificname']).'"':'NULL').','.
                    (isset($occurrenceArr['scientificnameauthorship'])?'"'.$this->conn->real_escape_string($occurrenceArr['scientificnameauthorship']).'"':'NULL').','.
                    (isset($occurrenceArr['identifiedby'])?'"'.$this->conn->real_escape_string($occurrenceArr['identifiedby']).'"':'NULL').','.
                    (isset($occurrenceArr['dateIdentified'])?'"'.$this->conn->real_escape_string($occurrenceArr['dateIdentified']).'"':'NULL').','.
                    (isset($occurrenceArr['identificationremarks'])?'"'.$this->conn->real_escape_string($occurrenceArr['identificationremarks']).'"':'NULL').','.
                    (isset($occurrenceArr['identificationqualifier'])?'"'.$this->conn->real_escape_string($occurrenceArr['identificationqualifier']).'"':'NULL').','.
                    (isset($occurrenceArr['typestatus'])?'"'.$this->conn->real_escape_string($occurrenceArr['typestatus']).'"':'NULL').','.
                    (isset($occurrenceArr['recordedby'])?'"'.$this->conn->real_escape_string($occurrenceArr['recordedby']).'"':'NULL').','.
                    (isset($occurrenceArr['recordnumber'])?'"'.$this->conn->real_escape_string($occurrenceArr['recordnumber']).'"':'NULL').','.
                    (isset($occurrenceArr['associatedcollectors'])?'"'.$this->conn->real_escape_string($occurrenceArr['associatedcollectors']).'"':'NULL').','.
                    (isset($occurrenceArr['eventdate'])?'"'.$this->conn->real_escape_string($occurrenceArr['eventdate']).'"':'NULL').','.
                    (isset($occurrenceArr['verbatimeventdate'])?'"'.$this->conn->real_escape_string($occurrenceArr['verbatimeventdate']).'"':'NULL').','.
                    (isset($occurrenceArr['habitat'])?'"'.$this->conn->real_escape_string($occurrenceArr['habitat']).'"':'NULL').','.
                    (isset($occurrenceArr['occurrenceremarks'])?'"'.$this->conn->real_escape_string($occurrenceArr['occurrenceremarks']).'"':'NULL').','.
                    (isset($occurrenceArr['waterbody'])?'"'.$this->conn->real_escape_string($occurrenceArr['waterbody']).'"':'NULL').','.
                    (isset($occurrenceArr['country'])?'"'.$this->conn->real_escape_string($occurrenceArr['country']).'"':'NULL').','.
                    (isset($occurrenceArr['stateprovince'])?'"'.$this->conn->real_escape_string($occurrenceArr['stateprovince']).'"':'NULL').','.
                    (isset($occurrenceArr['county'])?'"'.$this->conn->real_escape_string($occurrenceArr['county']).'"':'NULL').','.
                    (isset($occurrenceArr['locality'])?'"'.$this->conn->real_escape_string($occurrenceArr['locality']).'"':'NULL').','.
                    (isset($occurrenceArr['decimallatitude'])?'"'.$this->conn->real_escape_string($occurrenceArr['decimallatitude']).'"':'NULL').','.
                    (isset($occurrenceArr['decimallongitude'])?'"'.$this->conn->real_escape_string($occurrenceArr['decimallongitude']).'"':'NULL').','.
                    (isset($occurrenceArr['geodeticdatum'])?'"'.$this->conn->real_escape_string($occurrenceArr['geodeticdatum']).'"':'NULL').','.
                    (isset($occurrenceArr['coordinateuncertaintyinmeters'])?'"'.$this->conn->real_escape_string($occurrenceArr['coordinateuncertaintyinmeters']).'"':'NULL').','.
                    (isset($occurrenceArr['locationremarks'])?'"'.$this->conn->real_escape_string($occurrenceArr['locationremarks']).'"':'NULL').','.
                    (isset($occurrenceArr['verbatimcoordinates'])?'"'.$this->conn->real_escape_string($occurrenceArr['verbatimcoordinates']).'"':'NULL').','.
                    (isset($occurrenceArr['georeferenceprotocol'])?'"'.$this->conn->real_escape_string($occurrenceArr['georeferenceprotocol']).'"':'NULL').','.
                    (isset($occurrenceArr['georeferencesources'])?'"'.$this->conn->real_escape_string($occurrenceArr['georeferencesources']).'"':'NULL').','.
                    (isset($occurrenceArr['georeferenceremarks'])?'"'.$this->conn->real_escape_string($occurrenceArr['georeferenceremarks']).'"':'NULL').','.
                    (isset($occurrenceArr['minimumelevationinmeters'])?'"'.$this->conn->real_escape_string($occurrenceArr['minimumelevationinmeters']).'"':'NULL').','.
                    (isset($occurrenceArr['maximumelevationinmeters'])?'"'.$this->conn->real_escape_string($occurrenceArr['maximumelevationinmeters']).'"':'NULL').','.
                    (isset($occurrenceArr['verbatimelevation'])?'"'.$this->conn->real_escape_string($occurrenceArr['verbatimelevation']).'"':'NULL').
                    ')';
                if($this->conn->query($occinsertsql)){
                    $occid = $this->conn->insert_id;

                    if($determinationArr){
                        foreach($determinationArr as $id => $idarr){
                            $detinsertsql = 'INSERT INTO omoccurdeterminations(occid,identifiedBy,dateIdentified,dateIdentifiedInterpreted,'.
                                'sciname,scientificNameAuthorship,identificationQualifier,iscurrent,identificationRemarks) '.
                                'VALUES ('.$occid.','.
                                (isset($idarr['identifiedby'])?'"'.$this->conn->real_escape_string($idarr['identifiedby']).'"':'NULL').','.
                                (isset($idarr['dateidentified'])?'"'.$this->conn->real_escape_string($idarr['dateidentified']).'"':'NULL').','.
                                (isset($idarr['dateidentifiedinterpreted'])?'"'.$this->conn->real_escape_string($idarr['dateidentifiedinterpreted']).'"':'NULL').','.
                                (isset($idarr['scientificname'])?'"'.$this->conn->real_escape_string($idarr['scientificname']).'"':'NULL').','.
                                (isset($idarr['scientificnameauthorship'])?'"'.$this->conn->real_escape_string($idarr['scientificnameauthorship']).'"':'NULL').','.
                                (isset($idarr['identificationqualifier'])?'"'.$this->conn->real_escape_string($idarr['identificationqualifier']).'"':'NULL').','.
                                (isset($idarr['identificationiscurrent'])?'"'.$this->conn->real_escape_string($idarr['identificationiscurrent']).'"':'NULL').','.
                                (isset($idarr['identificationremarks'])?'"'.$this->conn->real_escape_string($idarr['identificationremarks']).'"':'NULL').
                                ')';
                            if(!$this->conn->query($detinsertsql)){
                                echo '<li>'.$detinsertsql.'</li>';
                            }
                        }
                    }

                    if($imageArr){
                        foreach($imageArr as $id => $idarr){
                            $imginsertsql = 'INSERT INTO images(occid,url,owner,accessrights,caption,format) '.
                                'VALUES ('.$occid.','.
                                (isset($idarr['accessuri'])?'"'.$idarr['accessuri'].'"':'NULL').','.
                                (isset($idarr['owner'])?'"'.$idarr['owner'].'"':'NULL').','.
                                (isset($idarr['usageterms'])?'"'.$idarr['usageterms'].'"':'NULL').','.
                                (isset($idarr['comments'])?'"'.$idarr['comments'].'"':'NULL').','.
                                (isset($idarr['format'])?'"'.$idarr['format'].'"':'NULL').
                                ')';
                            if(!$this->conn->query($imginsertsql)){
                                echo '<li>'.$imginsertsql.'</li>';
                            }
                        }
                    }
                }
                else{
                    echo '<li>'.$occinsertsql.'</li>';
                }*/

                $totalUpload++;
                $intervalUpload++;
                if($intervalUpload === 1000){
                    echo '<li>'.$totalUpload.' records uploaded</li>';
                    $intervalUpload = 0;
                }
            }
		}
		$rs->close();



        echo '<li>Upload Procedure Complete ('.date('Y-m-d h:i:s A').')!</li>';
		return true;
	}
	
	public function mapTaxa($makePrimaryLink,$tidStart,$restart): void
	{
		$successCnt = 0;
		set_time_limit(36000);

		if(!is_numeric($tidStart)) {
			$tidStart = 0;
		}
		$startingTid = 0;
		if($restart){
			$sql1 = 'SELECT tid FROM taxalinks '.
				'WHERE owner = "EOL" AND initialtimestamp > "'.date('Y-m-d',time()-(7 * 24 * 60 * 60)).'" '.
				'ORDER BY initialtimestamp DESC LIMIT 1';
			$rs1 = $this->conn->query($sql1);
			if($r1 = $rs1->fetch_object()){
				$startingTid = $r1->tid;
			}
			$rs1->free();
		}
		if($tidStart && $tidStart > $startingTid) {
			$startingTid = $tidStart;
		}
		$sql = 'SELECT t.tid, t.sciname '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE t.rankid IN(220,230,240,260) AND ts.taxauthid = 1 AND ts.tid = ts.tidaccepted '.
			'AND t.tid NOT IN (SELECT tid FROM taxalinks WHERE title = "Encyclopedia of Life" AND sourceidentifier IS NOT NULL) ';
		if($startingTid) {
			$sql .= 'AND t.tid > ' . $startingTid . ' ';
		}
		$sql .= 'ORDER BY t.tid';
		//echo $sql;
		$rs = $this->conn->query($sql);
		$recCnt = $rs->num_rows;
		echo '<div style="font-weight:">';
		echo 'Mapping EOL identifiers for '.$recCnt.' taxa ';
		if($startingTid) {
			echo '(starting tid: ' . $startingTid . ')';
		}
		echo '</div>'."\n";
		echo "<ol>\n";
		while($r = $rs->fetch_object()){
			$tid = $r->tid;
			$sciName = $r->sciname;
			$sciName = str_replace(array(' subsp. ',' ssp. ',' var. ',' f. '),' ',$sciName);
			if($this->queryEolIdentifier($tid, $sciName, $makePrimaryLink)){
				$successCnt++;
			}
		}
		echo "<li>EOL mapping successfully completed for $successCnt taxa</li>\n";
		echo "</ol>\n";
		$rs->close();
	}
}
