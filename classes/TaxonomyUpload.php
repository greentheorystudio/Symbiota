<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');
include_once($SERVER_ROOT.'/classes/TaxonomyUtilities.php');

class TaxonomyUpload{

	private $conn;
	private $uploadFileName;
	private $uploadTargetPath;
	private $taxAuthId = 1;
	private $statArr = array();

	private $verboseMode = 1; // 0 = silent, 1 = echo only, 2 = echo and log
	private $logFH;
	private $errorStr = '';

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
 		$this->setUploadTargetPath();
 		set_time_limit(3000);
		ini_set('max_input_time',120);
  		ini_set('auto_detect_line_endings', true);
	}

	public function __destruct(){
		if(!($this->conn === false)) {
			$this->conn->close();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fclose($this->logFH);
		}
	}

	public function setUploadFile($ulFileName = ''): void
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
			move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->uploadFileName);
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

	public function loadFile($fieldMap): void
	{
		$this->outputMsg('Starting Upload',0);
		$this->conn->query('DELETE FROM uploadtaxa');
		$this->conn->query('OPTIMIZE TABLE uploadtaxa');
		if(($fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb')) !== false){
			$headerArr = fgetcsv($fh);
			$uploadTaxaFieldArr = $this->getUploadTaxaFieldArr();
			$taxonUnitArr = $this->getTaxonUnitArr();
			$uploadTaxaIndexArr = array();
			$taxonUnitIndexArr = array();
			foreach($headerArr as $k => $sourceName){
				$sourceName = strtolower(trim($sourceName));
				if(array_key_exists($sourceName,$fieldMap)){
					$targetName = $fieldMap[$sourceName];
					if(in_array($targetName, $uploadTaxaFieldArr, true)){
						$uploadTaxaIndexArr[$k] = $targetName;
					}
					if(in_array($targetName, $taxonUnitArr, true)){
						$taxonUnitIndexArr[$k] = array_search($targetName, $taxonUnitArr, true);
					}
				}
			}
			$parentIndex = 0;
			if(!in_array('parentstr', $uploadTaxaIndexArr, true)){
				$parentIndex = max(array_keys($uploadTaxaIndexArr))+1;
				$uploadTaxaIndexArr[$parentIndex] = 'parentstr';
			}
			$familyIndex = 0;
			if(in_array('family', $fieldMap, true)) {
				$familyIndex = array_search(array_search('family', $fieldMap, true), $headerArr, true);
			}
			if(in_array('scinameinput', $fieldMap, true) || count($taxonUnitIndexArr) > 2){
				$recordCnt = 1;
				asort($taxonUnitIndexArr);
				$childParentArr = array();
				while($recordArr = fgetcsv($fh)){
					$parentStr = '';
					foreach($taxonUnitIndexArr as $index => $rankId){
						$taxonStr = $recordArr[$index];
						if($taxonStr){
							if(!array_key_exists($taxonStr,$childParentArr)){
								if($rankId === 10){
									$childParentArr[$taxonStr]['p'] = $taxonStr;
									$childParentArr[$taxonStr]['r'] = $rankId;
								}
								elseif($parentStr){
									$childParentArr[$taxonStr]['p'] = $parentStr;
									$childParentArr[$taxonStr]['r'] = $rankId;
									if($rankId > 140 && $familyIndex && $recordArr[$familyIndex]){
										$childParentArr[$taxonStr]['f'] = $recordArr[$familyIndex];
									}
								}
							}
							$parentStr = $taxonStr;
						}
					}
					if($parentIndex){
						$recordArr[$parentIndex] = 'PENDING:'.$parentStr;
					}
					if(in_array('scinameinput', $fieldMap, true)){
						$inputArr = array();
						foreach($uploadTaxaIndexArr as $recIndex => $targetField){
							$valIn = $this->cleanInStr($this->encodeString($recordArr[$recIndex]));
							if($targetField === 'acceptance' && !is_numeric($valIn)){
								$valInTest = strtolower($valIn);
								if($valInTest === 'accepted' || $valInTest === 'valid'){
									$valIn = 1;
								}
								elseif($valInTest === 'not accepted' || $valInTest === 'synonym'){
									$valIn = 0;
								}
								else{
									$valIn = '';
								}
							}
							if($valIn) {
								$inputArr[$targetField] = $valIn;
							}
						}
						if(array_key_exists('scinameinput', $inputArr)){
							if(isset($inputArr['unitname1'])){
                                $nameArr = array();
							    $sciArr['unitname1'] = $inputArr['unitname1'];
                                $nameArr[] = $inputArr['unitname1'];
                                $sciArr['unitname2'] = ($inputArr['unitname2'] ?? '');
                                if(isset($inputArr['unitname2'])){
                                    $nameArr[] = $inputArr['unitname2'];
                                }
                                $sciArr['unitind3'] = ($inputArr['unitind3'] ?? '');
                                if(isset($inputArr['unitind3'])){
                                    $nameArr[] = $inputArr['unitind3'];
                                }
                                $sciArr['unitname3'] = ($inputArr['unitname3'] ?? '');
                                if(isset($inputArr['unitname3'])){
                                    $nameArr[] = $inputArr['unitname3'];
                                }
                                $sciName = implode(' ', $nameArr);
                                $sciArr['sciname'] = $sciName;
                                $sciArr['rankid'] = ($inputArr['rankid'] ?? '');
                            }
							else{
                                $sciArr = (new TaxonomyUtilities)->parseScientificName($inputArr['scinameinput'],($inputArr['rankid'] ?? 0));
                            }
							foreach($sciArr as $sciKey => $sciValue){
								if(!array_key_exists($sciKey, $inputArr)) {
									$inputArr[$sciKey] = $sciValue;
								}
							}
							$sql1 = ''; $sql2 = '';
							unset($inputArr['identificationqualifier']);
							foreach($inputArr as $k => $v){
								$sql1 .= ','.$k;
								$inValue = $this->cleanInStr($v);
								$sql2 .= ','.($inValue?'"'.$inValue.'"':'NULL');
							}
							$sql = 'INSERT INTO uploadtaxa('.substr($sql1,1).') VALUES('.substr($sql2,1).')';
							//echo "<div>".$sql."</div>";
							if($this->conn->query($sql)){
								if($recordCnt%1000 === 0){
									$this->outputMsg('Upload count: '.$recordCnt,1);
									ob_flush();
									flush();
								}
							}
							else{
								$this->outputMsg('ERROR loading taxon: '.$this->conn->error);
							}
						}
						unset($inputArr);
					}
					$recordCnt++;
				}

				foreach($childParentArr as $taxon => $tArr){
					$sql = 'INSERT IGNORE INTO uploadtaxa(scinameinput,sciname,rankid,parentstr,family,acceptance) '.
						'VALUES ("'.$taxon.'","'.$taxon.'",'.$tArr['r'].',"'.$tArr['p'].'",'.(array_key_exists('f',$tArr)?'"'.$tArr['f'].'"':'NULL').',1)';
					if(!$this->conn->query($sql)){
						$this->outputMsg('ERROR loading taxonunit: '.$this->conn->error);
					}
				}
				$this->outputMsg($recordCnt.' taxon records pre-processed');
			}
			else{
				$this->outputMsg('ERROR: Scientific name is not mapped to &quot;scinameinput&quot;');
			}
			fclose($fh);
			$this->removeUploadFile();
			$this->setUploadCount();
		}
		else{
			echo 'ERROR thrown opening input file: '.$this->uploadTargetPath.$this->uploadFileName.'<br/>';
			if(!is_writable($this->uploadTargetPath)) {
				echo '<b>Target upload path is not writable. File permissions need to be adjusted</b>';
			}
			exit;
		}
	}

	private function removeUploadFile(): void
	{
		if($this->uploadTargetPath && $this->uploadFileName && file_exists($this->uploadTargetPath . $this->uploadFileName)) {
			unlink($this->uploadTargetPath.$this->uploadFileName);
		}
	}

    public function cleanUpload(): void
	{
        $sql = 'UPDATE uploadtaxa '.
            'SET RankName = "Superphylum" '.
            'WHERE RankName = "Superdivision"';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadtaxa '.
            'SET RankName = "Phylum" '.
            'WHERE RankName = "Division"';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadtaxa '.
            'SET RankName = "Subphylum" '.
            'WHERE RankName = "Subdivision"';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadtaxa '.
            'SET RankName = "Infraphylum" '.
            'WHERE RankName = "Infradivision"';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadtaxa '.
            'SET RankName = "Parvphylum" '.
            'WHERE RankName = "Parvdivision"';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }

		$sql = 'UPDATE uploadtaxa u INNER JOIN uploadtaxa u2 ON u.sourceParentId = u2.sourceId '.
			'SET u.parentstr = u2.sciname '.
			'WHERE ISNULL(u.parentstr) AND u.sourceParentId IS NOT NULL AND u2.sourceId IS NOT NULL';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u INNER JOIN uploadtaxa u2 ON u.sourceAcceptedId = u2.sourceId '.
			'SET u.acceptedstr = u2.sciname '.
			'WHERE ISNULL(u.acceptedstr) AND u.sourceAcceptedId IS NOT NULL AND u2.sourceId IS NOT NULL';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$sql = 'DELETE FROM uploadtaxa WHERE ISNULL(sciname)';
		$this->conn->query($sql);

		$this->outputMsg('Linking names already in thesaurus... ');
		$sql = 'UPDATE uploadtaxa u INNER JOIN taxa t ON u.sciname = t.sciname SET u.tid = t.tid WHERE ISNULL(u.tid)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.acceptedstr = u2.scinameinput '.
			'SET u1.tidaccepted = u2.tid '.
			'WHERE ISNULL(u1.tidaccepted) AND u2.tid IS NOT NULL';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u INNER JOIN taxa t ON u.acceptedstr = t.sciname '.
			'SET u.tidaccepted = t.tid '.
			'WHERE ISNULL(u.tidaccepted)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$this->outputMsg('Populating null family values... ');
		$sql = 'UPDATE uploadtaxa ut INNER JOIN taxa t ON ut.unitname1 = t.sciname '.
			'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'SET ut.family = ts.family '.
			'WHERE ts.taxauthid = '.$this->taxAuthId.' AND (ut.rankid > 140) AND (t.rankid = 180) AND (ts.family IS NOT NULL) AND ISNULL(ut.family)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.sourceParentId = u2.sourceId '.
			'SET u1.family = u2.sciname '.
			'WHERE u2.sourceId IS NOT NULL AND u1.sourceParentId IS NOT NULL AND u2.rankid = 140 AND ISNULL(u1.family) ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.unitname1 = u2.sciname '.
			'SET u1.family = u2.family '.
			'WHERE ISNULL(u1.family) AND u2.family IS NOT NULL ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.sourceAcceptedId = u2.sourceId '.
			'SET u1.family = u2.family '.
			'WHERE u1.sourceAcceptedId IS NOT NULL AND  u2.sourceId IS NOT NULL AND ISNULL(u1.family) AND u2.family IS NOT NULL ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.unitname1 = u2.sciname '.
			'INNER JOIN uploadtaxa u3 ON u2.sourceParentId = u3.sourceId '.
			'SET u1.family = u3.sciname '.
			'WHERE u2.sourceParentId IS NOT NULL AND u3.sourceId IS NOT NULL '.
			'AND ISNULL(u1.family) AND u1.rankid > 140 AND u2.rankid = 180 AND u3.rankid = 140';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u0 INNER JOIN uploadtaxa u1 ON u0.sourceAcceptedId = u1.sourceid '.
			'SET u0.family = u1.family '.
			'WHERE u0.sourceParentId IS NOT NULL AND u1.sourceId IS NOT NULL '.
			'AND ISNULL(u0.family) AND u0.rankid > 140 AND u1.family IS NOT NULL';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u0 INNER JOIN uploadtaxa u1 ON u0.scinameinput = u1.acceptedstr '.
			'SET u0.family = u1.family '.
			'WHERE ISNULL(u0.family) AND u0.rankid > 140 AND u1.family IS NOT NULL';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa SET family = NULL WHERE family = ""';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$this->outputMsg('Loading vernaculars... ');
		$this->transferVernaculars();

		$this->outputMsg('Set null author values... ');
		$sql = 'UPDATE uploadtaxa '.
			'SET author = TRIM(SUBSTRING(scinameinput,LENGTH(sciname)+1)) '.
			'WHERE ISNULL(author) AND (rankid <= 220)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa '.
			'SET author = TRIM(SUBSTRING(scinameinput,LOCATE(unitind3,scinameinput)+LENGTH(CONCAT_WS(" ",unitind3,unitname3)))) '.
			'WHERE ISNULL(author) AND rankid > 220';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa SET author = NULL WHERE author = ""';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$this->outputMsg('Populating and mapping parent taxon... ');
		$sql = 'UPDATE uploadtaxa '.
			'SET parentstr = CONCAT_WS(" ", unitname1, unitname2) '.
			'WHERE (ISNULL(parentstr) OR (parentstr LIKE "PENDING:%")) AND (rankid > 220)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa SET parentstr = unitname1 '.
			'WHERE ((parentstr IS NULL) OR (parentstr LIKE "PENDING:%")) AND (rankid = 220)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa SET parentstr = family '.
			'WHERE ((parentstr IS NULL) OR (parentstr LIKE "PENDING:%")) AND (rankid = 180)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa SET parentstr = SUBSTRING(parentstr,9) '.
			'WHERE (parentstr LIKE "PENDING:%")';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
		$sql = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.sourceAcceptedID = u2.sourceId '.
			'SET u1.sourceParentId = u2.sourceParentId, u1.parentStr = u2.parentStr '.
			'WHERE ISNULL(u1.sourceParentId) AND (u1.sourceAcceptedID IS NOT NULL) AND (u2.sourceParentId IS NOT NULL) AND (u1.rankid < 220) ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$sql = 'UPDATE uploadtaxa up INNER JOIN taxa t ON up.parentstr = t.sciname '.
			'SET parenttid = t.tid WHERE ISNULL(parenttid)';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$this->outputMsg('Add parents that are not yet in uploadtaxa table... ');
		$sql = 'INSERT IGNORE INTO uploadtaxa(scinameinput, SciName, family, RankId, UnitName1, UnitName2, parentstr, Source) '.
			'SELECT DISTINCT ut.parentstr, ut.parentstr, ut.family, 220 as r, ut.unitname1, ut.unitname2, ut.unitname1, ut.source '.
			'FROM uploadtaxa ut LEFT JOIN uploadtaxa ut2 ON ut.parentstr = ut2.sciname '.
			'WHERE (ut.parentstr <> "") AND (ut.parentstr IS NOT NULL) AND ISNULL(ut.parenttid) AND (ut.rankid > 220) AND ISNULL(ut2.sciname) ';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadtaxa up INNER JOIN taxa t ON up.parentstr = t.sciname '.
			'SET up.parenttid = t.tid '.
			'WHERE ISNULL(up.parenttid)';
		$this->conn->query($sql);

		$sql = 'INSERT IGNORE INTO uploadtaxa (scinameinput, SciName, family, RankId, UnitName1, parentstr, Source) '.
			'SELECT DISTINCT ut.parentstr, ut.parentstr, ut.family, 180 as r, ut.unitname1, ut.family, ut.source '.
			'FROM uploadtaxa ut LEFT JOIN uploadtaxa ut2 ON ut.parentstr = ut2.sciname '.
			'WHERE ut.parentstr <> "" AND ut.parentstr IS NOT NULL AND ISNULL(ut.parenttid) AND ut.family IS NOT NULL AND ut.rankid = 220 AND ISNULL(ut2.sciname)';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadtaxa up LEFT JOIN taxa t ON up.parentstr = t.sciname '.
			'SET up.parenttid = t.tid '.
			'WHERE ISNULL(up.parenttid)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadtaxa '.
			'SET acceptance = 0 '.
			'WHERE (acceptedstr IS NOT NULL) AND (sciname IS NOT NULL) AND (sciname <> acceptedstr)';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadtaxa '.
			'SET acceptance = 1 '.
			'WHERE ISNULL(acceptedstr) AND ISNULL(TidAccepted)';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadtaxa '.
			'SET acceptance = 1 '.
			'WHERE ISNULL(sciname) AND (sciname = acceptedstr)';
		$this->conn->query($sql);
		$this->outputMsg('Done processing taxa');
	}

	public function analysisUpload(): array
	{
		$retArr = array();
		$sql1 = 'SELECT count(*) as cnt FROM uploadtaxa';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$this->statArr['total'] = $r1->cnt;
		}
		$rs1->free();

		$sql2 = 'SELECT count(*) as cnt FROM uploadtaxa WHERE tid IS NOT NULL';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			$this->statArr['exist'] = $r2->cnt;
			$this->statArr['new'] = $this->statArr['total'] - $this->statArr['exist'];
		}
		$rs2->free();

		$sql3 = 'SELECT acceptance, count(*) AS cnt '.
			'FROM uploadtaxa '.
			'GROUP BY acceptance';
		$rs3 = $this->conn->query($sql3);
		while($r3 = $rs3->fetch_object()){
			if($r3->acceptance === 0) {
				$this->statArr['nonaccepted'] = $r3->cnt;
			}
			if($r3->acceptance === 1) {
				$this->statArr['accepted'] = $r3->cnt;
			}
		}
		$rs3->free();

		$sql4 = 'UPDATE uploadtaxa SET ErrorStatus = "FAILED: Unable to parse input scientific name" WHERE ISNULL(sciname)';
		if(!$this->conn->query($sql4)){
			$this->outputMsg('ERROR tagging non-parsed names: '.$this->conn->error,1);
		}

		$sql5 = 'UPDATE uploadtaxa u1 LEFT JOIN uploadtaxa u2 ON u1.acceptedStr = u2.sciname '.
			'SET u1.ErrorStatus = "FAILED: Non-accepted taxa linked to non-existent taxon" '.
			'WHERE (u1.acceptance = 0) AND ISNULL(u1.tidAccepted) AND ISNULL(u2.sciname)';
		if(!$this->conn->query($sql5)){
			$this->outputMsg('ERROR tagging non-accepted taxon linked to non-existent taxon: '.$this->conn->error,1);
		}

		$sql6a = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.acceptedStr = u2.sciname '.
			'SET u1.ErrorStatus = "FAILED: Non-accepted linked to another non-accepted taxon" '.
			'WHERE (u1.acceptance = 0) AND (u2.acceptance = 0)';
		if(!$this->conn->query($sql6a)){
			$this->outputMsg('ERROR tagging non-accepted linked to non-accepted (#1): '.$this->conn->error,1);
		}
		$sql6b = 'UPDATE uploadtaxa u INNER JOIN taxstatus ts ON u.tidaccepted = ts.tid '.
			'SET u.ErrorStatus = "FAILED: Non-accepted linked to another non-accepted taxon already within database" '.
			'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (u.acceptance = 0) AND (ts.tid <> ts.tidaccepted)';
		if(!$this->conn->query($sql6b)){
			$this->outputMsg('ERROR tagging non-accepted linked to non-accepted (#2): '.$this->conn->error,1);
		}

		$sql6 = 'UPDATE uploadtaxa u1 LEFT JOIN uploadtaxa u2 ON u1.parentStr = u2.sciname '.
			'SET u1.ErrorStatus = "FAILED: Taxa with non-existent parent taxon" '.
			'WHERE (u1.RankId > 10) AND ISNULL(u1.tid) AND ISNULL(u1.parentTid) AND ISNULL(u2.sciname) ';
		if(!$this->conn->query($sql6)){
			$this->outputMsg('ERROR tagging taxa with non-existent parent taxon: '.$this->conn->error,1);
		}

		$loopCnt = 0;
		do{
			$sql8 = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.parentStr = u2.sciname '.
				'SET u1.ErrorStatus = "FAILED: Taxa linked to a FAILED parent" '.
				'WHERE (u2.ErrorStatus LIKE "FAILED%") AND (ISNULL(u1.ErrorStatus) OR (u1.ErrorStatus NOT LIKE "FAILED%"))';
			if(!$this->conn->query($sql8)){
				$this->outputMsg('ERROR tagging taxa with FAILED parents: '.$this->conn->error,1);
				break;
			}
			$loopCnt++;
			if($loopCnt > 20){
				$this->outputMsg('ERROR looping: too many parent loops',1);
				break;
			}
		} while($this->conn->affected_rows);

		$sql9 = 'UPDATE uploadtaxa u1 INNER JOIN uploadtaxa u2 ON u1.acceptedStr = u2.sciname '.
			'SET u1.ErrorStatus = "FAILED: Non-accepted taxa linked to a FAILED name" '.
			'WHERE (u1.acceptance = 0) AND (u1.ErrorStatus NOT LIKE "FAILED%") AND (u2.ErrorStatus LIKE "FAILED%")';
		if(!$this->conn->query($sql9)){
			$this->outputMsg('ERROR tagging non-accepeted linked to FAILED name: '.$this->conn->error,1);
		}

		$sql = 'SELECT errorstatus, count(*) as cnt FROM uploadtaxa WHERE ErrorStatus LIKE "FAILED%" GROUP BY ErrorStatus';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->statArr['bad'][substr($r->errorstatus,7)] = $r->cnt;
		}
		$rs->free();

		return $retArr;
	}

	public function transferUpload(): void
	{
		$this->outputMsg('Starting data transfer...');
		$sql = 'INSERT INTO taxa(SciName, RankId, UnitInd1, UnitName1, UnitInd2, UnitName2, UnitInd3, UnitName3, Author, Source, Notes) '.
			'SELECT DISTINCT SciName, RankId, UnitInd1, UnitName1, UnitInd2, UnitName2, UnitInd3, UnitName3, Author, Source, Notes '.
			'FROM uploadtaxa '.
			'WHERE ISNULL(TID) AND (rankid = 10)';
		if($this->conn->query($sql)){
			$sql = 'INSERT INTO taxstatus(tid, tidaccepted, taxauthid, parenttid) '.
				'SELECT DISTINCT t.tid, t.tid, '.$this->taxAuthId.', t.tid '.
				'FROM taxa t LEFT JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE (t.rankid = 10) AND ISNULL(ts.tid)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: '.$this->conn->error,1);
			}
		}
		else{
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}

		$loopCnt = 0;
		do{
			$this->outputMsg('Starting loop '.$loopCnt);
			$this->outputMsg('Transferring taxa to taxon table... ',1);
			$sql = 'INSERT IGNORE INTO taxa(SciName, RankId, UnitInd1, UnitName1, UnitInd2, UnitName2, UnitInd3, UnitName3, Author, Source, Notes) '.
				'SELECT DISTINCT SciName, RankId, UnitInd1, UnitName1, UnitInd2, UnitName2, UnitInd3, UnitName3, Author, Source, Notes '.
				'FROM uploadtaxa '.
				'WHERE ISNULL(tid) AND (parenttid IS NOT NULL) AND (rankid IS NOT NULL) AND ISNULL(ErrorStatus) '.
				'ORDER BY RankId ASC ';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR loading taxa: '.$this->conn->error,1);
			}

			$sql = 'UPDATE uploadtaxa ut INNER JOIN taxa t ON ut.sciname = t.sciname '.
				'SET ut.tid = t.tid WHERE ISNULL(ut.tid)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR populating TIDs: '.$this->conn->error,1);
			}

			$sql = 'UPDATE uploadtaxa ut1 INNER JOIN uploadtaxa ut2 ON ut1.sourceacceptedid = ut2.sourceid '.
				'INNER JOIN taxa t ON ut2.sciname = t.sciname '.
				'SET ut1.tidaccepted = t.tid '.
				'WHERE (ut1.acceptance = 0) AND ISNULL(ut1.tidaccepted) AND (ut1.sourceacceptedid IS NOT NULL) AND (ut2.sourceid IS NOT NULL)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: '.$this->conn->error,1);
			}

			$sql = 'UPDATE uploadtaxa ut INNER JOIN taxa t ON ut.acceptedstr = t.sciname '.
				'SET ut.tidaccepted = t.tid '.
				'WHERE (ut.acceptance = 0) AND ISNULL(ut.tidaccepted) AND (ut.acceptedstr IS NOT NULL)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: '.$this->conn->error,1);
			}

			$sql = 'UPDATE uploadtaxa SET tidaccepted = tid '.
				'WHERE (acceptance = 1) AND ISNULL(tidaccepted) AND (tid IS NOT NULL)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR: '.$this->conn->error,1);
			}

			$this->outputMsg('Create parent and accepted links... ',1);
			$sql = 'INSERT IGNORE INTO taxstatus(TID, TidAccepted, taxauthid, ParentTid, Family, UnacceptabilityReason) '.
				'SELECT DISTINCT TID, TidAccepted, '.$this->taxAuthId.', ParentTid, Family, UnacceptabilityReason '.
				'FROM uploadtaxa '.
				'WHERE (tid IS NOT NULL) AND (TidAccepted IS NOT NULL) AND (parenttid IS NOT NULL)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR creating taxstatus links: '.$this->conn->error,1);
			}

			$this->outputMsg('Transferring vernaculars for new taxa... ',1);
			$this->transferVernaculars(1);

			$this->outputMsg('Preparing for next round... ',1);
			$sql = 'DELETE FROM uploadtaxa WHERE (tid IS NOT NULL) AND (tidaccepted IS NOT NULL) AND (parenttid IS NOT NULL)';
			$this->conn->query($sql);
			if(!$this->conn->affected_rows) {
                break;
            }

			$sql = 'UPDATE uploadtaxa ut1 INNER JOIN uploadtaxa ut2 ON ut1.sourceparentid = ut2.sourceid '.
				'INNER JOIN taxa AS t ON ut2.sciname = t.sciname '.
				'SET ut1.parenttid = t.tid '.
				'WHERE ISNULL(ut1.parenttid) AND (ut1.sourceparentid IS NOT NULL) AND (ut2.sourceid IS NOT NULL)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR populating parent TIDs based on sourceIDs: '.$this->conn->error,1);
			}

			$sql = 'UPDATE uploadtaxa up INNER JOIN taxa t ON up.parentstr = t.sciname '.
				'SET up.parenttid = t.tid '.
				'WHERE ISNULL(up.parenttid)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('ERROR populating parent TIDs: '.$this->conn->error,1);
			}
			$loopCnt++;
		}
		while($loopCnt < 100);

		$this->outputMsg('House cleaning... ');
		$sql1 = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname SET o.TidInterpreted = t.tid WHERE ISNULL(o.TidInterpreted)';
		$this->conn->query($sql1);

		$sql2 = 'UPDATE images i INNER JOIN omoccurrences o ON i.occid = o.occid '.
			'SET i.tid = o.TidInterpreted '.
			'WHERE ISNULL(i.tid) AND (o.TidInterpreted IS NOT NULL)';
		$this->conn->query($sql2);

		$sql3 = 'INSERT IGNORE INTO omoccurgeoindex(tid,decimallatitude,decimallongitude) '.
			'SELECT DISTINCT o.tidinterpreted, round(o.decimallatitude,2), round(o.decimallongitude,2) '.
			'FROM omoccurrences o '.
			'WHERE (o.tidinterpreted IS NOT NULL) AND (o.decimallatitude between -180 and 180) AND (o.decimallongitude between -180 and 180) '.
			'AND (ISNULL(o.cultivationStatus) OR o.cultivationStatus = 0) AND (ISNULL(o.coordinateUncertaintyInMeters) OR o.coordinateUncertaintyInMeters < 10000) ';
		$this->conn->query($sql3);
	}

	private function transferVernaculars($secondRound = 0): void
	{
		$sql = 'SELECT tid, vernacular, vernlang, source FROM uploadtaxa WHERE tid IS NOT NULL AND Vernacular IS NOT NULL ';
		if($secondRound) $sql .= 'AND tidaccepted IS NOT NULL';
		$rs = $this->conn->query($sql);
		while ($r = $rs->fetch_object()){
			$vernArr = array();
			$vernStr = $r->vernacular;
			if(strpos($vernStr,"\t")) {
				$vernArr = explode("\t",$vernStr);
			}
			elseif(strpos($vernStr, '|')){
				$vernArr = explode('|',$vernStr);
			}
			elseif(strpos($vernStr, ';')){
				$vernArr = explode(';',$vernStr);
			}
			elseif(strpos($vernStr, ',')){
				$vernArr = explode(',',$vernStr);
			}
			else{
				$vernArr[] = $vernStr;
			}
			$langStr = $r->vernlang;
			if(!$langStr) $langStr = 'en';
			foreach($vernArr as $vStr){
				if($vStr){
					$sqlInsert = 'INSERT INTO taxavernaculars(tid, VernacularName, Language, Source) '.
						'VALUES('.$r->tid.',"'.$vStr.'","'.$langStr.'",'.($r->source?'"'.$r->source.'"':'NULL').')';
					if(!$this->conn->query($sqlInsert) && strpos($this->conn->error, 'Duplicate') !== 0) {
						$this->outputMsg('ERROR: ' . $this->conn->error, 1);
					}
				}
			}
		}
	}

	public function exportUploadTaxa(): void
	{
		$fieldArr = array('tid','family','scinameInput','sciname','author','rankId','unitInd1','unitName1','unitInd2','unitName2',
			'unitInd3','unitName3,parentTid','parentStr','acceptance','tidAccepted','acceptedStr','unacceptabilityReason',
			'securityStatus','source','notes','vernacular','vernlang','sourceId','sourceAcceptedId','sourceParentId','errorStatus');
		$fileName = 'taxaUpload_'.time().'.csv';
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$sql = 'SELECT '.implode(',',$fieldArr).' FROM uploadtaxa ';
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
		$sql = 'SELECT count(*) as cnt FROM uploadtaxa';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->statArr['upload'] = $r->cnt;
		}
		$rs->free();
	}

	public function getTargetArr(): array
	{
		$retArr = $this->getUploadTaxaFieldArr();
		$retArr['scinameinput'] = 'scientific_name';
        $retArr['acceptedstr'] = 'accepted_scientific_name';
        $retArr['parentstr'] = 'parent_scientific_name';
        $retArr['sourceacceptedid'] = 'source_accepted_id';
        $retArr['unacceptabilityreason'] = 'unacceptability_reason';
        $retArr['sourceparentid'] = 'source_parent_id';
        $retArr['securitystatus'] = 'security_status';
        $retArr['vernlang'] = 'vernacular_language';
        $retArr['errorstatus'] = 'error_status';
        $retArr['sourceid'] = 'source_id';
        $retArr['rankid'] = 'rank_id';
        $retArr['rankname'] = 'rank_name';
        $retArr['author'] = 'taxon_author';
        $retArr['unitind1'] = 'unit_ind1';
        $retArr['unitname1'] = 'unit_name1';
        $retArr['unitind2'] = 'unit_ind2';
        $retArr['unitname2'] = 'unit_name2';
        $retArr['unitind3'] = 'unit_ind3';
        $retArr['unitname3'] = 'unit_name3';

        return $retArr;
	}

	private function getUploadTaxaFieldArr(): array
	{
		$targetArr = array();
		$sql = 'SHOW COLUMNS FROM uploadtaxa';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if($field !== 'infraauthor' && $field !== 'initialtimestamp' && $field !== 'sciname' && $field !== 'tid' && $field !== 'tidaccepted' && $field !== 'parenttid'){
				$targetArr[$field] = $field;
			}
		}
		$rs->free();

		return $targetArr;
	}

	private function getTaxonUnitArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits';
		//echo $sql.'<br/>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->rankid] = strtolower($r->rankname);
		}
		$rs->free();
		return $retArr;
	}

	public function getSourceArr(): array
	{
		$sourceArr = array();
		if(($fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb')) !== FALSE){
			$headerArr = fgetcsv($fh);
			foreach($headerArr as $field){
				$fieldStr = strtolower(trim($field));
				if($fieldStr){
					$sourceArr[] = $fieldStr;
				}
				else{
					break;
				}
			}
		}
		else{
			echo 'ERROR thrown opening input file: '.$this->uploadTargetPath.$this->uploadFileName.'<br/>';
			if(!is_writable($this->uploadTargetPath)) {
				echo '<b>Target upload path is not writable. File permissions need to be adjusted</b>';
			}
			exit;
		}
		return $sourceArr;
	}

	public function getTaxAuthorityArr(): array
	{
		$retArr = array();
		$sql = 'SELECT taxauthid, name FROM taxauthority ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->taxauthid] = $r->name;
			}
			$rs->free();
		}
		return $retArr;
	}

	private function setUploadTargetPath(): void
	{
		global $TEMP_DIR_ROOT, $SERVER_ROOT;
		$tPath = '';
		if(!$tPath && isset($TEMP_DIR_ROOT)){
			$tPath = $TEMP_DIR_ROOT;
			if(substr($tPath,-1) !== '/') {
				$tPath .= '/';
			}
			if(file_exists($tPath.'downloads')) {
				$tPath .= 'downloads/';
			}
		}
		elseif(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $SERVER_ROOT;
			if(substr($tPath,-1) !== '/') {
				$tPath .= '/';
			}
			$tPath .= 'temp/downloads/';
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

	public function setTaxaAuthId($id): void
	{
		if(is_numeric($id)){
			$this->taxAuthId = $id;
		}
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
		global $SERVER_ROOT;
		if(is_numeric($vMode)){
			$this->verboseMode = $vMode;
			if($this->verboseMode === 2){
				$LOG_PATH = $SERVER_ROOT;
				$server_root_sub = substr($SERVER_ROOT,-1);
				if($server_root_sub !== '/' && $server_root_sub !== '\\') {
					$LOG_PATH .= '/';
				}
				$LOG_PATH .= 'content/logs/taxaloader_' .date('Ymd'). '.log';
				$this->logFH = fopen($LOG_PATH, 'ab');
				fwrite($this->logFH, 'Start time: ' .date('Y-m-d h:i:s A')."\n");
			}
		}
	}

	private function outputMsg($str, $indent = 0): void
	{
		if($this->verboseMode > 0 || strpos($str, 'ERROR') === 0){
			echo '<li style="margin-left:'.(10*$indent).'px;'.(strpos($str, 'ERROR') === 0 ?'color:red':'').'">'.$str.'</li>';
			ob_flush();
			flush();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fwrite($this->logFH, ($indent ? str_repeat("\t", $indent) : '') . strip_tags($str) . "\n");
		}
	}

    private function cleanInStr($str){
		$newStr = TRIM($str);
		$newStr = preg_REPLACE('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}

    private function encodeString($inStr): string
	{
		global $CHARSET;
		$retStr = $inStr;
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr = str_replace($search, $replace, $inStr);
		$badwordchars=array("\xe2\x80\x98",
							"\xe2\x80\x99",
							"\xe2\x80\x9c",
							"\xe2\x80\x9d",
							"\xe2\x80\x94",
							"\xe2\x80\xa6"
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$inStr = str_REPLACE($badwordchars, $fixedwordchars, $inStr);

		if($inStr){
			$lowCharSet = strtolower($CHARSET);
			if($lowCharSet === 'utf-8' || $lowCharSet === 'utf8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($lowCharSet === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
 		}
		return $retStr;
	}
}
