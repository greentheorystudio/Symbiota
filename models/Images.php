<?php
include_once(__DIR__ . '/../services/DbService.php');

class Images{

	private $conn;

    private $fields = array(
        "imgid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "url" => array("dataType" => "string", "length" => 255),
        "thumbnailurl" => array("dataType" => "string", "length" => 255),
        "originalurl" => array("dataType" => "string", "length" => 255),
        "archiveurl" => array("dataType" => "string", "length" => 255),
        "photographer" => array("dataType" => "string", "length" => 100),
        "photographeruid" => array("dataType" => "number", "length" => 10),
        "imagetype" => array("dataType" => "string", "length" => 50),
        "format" => array("dataType" => "string", "length" => 45),
        "caption" => array("dataType" => "string", "length" => 750),
        "owner" => array("dataType" => "string", "length" => 250),
        "sourceurl" => array("dataType" => "string", "length" => 255),
        "referenceurl" => array("dataType" => "string", "length" => 255),
        "copyright" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "accessrights" => array("dataType" => "string", "length" => 255),
        "locality" => array("dataType" => "string", "length" => 250),
        "occid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 350),
        "anatomy" => array("dataType" => "string", "length" => 100),
        "username" => array("dataType" => "string", "length" => 45),
        "sourceidentifier" => array("dataType" => "string", "length" => 150),
        "mediamd5" => array("dataType" => "string", "length" => 45),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
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

    public function getTaxonImages($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT imgid, url, thumbnailurl, originalurl, archiveurl, photographer, imagetype, format, caption, owner, '.
            'sourceurl, referenceUrl, rights, accessrights, locality, occid, notes, anatomy, mediaMD5, dynamicProperties, sortsequence '.
            'FROM images '.
            'WHERE tid = ' . $tid . ' ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['imgid'] = $row->imgid;
            $resultArr['url'] = ($row->url && $GLOBALS['CLIENT_ROOT'] && strncmp($row->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->url) : $row->url;
            $resultArr['thumbnailurl'] = ($row->thumbnailurl && $GLOBALS['CLIENT_ROOT'] && strncmp($row->thumbnailurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->thumbnailurl) : $row->thumbnailurl;
            $resultArr['originalurl'] = ($row->originalurl && $GLOBALS['CLIENT_ROOT'] && strncmp($row->originalurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->originalurl) : $row->originalurl;
            $resultArr['archiveurl'] = $row->archiveurl;
            $resultArr['photographer'] = $row->photographer;
            $resultArr['imagetype'] = $row->imagetype;
            $resultArr['format'] = $row->format;
            $resultArr['caption'] = $row->caption;
            $resultArr['owner'] = $row->owner;
            $resultArr['sourceurl'] = $row->sourceurl;
            $resultArr['referenceUrl'] = $row->referenceUrl;
            $resultArr['rights'] = $row->rights;
            $resultArr['accessrights'] = $row->accessrights;
            $resultArr['locality'] = $row->locality;
            $resultArr['occid'] = $row->occid;
            $resultArr['notes'] = $row->notes;
            $resultArr['anatomy'] = $row->anatomy;
            $resultArr['mediaMD5'] = $row->mediaMD5;
            $resultArr['dynamicProperties'] = $row->dynamicProperties;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE images SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
