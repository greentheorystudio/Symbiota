<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class CollectionManager {

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

    public function getCollectionInfoArr($collId): array
    {
        $retArr = array();
        $sql = 'SELECT collectionname, icon, institutioncode, collectioncode, colltype, managementtype, datarecordingmethod, '.
            'defaultRepCount, isPublic '.
            'FROM omcollections WHERE collid = '.(int)$collId.' ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr['collectionname'] = $r->collectionname;
            $retArr['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($r->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->icon) : $r->icon;
            $retArr['institutioncode'] = $r->institutioncode;
            $retArr['collectioncode'] = $r->collectioncode;
            $retArr['colltype'] = $r->colltype;
            $retArr['managementtype'] = $r->managementtype;
            $retArr['datarecordingmethod'] = $r->datarecordingmethod;
            $retArr['defaultrepcount'] = $r->defaultRepCount;
            $retArr['ispublic'] = $r->isPublic;
            $retArr['additionalDataFields'] = null;
        }
        $rs->free();
        if(file_exists($GLOBALS['SERVER_ROOT'] . '/content/json/collection' . $collId . 'occurrencedatafields.json')) {
            $retArr['additionalDataFields'] = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/collection'.$collId.'occurrencedatafields.json'), true);
        }
        return $retArr;
    }
}
