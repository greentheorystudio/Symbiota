<?php
include_once(__DIR__ . '/WebServiceBase.php');
include_once(__DIR__ . '/OccurrenceUtilities.php');
include_once(__DIR__ . '/Sanitizer.php');

class WsOccurEditor extends WebServiceBase{

	private $occidArr = array();
	private $dwcArr = array();
	private $editType = 1;		//1 = occurrence, 2 = identification annotation...
	private $source;
	private $editor;
	private $origTimestamp;
	private $approvedFields;
	private $fieldTranslation;

	public function __construct(){
		parent::__construct(null);
		$this->approvedFields = array('catalognumber', 'othercatalognumbers', 'occurrenceid','family', 'sciname',
			'scientificnameauthorship', 'identifiedby', 'dateidentified', 'identificationreferences',
			'identificationremarks', 'taxonremarks', 'identificationqualifier', 'typestatus', 'recordedby', 'recordnumber',
			'associatedcollectors', 'eventdate', 'year', 'month', 'day','verbatimeventdate', 'habitat', 'substrate', 'fieldnumber',
			'occurrenceremarks', 'associatedtaxa', 'verbatimattributes',
			'dynamicproperties', 'reproductivecondition', 'cultivationstatus', 'establishmentmeans',
			'lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations',
			'country', 'stateprovince', 'county', 'municipality', 'locality', 'localitysecurity', 'localitysecurityreason',
			'decimallatitude', 'decimallongitude','geodeticdatum', 'coordinateuncertaintyinmeters',
			'locationremarks', 'verbatimcoordinates', 'georeferencedby', 'georeferenceprotocol', 'georeferencesources',
			'georeferenceverificationstatus', 'georeferenceremarks', 'minimumelevationinmeters', 'maximumelevationinmeters',
			'verbatimelevation', 'disposition', 'language', 'duplicatequantity',
			'basisofrecord', 'processingstatus', 'recordenteredby');
		$this->fieldTranslation = array('species'=>'specificepithet','scientificnameauthor'=>'scientificnameauthorship','collector'=>'recordedby','collectornumber'=>'recordnumber',
			'yearcollected'=>'year','monthcollected'=>'month','daycollected'=>'day','latitude'=>'decimallatitude','longitude'=>'decimallongitude',
			'minimumelevation'=>'minimumelevationinmeters','maximumelevation'=>'maximumelevationinmeters','minimumdepth'=>'minimumdepthinmeters',
			'maximumdepth'=>'maximumdepthinmeters','notes'=>'occurrenceremarks');
	}

    public function applyEdit(){
		if($this->editType === 1) {
            return $this->applyOccurrenceEdit();
        }
		/*if($this->editType === 2) {
            return $this->applyIdentificationEdit();
        }*/

		return true;
	}

