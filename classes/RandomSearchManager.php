<?php
include_once('DbConnection.php');
include_once('ImageLibraryManager.php');

class RandomSearchManager extends ImageLibraryManager{

	protected $conn;

	public function __construct() {
        parent::__construct();
	    $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
        parent::__destruct();
	    if(!($this->conn === false)) {
            $this->conn->close();
        }
	}

	public function getRandomTID(): array
	{
		$returnArray = Array();

		$sql = 'SELECT DISTINCT t.tid, t.SciName, tv.vernacularname '.
		'FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID '.
		'LEFT JOIN taxavernaculars tv ON ts.tid = tv.TID '.
		'WHERE (ts.taxauthid = 1) AND (t.RankId >= 140) '.
		'ORDER BY RAND() '.
		'LIMIT 1';

		$commonNames = '';
		$result = $this->conn->query($sql);
		$tid = 0;
		$sciName = '';
		while($row = $result->fetch_object()) {
			if($tid === $row->tid) {
				if($row->vernacularname) {
					if($commonNames) {
						$commonNames .= '; ' . $row->vernacularname;
					}
					else {
						$commonNames = $row->vernacularname;
					}
				}
			} else {
				if($tid) {
					if($commonNames) {
						$returnArray[$tid] = $sciName . ' [' . $commonNames . ']';
					}
					else {
						$returnArray[$tid] = $sciName;
					}
				}
				$tid = $row->tid;
				$sciName = $row->SciName;
				$commonNames = $row->vernacularname;
			}
	    }
		if(!array_key_exists($tid, $returnArray)) {
			if($commonNames) {
				$returnArray[$tid] = $sciName . ' [' . $commonNames . ']';
			}
			else {
				$returnArray[$tid] = $sciName;
			}
		}
	    $result->free();
	    return $returnArray;
	}	

}
