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
        $sql = 'SELECT a.adddataid, a.field, a.datavalue, a.initialtimestamp '.
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
        $sql = 'SELECT e.locationid, e.eventtype, e.fieldnotes, e.fieldnumber, e.eventdate, e.latestdatecollected, e.eventtime, '.
            'e.`year`, e.`month`, e.`day`, e.startdayofyear, e.enddayofyear, e.verbatimeventdate, e.habitat, e.localitysecurity, '.
            'e.localitysecurityreason, e.decimallatitude, e.decimallongitude, e.geodeticdatum, e.coordinateuncertaintyinmeters, '.
            'e.footprintwkt, e.eventremarks, e.georeferencedby, e.georeferenceprotocol, e.georeferencesources, e.georeferenceverificationstatus, '.
            'e.georeferenceremarks, e.minimumdepthinmeters, e.maximumdepthinmeters, e.verbatimdepth, e.samplingprotocol, '.
            'e.samplingeffort, e.initialtimestamp '.
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
        $sql = 'SELECT l.locationname, l.locationcode, l.waterbody, l.country, l.stateprovince, l.county, l.municipality, l.locality, '.
            'l.localitysecurity, l.localitysecurityreason, l.decimallatitude, l.decimallongitude, l.geodeticdatum, l.coordinateuncertaintyinmeters, '.
            'l.footprintwkt, l.coordinateprecision, l.locationremarks, l.verbatimcoordinates, l.verbatimcoordinatesystem, l.georeferencedby, '.
            'l.georeferenceprotocol, l.georeferencesources, l.georeferenceverificationstatus, l.georeferenceremarks, '.
            'l.minimumelevationinmeters, l.maximumelevationinmeters, l.verbatimelevation, l.initialtimestamp '.
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
        $sql = 'SELECT o.collid, o.dbpk, o.basisofrecord, o.occurrenceid, o.catalognumber, o.othercatalognumbers, o.ownerinstitutioncode, o.institutionid, o.collectionid, o.datasetid, o.institutioncode, '.
            'o.collectioncode, o.family, o.verbatimscientificname, o.sciname, o.tid, o.genus, o.specificepithet, o.taxonrank, o.infraspecificepithet, o.scientificnameauthorship, o.taxonremarks, '.
            'o.identifiedby, o.dateidentified, o.identificationreferences, o.identificationremarks, o.identificationqualifier, o.typestatus, o.recordedby, o.recordnumber, o.recordedbyid, o.associatedcollectors, o.eventdate, '.
            'o.latestdatecollected, o.`year`, o.`month`, o.`day`, o.startdayofyear, o.enddayofyear, o.verbatimeventdate, o.habitat, o.substrate, o.fieldnotes, o.fieldnumber, '.
            'o.eventid, o.occurrenceremarks, o.informationwithheld, o.datageneralizations, o.associatedoccurrences, o.associatedtaxa, o.dynamicproperties, o.verbatimattributes, o.behavior, o.reproductivecondition, o.cultivationstatus, '.
            'o.establishmentmeans, o.lifestage, o.sex, o.individualcount, o.samplingprotocol, o.samplingeffort, o.preparations, o.locationid, o.waterbody, o.country, o.stateprovince, '.
            'o.county, o.municipality, o.locality, o.localitysecurity, o.localitysecurityreason, o.decimallatitude, o.decimallongitude, o.geodeticdatum, o.coordinateuncertaintyinmeters, o.footprintwkt, o.coordinateprecision, '.
            'o.locationremarks, o.verbatimcoordinates, o.verbatimcoordinatesystem, o.georeferencedby, o.georeferenceprotocol, o.georeferencesources, o.georeferenceverificationstatus, o.georeferenceremarks, o.minimumelevationinmeters, o.maximumelevationinmeters, o.verbatimelevation, '.
            'o.minimumdepthinmeters, o.maximumdepthinmeters, o.verbatimdepth, o.previousidentifications, o.disposition, o.storagelocation, o.modified, o.language, o.observeruid, o.processingstatus, o.recordenteredby, '.
            'o.duplicatequantity, o.labelproject, o.dateentered, o.datelastmodified '.
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
        $sql = 'SELECT d.detid, d.identifiedby, d.dateidentified, d.sciname, d.verbatimscientificname, d.tid, d.scientificnameauthorship, ' .
            'd.identificationqualifier, d.iscurrent, d.appliedstatus, d.identificationreferences, d.identificationremarks, d.sortsequence '.
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
        $sql = 'SELECT t.kingdomId, t.rankid, t.sciname, t.unitind1, unitname1, t.unitind2, unitname2, t.unitind3, unitname3, '.
            't.author, t.tidaccepted, t.parenttid, t.family, t.source, t.notes, t.hybrid, t.securitystatus '.
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
