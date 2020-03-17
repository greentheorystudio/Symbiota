<?php
include_once('DbConnection.php');
include_once('ImageLibraryManager.php');

// Random Search based on TaxonQuickSearchManager

class RandomSearchManager extends ImageLibraryManager{

	// Had to change from private to protected
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

	public function getRandomTID(){
		$returnArray = Array();

		// Query that selects one random taxon
		// Improve search to include only taxa with images?
		$sql = "SELECT DISTINCT t.tid, t.SciName, tv.vernacularname
		FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID
		LEFT JOIN taxavernaculars tv ON ts.tid = tv.TID
		WHERE (ts.taxauthid = 1) AND (t.RankId >= 140) 
		ORDER BY RAND()
		LIMIT 1";

		// Cleaning up result and fetching scientific name and vernaculars
		$result = $this->conn->query($sql);
		$commonNames = "";
		$result = $this->conn->query($sql);
		$tid = 0;
		$sciName = "";
		while($row = $result->fetch_object()) {
			if($tid == $row->tid) {
				if($row->vernacularname) {
					if($commonNames) $commonNames .= "; ".$row->vernacularname;
					else $commonNames = $row->vernacularname;
				}
			} else {
				if($tid) {
					if($commonNames) $returnArray[$tid] = $sciName." [".$commonNames."]";
					else $returnArray[$tid] = $sciName;
				}
				$tid = $row->tid;
				$sciName = $row->SciName;
				$commonNames = $row->vernacularname;
			}
	    }
		if(!array_key_exists($tid, $returnArray)) {
			if($commonNames) $returnArray[$tid] = $sciName." [".$commonNames."]";
			else $returnArray[$tid] = $sciName;
		}
	    $result->free();
	    return $returnArray;
	}	

}	
?>
