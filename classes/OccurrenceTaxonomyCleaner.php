<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceTaxonomyCleaner extends Manager{

	private $collid = 0;

	public function __construct(){
		parent::__construct();
	}

	public function protectGlobalSpecies($collid = null): int
    {
        $status = 0;
        $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'SET o.localitySecurity = 1 WHERE t.securitystatus = 1 ';
        if($collid) {
            $sql .= 'AND o.collid = ' . $collid . ' ';
        }
        if($this->conn->query($sql)){
            $status += $this->conn->affected_rows;
        }
        $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'SET o.localitySecurity = 0 '.
            'WHERE t.TID IS NOT NULL AND t.securitystatus <> 1 ';
        if($collid) {
            $sql2 .= 'AND o.collid = ' . $collid . ' ';
        }
        if($this->conn->query($sql2)){
            $status += $this->conn->affected_rows;
        }
        return $status;
    }

	public function setCollId($collid): void
	{
		if(is_numeric($collid)){
			$this->collid = (int)$collid;
		}
	}
}
