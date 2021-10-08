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
            'FROM collectionobject '.
            'WHERE AltCatalogNumber LIKE "%MAD" OR (AltCatalogNumber LIKE "v%" AND AltCatalogNumber LIKE "%WIS") ';
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
		$occurrenceArr = array();
        $determinationArr = array();
        $imageArr = array();
        $collectionObjectIDArr = array();
        $collectionEventIDArr = array();
        $localityIDArr = array();
		if($index > 1){
            $limitBottom = $limit * ($index - 1);
        }

        echo '<li>Gathering Specify collection objects...</li>';
		$sql = 'SELECT co.CollectionObjectID, co.GUID, co.AltCatalogNumber, co.FieldNumber, co.CollectingEventID, '.
            'CONCAT_WS(" ",co.Remarks,coa.Remarks) AS occurrenceRemarks '.
			'FROM collectionobject AS co LEFT JOIN collectionobjectattribute AS coa ON co.CollectionObjectAttributeID = coa.CollectionObjectAttributeID '.
            'WHERE co.AltCatalogNumber LIKE "%MAD" OR (co.AltCatalogNumber LIKE "v%" AND co.AltCatalogNumber LIKE "%WIS") '.
            'LIMIT ' . $limitBottom . ',' . $limit;
		$rs = $this->conn->query($sql);
		//echo $sql;
        while($r = $rs->fetch_object()){
			$coreId = (int)$r->CollectionObjectID;
            $collectionObjectIDArr[$coreId] = null;
            $collectionEventIDArr[(int)$r->CollectingEventID][] = $coreId;
            $occurrenceArr[$coreId]['occurrenceid'] = $r->GUID;
            $occurrenceArr[$coreId]['catalognumber'] = $r->AltCatalogNumber;
            $occurrenceArr[$coreId]['recordnumber'] = $r->FieldNumber;
            $occurrenceArr[$coreId]['occurrenceremarks'] = $r->occurrenceRemarks;
        }
		$rs->close();


        $collectionObjectIDStr = implode(',', array_keys($collectionObjectIDArr));

        if($collectionObjectIDStr){
            $collectionEventIDStr = implode(',', array_keys($collectionEventIDArr));

            if($collectionEventIDStr){
                echo '<li>Gathering Specify collectors...</li>';
                $colSql = 'SELECT c.CollectingEventID, a.FirstName, a.MiddleInitial, a.LastName, c.IsPrimary '.
                    'FROM collector AS c LEFT JOIN agent AS a ON c.AgentID = a.AgentID '.
                    'WHERE c.CollectingEventID IN('.$collectionEventIDStr.') '.
                    'ORDER BY c.CollectingEventID, c.OrderNumber ';
                $colRs = $this->conn->query($colSql);
                //echo $colSql;
                while($colR = $colRs->fetch_object()){
                    $id = (int)$colR->CollectingEventID;
                    $objidarr = $collectionEventIDArr[$id];
                    $primary = (int)$colR->IsPrimary;
                    $collector = $colR->MiddleInitial;
                    if($colR->FirstName){
                        $collector = $colR->FirstName . ($collector ? ' ' . $collector : '');
                    }
                    if($colR->LastName){
                        $collector = $colR->LastName . ($collector ? ', ' . $collector : '');
                    }
                    foreach($objidarr as $oid){
                        if($primary === 1){
                            $occurrenceArr[$oid]['recordedby'] = (isset($occurrenceArr[$oid]['recordedby']) ? $occurrenceArr[$oid]['recordedby'] . ', ' : '') . $collector;
                        }
                        else{
                            $occurrenceArr[$oid]['associatedcollectors'] = (isset($occurrenceArr[$oid]['associatedcollectors']) ? $occurrenceArr[$oid]['associatedcollectors'] . ', ' : '') . $collector;
                        }
                    }
                }
                $colRs->close();

                echo '<li>Gathering Specify collection events...</li>';
                $colEvSql = 'SELECT ce.CollectingEventID, ce.LocalityID, ce.StartDate, ce.StartDatePrecision, ce.StartDateVerbatim, ce.VerbatimLocality, ce.Remarks '.
                    'FROM collectingevent AS ce '.
                    'WHERE ce.CollectingEventID IN('.$collectionEventIDStr.') ';
                //echo $colEvSql;
                $colEvRs = $this->conn->query($colEvSql);
                while($colEvR = $colEvRs->fetch_object()){
                    $id = (int)$colEvR->CollectingEventID;
                    $precision = (int)$colEvR->StartDatePrecision;
                    $objidarr = $collectionEventIDArr[$id];
                    $habitatStr = '';
                    if($colEvR->VerbatimLocality){
                        $habitatStr = $colEvR->VerbatimLocality;
                    }
                    if($colEvR->Remarks && $colEvR->Remarks !== ''){
                        $habitatStr .= ' ' . $colEvR->Remarks;
                    }
                    foreach($objidarr as $oid){
                        $localityIDArr[(int)$colEvR->LocalityID][] = $oid;
                        if($colEvR->StartDate){
                            $dateArr = explode('-', $colEvR->StartDate);
                            if($precision === 1){
                                $occurrenceArr[$oid]['eventdate'] = $colEvR->StartDate;
                                $occurrenceArr[$oid]['day'] = $dateArr[2];
                                $occurrenceArr[$oid]['month'] = $dateArr[1];
                                $occurrenceArr[$oid]['year'] = $dateArr[0];
                            }
                            elseif($precision === 2){
                                $occurrenceArr[$oid]['eventdate'] = $dateArr[0] . '-' . $dateArr[1] . '-00';
                                $occurrenceArr[$oid]['verbatimeventdate'] = $dateArr[0] . '-' . $dateArr[1];
                                $occurrenceArr[$oid]['month'] = $dateArr[1];
                                $occurrenceArr[$oid]['year'] = $dateArr[0];
                            }
                            elseif($precision === 3){
                                $occurrenceArr[$oid]['eventdate'] = $dateArr[0] . '-00-00';
                                $occurrenceArr[$oid]['verbatimeventdate'] = $dateArr[0];
                                $occurrenceArr[$oid]['year'] = $dateArr[0];
                            }
                        }
                        if($colEvR->StartDateVerbatim){
                            $occurrenceArr[$oid]['verbatimeventdate'] = $colEvR->StartDateVerbatim;
                        }
                        if($habitatStr){
                            $occurrenceArr[$oid]['habitat'] = $habitatStr;
                        }
                    }
                }
                $colEvRs->close();
            }

            $localityIDStr = implode(',', array_keys($localityIDArr));

            if($localityIDStr){
                echo '<li>Gathering Specify collection localities...</li>';
                $locSql = 'SELECT loc.LocalityID, geo1.RankID AS geo1RankId, geo1.`Name` AS geo1Name, geo2.RankID AS geo2RankId, geo2.`Name` AS geo2Name, '.
                    'geo3.RankID AS geo3RankId, geo3.`Name` AS geo3Name, loc.LocalityName, loc.Remarks, loc.Latitude1, loc.Latitude2, '.
                    'loc.Longitude1, loc.Longitude2, ld.Township, ld.TownshipDirection, ld.RangeDesc, ld.RangeDirection, ld.Section, ld.Drainage, '.
                    'ld.SectionPart, ld.WaterBody, ld.UtmNorthing, ld.UtmEasting, ld.UtmDatum, ld.RangeDirection, ld.Section, '.
                    'loc.Lat1Text, loc.Lat2Text, loc.Long1Text, loc.Long2Text, loc.MinElevation, loc.MaxElevation, loc.VerbatimElevation, '.
                    'loc.NamedPlace, loc.Text3 '.
                    'FROM locality AS loc LEFT JOIN localitydetail AS ld ON loc.LocalityID = ld.LocalityID '.
                    'LEFT JOIN geography AS geo1 ON loc.GeographyID = geo1.GeographyID '.
                    'LEFT JOIN geography AS geo2 ON geo1.ParentID = geo2.GeographyID '.
                    'LEFT JOIN geography AS geo3 ON geo2.ParentID = geo3.GeographyID '.
                    'WHERE loc.LocalityID IN('.$localityIDStr.') ';
                $locRs = $this->conn->query($locSql);
                //echo $locSql;
                while($locR = $locRs->fetch_object()){
                    $id = (int)$locR->LocalityID;
                    $objidarr = $localityIDArr[$id];
                    $sectionDetails = '';
                    if($locR->Drainage || $locR->TownshipDirection || $locR->RangeDirection || $locR->SectionPart || $locR->WaterBody){
                        $sectionDetails .= ($locR->Drainage?$locR->Drainage . '; ':'');
                        $sectionDetails .= ($locR->TownshipDirection?'T' . $locR->TownshipDirection . ' ':'');
                        $sectionDetails .= ($locR->RangeDirection?'R' . $locR->RangeDirection . ' ':'');
                        $sectionDetails .= ($locR->SectionPart?'sec' . $locR->SectionPart . ' ':'');
                        $sectionDetails .= ($locR->WaterBody ?: '');
                    }
                    foreach($objidarr as $oid){
                        if($locR->NamedPlace && $locR->NamedPlace !== ''){
                            $occurrenceArr[$oid]['locality'] = $locR->NamedPlace . '. ';
                        }
                        if($locR->LocalityName && $locR->LocalityName !== '' && $locR->LocalityName !== 'n/a' && $locR->LocalityName !== 'N/A'){
                            $occurrenceArr[$oid]['locality'] = ($occurrenceArr[$oid]['locality'] ?? '') . $locR->LocalityName;
                        }
                        $occurrenceArr[$oid]['locationremarks'] = $locR->Remarks;
                        $occurrenceArr[$oid]['trstownship'] = $locR->Township;
                        $occurrenceArr[$oid]['trsrange'] = $locR->RangeDesc;
                        $occurrenceArr[$oid]['trssection'] = $locR->Section;
                        $occurrenceArr[$oid]['trssectiondetails'] = $sectionDetails;
                        $occurrenceArr[$oid]['utmnorthing'] = $locR->UtmNorthing;
                        $occurrenceArr[$oid]['utmeasting'] = $locR->UtmEasting;
                        $occurrenceArr[$oid]['utmzoning'] = $locR->UtmDatum;
                        $occurrenceArr[$oid]['geodeticdatum'] = $locR->Text3;
                        $occurrenceArr[$oid]['minimumelevationinmeters'] = $locR->MinElevation;
                        $occurrenceArr[$oid]['maximumelevationinmeters'] = $locR->MaxElevation;
                        $occurrenceArr[$oid]['verbatimelevation'] = $locR->VerbatimElevation;
                        if($locR->Lat1Text){
                            $verbatimLat = $locR->Lat1Text . ($locR->Lat2Text ? ' - ' . $locR->Lat2Text : '');
                            $verbatimLong = $locR->Long1Text . ($locR->Long2Text ? ' - ' . $locR->Long2Text : '');
                            $occurrenceArr[$oid]['verbatimcoordinates'] = $verbatimLat . ', ' . $verbatimLong;
                        }
                        if(!$locR->Latitude2 && $locR->Latitude1 && $locR->Longitude1){
                            $occurrenceArr[$oid]['decimallatitude'] = $locR->Latitude1;
                            $occurrenceArr[$oid]['decimallongitude'] = $locR->Longitude1;
                            $occurrenceArr[$oid]['processingstatus'] = 'stage 3';
                        }
                        else{
                            $occurrenceArr[$oid]['processingstatus'] = 'stage 2';
                        }
                        if((int)$locR->geo1RankId === 200){
                            $occurrenceArr[$oid]['country'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 200){
                            $occurrenceArr[$oid]['country'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 200){
                            $occurrenceArr[$oid]['country'] = $locR->geo3Name;
                        }
                        if((int)$locR->geo1RankId === 300){
                            $occurrenceArr[$oid]['stateprovince'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 300){
                            $occurrenceArr[$oid]['stateprovince'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 300){
                            $occurrenceArr[$oid]['stateprovince'] = $locR->geo3Name;
                        }
                        if((int)$locR->geo1RankId === 400){
                            $occurrenceArr[$oid]['county'] = $locR->geo1Name;
                        }
                        if((int)$locR->geo2RankId === 400){
                            $occurrenceArr[$oid]['county'] = $locR->geo2Name;
                        }
                        if((int)$locR->geo3RankId === 400){
                            $occurrenceArr[$oid]['county'] = $locR->geo3Name;
                        }
                    }
                }
                $locRs->close();

                echo '<li>Gathering Specify georeferencing details...</li>';
                $geoCSql = 'SELECT LocalityID, MaxUncertaintyEst, Protocol, Source, GeoRefRemarks '.
                    'FROM geocoorddetail '.
                    'WHERE LocalityID IN('.$localityIDStr.') '.
                    'ORDER BY LocalityID, TimestampCreated, GeoCoordDetailID DESC ';
                $geoCRs = $this->conn->query($geoCSql);
                //echo $geoCSql;
                $id = 0;
                while($geoCR = $geoCRs->fetch_object()){
                    if($id !== (int)$geoCR->LocalityID){
                        $id = (int)$geoCR->LocalityID;
                        $objidarr = $localityIDArr[$id];
                        foreach($objidarr as $oid){
                            $occurrenceArr[$oid]['coordinateuncertaintyinmeters'] = $geoCR->MaxUncertaintyEst;
                            $occurrenceArr[$oid]['georeferenceprotocol'] = $geoCR->Protocol;
                            $occurrenceArr[$oid]['georeferencesources'] = $geoCR->Source;
                            $occurrenceArr[$oid]['georeferenceremarks'] = $geoCR->GeoRefRemarks;
                        }
                    }
                }
                $geoCRs->close();
            }

            echo '<li>Gathering Specify determinations...</li>';
            $detSql = 'SELECT DISTINCT det.CollectionObjectID, det.DeterminedDate, det.Remarks, det.Qualifier, det.TypeStatusName, det.VarQualifer, det.Text2, '.
                'det.SubSpQualifier, det.IsCurrent, tx.FullName, tx.Author, ag.LastName, ag.MiddleInitial, ag.FirstName '.
                'FROM determination AS det LEFT JOIN taxon AS tx ON det.TaxonID = tx.TaxonID '.
                'LEFT JOIN agent AS ag ON det.DeterminerID = ag.AgentID '.
                'WHERE det.CollectionObjectID IN('.$collectionObjectIDStr.') '.
                'ORDER BY det.CollectionObjectID, det.DeterminedDate ';
            //echo $detSql;
            $detRs = $this->conn->query($detSql);
            while($detR = $detRs->fetch_object()){
                $qualifier = '';
                $id = (int)$detR->CollectionObjectID;
                $detDateDate = $detR->DeterminedDate ?: '';
                $detDateText = $detR->Text2 ?: '';
                $detDate = $detDateText ?: $detDateDate;
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
                    $occurrenceArr[$id]['typestatus'] = (isset($occurrenceArr[$id]['typestatus']) ? $occurrenceArr[$id]['typestatus'] . ', ' : '') . $detR->TypeStatusName;
                }
                if($isCurrent === 1){
                    $occurrenceArr[$id]['scientificname'] = $detR->FullName;
                    if($detR->Author){
                        $occurrenceArr[$id]['scientificnameauthorship'] = $detR->Author;
                    }
                    if($determiner){
                        $occurrenceArr[$id]['identifiedby'] = $determiner;
                    }
                    if($detDate){
                        $occurrenceArr[$id]['dateidentified'] = $detDate;
                    }
                    if($detR->Remarks){
                        $occurrenceArr[$id]['identificationremarks'] = $detR->Remarks;
                    }
                    if($qualifier){
                        $occurrenceArr[$id]['identificationqualifier'] = $qualifier;
                    }
                }
                else{
                    $determinationArr[$id][$detDate][$determiner]['scientificname'] = $detR->FullName;
                    if($detR->Author){
                        $determinationArr[$id][$detDate][$determiner]['scientificnameauthorship'] = $detR->Author;
                    }
                    if($determiner){
                        $determinationArr[$id][$detDate][$determiner]['identifiedby'] = $determiner;
                    }
                    elseif(isset($occurrenceArr[$id]['recordedby'])){
                        $determinationArr[$id][$detDate][$determiner]['identifiedby'] = $occurrenceArr[$id]['recordedby'];
                    }
                    else{
                        $determinationArr[$id][$detDate][$determiner]['identifiedby'] = 'N/A';
                    }
                    $determinationArr[$id][$detDate][$determiner]['dateidentified'] = $detDate ?: 'N/A';
                    if($detDateDate){
                        $determinationArr[$id][$detDate][$determiner]['dateidentifiedinterpreted'] = $detDateDate;
                    }
                    if($detR->Remarks){
                        $determinationArr[$id][$detDate][$determiner]['identificationremarks'] = $detR->Remarks;
                    }
                    if($qualifier){
                        $determinationArr[$id][$detDate][$determiner]['identificationqualifier'] = $qualifier;
                    }
                    $determinationArr[$id][$detDate][$determiner]['identificationiscurrent'] = $isCurrent;
                }
            }
            $detRs->close();
            //echo json_encode($determinationArr);

            echo '<li>Gathering Specify images...</li>';
            $imgSql = 'SELECT DISTINCT coa.CollectionObjectID, att.AttachmentID, att.AttachmentLocation, att.CopyrightHolder, att.License, att.Remarks, '.
                'att.MimeType '.
                'FROM collectionobjectattachment AS coa LEFT JOIN attachment AS att ON coa.AttachmentID = att.AttachmentID '.
                'WHERE coa.CollectionObjectID IN('.$collectionObjectIDStr.') ';
            //echo $imgSql;
            $imgRs = $this->conn->query($imgSql);
            while($imgR = $imgRs->fetch_object()){
                $id = (int)$imgR->CollectionObjectID;
                $imgCoreId = $imgR->AttachmentID;
                $imageArr[$id][$imgCoreId]['accessuri'] = 'https://wisflora.herbarium.wisc.edu/specifyimages/originals/' . $imgR->AttachmentLocation;
                $imageArr[$id][$imgCoreId]['owner'] = $imgR->CopyrightHolder;
                $imageArr[$id][$imgCoreId]['usageterms'] = $imgR->License;
                $imageArr[$id][$imgCoreId]['comments'] = $imgR->Remarks;
                $imageArr[$id][$imgCoreId]['format'] = $imgR->MimeType;
            }
            $imgRs->close();

            echo '<li>Inserting occurrences...</li>';
            $occinsertsqlprefix = 'INSERT INTO omoccurrences(collid,dbpk,basisOfRecord,occurrenceID,catalogNumber,sciname,scientificNameAuthorship,'.
                'identifiedBy,dateIdentified,identificationRemarks,identificationQualifier,typeStatus,recordedBy,recordNumber,associatedCollectors,'.
                'eventDate,verbatimEventDate,habitat,occurrenceRemarks,country,stateProvince,county,locality,decimalLatitude,`day`,`month`,`year`,'.
                'decimalLongitude,geodeticDatum,coordinateUncertaintyInMeters,locationRemarks,verbatimCoordinates,georeferenceProtocol,'.
                'georeferenceSources,georeferenceRemarks,processingstatus,minimumElevationInMeters,maximumElevationInMeters,verbatimElevation) '.
                'VALUES ';
            $occinsertsql = '';
            $rep = 0;
            foreach($occurrenceArr as $id => $idarr){
                $idarr = OccurrenceUtilities::occurrenceArrayCleaning($idarr);
                $occinsertsql .= '(1,'.$id.',"PreservedSpecimen",'.
                    (isset($idarr['occurrenceid']) && $idarr['occurrenceid']?'"'.$this->conn->real_escape_string($idarr['occurrenceid']).'"':'NULL').','.
                    (isset($idarr['catalognumber']) && $idarr['catalognumber']?'"'.$this->conn->real_escape_string($idarr['catalognumber']).'"':'NULL').','.
                    (isset($idarr['scientificname']) && $idarr['scientificname']?'"'.$this->conn->real_escape_string($idarr['scientificname']).'"':'NULL').','.
                    (isset($idarr['scientificnameauthorship']) && $idarr['scientificnameauthorship']?'"'.$this->conn->real_escape_string($idarr['scientificnameauthorship']).'"':'NULL').','.
                    (isset($idarr['identifiedby']) && $idarr['identifiedby']?'"'.$this->conn->real_escape_string($idarr['identifiedby']).'"':'NULL').','.
                    (isset($idarr['dateidentified']) && $idarr['dateidentified']?'"'.$this->conn->real_escape_string($idarr['dateidentified']).'"':'NULL').','.
                    (isset($idarr['identificationremarks']) && $idarr['identificationremarks']?'"'.$this->conn->real_escape_string($idarr['identificationremarks']).'"':'NULL').','.
                    (isset($idarr['identificationqualifier']) && $idarr['identificationqualifier']?'"'.$this->conn->real_escape_string($idarr['identificationqualifier']).'"':'NULL').','.
                    (isset($idarr['typestatus']) && $idarr['typestatus']?'"'.$this->conn->real_escape_string($idarr['typestatus']).'"':'NULL').','.
                    (isset($idarr['recordedby']) && $idarr['recordedby']?'"'.$this->conn->real_escape_string($idarr['recordedby']).'"':'NULL').','.
                    (isset($idarr['recordnumber']) && $idarr['recordnumber']?'"'.$this->conn->real_escape_string($idarr['recordnumber']).'"':'NULL').','.
                    (isset($idarr['associatedcollectors']) && $idarr['associatedcollectors']?'"'.$this->conn->real_escape_string($idarr['associatedcollectors']).'"':'NULL').','.
                    (isset($idarr['eventdate']) && $idarr['eventdate']?'"'.$this->conn->real_escape_string($idarr['eventdate']).'"':'NULL').','.
                    (isset($idarr['verbatimeventdate']) && $idarr['verbatimeventdate']?'"'.$this->conn->real_escape_string($idarr['verbatimeventdate']).'"':'NULL').','.
                    (isset($idarr['habitat']) && $idarr['habitat']?'"'.$this->conn->real_escape_string($idarr['habitat']).'"':'NULL').','.
                    (isset($idarr['occurrenceremarks']) && $idarr['occurrenceremarks']?'"'.$this->conn->real_escape_string($idarr['occurrenceremarks']).'"':'NULL').','.
                    (isset($idarr['country']) && $idarr['country']?'"'.$this->conn->real_escape_string($idarr['country']).'"':'NULL').','.
                    (isset($idarr['stateprovince']) && $idarr['stateprovince']?'"'.$this->conn->real_escape_string($idarr['stateprovince']).'"':'NULL').','.
                    (isset($idarr['county']) && $idarr['county']?'"'.$this->conn->real_escape_string($idarr['county']).'"':'NULL').','.
                    (isset($idarr['locality']) && $idarr['locality']?'"'.$this->conn->real_escape_string($idarr['locality']).'"':'NULL').','.
                    (isset($idarr['decimallatitude']) && $idarr['decimallatitude']?'"'.$this->conn->real_escape_string($idarr['decimallatitude']).'"':'NULL').','.
                    (isset($idarr['day']) && $idarr['day']?'"'.$this->conn->real_escape_string($idarr['day']).'"':'NULL').','.
                    (isset($idarr['month']) && $idarr['month']?'"'.$this->conn->real_escape_string($idarr['month']).'"':'NULL').','.
                    (isset($idarr['year']) && $idarr['year']?'"'.$this->conn->real_escape_string($idarr['year']).'"':'NULL').','.
                    (isset($idarr['decimallongitude']) && $idarr['decimallongitude']?'"'.$this->conn->real_escape_string($idarr['decimallongitude']).'"':'NULL').','.
                    (isset($idarr['geodeticdatum']) && $idarr['geodeticdatum']?'"'.$this->conn->real_escape_string($idarr['geodeticdatum']).'"':'NULL').','.
                    (isset($idarr['coordinateuncertaintyinmeters']) && $idarr['coordinateuncertaintyinmeters']?'"'.$this->conn->real_escape_string($idarr['coordinateuncertaintyinmeters']).'"':'NULL').','.
                    (isset($idarr['locationremarks']) && $idarr['locationremarks']?'"'.$this->conn->real_escape_string($idarr['locationremarks']).'"':'NULL').','.
                    (isset($idarr['verbatimcoordinates']) && $idarr['verbatimcoordinates']?'"'.$this->conn->real_escape_string($idarr['verbatimcoordinates']).'"':'NULL').','.
                    (isset($idarr['georeferenceprotocol']) && $idarr['georeferenceprotocol']?'"'.$this->conn->real_escape_string($idarr['georeferenceprotocol']).'"':'NULL').','.
                    (isset($idarr['georeferencesources']) && $idarr['georeferencesources']?'"'.$this->conn->real_escape_string($idarr['georeferencesources']).'"':'NULL').','.
                    (isset($idarr['georeferenceremarks']) && $idarr['georeferenceremarks']?'"'.$this->conn->real_escape_string($idarr['georeferenceremarks']).'"':'NULL').','.
                    (isset($idarr['processingstatus']) && $idarr['processingstatus']?'"'.$this->conn->real_escape_string($idarr['processingstatus']).'"':'NULL').','.
                    (isset($idarr['minimumelevationinmeters']) && $idarr['minimumelevationinmeters']?'"'.$this->conn->real_escape_string($idarr['minimumelevationinmeters']).'"':'NULL').','.
                    (isset($idarr['maximumelevationinmeters']) && $idarr['maximumelevationinmeters']?'"'.$this->conn->real_escape_string($idarr['maximumelevationinmeters']).'"':'NULL').','.
                    (isset($idarr['verbatimelevation']) && $idarr['verbatimelevation']?'"'.$this->conn->real_escape_string($idarr['verbatimelevation']).'"':'NULL').
                    '),';
                $rep++;
                if($rep === 1000){
                    $occinsertsql = substr($occinsertsql, 0, -1);
                    if(!$this->conn->query($occinsertsqlprefix . $occinsertsql)){
                        echo '<li>occ insert FAILED...SQL: '.$occinsertsqlprefix . $occinsertsql.'</li>';
                    }
                    $rep = 0;
                    $occinsertsql = '';
                }
            }
            $occinsertsql = substr($occinsertsql, 0, -1);
            //echo $occinsertsqlprefix . $occinsertsql;
            if(!$occinsertsql || $this->conn->query($occinsertsqlprefix . $occinsertsql)){
                $occSql = 'SELECT occid, dbpk FROM omoccurrences WHERE dbpk IN('.$collectionObjectIDStr.') ';
                $occRs = $this->conn->query($occSql);
                while($occR = $occRs->fetch_object()){
                    $occid = (int)$occR->occid;
                    $dbpk = (int)$occR->dbpk;
                    $collectionObjectIDArr[$dbpk] = $occid;
                }
                $occRs->close();

                if($determinationArr){
                    echo '<li>Inserting determinations...</li>';
                    $detinsertsqlprefix = 'INSERT INTO omoccurdeterminations(occid,identifiedBy,dateIdentified,dateIdentifiedInterpreted,'.
                        'sciname,scientificNameAuthorship,identificationQualifier,iscurrent,identificationRemarks) '.
                        'VALUES ';
                    foreach($determinationArr as $oid => $detarr){
                        $occ = $collectionObjectIDArr[$oid];
                        foreach($detarr as $detdate){
                            foreach($detdate as $determiner => $darr){
                                $detinsertsql = '('.$occ.','.
                                    (isset($darr['identifiedby']) && $darr['identifiedby']?'"'.$this->conn->real_escape_string($darr['identifiedby']).'"':'NULL').','.
                                    (isset($darr['dateidentified']) && $darr['dateidentified']?'"'.$this->conn->real_escape_string($darr['dateidentified']).'"':'NULL').','.
                                    (isset($darr['dateidentifiedinterpreted']) && $darr['dateidentifiedinterpreted']?'"'.$this->conn->real_escape_string($darr['dateidentifiedinterpreted']).'"':'NULL').','.
                                    (isset($darr['scientificname']) && $darr['scientificname']?'"'.$this->conn->real_escape_string($darr['scientificname']).'"':'NULL').','.
                                    (isset($darr['scientificnameauthorship']) && $darr['scientificnameauthorship']?'"'.$this->conn->real_escape_string($darr['scientificnameauthorship']).'"':'NULL').','.
                                    (isset($darr['identificationqualifier']) && $darr['identificationqualifier']?'"'.$this->conn->real_escape_string($darr['identificationqualifier']).'"':'NULL').','.
                                    (isset($darr['identificationiscurrent']) && $darr['identificationiscurrent']?'"'.$this->conn->real_escape_string($darr['identificationiscurrent']).'"':'NULL').','.
                                    (isset($darr['identificationremarks']) && $darr['identificationremarks']?'"'.$this->conn->real_escape_string($darr['identificationremarks']).'"':'NULL').
                                    ')';
                                if(!$this->conn->query($detinsertsqlprefix . $detinsertsql)){
                                    echo '<li>det insert FAILED...SQL: '.$detinsertsqlprefix . $detinsertsql.'</li>';
                                }
                            }
                        }
                    }
                }

                if($imageArr){
                    echo '<li>Inserting images...</li>';
                    $imginsertsqlprefix = 'INSERT INTO images(occid,url,owner,accessrights,caption,format) '.
                        'VALUES ';
                    $imginsertsql = '';
                    $rep = 0;
                    foreach($imageArr as $oid => $imgarr){
                        $occ = $collectionObjectIDArr[$oid];
                        foreach($imgarr as $iid => $iarr){
                            $imginsertsql .= '('.$occ.','.
                                (isset($iarr['accessuri']) && $iarr['accessuri']?'"'.$iarr['accessuri'].'"':'NULL').','.
                                (isset($iarr['owner']) && $iarr['owner']?'"'.$iarr['owner'].'"':'NULL').','.
                                (isset($iarr['usageterms']) && $iarr['usageterms']?'"'.$iarr['usageterms'].'"':'NULL').','.
                                (isset($iarr['comments']) && $iarr['comments']?'"'.$iarr['comments'].'"':'NULL').','.
                                (isset($iarr['format']) && $iarr['format']?'"'.$iarr['format'].'"':'NULL').
                                '),';
                            $rep++;
                            if($rep === 1000){
                                $imginsertsql = substr($imginsertsql, 0, -1);
                                $this->conn->query($imginsertsqlprefix . $imginsertsql);
                                $rep = 0;
                                $imginsertsql = '';
                            }
                        }
                    }
                    $imginsertsql = substr($imginsertsql, 0, -1);
                    $this->conn->query($imginsertsqlprefix . $imginsertsql);
                }
            }
            else{
                echo '<li>occ insert FAILED...SQL: '.$occinsertsqlprefix . $occinsertsql.'</li>';
            }
        }

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
