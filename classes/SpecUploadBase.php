<?php
include_once($SERVER_ROOT.'/classes/SpecUpload.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMaintenance.php');
include_once($SERVER_ROOT.'/classes/OccurrenceUtilities.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');

class SpecUploadBase extends SpecUpload{

	protected $transferCount = 0;
	protected $identTransferCount = 0;
	protected $imageTransferCount = 0;
	protected $includeIdentificationHistory = true;
	protected $includeImages = true;
	private $matchCatalogNumber = 1;
	private $matchOtherCatalogNumbers = 0;
	private $verifyImageUrls = false;
	private $processingStatus = '';
	protected $nfnIdentifier;
	protected $uploadTargetPath;

	protected $sourceArr = array();
	protected $identSourceArr = array();
	protected $imageSourceArr = array();
	protected $fieldMap = array();
	protected $identFieldMap = array();
	protected $imageFieldMap = array();
	protected $symbFields = array();
	protected $identSymbFields = array();
	protected $imageSymbFields = array();
	private $sourceDatabaseType = '';

	public function __construct() {
		parent::__construct();
		set_time_limit(7200);
	 	ini_set('max_input_time',240);
	}

	public function setFieldMap($fm): void
	{
		$this->fieldMap = $fm;
	}

	public function setIdentFieldMap($fm): void
	{
		$this->identFieldMap = $fm;
	}

	public function setImageFieldMap($fm): void
	{
		$this->imageFieldMap = $fm;
	}

	public function getDbpk(){
		$dbpk = '';
		if(array_key_exists('dbpk',$this->fieldMap)){
			$dbpk = $this->fieldMap['dbpk']['field'];
		}
		return $dbpk;
	}

	public function loadFieldMap($autoBuildFieldMap = false): void
	{
		if($this->uploadType === $this->DIGIRUPLOAD) {
			$autoBuildFieldMap = true;
		}
		if($this->uspid && !$this->fieldMap){
			if($this->uploadType === $this->FILEUPLOAD || $this->uploadType === $this->SKELETAL || $this->uploadType === $this->DWCAUPLOAD ||
                $this->uploadType === $this->IPTUPLOAD || $this->uploadType === $this->DIRECTUPLOAD || $this->uploadType === $this->SCRIPTUPLOAD){
                $sql = 'SELECT usm.sourcefield, usm.symbspecfield FROM uploadspecmap usm WHERE (usm.uspid = '.$this->uspid.')';
                $rs = $this->conn->query($sql);
                while($row = $rs->fetch_object()){
                    $sourceField = $row->sourcefield;
                    $symbField = $row->symbspecfield;
                    if(strpos($symbField, 'ID-') === 0){
                        $this->identFieldMap[substr($symbField,3)]['field'] = $sourceField;
                    }
                    elseif(strpos($symbField, 'IM-') === 0){
                        $this->imageFieldMap[substr($symbField,3)]['field'] = $sourceField;
                    }
                    else{
                        $this->fieldMap[$symbField]['field'] = $sourceField;
                    }
                }
                $rs->free();
            }
		}

		$skipOccurFields = array('initialtimestamp','occid','collid','tidinterpreted','fieldnotes','coordinateprecision',
			'verbatimcoordinatesystem','institutionid','collectionid','associatedoccurrences','datasetid','associatedreferences',
			'previousidentifications','associatedsequences');
		if($this->collMetadataArr['managementtype'] === 'Live Data' && $this->collMetadataArr['guidtarget'] !== 'occurrenceId'){
			$skipOccurFields[] = 'occurrenceid';
		}
		$sql = 'SHOW COLUMNS FROM uploadspectemp';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if(!in_array($field, $skipOccurFields, true)){
				if($autoBuildFieldMap){
					$this->fieldMap[$field]['field'] = $field;
				}
				$type = $row->Type;
				$this->symbFields[] = $field;
				if(array_key_exists($field,$this->fieldMap)){
					if(strpos($type, 'double') !== false || strpos($type, 'int') !== false){
						$this->fieldMap[$field]['type'] = 'numeric';
					}
					elseif(strpos($type, 'decimal') !== false){
						$this->fieldMap[$field]['type'] = 'decimal';
						if(preg_match('/\((.*)\)$/', $type, $matches)){
							$this->fieldMap[$field]['size'] = $matches[1];
						}
					}
					elseif(strpos($type, 'date') !== false){
						$this->fieldMap[$field]['type'] = 'date';
					}
					else{
						$this->fieldMap[$field]['type'] = 'string';
						if(preg_match('/\((\d+)\)$/', $type, $matches)){
							$this->fieldMap[$field]['size'] = substr($matches[0],1, -1);
						}
					}
				}
			}
		}
		$rs->free();

