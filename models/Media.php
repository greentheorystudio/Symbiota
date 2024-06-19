<?php
include_once(__DIR__ . '/../services/DbService.php');

class Media{

	private $conn;

    private $fields = array(
        "mediaid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "accessuri" => array("dataType" => "string", "length" => 2048),
        "title" => array("dataType" => "string", "length" => 255),
        "creatoruid" => array("dataType" => "number", "length" => 10),
        "creator" => array("dataType" => "string", "length" => 45),
        "type" => array("dataType" => "string", "length" => 45),
        "format" => array("dataType" => "string", "length" => 45),
        "owner" => array("dataType" => "string", "length" => 250),
        "furtherinformationurl" => array("dataType" => "string", "length" => 2048),
        "language" => array("dataType" => "string", "length" => 45),
        "usageterms" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "bibliographiccitation" => array("dataType" => "string", "length" => 255),
        "publisher" => array("dataType" => "string", "length" => 255),
        "contributor" => array("dataType" => "string", "length" => 255),
        "locationcreated" => array("dataType" => "string", "length" => 1000),
        "description" => array("dataType" => "string", "length" => 1000),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function getTaxonAudios($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT mediaid, occid, accessuri, title, creator, type, format, owner, furtherinformationurl, language, usageterms, '.
            'rights, bibliographiccitation, publisher, contributor, locationcreated, description, sortsequence '.
            'FROM media '.
            'WHERE tid = '.$tid.' AND format LIKE "audio/%" ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['mediaid'] = $row->mediaid;
            $resultArr['occid'] = $row->occid;
            $resultArr['accessuri'] = ($row->accessuri && $GLOBALS['CLIENT_ROOT'] && strncmp($row->accessuri, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->accessuri) : $row->accessuri;
            $resultArr['title'] = $row->title;
            $resultArr['creator'] = $row->creator;
            $resultArr['type'] = $row->type;
            $resultArr['format'] = $row->format;
            $resultArr['owner'] = $row->owner;
            $resultArr['furtherinformationurl'] = $row->furtherinformationurl;
            $resultArr['language'] = $row->language;
            $resultArr['usageterms'] = $row->usageterms;
            $resultArr['rights'] = $row->rights;
            $resultArr['bibliographiccitation'] = $row->bibliographiccitation;
            $resultArr['publisher'] = $row->publisher;
            $resultArr['contributor'] = $row->contributor;
            $resultArr['locationcreated'] = $row->locationcreated;
            $resultArr['description'] = $row->description;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getTaxonVideos($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT mediaid, occid, accessuri, title, creator, type, format, owner, furtherinformationurl, language, usageterms, '.
            'rights, bibliographiccitation, publisher, contributor, locationcreated, description, sortsequence '.
            'FROM media '.
            'WHERE tid = ' . $tid . ' AND format LIKE "video/%" ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['mediaid'] = $row->mediaid;
            $resultArr['occid'] = $row->occid;
            $resultArr['accessuri'] = ($row->accessuri && $GLOBALS['CLIENT_ROOT'] && strncmp($row->accessuri, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->accessuri) : $row->accessuri;
            $resultArr['title'] = $row->title;
            $resultArr['creator'] = $row->creator;
            $resultArr['type'] = $row->type;
            $resultArr['format'] = $row->format;
            $resultArr['owner'] = $row->owner;
            $resultArr['furtherinformationurl'] = $row->furtherinformationurl;
            $resultArr['language'] = $row->language;
            $resultArr['usageterms'] = $row->usageterms;
            $resultArr['rights'] = $row->rights;
            $resultArr['bibliographiccitation'] = $row->bibliographiccitation;
            $resultArr['publisher'] = $row->publisher;
            $resultArr['contributor'] = $row->contributor;
            $resultArr['locationcreated'] = $row->locationcreated;
            $resultArr['description'] = $row->description;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE media SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
