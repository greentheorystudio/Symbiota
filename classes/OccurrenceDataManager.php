<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceDataManager{

	private $conn;

    public function __construct(){
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function getAdditionalData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT a.adddataID, a.field, a.datavalue, a.initialtimestamp '.
            'FROM omoccuradditionaldata AS a '.
            'WHERE a.eventID = ' . $eventid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getCollectionEventData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT e.locationID, e.eventType, e.fieldNotes, e.fieldnumber, e.eventDate, e.latestDateCollected, e.eventTime, '.
            'e.`year`, e.`month`, e.`day`, e.startDayOfYear, e.endDayOfYear, e.verbatimEventDate, e.habitat, e.localitySecurity, '.
            'e.localitySecurityReason, e.decimalLatitude, e.decimalLongitude, e.geodeticDatum, e.coordinateUncertaintyInMeters, '.
            'e.footprintWKT, e.eventRemarks, e.georeferencedBy, e.georeferenceProtocol, e.georeferenceSources, e.georeferenceVerificationStatus, '.
            'e.georeferenceRemarks, e.minimumDepthInMeters, e.maximumDepthInMeters, e.verbatimDepth, e.samplingProtocol, '.
            'e.samplingEffort, e.initialtimestamp '.
            'FROM omoccurcollectingevents AS e '.
            'WHERE e.eventID = ' . $eventid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getLocationData($locationid): array
    {
        $retArr = array();
        $sql = 'SELECT l.locationName, l.locationCode, l.waterBody, l.country, l.stateProvince, l.county, l.municipality, l.locality, '.
            'l.localitySecurity, l.localitySecurityReason, l.decimalLatitude, l.decimalLongitude, l.geodeticDatum, l.coordinateUncertaintyInMeters, '.
            'l.footprintWKT, l.coordinatePrecision, l.locationRemarks, l.verbatimCoordinates, l.verbatimCoordinateSystem, l.georeferencedBy, '.
            'l.georeferenceProtocol, l.georeferenceSources, l.georeferenceVerificationStatus, l.georeferenceRemarks, '.
            'l.minimumElevationInMeters, l.maximumElevationInMeters, l.verbatimElevation, l.initialtimestamp '.
            'FROM omoccurlocations AS l '.
            'WHERE l.locationID = ' . $locationid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getLock($occid): int
    {
        $isLocked = 0;
        $delSql = 'DELETE FROM omoccureditlocks WHERE ts < '.(time()-900).' OR uid = '.$GLOBALS['SYMB_UID'].' ';
        if($this->conn->query($delSql)) {
            $sqlFind = 'SELECT * FROM omoccureditlocks WHERE occid = ' . $occid . ' ';
            $frs = $this->conn->query($sqlFind);
            if(!$frs->num_rows){
                $sql = 'INSERT INTO omoccureditlocks(occid,uid,ts) '.
                    'VALUES ('.$occid.','.$GLOBALS['SYMB_UID'].','.time().')';
                $this->conn->query($sql);
            }
            else{
                $isLocked = true;
            }
        }
        return $isLocked;
    }

    public function getOccurrenceChecklistData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT c.clid, c.name '.
            'FROM fmchecklists AS c LEFT JOIN fmvouchers AS v ON c.clid = v.clid '.
            'WHERE v.occid = ' . $occid . ' ORDER BY c.name ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT o.collid, o.dbpk, o.basisOfRecord, o.occurrenceID, o.catalogNumber, o.otherCatalogNumbers, o.ownerInstitutionCode, o.institutionID, o.collectionID, o.datasetID, o.institutionCode, '.
            'o.collectionCode, o.family, o.verbatimScientificName, o.sciname, o.tid, o.genus, o.specificEpithet, o.taxonRank, o.infraspecificEpithet, o.scientificNameAuthorship, o.taxonRemarks, '.
            'o.identifiedBy, o.dateIdentified, o.identificationReferences, o.identificationRemarks, o.identificationQualifier, o.typeStatus, o.recordedBy, o.recordNumber, o.recordedbyid, o.associatedCollectors, o.eventDate, '.
            'o.latestDateCollected, o.year, o.month, o.day, o.startDayOfYear, o.endDayOfYear, o.verbatimEventDate, o.habitat, o.substrate, o.fieldNotes, o.fieldnumber, '.
            'o.eventID, o.occurrenceRemarks, o.informationWithheld, o.dataGeneralizations, o.associatedOccurrences, o.associatedTaxa, o.dynamicProperties, o.verbatimAttributes, o.behavior, o.reproductiveCondition, o.cultivationStatus, '.
            'o.establishmentMeans, o.lifeStage, o.sex, o.individualCount, o.samplingProtocol, o.samplingEffort, o.preparations, o.locationID, o.waterBody, o.country, o.stateProvince, '.
            'o.county, o.municipality, o.locality, o.localitySecurity, o.localitySecurityReason, o.decimalLatitude, o.decimalLongitude, o.geodeticDatum, o.coordinateUncertaintyInMeters, o.footprintWKT, o.coordinatePrecision, '.
            'o.locationRemarks, o.verbatimCoordinates, o.verbatimCoordinateSystem, o.georeferencedBy, o.georeferenceProtocol, o.georeferenceSources, o.georeferenceVerificationStatus, o.georeferenceRemarks, o.minimumElevationInMeters, o.maximumElevationInMeters, o.verbatimElevation, '.
            'o.minimumDepthInMeters, o.maximumDepthInMeters, o.verbatimDepth, o.previousIdentifications, o.disposition, o.storageLocation, o.modified, o.language, o.observeruid, o.processingstatus, o.recordEnteredBy, '.
            'o.duplicateQuantity, o.labelProject, o.dateEntered, o.dateLastModified '.
            'FROM omoccurrences AS o '.
            'WHERE o.occid = ' . $occid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
            if($retArr['tid'] && (int)$retArr['tid'] > 0){
                $retArr['taxonData'] = $this->getTaxonData($retArr['tid']);
            }
        }
        return $retArr;
    }

    public function getOccurrenceDeterminationData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT d.detid, d.identifiedBy, d.dateIdentified, d.sciname, d.verbatimscientificname, d.tid, d.scientificNameAuthorship, ' .
            'd.identificationQualifier, d.iscurrent, d.appliedstatus, d.identificationReferences, d.identificationRemarks, d.sortsequence '.
            'FROM omoccurdeterminations AS d '.
            'WHERE d.occid = ' . $occid . ' ORDER BY d.iscurrent DESC, d.sortsequence ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                if($nodeArr['tid'] && (int)$nodeArr['tid'] > 0){
                    $nodeArr['taxonData'] = $this->getTaxonData($nodeArr['tid']);
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceDuplicateData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT d.duplicateid, d.title, d.description, d.notes '.
            'FROM omoccurduplicates AS d INNER JOIN omoccurduplicatelink AS l ON d.duplicateid = l.duplicateid '.
            'WHERE l.occid = ' . $occid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceGeneticLinkData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT g.idoccurgenetic, g.identifier, g.resourcename, g.locus, g.resourceurl, g.notes '.
            'FROM omoccurgenetic AS g '.
            'WHERE g.occid = ' . $occid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceImageData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT i.imgid, i.url, i.thumbnailurl, i.originalurl, i.caption, i.photographer, i.photographeruid, i.sourceurl, i.copyright, '.
            'i.notes, i.username, i.sortsequence, i.initialtimestamp '.
            'FROM images AS i '.
            'WHERE i.occid = ' . $occid . ' ORDER BY i.sortsequence ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceMediaData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT m.mediaid, m.accessuri, m.title, m.creatoruid, m.creator, m.`type`, m.`format`, m.owner, m.furtherinformationurl, '.
            'm.language, m.usageterms, m.rights, m.bibliographiccitation, m.publisher, m.contributor, m.locationcreated, m.description, m.sortsequence '.
            'FROM media AS m '.
            'WHERE m.occid = ' . $occid . ' ORDER BY m.sortsequence ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonData($tid): array
    {
        $retArr = array();
        $sql = 'SELECT t.kingdomId, t.RankId, t.SciName, t.UnitInd1, UnitName1, t.UnitInd2, UnitName2, t.UnitInd3, UnitName3, '.
            't.Author, t.tidaccepted, t.parenttid, t.family, t.Source, t.Notes, t.Hybrid, t.SecurityStatus '.
            'FROM taxa AS t '.
            'WHERE t.tid = ' . $tid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }
}