		if($this->uploadType === $this->FILEUPLOAD || $this->uploadType === $this->SKELETAL || $this->uploadType === $this->DWCAUPLOAD ||
            $this->uploadType === $this->IPTUPLOAD || $this->uploadType === $this->DIRECTUPLOAD){
            $skipDetFields = array('detid','occid','tidinterpreted','idbyid','appliedstatus','sortsequence','sourceidentifier','initialtimestamp');

            $rs = $this->conn->query('SHOW COLUMNS FROM uploaddetermtemp');
            while($r = $rs->fetch_object()){
                $field = strtolower($r->Field);
                if(!in_array($field, $skipDetFields, true)){
                    if($autoBuildFieldMap){
                        $this->identFieldMap[$field]['field'] = $field;
                    }
                    $type = $r->Type;
                    $this->identSymbFields[] = $field;
                    if(array_key_exists($field,$this->identFieldMap)){
                        if(strpos($type, 'double') !== false || strpos($type, 'int') !== false || strpos($type, 'decimal') !== false){
                            $this->identFieldMap[$field]['type'] = 'numeric';
                        }
                        elseif(strpos($type, 'date') !== false){
                            $this->identFieldMap[$field]['type'] = 'date';
                        }
                        else{
                            $this->identFieldMap[$field]['type'] = 'string';
                            if(preg_match('/\(\d+\)$/', $type, $matches)){
                                $this->identFieldMap[$field]['size'] = substr($matches[0],1, -1);
                            }
                        }
                    }
                }
            }
            $rs->free();

            $this->identSymbFields[] = 'genus';
            $this->identSymbFields[] = 'specificepithet';
            $this->identSymbFields[] = 'taxonrank';
            $this->identSymbFields[] = 'infraspecificepithet';
            $this->identSymbFields[] = 'coreid';

            $skipImageFields = array('tid','photographeruid','imagetype','occid','dbpk','specimenguid','collid','username','sortsequence','initialtimestamp');
            $rs = $this->conn->query('SHOW COLUMNS FROM uploadimagetemp');
            while($r = $rs->fetch_object()){
                $field = strtolower($r->Field);
                if(!in_array($field, $skipImageFields, true)){
                    if($autoBuildFieldMap){
                        $this->imageFieldMap[$field]['field'] = $field;
                    }
                    $type = $r->Type;
                    $this->imageSymbFields[] = $field;
                    if(array_key_exists($field,$this->imageFieldMap)){
                        if(strpos($type, 'double') !== false || strpos($type, 'int') !== false || strpos($type, 'decimal') !== false){
                            $this->imageFieldMap[$field]['type'] = 'numeric';
                        }
                        elseif(strpos($type, 'date') !== false){
                            $this->imageFieldMap[$field]['type'] = 'date';
                        }
                        else{
                            $this->imageFieldMap[$field]['type'] = 'string';
                            if(preg_match('/\(\d+\)$/', $type, $matches)){
                                $this->imageFieldMap[$field]['size'] = substr($matches[0],1, -1);
                            }
                        }
                    }
                }
            }
            $rs->free();
        }
	}

	public function echoFieldMapTable($autoMap, $mode): void
	{
        $prefix = '';
		$fieldMap = $this->fieldMap;
		$symbFields = $this->symbFields;
		$sourceArr = $this->sourceArr;
		$translationMap = array('accession'=>'catalognumber','accessionid'=>'catalognumber','accessionnumber'=>'catalognumber',
				'taxonfamilyname'=>'family','scientificname'=>'sciname','species'=>'specificepithet','commonname'=>'taxonremarks',
				'observer'=>'recordedby','collector'=>'recordedby','primarycollector'=>'recordedby','field:collector'=>'recordedby','collectedby'=>'recordedby',
				'collectornumber'=>'recordnumber','collectionnumber'=>'recordnumber','field:collectorfieldnumber'=>'recordnumber',
				'datecollected'=>'eventdate','date'=>'eventdate','collectiondate'=>'eventdate','observedon'=>'eventdate','dateobserved'=>'eventdate',
				'cf' => 'identificationqualifier','detby'=>'identifiedby','determinor'=>'identifiedby','determinationdate'=>'dateidentified',
				'placestatename'=>'stateprovince','state'=>'stateprovince','placecountyname'=>'county','municipiocounty'=>'county',
				'location'=>'locality','field:localitydescription'=>'locality','latitude'=>'verbatimlatitude','longitude'=>'verbatimlongitude',
				'elevationmeters'=>'minimumelevationinmeters','field:associatedspecies'=>'associatedtaxa',
				'specimennotes'=>'occurrenceremarks','notes'=>'occurrenceremarks','generalnotes'=>'occurrenceremarks',
				'description'=>'verbatimattributes','field:habitat'=>'habitat','habitatdescription'=>'habitat',
				'subject_references'=>'tempfield01','subject_recordid'=>'tempfield02');
		if($mode === 'ident'){
			$prefix = 'ID-';
			$fieldMap = $this->identFieldMap;
			$symbFields = $this->identSymbFields;
			$sourceArr = $this->identSourceArr;
			$translationMap = array('scientificname'=>'sciname','detby'=>'identifiedby','determinor'=>'identifiedby',
				'determinationdate'=>'dateidentified','notes'=>'identificationremarks','cf' => 'identificationqualifier');
		}
		elseif($mode === 'image'){
			$prefix = 'IM-';
			$fieldMap = $this->imageFieldMap;
			$symbFields = $this->imageSymbFields;
			$sourceArr = $this->imageSourceArr;
			$translationMap = array('accessuri'=>'originalurl','thumbnailaccessuri'=>'thumbnailurl','goodqualityaccessuri'=>'url','creator'=>'owner');
		}

		$sourceSymbArr = array();
		foreach($fieldMap as $symbField => $fArr){
            $sourceSymbArr[$fArr['field']] = $symbField;
		}

		if($this->uploadType === $this->NFNUPLOAD && !in_array('subject_references', $this->sourceArr, true) && !in_array('recordid', $this->sourceArr, true)){
			echo '<div style="color:red">ERROR: input field does not contain proper identifier field (e.g. subject_references, recordID)</div>';
			return;
		}
		echo '<table class="styledtable" style="width:600px;font-family:Arial,serif;font-size:12px;">';
		echo '<tr><th>Source Field</th><th>Target Field</th></tr>'."\n";
		sort($symbFields);
		$autoMapArr = array();
		foreach($sourceArr as $fieldName){
			if($fieldName === 'coreid') {
                continue;
            }
			$diplayFieldName = $fieldName;
			$fieldName = strtolower(trim($fieldName));
			if($this->uploadType === $this->NFNUPLOAD && ($fieldName === 'subject_recordid' || $fieldName === 'subject_references')){
				echo '<input type="hidden" name="sf[]" value="'.$fieldName.'" />';
				echo '<input type="hidden" name="tf[]" value="'.$translationMap[$fieldName].'" />';
			}
			else{
				if($this->uploadType === $this->NFNUPLOAD && strpos($fieldName, 'subject_') === 0) {
					continue;
				}
				$isAutoMapped = false;
				$tranlatedFieldName = str_replace(array('_',' ','.'),'',$fieldName);
				if($autoMap){
					if(array_key_exists($tranlatedFieldName,$translationMap)) {
						$tranlatedFieldName = strtolower($translationMap[$tranlatedFieldName]);
					}
					if(in_array($tranlatedFieldName, $symbFields, true)){
						$isAutoMapped = true;
					}
				}
				echo "<tr>\n";
				echo "<td style='padding:2px;'>";
				echo $diplayFieldName;
				echo "<input type='hidden' name='".$prefix."sf[]' value='".$fieldName."' />";
				echo "</td>\n";
				echo "<td>\n";
				echo "<select name='".$prefix."tf[]' style='background:".(!array_key_exists($fieldName,$sourceSymbArr)&&!$isAutoMapped? 'yellow' : '')."'>";
				echo "<option value=''>Select Target Field</option>\n";
				echo "<option value='unmapped'".(isset($sourceSymbArr[$fieldName]) && strpos($sourceSymbArr[$fieldName], 'unmapped') === 0 ? 'SELECTED' : '').">Leave Field Unmapped</option>\n";
				echo "<option value=''>-------------------------</option>\n";
				if(array_key_exists($fieldName,$sourceSymbArr)){
					foreach($symbFields as $sField){
						echo '<option ' .($sourceSymbArr[$fieldName] === $sField? 'SELECTED' : ''). '>' .$sField."</option>\n";
					}
				}
				elseif($isAutoMapped){
					foreach($symbFields as $sField){
						echo '<option ' .($tranlatedFieldName === $sField? 'SELECTED' : ''). '>' .$sField."</option>\n";
					}
				}
				else{
					foreach($symbFields as $sField){
						echo '<option>' .$sField."</option>\n";
					}
				}
				echo "</select></td>\n";
				echo "</tr>\n";
			}
		}
		echo '</table>';
	}

	public function saveFieldMap($newTitle = ''): string
	{
		$statusStr = '';
		if(!$this->uspid && $newTitle){
			$this->uspid = $this->createUploadProfile(array('uploadtype'=>$this->uploadType,'title'=>$newTitle));
			$this->readUploadParameters();
		}
		if($this->uspid){
			$this->deleteFieldMap();
			$sqlInsert = 'INSERT INTO uploadspecmap(uspid,symbspecfield,sourcefield) ';
			$sqlValues = 'VALUES (' .$this->uspid;
			foreach($this->fieldMap as $k => $v){
				$sourceField = $v['field'];
				$sql = $sqlInsert.$sqlValues.",'".$k."','".$sourceField."')";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving field map: '.$this->conn->error;
				}
			}
			foreach($this->identFieldMap as $k => $v){
				$sourceField = $v['field'];
				$sql = $sqlInsert.$sqlValues.",'ID-".$k."','".$sourceField."')";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving identification field map: '.$this->conn->error;
				}
			}

			foreach($this->imageFieldMap as $k => $v){
				$sourceField = $v['field'];
				$sql = $sqlInsert.$sqlValues.",'IM-".$k."','".$sourceField."')";
				//echo "<div>".$sql."</div>";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving image field map: '.$this->conn->error;
				}
			}

		}
		return $statusStr;
	}

	public function deleteFieldMap(): string
	{
		$statusStr = '';
		if($this->uspid){
			$sql = 'DELETE FROM uploadspecmap WHERE (uspid = '.$this->uspid.') ';
			//echo "<div>$sql</div>";
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR deleting field map: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

 	public function analyzeUpload(): bool
	{
 		return true;
 	}

 	protected function prepUploadData(): void
	{
	 	$this->outputMsg('<li>Clearing staging tables</li>');
 		$sqlDel1 = 'DELETE FROM uploadspectemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel1);
		$sqlDel2 = 'DELETE FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel2);
		$sqlDel3 = 'DELETE FROM uploadimagetemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel3);
 	}

 	public function uploadData($finalTransfer): void
	{
 		$this->outputMsg('<li>Initiating data upload</li>');
 		$this->prepUploadData();

	 	if($this->uploadType === $this->STOREDPROCEDURE){
			$this->cleanUpload();
 		}
 		elseif($this->uploadType === $this->SCRIPTUPLOAD){
 			if(system($this->queryStr)){
				$this->outputMsg('<li>Script Upload successful</li>');
				$this->outputMsg('<li>Initializing final transfer steps...</li>');
				$this->cleanUpload();
			}
		}
		if($finalTransfer){
			$this->finalTransfer();
		}
		else{
			$this->outputMsg('<li>Record upload complete, ready for final transfer and activation</li>');
		}
 	}

	protected function cleanUpload(): void
	{

		if($this->collMetadataArr['managementtype'] === 'Snapshot' || $this->collMetadataArr['managementtype'] === 'Aggregate'){
			$this->outputMsg('<li>Linking records (e.g. matching Primary Identifier)... </li>');
			$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.dbpk = o.dbpk) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.dbpk IS NOT NULL) AND (o.dbpk IS NOT NULL)';
			$this->conn->query($sql);
		}

		if($this->storedProcedure){
			if($this->conn->query('CALL '.$this->storedProcedure)){
				$this->outputMsg('<li style="margin-left:10px;">Stored procedure executed: '.$this->storedProcedure.'</li>');
				if($this->conn->more_results()) {
					$this->conn->next_result();
				}
			}
			else{
				$this->outputMsg('<li style="margin-left:10px;"><span style="color:red;">ERROR: Stored Procedure failed ('.$this->storedProcedure.'): '.$this->conn->error.'</span></li>');
			}
		}

 		$this->recordCleaningStage1();

		if($this->collMetadataArr['managementtype'] === 'Live Data' || $this->uploadType === $this->SKELETAL){
			if($this->matchCatalogNumber){
				$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li><span style="color:red;">Warning: unable to match on catalog number: '.$this->conn->error.'</span></li>');
				}
			}
			if($this->matchOtherCatalogNumbers){
				$sql2 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.otherCatalogNumbers = o.otherCatalogNumbers) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.othercatalogNumbers IS NOT NULL) AND (o.othercatalogNumbers IS NOT NULL) ';
				if(!$this->conn->query($sql2)){
					$this->outputMsg('<li><span style="color:red;">Warning: unable to match on other catalog numbers: '.$this->conn->error.'</span></li>');
				}
			}
		}

		$this->setTransferCount();
		$this->setIdentTransferCount();
		$this->setImageTransferCount();
	}

	private function recordCleaningStage1(): void
	{
		$this->outputMsg('<li>Data cleaning:</li>');
		$this->outputMsg('<li style="margin-left:10px;">Cleaning event dates...</li>');

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.year = YEAR(u.eventDate) '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.eventDate IS NOT NULL) AND (u.year IS NULL)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.month = MONTH(u.eventDate) '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.month IS NULL) AND (u.eventDate IS NOT NULL)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.day = DAY(u.eventDate) '.
			'WHERE u.collid IN('.$this->collId.') AND u.day IS NULL AND u.eventDate IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.startDayOfYear = DAYOFYEAR(u.eventDate) '.
			'WHERE u.collid IN('.$this->collId.') AND u.startDayOfYear IS NULL AND u.eventDate IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.endDayOfYear = DAYOFYEAR(u.LatestDateCollected) '.
			'WHERE u.collid IN('.$this->collId.') AND u.endDayOfYear IS NULL AND u.LatestDateCollected IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE IGNORE uploadspectemp u '.
			'SET u.eventDate = CONCAT_WS("-",LPAD(u.year,4,"19"),IFNULL(LPAD(u.month,2,"0"),"00"),IFNULL(LPAD(u.day,2,"0"),"00")) '.
			'WHERE (u.eventDate IS NULL) AND (u.year > 1300) AND (u.year < 2020) AND (collid = IN('.$this->collId.'))';
		$this->conn->query($sql);

		$this->outputMsg('<li style="margin-left:10px;">Cleaning country and state/province ...</li>');
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupcountry c ON u.country = c.iso3 '.
			'SET u.country = c.countryName '.
			'WHERE (u.collid IN('.$this->collId.'))';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupcountry c ON u.country = c.iso '.
			'SET u.country = c.countryName '.
			'WHERE u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateProvince = s.abbrev '.
			'SET u.stateProvince = s.stateName '.
			'WHERE u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateprovince = s.statename '.
			'INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
			'SET u.country = c.countryName '.
			'WHERE u.country IS NULL AND c.countryname = "United States" AND u.collid IN('.$this->collId.')';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateprovince = s.statename '.
			'INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
			'SET u.country = c.countryName '.
			'WHERE u.country IS NULL AND u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$this->outputMsg('<li style="margin-left:10px;">Cleaning coordinates...</li>');
		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLongitude = -1*DecimalLongitude '.
			'WHERE (DecimalLongitude > 0) AND (Country IN("USA","United States","U.S.A.","Canada","Mexico")) AND (stateprovince != "Alaska" OR stateprovince IS NULL) AND (collid IN('.$this->collId.'))';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLatitude = NULL, DecimalLongitude = NULL '.
			'WHERE DecimalLatitude = 0 AND DecimalLongitude = 0 AND collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp '.
			'SET verbatimcoordinates = CONCAT_WS(" ",DecimalLatitude, DecimalLongitude) '.
			'WHERE verbatimcoordinates IS NULL AND collid IN('.$this->collId.') '.
			'AND (DecimalLatitude < -90 OR DecimalLatitude > 90 OR DecimalLongitude < -180 OR DecimalLongitude > 180)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLatitude = NULL, DecimalLongitude = NULL '.
			'WHERE collid IN('.$this->collId.') AND (DecimalLatitude < -90 OR DecimalLatitude > 90 OR DecimalLongitude < -180 OR DecimalLongitude > 180)';
		$this->conn->query($sql);


		$this->outputMsg('<li style="margin-left:10px;">Cleaning taxonomy...</li>');
		$sql = 'UPDATE uploadspectemp SET family = sciname '.
			'WHERE (family IS NULL) AND (sciname LIKE "%aceae" OR sciname LIKE "%idae")';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp SET sciname = family WHERE (family IS NOT NULL) AND (sciname IS NULL) ';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u INNER JOIN taxa t ON u.sciname = t.sciname '.
			'SET u.scientificNameAuthorship = t.author '.
			'WHERE u.scientificNameAuthorship IS NULL AND t.author IS NOT NULL';
		$this->conn->query($sql);
	}

	public function getTransferReport(): array
	{
		$reportArr = array();
		$reportArr['occur'] = $this->getTransferCount();
		if($this->uploadType === $this->DWCAUPLOAD || $this->uploadType === $this->IPTUPLOAD){
			if($this->includeIdentificationHistory) {
				$reportArr['ident'] = $this->getIdentTransferCount();
			}
			if($this->includeImages) {
				$reportArr['image'] = $this->getImageTransferCount();
			}
		}
		$sql = 'SELECT count(*) AS cnt '.
			'FROM uploadspectemp '.
			'WHERE (associatedMedia IS NOT NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$cnt = ($reportArr['image'] ?? 0) + $r->cnt;
			if($cnt) {
				$reportArr['image'] = $cnt;
			}
		}
		$rs->free();

		$sql = 'SELECT count(*) AS cnt '.
			'FROM uploadspectemp '.
			'WHERE (occid IS NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$reportArr['new'] = $r->cnt;
		}
		$rs->free();

		$sql = 'SELECT count(*) AS cnt '.
			'FROM uploadspectemp '.
			'WHERE (occid IS NOT NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$reportArr['update'] = $r->cnt;
		}
		$rs->free();

		if($this->collMetadataArr['managementtype'] === 'Live Data' && !$this->matchCatalogNumber  && !$this->matchOtherCatalogNumbers){
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM uploadspectemp u INNER JOIN omoccurrences o ON u.collid = o.collid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber = o.catalogNumber OR u.othercatalogNumbers = o.othercatalogNumbers) ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['matchappend'] = $r->cnt;
			}
			$rs->free();
		}

		if($this->uploadType !== $this->SKELETAL && $this->collMetadataArr['managementtype'] === 'Snapshot'){
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) '.
				'AND (o.catalogNumber IS NOT NULL) AND (o.dbpk IS NULL)';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['sync'] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM omoccurrences o LEFT JOIN uploadspectemp u  ON (o.occid = u.occid) '.
				'WHERE (o.collid IN('.$this->collId.')) AND (u.occid IS NULL)';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['exist'] = $r->cnt;
			}
			$rs->free();
		}

		if($this->uploadType !== $this->SKELETAL && ($this->collMetadataArr['managementtype'] === 'Snapshot' || $this->collMetadataArr['managementtype'] === 'Aggregate')){
			$sql = 'SELECT count(*) AS cnt FROM uploadspectemp '.
				'WHERE (dbpk IS NULL) AND (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['nulldbpk'] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT dbpk FROM uploadspectemp '.
				'GROUP BY dbpk, collid, basisofrecord '.
				'HAVING (Count(*)>1) AND (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			$reportArr['dupdbpk'] = $rs->num_rows;
			$rs->free();
		}

		return $reportArr;
	}

	public function finalTransfer(): void
	{
		global $QUICK_HOST_ENTRY_IS_ACTIVE;
		$this->recordCleaningStage2();
		$this->transferOccurrences();
		$this->prepareAssociatedMedia();
		$this->prepareImages();
		$this->transferIdentificationHistory();
		$this->transferImages();
		if($QUICK_HOST_ENTRY_IS_ACTIVE){
			$this->transferHostAssociations();
		}
		$this->finalCleanup();
		$this->outputMsg('<li style="">Upload Procedure Complete ('.date('Y-m-d h:i:s A').')!</li>');
		$this->outputMsg(' ');
	}

	private function recordCleaningStage2(): void
	{
		$this->outputMsg('<li>Starting Stage 2 cleaning</li>');
		if($this->uploadType === $this->NFNUPLOAD){
			$sql = 'DELETE FROM uploadspectemp WHERE (occid IS NULL) AND (collid IN('.$this->collId.'))';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px"><span style="color:red;">ERROR</span> deleting occurrences ('.$this->conn->error.')</li>');
			}
		}
		else{
			if($this->collMetadataArr['managementtype'] === 'Snapshot' || $this->uploadType === $this->SKELETAL){
				$this->outputMsg('<li style="margin-left:10px;">Populating source identifiers (dbpk) to relink occurrences processed within portal...</li>');
				$sql = 'UPDATE IGNORE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid, o.dbpk = u.dbpk '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) AND (o.dbpk IS NULL) ';
				$this->conn->query($sql);
			}

			if(($this->collMetadataArr['managementtype'] === 'Snapshot' && $this->uploadType !== $this->SKELETAL) || $this->collMetadataArr['managementtype'] === 'Aggregate'){
				$this->outputMsg('<li style="margin-left:10px;">Remove NULL dbpk values...</li>');
				$sql = 'DELETE FROM uploadspectemp WHERE (dbpk IS NULL) AND (collid IN('.$this->collId.'))';
				$this->conn->query($sql);

				$this->outputMsg('<li style="margin-left:10px;">Remove duplicate dbpk values...</li>');
				$sql = 'DELETE u.* '.
					'FROM uploadspectemp u INNER JOIN (SELECT dbpk FROM uploadspectemp '.
					'GROUP BY dbpk, collid HAVING Count(*)>1 AND collid IN('.$this->collId.')) t2 ON u.dbpk = t2.dbpk '.
					'WHERE collid IN('.$this->collId.')';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px"><span style="color:red;">ERROR</span> ('.$this->conn->error.')</li>');
				}
			}
		}
	}

	protected function transferOccurrences(): void
	{
		if($this->uploadType === $this->NFNUPLOAD){
			$this->outputMsg('<li>Transferring edits to versioning tables...</li>');
			$this->versionOccurrenceEdits();
		}
		$this->outputMsg('<li>Updating existing records... </li>');
		$fieldArr = array('basisOfRecord', 'catalogNumber','otherCatalogNumbers','occurrenceid',
			'ownerInstitutionCode','institutionID','collectionID','institutionCode','collectionCode',
			'family','scientificName','sciname','tidinterpreted','genus','specificEpithet','datasetID','taxonRank','infraspecificEpithet',
			'scientificNameAuthorship','identifiedBy','dateIdentified','identificationReferences','identificationRemarks',
			'taxonRemarks','identificationQualifier','typeStatus','recordedBy','recordNumber','associatedCollectors',
			'eventDate','Year','Month','Day','startDayOfYear','endDayOfYear','verbatimEventDate',
			'habitat','substrate','fieldnumber','occurrenceRemarks','informationWithheld','associatedOccurrences',
			'associatedTaxa','dynamicProperties','verbatimAttributes','reproductiveCondition','cultivationStatus','establishmentMeans',
			'lifestage','sex','individualcount','samplingprotocol','preparations',
			'country','stateProvince','county','municipality','locality','localitySecurity','localitySecurityReason',
			'decimalLatitude','decimalLongitude','geodeticDatum','coordinateUncertaintyInMeters','footprintWKT','coordinatePrecision',
			'locationRemarks','verbatimCoordinates','verbatimCoordinateSystem','georeferencedBy','georeferenceProtocol','georeferenceSources',
			'georeferenceVerificationStatus','georeferenceRemarks','minimumElevationInMeters','maximumElevationInMeters','verbatimElevation',
			'previousIdentifications','disposition','modified','language','recordEnteredBy','labelProject','duplicateQuantity','processingStatus');
		$sqlFragArr = array();
		foreach($fieldArr as $v){
			if($v === 'processingStatus' && $this->processingStatus){
				$sqlFragArr[$v] = 'o.processingStatus = u.processingStatus';
			}
			elseif($this->uploadType === $this->SKELETAL || $this->uploadType === $this->NFNUPLOAD){
				$sqlFragArr[$v] = 'o.'.$v.' = IFNULL(o.'.$v.',u.'.$v.')';
			}
			else{
				$sqlFragArr[$v] = 'o.'.$v.' = u.'.$v;
			}
		}
		$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON u.occid = o.occid '.
			'SET '.implode(',',$sqlFragArr).' WHERE (u.collid IN('.$this->collId.'))';
		//echo '<div>'.$sql.'</div>'; exit;
		if(!$this->conn->query($sql)){
			$this->outputMsg('<li style="margin-left:10px">FAILED! ERROR: '.$this->conn->error.'</li> ');
		}

		if($this->uploadType !== $this->NFNUPLOAD){
			$this->outputMsg('<li>Transferring new records...</li>');
			$sql = 'INSERT IGNORE INTO omoccurrences (collid, dbpk, dateentered, '.implode(', ',$fieldArr).' ) '.
				'SELECT u.collid, u.dbpk, "'.date('Y-m-d H:i:s').'", u.'.implode(', u.',$fieldArr).' FROM uploadspectemp u '.
				'WHERE u.occid IS NULL AND u.collid IN('.$this->collId.')';
			//echo '<div>'.$sql.'</div>'; exit;
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
			}

			$this->outputMsg('<li>Linking records in prep for loading determination history and associatedmedia...</li>');
			$sqlOcc1 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.dbpk = o.dbpk) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.occid IS NULL) AND (u.dbpk IS NOT NULL) AND (o.dbpk IS NOT NULL) AND (u.collid IN('.$this->collId.'))';
			if(!$this->conn->query($sqlOcc1)){
				$this->outputMsg('<li>ERROR updating occid after occurrence insert: '.$this->conn->error.'</li>');
			}
			$sqlOcc2 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) ';
			if(!$this->conn->query($sqlOcc2)){
				$this->outputMsg('<li>ERROR updating occid (2nd step) after occurrence insert: '.$this->conn->error.'</li>');
			}

			$rsTest = $this->conn->query('SHOW COLUMNS FROM uploadspectemp WHERE field = "exsiccatiIdentifier"');
			if($rsTest->num_rows){
				$sqlExs2 = 'INSERT INTO omexsiccatinumbers(ometid, exsnumber) '.
					'SELECT DISTINCT u.exsiccatiIdentifier, u.exsiccatinumber '.
					'FROM uploadspectemp u LEFT JOIN omexsiccatinumbers e ON u.exsiccatiIdentifier = e.ometid AND u.exsiccatinumber = e.exsnumber '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NOT NULL) '.
					'AND (u.exsiccatiIdentifier IS NOT NULL) AND (u.exsiccatinumber IS NOT NULL) AND (e.exsnumber IS NULL)';
				if(!$this->conn->query($sqlExs2)){
					$this->outputMsg('<li>ERROR adding new exsiccati numbers: '.$this->conn->error.'</li>');
				}
				$sqlExs3 = 'INSERT IGNORE INTO omexsiccatiocclink(omenid,occid) '.
					'SELECT e.omenid, u.occid '.
					'FROM uploadspectemp u INNER JOIN omexsiccatinumbers e ON u.exsiccatiIdentifier = e.ometid AND u.exsiccatinumber = e.exsnumber '.
					'WHERE (u.collid IN('.$this->collId.')) AND (e.omenid IS NOT NULL) AND (u.occid IS NOT NULL)';
				if($this->conn->query($sqlExs3)){
					$this->outputMsg('<li>Specimens linked to exsiccati index </li>');
				}
				else{
					$this->outputMsg('<li>ERROR adding new exsiccati numbers: '.$this->conn->error.'</li>');
				}
			}
			$rsTest->free();
		}
	}

	private function versionOccurrenceEdits(): void
	{
		$nfnFieldArr = array();
		$sql = 'SHOW COLUMNS FROM omoccurrences';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if(in_array($field, $this->symbFields, true)) {
				$nfnFieldArr[] = $field;
			}
		}
		$rs->free();

		$sqlFrag = '';
		foreach($nfnFieldArr as $field){
			$sqlFrag .= ',u.'.$field.',o.'.$field.' as old_'.$field;
		}
		$sql = 'SELECT o.occid'.$sqlFrag.' FROM omoccurrences o INNER JOIN uploadspectemp u ON o.occid = u.occid '.
			'WHERE o.collid IN('.$this->collId.') AND u.collid IN('.$this->collId.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$editArr = array();
			foreach($nfnFieldArr as $field){
				if($r[$field] && $r['old_'.$field] !== $r[$field]){
					if($r['old_'.$field] && $field !== 'processingstatus'){
						$editArr[0]['old'][$field] = $r['old_'.$field];
						$editArr[0]['new'][$field] = $r[$field];
					}
					else{
						$editArr[1]['old'][$field] = $r['old_'.$field];
						$editArr[1]['new'][$field] = $r[$field];
					}
				}
			}
			foreach($editArr as $appliedStatus => $eArr){
				$sql = 'INSERT INTO omoccurrevisions(occid, oldValues, newValues, externalSource, reviewStatus, appliedStatus) '.
					'VALUES('.$r['occid'].',"'.$this->cleanInStr(json_encode($eArr['old'])).'","'.$this->cleanInStr(json_encode($eArr['new'])).'","Notes from Nature Expedition",1,'.$appliedStatus.')';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px;">ERROR adding edit revision ('.$this->conn->error.')</li>');
				}
			}
		}
		$rs->free();
	}

	protected function transferIdentificationHistory(): void
	{
		$sql = 'SELECT count(*) AS cnt FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if(($r = $rs->fetch_object()) && $r->cnt) {
			$this->outputMsg('<li>Transferring Determination History...</li>');

			$sql = 'UPDATE uploaddetermtemp ud INNER JOIN uploadspectemp u ON ud.collid = u.collid AND ud.dbpk = u.dbpk '.
				'SET ud.occid = u.occid '.
				'WHERE (ud.occid IS NULL) AND (u.occid IS NOT NULL) AND (ud.collid IN('.$this->collId.'))';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploaddetermtemp: '.$this->conn->error.'</li> ');
			}

			$sqlDel = 'DELETE u.* '.
				'FROM uploaddetermtemp u INNER JOIN omoccurdeterminations d ON u.occid = d.occid '.
				'WHERE (u.collid IN('.$this->collId.')) '.
				'AND (d.sciname = u.sciname) AND (d.identifiedBy = u.identifiedBy) AND (d.dateIdentified = u.dateIdentified)';
			$this->conn->query($sqlDel);

			$sql = 'INSERT IGNORE INTO omoccurdeterminations (occid, sciname, scientificNameAuthorship, identifiedBy, dateIdentified, '.
				'identificationQualifier, iscurrent, identificationReferences, identificationRemarks, sourceIdentifier) '.
				'SELECT u.occid, u.sciname, u.scientificNameAuthorship, u.identifiedBy, u.dateIdentified, '.
				'u.identificationQualifier, u.iscurrent, u.identificationReferences, u.identificationRemarks, sourceIdentifier '.
				'FROM uploaddetermtemp u '.
				'WHERE u.occid IS NOT NULL AND (u.collid IN('.$this->collId.'))';
			if($this->conn->query($sql)){
				$sqlDel = 'DELETE * '.
					'FROM uploaddetermtemp '.
					'WHERE (collid IN('.$this->collId.'))';
				$this->conn->query($sqlDel);
			}
			else{
				$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
			}
		}
		$rs->free();
	}

	private function prepareAssociatedMedia(): void
	{
		$sql = 'SELECT associatedmedia, tidinterpreted, occid '.
			'FROM uploadspectemp '.
			'WHERE (associatedmedia IS NOT NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$this->outputMsg('<li>Preparing associatedMedia for image transfer...</li>');
			while($r = $rs->fetch_object()){
				$mediaArr = explode(',',trim(str_replace(array(';','|'),',',$r->associatedmedia),', '));
				foreach($mediaArr as $mediaUrl){
					$mediaUrl = trim($mediaUrl);
					if(strpos($mediaUrl,'"')) {
						continue;
					}
					$this->loadImageRecord(array('occid'=>$r->occid,'tid'=>($r->tidinterpreted?:''),'originalurl'=>$mediaUrl,'url'=>'empty'));
				}
			}
		}
		$rs->free();
	}

	private function prepareImages(): void
	{
		$sql = 'SELECT * FROM uploadimagetemp WHERE collid = '.$this->collId;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$this->outputMsg('<li>Preparing images for transfer... </li>');
			$sql = 'DELETE FROM uploadimagetemp '.
				'WHERE (originalurl LIKE "%.dng" OR originalurl LIKE "%.tif") AND (collid = '.$this->collId.')';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">step 1 of 4... </li>');
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">WARNING removing non-jpgs from uploadimagetemp: '.$this->conn->error.'</li> ');
			}

			$sql = 'UPDATE uploadimagetemp ui INNER JOIN uploadspectemp u ON ui.collid = u.collid AND ui.dbpk = u.dbpk '.
				'SET ui.occid = u.occid '.
				'WHERE (ui.occid IS NULL) AND (u.occid IS NOT NULL) AND (ui.collid = '.$this->collId.')';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">step 2 of 4... </li>');
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploadimagetemp: '.$this->conn->error.'</li> ');
			}

			$sql = 'DELETE ui.* '.
				'FROM uploadimagetemp ui LEFT JOIN uploadspectemp u ON ui.collid = u.collid AND ui.dbpk = u.dbpk '.
				'WHERE (ui.occid IS NULL) AND (ui.collid = '.$this->collId.') AND (u.collid IS NULL)';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">step 3 of 4... </li>');
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">WARNING deleting orphaned uploadimagetemp records: '.$this->conn->error.'</li> ');
			}

			$sql = 'DELETE u.* FROM uploadimagetemp u INNER JOIN images i ON u.occid = i.occid '.
				'WHERE (u.collid = '.$this->collId.') AND (u.originalurl = i.originalurl)';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">step 4 of 4... </li>');
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">ERROR deleting uploadimagetemp records with matching originalurls: '.$this->conn->error.'</li> ');
			}
			$sql = 'DELETE u.* FROM uploadimagetemp u INNER JOIN images i ON u.occid = i.occid '.
				'WHERE (u.collid = '.$this->collId.') AND (u.url = i.url) AND (i.url != "") AND (i.url != "empty")';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">ERROR deleting uploadimagetemp records with matching originalurls: '.$this->conn->error.'</li> ');
			}

			$this->setImageTransferCount();
			$this->outputMsg('<li style="margin-left:10px;">Revised count: '.$this->imageTransferCount.' images</li> ');
		}
		$rs->free();
	}

	protected function transferImages(): void
	{
		$sql = 'SELECT count(*) AS cnt FROM uploadimagetemp WHERE (collid = '.$this->collId.')';
		$rs = $this->conn->query($sql);
		if(($r = $rs->fetch_object()) && $r->cnt) {
			$this->outputMsg('<li>Transferring images...</li>');
			$sql = 'UPDATE uploadimagetemp ui INNER JOIN uploadspectemp u ON ui.collid = u.collid AND ui.dbpk =u.dbpk '.
				'SET ui.occid = u.occid '.
				'WHERE (ui.occid IS NULL) AND (u.occid IS NOT NULL) AND (ui.collid = '.$this->collId.')';
			//echo $sql.'<br/>';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploadimagetemp: '.$this->conn->error.'</li> ');
			}

			$this->setImageTransferCount();

			$sql = 'INSERT INTO images(url,thumbnailurl,originalurl,archiveurl,occid,tid,format,caption,photographer,owner,sourceIdentifier,notes ) '.
				'SELECT url,thumbnailurl,originalurl,archiveurl,occid,tid,format,caption,photographer,owner,sourceIdentifier,notes '.
				'FROM uploadimagetemp '.
				'WHERE (occid IS NOT NULL) AND (collid = '.$this->collId.')';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">'.$this->imageTransferCount.' images transferred</li> ');
			}
			else{
				$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
			}
		}
		$rs->free();
	}

	protected function transferHostAssociations(): void
	{
		$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE collid = '.$this->collId.' AND `host` IS NOT NULL';
		$rs = $this->conn->query($sql);
		if(($r = $rs->fetch_object()) && $r->cnt) {
			$this->outputMsg('<li>Transferring host associations...</li>');
			$sql = 'UPDATE uploadspectemp AS s LEFT JOIN omoccurassociations AS a ON s.occid = a.occid '.
				'SET a.verbatimsciname = s.`host` '.
				'WHERE a.occid IS NOT NULL AND s.`host` IS NOT NULL AND a.relationship = "host" ';
			//echo $sql.'<br/>';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING updating host associations within omoccurassociations: '.$this->conn->error.'</li> ');
			}

			$sql = 'INSERT INTO omoccurassociations(occid,relationship,verbatimsciname) '.
				'SELECT s.occid, "host", s.`host` '.
				'FROM uploadspectemp AS s LEFT JOIN omoccurassociations AS a ON s.occid = a.occid '.
				'WHERE ISNULL(a.occid) AND s.`host` IS NOT NULL ';
			if($this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px;">Host associations updated</li> ');
			}
			else{
				$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
			}
		}
		$rs->free();
	}

	protected function finalCleanup(): void
	{
		$this->outputMsg('<li>Records Transferred</li>');
		$this->outputMsg('<li>Cleaning house</li>');

		$sql = 'UPDATE omcollectionstats SET uploaddate = CURDATE() WHERE collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$sql = 'DELETE FROM uploadspectemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		$this->conn->query('OPTIMIZE TABLE uploadspectemp');

		$sql = 'DELETE FROM uploaddetermtemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		$this->conn->query('OPTIMIZE TABLE uploaddetermtemp');

		$sql = 'DELETE FROM uploadimagetemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		$this->conn->query('OPTIMIZE TABLE uploadimagetemp');

		$occurMain = new OccurrenceMaintenance($this->conn);

		if(!$occurMain->generalOccurrenceCleaning($this->collId)){
			$errorArr = $occurMain->getErrorArr();
			foreach($errorArr as $errorStr){
				echo '<li style="margin-left:20px;">'.$errorStr.'</li>';
			}
		}

		$this->outputMsg('<li style="margin-left:10px;">Protecting sensitive species...</li>');
		$occurMain->protectRareSpecies($this->collId);

		$this->outputMsg('<li style="margin-left:10px;">Updating statistics...</li>');
		if(!$occurMain->updateCollectionStats($this->collId)){
			$errorArr = $occurMain->getErrorArr();
			foreach($errorArr as $errorStr){
				echo '<li style="margin-left:20px;">'.$errorStr.'</li>';
			}
		}

		$this->outputMsg('<li style="margin-left:10px;">Populating global unique identifiers (GUIDs) for all records... </li>');
		$uuidManager = new UuidFactory();
		$uuidManager->setSilent(1);
		$uuidManager->populateGuids();

		if($this->imageTransferCount){
			$this->outputMsg('<li style="margin-left:10px;color:orange">WARNING: Image thumbnails may need to be created using the <a href="../../imagelib/admin/thumbnailbuilder.php?collid='.$this->collId.'">Image Thumbnail Builder</a></li>');
		}
	}

	protected function loadRecord($recMap): void
	{
		$recMap = OccurrenceUtilities::occurrenceArrayCleaning($recMap);
		if(array_key_exists('institutioncode',$recMap) && $recMap['institutioncode'] === $this->collMetadataArr['institutioncode']){
			unset($recMap['institutioncode']);
		}
		if(array_key_exists('collectioncode',$recMap) && $recMap['collectioncode'] === $this->collMetadataArr['collectioncode']){
			unset($recMap['collectioncode']);
		}

		if($this->pKField && array_key_exists($this->pKField,$recMap) && !array_key_exists('dbpk',$recMap)){
			$recMap['dbpk'] = $recMap[$this->pKField];
		}

		if(array_key_exists('dbpk',$recMap)){
			$recMap['dbpk'] = trim(preg_replace('/\s\s+/',' ',$recMap['dbpk']));
		}

		if($this->processingStatus){
			$recMap['processingstatus'] = $this->processingStatus;
		}
		elseif($this->uploadType === $this->SKELETAL){
			$recMap['processingstatus'] = 'unprocessed';
		}

		if($this->sourceDatabaseType === 'specify' && (!isset($recMap['occurrenceid']) || !$recMap['occurrenceid']) && strlen($recMap['dbpk']) === 36) {
			$recMap['occurrenceid'] = $recMap['dbpk'];
		}

		if(!array_key_exists('basisofrecord',$recMap) || !$recMap['basisofrecord']){
			$recMap['basisofrecord'] = ($this->collMetadataArr['colltype'] === 'Preserved Specimens' ?'PreservedSpecimen':'HumanObservation');
		}

		$sqlFragments = $this->getSqlFragments($recMap,$this->fieldMap);
		if($sqlFragments){
			$sql = 'INSERT INTO uploadspectemp(collid'.$sqlFragments['fieldstr'].') '.
				'VALUES('.$this->collId.$sqlFragments['valuestr'].')';
			//echo "<div>SQL: ".$sql."</div>";
			if($this->conn->query($sql)){
				$this->transferCount++;
				if($this->transferCount%1000 === 0) {
					$this->outputMsg('<li style="margin-left:10px;">Count: ' . $this->transferCount . '</li>');
				}
			}
			else{
				$this->outputMsg('<li>FAILED adding record #' .$this->transferCount. '</li>');
				$this->outputMsg("<li style='margin-left:10px;'>Error: ".$this->conn->error. '</li>');
				$this->outputMsg("<li style='margin:0 0 10px 10px;'>SQL: $sql</li>");
			}
		}
	}

	protected function loadIdentificationRecord($recMap): void
	{
		if($recMap){
			if(isset($recMap['coreid']) && !isset($recMap['dbpk'])){
				$recMap['dbpk'] = $recMap['coreid'];
				unset($recMap['coreid']);
			}

			if(isset($recMap['dbpk']) && $recMap['dbpk'] && (isset($recMap['sciname']) || isset($recMap['genus']))){

				if(!array_key_exists('sciname',$recMap) || !$recMap['sciname']){
					if(array_key_exists('genus',$recMap)){
						$sciName = $recMap['genus'];
						if(array_key_exists('specificepithet',$recMap) && $recMap['specificepithet']) {
							$sciName .= ' ' . $recMap['specificepithet'];
						}
						if(array_key_exists('taxonrank',$recMap) && $recMap['taxonrank']) {
							$sciName .= ' ' . $recMap['taxonrank'];
						}
						if(array_key_exists('infraspecificepithet',$recMap) && $recMap['infraspecificepithet']) {
							$sciName .= ' ' . $recMap['infraspecificepithet'];
						}
						$recMap['sciname'] = trim($sciName);
					}
				}
				unset($recMap['genus'], $recMap['specificepithet'], $recMap['taxonrank'], $recMap['infraspecificepithet']);
				if(!array_key_exists('scientificnameauthorship',$recMap) || !$recMap['scientificnameauthorship']){
					$parsedArr = OccurrenceUtilities::parseScientificName($recMap['sciname']);
					if(array_key_exists('author',$parsedArr)){
						$recMap['scientificnameauthorship'] = $parsedArr['author'];
						$recMap['sciname'] = trim($parsedArr['unitname1'].' '.$parsedArr['unitname2'].' '.$parsedArr['unitind3'].' '.$parsedArr['unitname3']);
					}
				}

				$sqlFragments = $this->getSqlFragments($recMap,$this->identFieldMap);
				if($sqlFragments){
					if(array_key_exists('identifiedby',$recMap) || array_key_exists('dateidentified',$recMap)){
						if(!array_key_exists('identifiedby',$recMap)) {
							$recMap['identifiedby'] = 'not specified';
						}
						if(!array_key_exists('dateidentified',$recMap)) {
							$recMap['dateidentified'] = 'not specified';
						}
						$sql = 'INSERT INTO uploaddetermtemp(collid'.$sqlFragments['fieldstr'].') '.
							'VALUES('.$this->collId.$sqlFragments['valuestr'].')';
						//echo "<div>SQL: ".$sql."</div>"; exit;

						if($this->conn->query($sql)){
							$this->identTransferCount++;
							if($this->identTransferCount%1000 === 0) {
								$this->outputMsg('<li style="margin-left:10px;">Count: ' . $this->identTransferCount . '</li>');
							}
						}
						else{
							$outStr = '<li>FAILED adding identification history record #'.$this->identTransferCount.'</li>';
							$outStr .= '<li style="margin-left:10px;">Error: '.$this->conn->error.'</li>';
							$outStr .= '<li style="margin:0 0 10px 10px;">SQL: '.$sql.'</li>';
							$this->outputMsg($outStr);
						}
					}
				}
			}
		}
	}

	protected function loadImageRecord($recMap): ?bool
	{
		if($recMap){
			if(isset($recMap['originalurl']) && $recMap['originalurl']){
				$testUrl = $recMap['originalurl'];
			}
			elseif(isset($recMap['url']) && $recMap['url']){
				$testUrl = $recMap['url'];
			}
			else{
				return false;
			}
			if(stripos($testUrl,'.dng') || stripos($testUrl,'.tif')){
				return false;
			}
			$skipFormats = array('image/tiff','image/dng','image/bmp','text/html','application/xml','application/pdf','tif','tiff','dng','html','pdf');
			$allowedFormats = array('image/jpeg','image/gif','image/png');
			$imgFormat = '';
			if(isset($recMap['format']) && $recMap['format']){
				$imgFormat = strtolower($recMap['format']);
				if(in_array($imgFormat, $skipFormats, true)) {
					return false;
				}
			}
			else{
				$ext = strtolower(substr(strrchr($testUrl, '.'), 1));
				if(strpos($testUrl,'?')) {
					$ext = substr($ext, 0, strpos($ext, '?'));
				}
				if($ext === 'gif') {
					$imgFormat = 'image/gif';
				}
				if($ext === 'png') {
					$imgFormat = 'image/png';
				}
				if($ext === 'jpg') {
					$imgFormat = 'image/jpeg';
				}
				elseif($ext === 'jpeg') {
					$imgFormat = 'image/jpeg';
				}
				if(!$imgFormat){
					$imgFormat = $this->getMimeType($testUrl);
					if(!in_array(strtolower($imgFormat), $allowedFormats, true)) {
						return false;
					}
				}
			}
			if($imgFormat) {
				$recMap['format'] = $imgFormat;
			}

			if($this->verifyImageUrls && !$this->urlExists($testUrl)) {
				$this->outputMsg('<li style="margin-left:20px;">Bad url: '.$testUrl.'</li>');
				return false;
			}

			if(strpos($testUrl,'inaturalist.org')){
				if(strpos($testUrl,'/original.')){
					$recMap['originalurl'] = $testUrl;
					$recMap['url'] = str_replace('/original.', '/medium.', $testUrl);
					$recMap['thumbnailurl'] = str_replace('/original.', '/small.', $testUrl);
				}
				elseif(strpos($testUrl,'/medium.')){
					$recMap['url'] = $testUrl;
					$recMap['thumbnailurl'] = str_replace('/medium.', '/small.', $testUrl);
					$recMap['originalurl'] = str_replace('/medium.', '/original.', $testUrl);
				}
			}

			if(!isset($recMap['url'])) {
				$recMap['url'] = 'empty';
			}

			$sqlFragments = $this->getSqlFragments($recMap,$this->imageFieldMap);
			if($sqlFragments){
				$sql = 'INSERT INTO uploadimagetemp(collid'.$sqlFragments['fieldstr'].') '.
					'VALUES('.$this->collId.$sqlFragments['valuestr'].')';
				if($this->conn->query($sql)){
					$this->imageTransferCount++;
					$repInt = 1000;
					if($this->verifyImageUrls) {
						$repInt = 100;
					}
					if($this->imageTransferCount%$repInt === 0) {
						$this->outputMsg('<li style="margin-left:10px;">' . $this->imageTransferCount . ' images processed</li>');
					}
				}
				else{
					$this->outputMsg('<li>FAILED adding image record #' .$this->imageTransferCount. '</li>');
					$this->outputMsg("<li style='margin-left:10px;'>Error: ".$this->conn->error. '</li>');
					$this->outputMsg("<li style='margin:0 0 10px 10px;'>SQL: $sql</li>");
				}
			}
		}
		return true;
	}

	private function getSqlFragments($recMap,$fieldMap){
		$hasValue = false;
		$sqlFields = '';
		$sqlValues = '';
		foreach($recMap as $symbField => $valueStr){
			if(strpos($symbField, 'unmapped') !== 0){
				$sqlFields .= ','.$symbField;
				$valueStr = $this->encodeString($valueStr);
				$valueStr = $this->cleanInStr($valueStr);
				if($valueStr) {
					$hasValue = true;
				}
				$type = '';
				$size = 0;
				if(array_key_exists($symbField,$fieldMap)){
					if(array_key_exists('type',$fieldMap[$symbField])){
						$type = $fieldMap[$symbField]['type'];
					}
					if(array_key_exists('size',$fieldMap[$symbField])){
						$size = $fieldMap[$symbField]['size'];
					}
				}
				switch($type){
					case 'numeric':
						if(is_numeric($valueStr)){
							$sqlValues .= ',' .$valueStr;
						}
						elseif(is_numeric(str_replace(',', '',$valueStr))){
							$sqlValues .= ',' .str_replace(',', '',$valueStr);
						}
						else{
							$sqlValues .= ',NULL';
						}
						break;
					case 'decimal':
						if(strpos($valueStr,',')){
							$sqlValues = str_replace(',','',$valueStr);
						}
						if($valueStr && $size && strpos($size,',') !== false){
							$tok = explode(',',$size);
							$m = $tok[0];
							$d = $tok[1];
							if($m && $d){
								$dec = substr($valueStr,strpos($valueStr,'.'));
								if(strlen($dec) > $d){
									$valueStr = round($valueStr,$d);
								}
								$rawLen = strlen(str_replace(array('-','.'),'',$valueStr));
								if($rawLen > $m){
									if(strpos($valueStr,'.') !== false){
										$decLen = strlen(substr($valueStr,strpos($valueStr,'.')));
										if($decLen < ($rawLen - $m)){
											$valueStr = '';
										}
										else{
											$valueStr = round($valueStr,$decLen - ($rawLen - $m));
										}
									}
									else{
										$valueStr = '';
									}
								}
							}
						}
						if(is_numeric($valueStr)){
							$sqlValues .= ',' .$valueStr;
						}
						else{
							$sqlValues .= ',NULL';
						}
						break;
					case 'date':
						$dateStr = OccurrenceUtilities::formatDate($valueStr);
						if($dateStr){
							$sqlValues .= ',"'.$dateStr.'"';
						}
						else{
							$sqlValues .= ',NULL';
						}
						break;
					default:
						if($size && strlen($valueStr) > $size){
							$valueStr = substr($valueStr,0,$size);
						}
						if(substr($valueStr,-1) === "\\"){
							$valueStr = rtrim($valueStr,"\\");
						}
						if($valueStr){
							$sqlValues .= ',"'.$valueStr.'"';
						}
						else{
							$sqlValues .= ',NULL';
						}
				}
			}
		}
		if(!$hasValue) {
			return false;
		}
		return array('fieldstr' => $sqlFields,'valuestr' => $sqlValues);
	}

	public function getTransferCount(): int
	{
		if(!$this->transferCount) {
			$this->setTransferCount();
		}
		return $this->transferCount;
	}

	private function setTransferCount(): void
	{
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE (collid IN('.$this->collId.')) ';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->transferCount = $row->cnt;
			}
			$rs->free();
		}
	}

	public function getIdentTransferCount(): int
	{
		if(!$this->identTransferCount) {
			$this->setIdentTransferCount();
		}
		return $this->identTransferCount;
	}

	private function setIdentTransferCount(): void
	{
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploaddetermtemp '.
				'WHERE (collid IN('.$this->collId.'))';
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->identTransferCount = $row->cnt;
			}
			$rs->free();
		}
	}

	private function getImageTransferCount(): int
	{
		if(!$this->imageTransferCount) {
			$this->setImageTransferCount();
		}
		return $this->imageTransferCount;
	}

	private function setImageTransferCount(): void
	{
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploadimagetemp WHERE (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->imageTransferCount = $r->cnt;
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">ERROR setting image upload count: '.$this->conn->error.'</li> ');
			}
			$rs->free();
		}
	}

	protected function setUploadTargetPath(): void
	{
		global $SERVER_ROOT, $TEMP_DIR_ROOT;
		$tPath = $TEMP_DIR_ROOT;
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $SERVER_ROOT. '/temp';
		}
		if(substr($tPath,-1) !== '/' && substr($tPath,-1) !== '\\'){
			$tPath .= '/';
		}
		if(file_exists($tPath. 'downloads')){
			$tPath .= 'downloads/';
		}
		$this->uploadTargetPath = $tPath;
	}

	public function setIncludeIdentificationHistory($boolIn): void
	{
		$this->includeIdentificationHistory = $boolIn;
	}

	public function setIncludeImages($boolIn): void
	{
		$this->includeImages = $boolIn;
	}

	public function setMatchCatalogNumber($match): void
	{
		$this->matchCatalogNumber = $match;
	}

	public function setMatchOtherCatalogNumbers($match): void
	{
		$this->matchOtherCatalogNumbers = $match;
	}

	public function setVerifyImageUrls($v): void
	{
		$this->verifyImageUrls = $v;
	}

	public function setProcessingStatus($s): void
	{
		$this->processingStatus = $s;
	}

	public function setSourceDatabaseType($type): void
	{
		$this->sourceDatabaseType = $type;
	}

	private function getMimeType($url){
		if(strpos($url, 'http') === false){
	        $url = 'http://' .$url;
	    }
	    $handle = curl_init($url);
	    curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_NOBODY, true);
	    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true );
		curl_exec($handle);

		return curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
	}

	protected function urlExists($url) {
		$exists = false;
		if(strpos($url, 'http') === false){
			$url = 'http://' .$url;
		}
    	if(function_exists('curl_init')){
	    	$handle   = curl_init($url);
			if (false === $handle){
				$exists = false;
		    }
		    curl_setopt($handle, CURLOPT_HEADER, false);
		    curl_setopt($handle, CURLOPT_FAILONERROR, true);
		    curl_setopt($handle, CURLOPT_HTTPHEADER, Array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15') ); // request as if Firefox
		    curl_setopt($handle, CURLOPT_NOBODY, true);
		    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		    $exists = curl_exec($handle);
		    curl_close($handle);
		}

		if(!$exists && file_exists($url)){
			$exists = true;
		}

		if(!$exists){
	    	$exists = (@fclose(@fopen($url, 'rb')));
	    }
	    return $exists;
	}

	protected function encodeString($inStr){
		global $CHARSET;
		$retStr = $inStr;

		$badwordchars=array("\xe2\x80\x98",
							"\xe2\x80\x99",
							"\xe2\x80\x9c",
							"\xe2\x80\x9d",
							"\xe2\x80\x94",
							"\xe2\x80\xa6"
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$inStr = str_replace($badwordchars, $fixedwordchars, $inStr);

		if($inStr){
			$lowerStr = strtolower($CHARSET);
			if($lowerStr === 'utf-8' || $lowerStr === 'utf8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($lowerStr === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
		}
		return $retStr;
	}
}
