<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');
include_once(__DIR__ . '/Sanitizer.php');

class ChecklistLoaderManager {

	private $conn;
	private $clid;
	private $clMeta = array();
	private $problemTaxa = array();
	private $errorArr = array();
	private $errorStr = '';

	public function __construct(){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function uploadCsvList($thesId): int
	{
		set_time_limit(300);
		ini_set('max_input_time',300);
		ini_set('auto_detect_line_endings', true);
		$successCnt = 0;

		$fh = fopen($_FILES['uploadfile']['tmp_name'], 'rb') or die("Can't open file. File may be too large. Try uploading file in sections.");

		$headerArr = array();
		$headerData = fgetcsv($fh);
		foreach($headerData as $k => $v){
			$vStr = strtolower($v);
			$vStr = str_replace(Array(' ','.','_'),'',$vStr);
			if(in_array($vStr, Array('scientificnamewithauthor', 'scientificname', 'taxa', 'speciesname', 'taxon'))){
				$vStr = 'sciname';
			}
			if(is_string($vStr) || is_int($vStr)){
                $headerArr[$vStr] = $k;
            }
		}
		if(array_key_exists('sciname',$headerArr)){
			$cnt = 0;
			flush();
			while($valueArr = fgetcsv($fh)){
				if($valueArr){
                    $sciNameStr = Sanitizer::cleanInStr($valueArr[$headerArr['sciname']]);
                    if($sciNameStr){
                        $tid = 0;
                        $rankId = 0;
                        $family = '';
                        $sciNameArr = (new TaxonomyUtilities)->parseScientificName($sciNameStr);
                        if($thesId && is_numeric($thesId)){
                            $sql = 'SELECT t2.tid, t.sciname, ts.family, t2.rankid '.
                                'FROM (taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid) '.
                                'INNER JOIN taxa t2 ON ts.tidaccepted = t2.tid '.
                                'WHERE (ts.taxauthid = '.$thesId.') ';
                        }
                        else{
                            $sql = 'SELECT t.tid, t.sciname, ts.family, t.rankid '.
                                'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
                                'WHERE ts.taxauthid = 1 ';
                        }
                        $cleanSciName = $this->encodeString($sciNameArr['sciname']);
                        $sql .= 'AND (t.sciname IN("'.$sciNameStr.'"'.($cleanSciName?',"'.$cleanSciName.'"':'').'))';
                        $rs = $this->conn->query($sql);
                        if($rs){
                            while($row = $rs->fetch_object()){
                                $tid = $row->tid;
                                $rankId = $row->rankid;
                                $family = $row->family;
                                if($sciNameStr === $row->sciname) {
                                    break;
                                }
                            }
                            $rs->free();
                        }

                        //Load taxon into checklist
                        if($tid){
                            if($rankId >= 180){
                                $sqlInsert = '';
                                $sqlValues = '';
                                if(array_key_exists('family',$headerArr) && $valueArr[$headerArr['family']]){
                                    $famValue = Sanitizer::cleanInStr($valueArr[$headerArr['family']]);
                                    if(strcasecmp($family, $famValue)){
                                        $sqlInsert .= ',familyoverride';
                                        $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['family']]).'"';
                                    }
                                }
                                if(array_key_exists('habitat',$headerArr) && $valueArr[$headerArr['habitat']]){
                                    $sqlInsert .= ',habitat';
                                    $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['habitat']]).'"';
                                }
                                if(array_key_exists('abundance',$headerArr) && $valueArr[$headerArr['abundance']]){
                                    $sqlInsert .= ',abundance';
                                    $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['abundance']]).'"';
                                }
                                if(array_key_exists('notes',$headerArr) && $valueArr[$headerArr['notes']]){
                                    $sqlInsert .= ',notes';
                                    $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['notes']]).'"';
                                }
                                if(array_key_exists('internalnotes',$headerArr) && $valueArr[$headerArr['internalnotes']]){
                                    $sqlInsert .= ',internalnotes';
                                    $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['internalnotes']]).'"';
                                }
                                if(array_key_exists('source',$headerArr) && $valueArr[$headerArr['source']]){
                                    $sqlInsert .= ',source';
                                    $sqlValues .= ',"'.Sanitizer::cleanInStr($valueArr[$headerArr['source']]).'"';
                                }

                                $sql = 'INSERT INTO fmchklsttaxalink (tid,clid'.$sqlInsert.') VALUES ('.$tid.', '.$this->clid.$sqlValues.')';
                                //echo $sql; exit;
                                if($this->conn->query($sql)){
                                    $successCnt++;
                                }
                                else{
                                    $this->errorArr[] = $sciNameStr." (TID = $tid) failed to load<br />Error msg: ".$this->conn->error;
                                    //echo $sql."<br />";
                                }
                            }
                            else{
                                $this->errorArr[] = $sciNameStr. ' failed to load (taxon must be of genus, species, or infraspecific ranking)';
                            }
                        }
                        else{
                            $this->problemTaxa[] = $cleanSciName;
                        }
                        $cnt++;
                        if($cnt%500 === 0) {
                            echo '<li style="margin-left:10px;">'.$cnt.' taxa loaded</li>';
                            flush();
                        }
                    }
                }
			}
			fclose($fh);
			if($cnt && $this->clMeta['type'] === 'rarespp'){
				$occurMain = new OccurrenceMaintenance($this->conn);
				$occurMain->protectStateRareSpecies();
			}
		}
		else{
			$this->errorStr = 'ERROR: unable to locate scientific name column';
		}
		return $successCnt;
	}

	public function resolveProblemTaxa(): void
	{
		if($this->problemTaxa){
			echo '<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">';
			echo '<tr><th>Cnt</th><th>Name</th><th>Actions</th></tr>';
			$cnt = 1;
			foreach($this->problemTaxa as $nameStr){
				echo '<tr>';
				echo '<td>'.$cnt.'</td>';
				echo '<td>'.$nameStr.'</td>';
				echo '<td>';
				echo '</td>';
				echo '</tr>';
				flush();
				$cnt++;
			}
			echo '</table>';
		}
	}

    public function setClid($c): void
	{
		if($c && is_numeric($c)){
			$this->clid = $c;
			$this->setChecklistMetadata();
		}
	}

	public function getProblemTaxa(): array
	{
		return $this->problemTaxa;
	}

	public function getErrorArr(): array
	{
		return $this->errorArr;
	}

	public function getErrorStr(): string
	{
		return $this->errorStr;
	}

	private function setChecklistMetadata(): void
	{
		if($this->clid){
			$sql = 'SELECT name, authors, type '.
				'FROM fmchecklists '.
				'WHERE clid = '.$this->clid;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->clMeta['name'] = $row->name;
				$this->clMeta['authors'] = $row->authors;
				$this->clMeta['type'] = $row->type;
			}
			$rs->free();
		}
	}

	public function getChecklistMetadata(): array
	{
		return $this->clMeta;
	}

	public function getThesauri(): array
	{
		$retArr = array();
		$sql = 'SELECT taxauthid, name FROM taxauthority WHERE isactive = 1';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->taxauthid] = $row->name;
		}
		return $retArr;
	}

	private function encodeString($inStr): string
	{
		$retStr = $inStr;
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr= str_replace($search, $replace, $inStr);

		if($inStr){
			if(strtolower($GLOBALS['CHARSET']) === 'utf-8' || strtolower($GLOBALS['CHARSET']) === 'utf8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif(strtolower($GLOBALS['CHARSET']) === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
 		}
		return $retStr;
	}
}
