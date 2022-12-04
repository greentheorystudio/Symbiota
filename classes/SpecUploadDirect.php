<?php
include_once(__DIR__ . '/SpecUploadBase.php');

class SpecUploadDirect extends SpecUploadBase {

	public function analyzeUpload(): bool
	{
		$status = false;
	    if($sourceConn = $this->getSourceConnection()){
			$sql = trim($this->queryStr);
			if(substr($sql,-1) === ';') {
				$sql = substr($sql, 0, -1);
			}
			if(strlen($sql) > 20 && stripos(substr($sql,-20), ' limit ') === false) {
				$sql .= ' LIMIT 10';
			}
			$rs = $sourceConn->query($sql);
			if($rs){
				$sourceArr = array();
				if($row = $rs->fetch_assoc()){
					foreach($row as $k => $v){
						$sourceArr[] = strtolower($k);
					}
                    $this->sourceArr = $sourceArr;
                    $status = true;
				}
				else{
					echo '<div style="font-weight:bold;color:red;margin:25px;">Query did not return any records</div>';
				}
				$rs->close();
			}
			else{
				echo '<div style="font-weight:bold;margin:15px;">ERROR</div>';
			}
			$sourceConn->close();
		}
		return $status;
	}

 	public function uploadData($finalTransfer): void
	{
 		$sourceConn = $this->getSourceConnection();
		if($sourceConn){
			$this->prepUploadData();
			echo "<li style='font-weight:bold;'>Connected to Source Database</li>";
			set_time_limit(800);
			$sourceConn->query('SET NAMES ' .str_replace('-','',strtolower($GLOBALS['CHARSET'])). ';');
			if($result = $sourceConn->query($this->queryStr)){
				echo "<li style='font-weight:bold;'>Results obtained from Source Connection, now reading Resultset... </li>";
				$this->transferCount = 0;
				while($row = $result->fetch_assoc()){
					$recMap = array();
					$row = array_change_key_case($row);
					foreach($this->fieldMap as $symbField => $sMap){
						$valueStr = $row[$sMap['field']];
						$recMap[$symbField] = $valueStr;
					}
					$this->loadRecord($recMap);
					unset($recMap);
				}
				$result->close();
				
				$this->cleanUpload();

				if($finalTransfer){
					$this->finalTransfer();
				}
			}
			else{
				echo "<hr /><div style='color:red;'>Unable to create a Resultset with the Source Connection. Check connection parameters, source sql statement, and firewall restriction</div>";
				echo '<div style="color:red;">ERROR</div><hr />';
			}
			$sourceConn->close();
		}
	}
	
	private function getSourceConnection() {
        $connection = null;
	    if(!$this->server || !$this->username || !$this->password || !$this->schemaName){
			echo "<div style='color:red;'>One of the required connection variables are null. Please resolve.</div>";
			return false;
		}

        $connection = new mysqli($this->server, $this->username, $this->password, $this->schemaName);
        if($connection->connect_error){
            echo "<div style='color:red;'>Could not connect to Source database!</div>";
            echo "<div style='color:red;'>ERROR: ".mysqli_connect_error(). '</div>';
        }
        return $connection;
    }

	public function getDbpkOptions(): array
	{
		$sFields = $this->sourceArr;
		sort($sFields);
		return $sFields;
	}
}
