<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class GlossaryUpload{
	
	private $conn;
	private $uploadFileName;
	private $uploadTargetPath;
	private $statArr = array();
	
	private $verboseMode = 1;
	private $logFH;
	private $errorStr = '';
	
	public function __construct() {
		$connection = new DbService();
		$this->conn = $connection->getConnection();
 		$this->setUploadTargetPath();
 		set_time_limit(3000);
		ini_set('max_input_time',120);
  	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
            fclose($this->logFH);
        }
	}
	
	public function setUploadFile($ulFileName = null): void
	{
		if($ulFileName){
			if(file_exists($ulFileName)){
				$pos = strrpos($ulFileName, '/');
				if(!$pos) {
					$pos = strrpos($ulFileName, "\\");
				}
				$this->uploadFileName = substr($ulFileName,$pos+1);
				copy($ulFileName,$this->uploadTargetPath.$this->uploadFileName);
			}
		}
		elseif(array_key_exists('uploadfile',$_FILES)){
			$this->uploadFileName = $_FILES['uploadfile']['name'];
			if(is_writable($this->uploadTargetPath)){
                move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->uploadFileName);
            }
		}
		if(file_exists($this->uploadTargetPath.$this->uploadFileName) && substr($this->uploadFileName,-4) === '.zip'){
			$zip = new ZipArchive;
			$zip->open($this->uploadTargetPath.$this->uploadFileName);
			$zipFile = $this->uploadTargetPath.$this->uploadFileName;
			$this->uploadFileName = $zip->getNameIndex(0);
			$zip->extractTo($this->uploadTargetPath);
			$zip->close();
			unlink($zipFile);
		}
	}

	public function loadFile($fieldMap,$languageArr,$tidStr,$batchSources): void
	{
		$batchSources = SanitizerService::cleanInStr($this->conn,$this->encodeString($batchSources));
		$this->outputMsg('Starting Upload');
		$this->conn->query('TRUNCATE TABLE uploadglossary');
		$this->conn->query('OPTIMIZE TABLE uploadglossary');
		$fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb') or die("Can't open file");
		$newTermColumn = false;
		foreach($languageArr as $lang){
			foreach($fieldMap as $csvField => $field){
				if($field === $lang.'_term'){
					$newTermColumn = true;
				}
			}
		}
		if($newTermColumn){
			$this->conn->query('SET autocommit=0');
			$this->conn->query('SET unique_checks=0');
			$this->conn->query('SET foreign_key_checks=0');
			$id = 1;
			$recordCnt = 0;
			while($recordArr = fgetcsv($fh)){
				if($recordArr){
                    foreach($languageArr as $lang){
                        $term = '';
                        $definition = '';
                        $source = '';
                        $author = '';
                        $translator = '';
                        $notes = '';
                        $resourceUrl = '';
                        $synonym = '';
                        foreach($fieldMap as $csvField => $field){
                            if($field === $lang.'_term'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $term = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_definition'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $definition = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                                if(strlen($definition) > 2000){
                                    $definition = '';
                                    $this->outputMsg('Definition for '.$term.' in '.ucfirst($lang).' is more than 2000 characters and was set to NULL.');
                                }
                            }
                            if($field === $lang.'_source'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $source = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_author'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $author = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_translator'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $translator = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_notes'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $notes = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_resourceurl'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $resourceUrl = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                            if($field === $lang.'_synonym'){
                                $index = array_search($csvField, array_keys($fieldMap), true);
                                if(is_string($index) || is_int($index)){
                                    $synonym = SanitizerService::cleanInStr($this->conn,$this->encodeString($recordArr[$index]));
                                }
                            }
                        }
                        if($term){
                            $sql = 'INSERT INTO uploadglossary(term,definition,`language`,source,author,translator,notes,resourceurl,tidStr,newGroupId) ';
                            if($source){
                                $sql .= 'VALUES ("'.$term.'",'.($definition?'"'.$definition.'"':'null').',"'.ucfirst($lang).'","'.$source.'",';
                            }
                            else{
                                $sql .= 'VALUES ("'.$term.'",'.($definition?'"'.$definition.'"':'null').',"'.ucfirst($lang).'",'.($batchSources?'"'.$batchSources.'"':'null').',';
                            }
                            $sql .= ($author?'"'.$author.'"':'null').','.($translator?'"'.$translator.'"':'null').','.($notes?'"'.$notes.'"':'null').','.($resourceUrl?'"'.$resourceUrl.'"':'null').',"'.$tidStr.'",'.$id.')';
                            //echo "<div>".$sql."</div>";
                            if($this->conn->query($sql)){
                                $recordCnt++;
                                if($recordCnt%1000 === 0){
                                    $this->outputMsg('Upload count: '.$recordCnt,1);
                                    flush();
                                }
                            }
                            else{
                                $this->outputMsg('ERROR loading term.');
                            }
                            if($synonym){
                                $sql = 'INSERT INTO uploadglossary(term,definition,`language`,source,author,translator,notes,resourceurl,tidStr,synonym,newGroupId) ';
                                if($source){
                                    $sql .= 'VALUES ("'.$synonym.'",'.($definition?'"'.$definition.'"':'null').',"'.ucfirst($lang).'","'.$source.'",';
                                }
                                else{
                                    $sql .= 'VALUES ("'.$synonym.'",'.($definition?'"'.$definition.'"':'null').',"'.ucfirst($lang).'",'.($batchSources?'"'.$batchSources.'"':'null').',';
                                }
                                $sql .= ($author?'"'.$author.'"':'null').','.($translator?'"'.$translator.'"':'null').','.($notes?'"'.$notes.'"':'null').','.($resourceUrl?'"'.$resourceUrl.'"':'null').',"'.$tidStr.'",1,'.$id.')';
                                //echo "<div>".$sql."</div>";
                                if($this->conn->query($sql)){
                                    $recordCnt++;
                                    if($recordCnt%1000 === 0){
                                        $this->outputMsg('Upload count: '.$recordCnt,1);
                                        flush();
                                    }
                                }
                                else{
                                    $this->outputMsg('ERROR loading term.');
                                }
                            }
                        }
                    }
                }
				$id++;
			}
			$this->conn->query('COMMIT');
			$this->conn->query('SET autocommit=1');
			$this->conn->query('SET unique_checks=1');
			$this->conn->query('SET foreign_key_checks=1');
		}
		else{
			$this->outputMsg('ERROR: Terms not mapped to appropriate Target Field');
		}
		fclose($fh);
		$this->setUploadCount();
	}

	public function cleanUpload($tidStr): void
	{
		$this->outputMsg('Linking terms already in database... ');
		$sql = 'UPDATE uploadglossary AS ug LEFT JOIN glossary AS g ON ug.term = g.term AND ug.`language` = g.`language` '.
			'LEFT JOIN glossarytermlink AS gt ON g.glossid = gt.glossid '.
			'LEFT JOIN glossarytaxalink AS gx ON gt.glossgrpid = gx.glossid '.
			'SET ug.currentGroupId = gt.glossgrpid, ug.term = NULL '.
			'WHERE gx.tid IN('.$tidStr.') ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Linking term.',1);
		}
		$sql = 'UPDATE uploadglossary AS u1 LEFT JOIN uploadglossary AS u2 ON u1.newGroupId = u2.newGroupId '.
			'SET u2.currentGroupId = u1.currentGroupId '. 
			'WHERE u1.currentGroupId IS NOT NULL AND ISNULL(u1.term) AND u2.term IS NOT NULL ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Linking term.',1);
		}
		
	}
	
	public function analysisUpload(): void
	{
		$sql1 = 'SELECT count(*) as cnt FROM uploadglossary';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$this->statArr['total'] = $r1->cnt;
		}
		$rs1->free();

		$sql2 = 'SELECT count(*) as cnt FROM uploadglossary WHERE ISNULL(term)';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			$this->statArr['exist'] = $r2->cnt;
			$this->statArr['new'] = $this->statArr['total'] - $this->statArr['exist'];
		}
		$rs2->free();
	}

	public function transferUpload(): void
	{
		$languageArr = array();
		$this->outputMsg('Starting data transfer...');
		
		$sql = 'SELECT COUNT(*) AS cnt FROM uploadglossary WHERE currentGroupId IS NOT NULL';
		$rs = $this->conn->query($sql);
		$r = $rs->fetch_object();
		$existingTerms = $r->cnt;
		
		$sql = 'SELECT DISTINCT tidStr FROM uploadglossary';
		$rs = $this->conn->query($sql);
		$r = $rs->fetch_object();
		$tidStr = $r->tidStr;
		
		$sql = 'SELECT DISTINCT `language` FROM uploadglossary';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$languageArr[] = $r->language;
			}
			$rs->close();
		}
		
		$this->outputMsg('Transferring terms to glossary table... ');
		if($existingTerms){
			$this->outputMsg('Adding translations for existing terms... ');
			$sql = 'INSERT INTO glossary(term,definition,`language`,source,notes,resourceurl,uid) '.
				'SELECT term, definition, `language`, source, notes, resourceurl, '.$GLOBALS['SYMB_UID'].' '.
				'FROM uploadglossary '.
				'WHERE term IS NOT NULL AND currentGroupId IS NOT NULL ';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: Transferring terms.',1);
			}
			
			$this->outputMsg('Linking translations to existing terms... ');
			$sql = 'INSERT INTO glossarytermlink(glossgrpid,glossid) '.
				'SELECT DISTINCT ug.currentGroupId, g.glossid '.
				'FROM glossary AS g LEFT JOIN uploadglossary AS ug ON g.term = ug.term AND g.`language` = ug.`language` '.
				'LEFT JOIN glossarytaxalink AS gx ON ug.currentGroupId = gx.glossid '.
				'WHERE ug.term IS NOT NULL AND ug.currentGroupId IS NOT NULL AND gx.tid IN('.$tidStr.') ';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: Transferring terms.',1);
			}
		}
		
		if(in_array('English', $languageArr, true)){
			$primaryLanguage = 'English';
		}
		else{
			$primaryLanguage = $languageArr[0];
		}
		
		$tidArr = explode(',',$tidStr);
		
		$this->outputMsg('Adding new '.$primaryLanguage.' terms... ');
		$sql = 'INSERT INTO glossary(term,definition,`language`,source,translator,author,notes,resourceurl,uid) '.
			'SELECT term, definition, `language`, source, translator, author, notes, resourceurl, '.$GLOBALS['SYMB_UID'].' '.
			'FROM uploadglossary '.
			'WHERE term IS NOT NULL AND ISNULL(currentGroupId) AND `language` = "'.$primaryLanguage.'" ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Adding new terms.',1);
		}
		
		$this->outputMsg('Adding new '.$primaryLanguage.' term links... ');
		$sql = 'INSERT INTO glossarytermlink(glossgrpid,glossid) '.
			'SELECT DISTINCT g.glossid, g.glossid '.
			'FROM glossary AS g LEFT JOIN uploadglossary AS ug ON g.term = ug.term AND g.`language` = ug.`language` '.
			'WHERE ug.term IS NOT NULL AND ISNULL(ug.currentGroupId) AND ug.`language` = "'.$primaryLanguage.'" '.
			'AND g.glossid NOT IN(SELECT glossid FROM glossarytermlink) ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Adding new terms.',1);
		}
		
		$this->outputMsg('Linking synonyms to new '.$primaryLanguage.' terms... ');
		$sql = 'INSERT INTO glossarytermlink(glossgrpid,glossid,relationshipType) '.
			'SELECT DISTINCT gt.glossgrpid, g1.glossid, "synonym" '.
			'FROM glossary AS g1 LEFT JOIN uploadglossary AS ug1 ON g1.term = ug1.term AND g1.`language` = ug1.`language` '.
			'LEFT JOIN uploadglossary AS ug2 ON ug1.newGroupId = ug2.newGroupId '.
			'LEFT JOIN glossary AS g2 ON g2.term = ug2.term AND g2.`language` = ug2.`language` '.
			'LEFT JOIN glossarytermlink AS gt ON g2.glossid = gt.glossid '.
			'WHERE ug1.term IS NOT NULL AND ISNULL(ug1.currentGroupId) AND ug1.`language` = "'.$primaryLanguage.'" '.
			'AND ug2.term IS NOT NULL AND ISNULL(ug2.currentGroupId) '.
			'AND ug2.`language` = "'.$primaryLanguage.'" AND ISNULL(ug2.synonym) '.
			'AND g1.glossid NOT IN(SELECT glossid FROM glossarytermlink) AND ug1.synonym = 1 ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Linking synonyms.',1);
		}
		$sql = 'INSERT INTO glossarytermlink(glossgrpid,glossid,relationshipType) '.
			'SELECT DISTINCT gt.glossgrpid, g1.glossid, "synonym" '.
			'FROM glossary AS g1 LEFT JOIN uploadglossary AS ug1 ON g1.term = ug1.term AND g1.`language` = ug1.`language` '.
			'LEFT JOIN uploadglossary AS ug2 ON ug1.newGroupId = ug2.newGroupId '.
			'LEFT JOIN glossary AS g2 ON g2.term = ug2.term AND g2.`language` = ug2.`language` '.
			'LEFT JOIN glossarytermlink AS gt ON g2.glossid = gt.glossid '.
			'WHERE ug1.term IS NOT NULL AND ISNULL(ug1.currentGroupId) AND ug1.`language` = "'.$primaryLanguage.'" '.
			'AND ug2.term IS NOT NULL AND ISNULL(ug2.currentGroupId) '.
			'AND ug2.`language` = "'.$primaryLanguage.'" AND ISNULL(ug2.synonym) '.
			'AND g1.glossid NOT IN(SELECT glossid FROM glossarytermlink) AND ug2.synonym = 1 ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: Linking synonyms.',1);
		}
		
		$this->outputMsg('Linking taxa to new '.$primaryLanguage.' terms... ');
		foreach($tidArr as $tId){
			$sql = 'INSERT INTO glossarytaxalink(glossid,tid) '.
				'SELECT DISTINCT gt.glossgrpid, '.$tId.' '.
				'FROM glossary AS g LEFT JOIN uploadglossary AS ug ON g.term = ug.term AND g.`language` = ug.`language` '.
				'LEFT JOIN glossarytermlink AS gt ON g.glossid = gt.glossid '.
				'WHERE ug.term IS NOT NULL AND ISNULL(ug.currentGroupId) AND ug.`language` = "'.$primaryLanguage.'" '.
				'AND gt.glossgrpid NOT IN(SELECT glossid FROM glossarytaxalink WHERE tid = '.$tId.') ';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: Linking taxa.',1);
			}
		}
		
		foreach($languageArr as $lang){
			if($lang !== $primaryLanguage){
				$this->outputMsg('Adding new '.$lang.' terms... ');
				$sql = 'INSERT INTO glossary(term,definition,`language`,source,translator,author,notes,resourceurl,uid) '.
					'SELECT term, definition, `language`, source, translator, author, notes, resourceurl, '.$GLOBALS['SYMB_UID'].' '.
					'FROM uploadglossary '.
					'WHERE term IS NOT NULL AND ISNULL(currentGroupId) AND `language` = "'.$lang.'" ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('ERROR: Adding new terms.',1);
				}
				
				$this->outputMsg('Linking new '.$lang.' translations to new '.$primaryLanguage.' terms... ');
				$sql = 'INSERT INTO glossarytermlink(glossgrpid,glossid) '.
					'SELECT DISTINCT gt.glossgrpid, g1.glossid '.
					'FROM glossary AS g1 LEFT JOIN uploadglossary AS ug1 ON g1.term = ug1.term AND g1.`language` = ug1.`language` '.
					'LEFT JOIN uploadglossary AS ug2 ON ug1.newGroupId = ug2.newGroupId '.
					'LEFT JOIN glossary AS g2 ON ug2.term = g2.term AND ug2.`language` = g2.`language` '.
					'LEFT JOIN glossarytermlink AS gt ON g2.glossid = gt.glossid '.
					'LEFT JOIN glossarytaxalink AS gx ON gt.glossgrpid = gx.glossid '.
					'WHERE ug1.term IS NOT NULL AND ISNULL(ug1.currentGroupId) AND ug1.`language` = "'.$lang.'" '.
					'AND ug2.`language` = "'.$primaryLanguage.'" AND gx.tid IN('.$tidStr.') '.
					'AND g1.glossid NOT IN(SELECT glossid FROM glossarytermlink) AND ISNULL(ug2.synonym) ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('ERROR: Linking new translations.',1);
				}
			}
		}
		$this->outputMsg('Done! ');
	}

	public function exportUploadTerms(): void
	{
		$fieldArr = array('term','definition','`language`','source','translator','author','notes','resourceurl');
		$fileName = 'termUpload_'.time().'.csv';
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$sql = 'SELECT '.implode(',',$fieldArr).' FROM uploadglossary WHERE term IS NOT NULL ';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$out = fopen('php://output', 'wb');
			echo implode(',',$fieldArr)."\n";
			while($r = $rs->fetch_assoc()){
				fputcsv($out, $r);
			}
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
		$rs->free();
	}

	private function setUploadCount(): void
	{
		$sql = 'SELECT count(*) as cnt FROM uploadglossary';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->statArr['upload'] = $r->cnt;
		}
		$rs->free();
	}

	public function getFieldArr(): array
	{
		$fieldArr = array();
		$languageArr = array();
		$targetFieldArr = $this->getUploadGlossaryFieldArr();
		$fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb') or die("Can't open file");
		$headerArr = fgetcsv($fh);
		foreach($headerArr as $field){
			$fieldStr = strtolower(trim($field));
			if($fieldStr){
				$fieldArr['source'][] = $fieldStr;
				$fieldStr = str_replace(' ', '_',$fieldStr);
				if(strpos($fieldStr,'_') === false){
					$languageArr[] = $fieldStr;
				}
			}
			else{
				break;
			}
		}
		$fieldArr['languages'] = json_encode($languageArr);
		foreach($languageArr as $lang){
			$fieldArr['target'][] = $lang.'_synonym';
			foreach($targetFieldArr as $target){
				$fieldArr['target'][] = $lang.'_'.$target;
			}
		}
		return $fieldArr;
	}
	
	private function getUploadGlossaryFieldArr(): array
	{
		$targetArr = array();
		$sql = 'SHOW COLUMNS FROM uploadglossary';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if($field !== 'language' && $field !== 'tidstr' && $field !== 'synonym' && $field !== 'newgroupid' && $field !== 'currentgroupid' && $field !== 'initialtimestamp'){
				$targetArr[$field] = $field;
			}
		}
		$rs->free();
		
		return $targetArr;
	}
	
	private function setUploadTargetPath(): void
	{
		$tPath = $GLOBALS['TEMP_DIR_ROOT'];
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $GLOBALS['SERVER_ROOT'];
			if(substr($tPath,-1) !== '/') {
				$tPath .= '/';
			}
			$tPath .= 'temp/downloads';
		}
		if(substr($tPath,-1) !== '/') {
			$tPath .= '/';
		}
		$this->uploadTargetPath = $tPath; 
	}

	public function setFileName($fName): void
	{
		$this->uploadFileName = $fName;
	}

	public function getFileName(){
		return $this->uploadFileName;
	}
	
	public function getStatArr(): array
	{
		return $this->statArr;
	}
	
	public function getErrorStr(): string
	{
		return $this->errorStr;
	}

	public function setVerboseMode($vMode): void
	{
		if(is_numeric($vMode)){
			$this->verboseMode = $vMode;
			if($this->verboseMode === 2){
				$GLOBALS['LOG_PATH'] = $GLOBALS['SERVER_ROOT'];
				$subStr = substr($GLOBALS['SERVER_ROOT'],-1);
				if($subStr !== '/' && $subStr !== '\\') {
					$GLOBALS['LOG_PATH'] .= '/';
				}
				$GLOBALS['LOG_PATH'] .= 'content/logs/glossaryloader_' .date('Ymd'). '.log';
				$this->logFH = fopen($GLOBALS['LOG_PATH'], 'ab');
				fwrite($this->logFH, 'Start time: ' .date('Y-m-d h:i:s A')."\n");
			}
		}
	}

	private function outputMsg($str, $indent = null): void
	{
		if($this->verboseMode > 0 || strncmp($str, 'ERROR', 5) === 0){
			echo '<li style="margin-left:'.(10*$indent).'px;'.(strncmp($str, 'ERROR', 5) === 0 ?'color:red':'').'">'.$str.'</li>';
			flush();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fwrite($this->logFH, ($indent ? str_repeat("\t", $indent) : '') . strip_tags($str) . "\n");
		}
	}

	private function encodeString($inStr): string
	{
		$retStr = $inStr;
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr= str_replace($search, $replace, $inStr);
		$badwordchars=array("\xe2\x80\x98",
							"\xe2\x80\x99",
							"\xe2\x80\x9c",
							"\xe2\x80\x9d",
							"\xe2\x80\x94",
							"\xe2\x80\xa6"
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		return str_replace($badwordchars, $fixedwordchars, $inStr);
	}
}