	private function applyOccurrenceEdit(): string
    {
		$successArr = array();
		$approvedGeolocateFields = array('decimallatitude','decimallongitude','geodeticdatum','coordinateuncertaintyinmeters','georeferencedby',
			'georeferenceprotocol','georeferencesources','georeferenceverificationstatus','georeferenceremarks');
		foreach($this->occidArr as $occid){
			if($this->source === 'geolocate'){
				$this->dwcArr['georeferencesources'] = 'GeoLocate (CoGe)';
				$this->dwcArr['georeferencedby'] = $this->editor;
			}
			$sql = 'SELECT '.implode(',',array_keys($this->dwcArr)).' FROM omoccurrences WHERE occid = '.$occid;
			$rs = $this->conn->query($sql);
			$oldValueArr = $rs->fetch_assoc();
			$rs->free();
			if(!$oldValueArr){
				$this->warningArr[$occid] = 'Identifier not valid';
				continue;
			}

			$vettedOldValues = array();
			$vettedNewValues = array();
			foreach($this->dwcArr as $symbField => $value){
				if(array_key_exists($symbField, $oldValueArr) && $value !== $oldValueArr[$symbField]){
					$vettedOldValues[$symbField] = $oldValueArr[$symbField]; 
					$vettedNewValues[$symbField] = $value;
				}
			}
			if(!$vettedNewValues){
				$this->warningArr[$occid] = 'No values have changed';
				continue;
			}
			
			$appliedStatus = 0;
			if($this->source === 'geolocate'){
				if((!isset($vettedNewValues['decimallatitude'], $vettedNewValues['decimallongitude'])) && !isset($vettedNewValues['georeferenceremarks'])){
					$this->warningArr[$occid] = 'decimalLatitude, decimalLongitude, or georeferenceRemarks are NULL or unchanged';
					continue;
				}

				$vettedOldValues = array_intersect_key($vettedOldValues, array_flip($approvedGeolocateFields));
				$vettedNewValues = array_intersect_key($vettedNewValues, array_flip($approvedGeolocateFields));

				$sqlTest = 'SELECT ocedid FROM omoccuredits WHERE occid = '.$occid.' AND fieldname IN("decimallatitude","decimallongitude") AND (FieldValueNew IS NOT NULL)';
				$rsTest = $this->conn->query($sqlTest);
				if($rsTest->num_rows){
					$appliedStatus = 0;
				}
				else{
					$appliedStatus = 1;
				}
				$rsTest->free();
			}

			$newValueJson = json_encode($vettedNewValues);
			$abort = false;
			$sql2 = 'SELECT newvalues,externalsource,externaleditor '.
				'FROM omoccurrevisions WHERE occid = '.$occid;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				if($newValueJson === $r2->newvalues && $this->source === $r2->externalsource && $this->editor === $r2->externaleditor){
					$abort = true;
					break;
				}
			}
			$rs2->free();
			if($abort){
				$this->warningArr[$occid] = 'Duplicate edit previously submitted';
				continue;
			}
			
			$sql3 = 'INSERT INTO omoccurrevisions(occid,oldValues,newValues,externalSource,externalEditor,reviewStatus,appliedStatus,externalTimestamp) '.
				'VALUES('.$occid.',"'.Sanitizer::cleanInStr(json_encode($vettedOldValues)).'","'.Sanitizer::cleanInStr($newValueJson).'",'.
				($this->source?'"'.Sanitizer::cleanInStr($this->source).'"':'NULL').','.($this->editor?'"'.Sanitizer::cleanInStr($this->editor).'"':'NULL').
				',1,'.$appliedStatus.','.($this->origTimestamp?'"'.Sanitizer::cleanInStr($this->origTimestamp).'"':'NULL').')';
			//echo $sql2; exit;
			if($this->conn->query($sql3)){
				if($appliedStatus){
					$sqlIns = '';
					foreach($vettedNewValues as $k => $v){
						$sqlIns .= ', '.$k.' = "'.Sanitizer::cleanInStr($v).'" ';
					}
					$sql4 = 'UPDATE omoccurrences SET'.substr($sqlIns, 1).'WHERE occid = '.$occid;
					//echo $sql3; exit;
					if(!$this->conn->query($sql4)){
						$this->logOrEcho('ERROR activating edit within occurrence table (occid: '.$occid.').');
						continue;
					}
				}
				//return '{"Result":[{"Status":"SUCCESS","Message":"Edits successfully submitted '.($appliedStatus?'and activated':'but not activated').'"}]}';
				$successArr[$occid] = 'Edits submitted '.($appliedStatus?'and activated':'but NOT activated');
			}
			else{
				$this->logOrEcho('ERROR updating occurrence revisions table (occid: '.$occid.').');
			}
		}
		//Build and return result string
		$retStr = '';
		if($this->warningArr){
			$errStr = '';
			foreach($this->warningArr as $oKey => $msg){
				$errStr .= '{"occid":"'.$oKey.'","Message":"'.$msg.'"},';
			}
			$retStr = '"Error":['.trim($errStr,' ,').']';
		}
		if($successArr){
			$msgStr = '';
			foreach($successArr as $oKey => $msg){
				$msgStr .= '{"occid":"'.$oKey.'","Message":"'.$msg.'"},';
			}
			if($retStr) {
				$retStr .= ',';
			}
			$retStr .= '"Success":['.trim($msgStr,' ,').']';
		}
		return '{"Result":{'.$retStr.'}}';
	}

	public function setOccid($occidStr): void
	{
		if(preg_match('/^[\d,]+$/', $occidStr)){
			if(strpos($occidStr, ',') !== false){
				$this->occidArr = explode(',',$occidStr);
			}
			else{
				$this->occidArr[] = $occidStr;
			}
		}
	}
	
	public function setRecordID($guid): bool
	{
		$status = false;
		$guid = preg_replace("/[^A-Za-z0-9\-]/", '',$guid);
		$sql = 'SELECT occid FROM guidoccurrences WHERE guid = "'.$guid.'"';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->occidArr[] = $r->occid;
			$status = true;
		}
		$rs->free();
		return $status;
	}

	public function setDwcArr($dwcObj): bool
	{
		$recArr = json_decode($dwcObj,true);
		if($recArr){
			$recArr = array_change_key_case($recArr);
			foreach($this->fieldTranslation as $otherName => $symbName){
				if(array_key_exists($otherName, $recArr) && !array_key_exists($symbName, $recArr)){
					$recArr[$symbName] = $recArr[$otherName];
					unset($recArr[$otherName]);
				}
			}
			$recArr = array_intersect_key($recArr, array_flip($this->approvedFields));
			$this->dwcArr = OccurrenceUtilities::occurrenceArrayCleaning($recArr);
			foreach($this->dwcArr as $k => $v){
				$this->dwcArr[$k] = urldecode($v);
			}
			if($this->dwcArr) {
				return true;
			}
		}
		return false;
	}

	public function setEditType($type): void
	{
		if(is_numeric($type)){
			$this->editType = $type;
		}
		elseif(strtolower($type) === 'occurrence'){
			$this->editType = 1;
		}
		elseif(strtolower($type) === 'identification'){
			$this->editType = 2;
		}
	}

	public function setSource($s): void
	{
		$this->source = $s;
	}

	public function setEditor($e): void
	{
		$this->editor = $e;
	}

	public function setOrigTimestamp($ts): void
	{
		$this->origTimestamp = $ts;
	}
}
