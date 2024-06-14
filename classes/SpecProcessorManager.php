<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');

class SpecProcessorManager {

	protected $conn;
	protected $collid = 0;
	protected $title;
	protected $collectionName;
	protected $institutionCode;
	protected $collectionCode;
	protected $projectType;
	protected $managementType;
	protected $specKeyPattern;
	protected $coordX1;
	protected $coordX2;
	protected $coordY1;
	protected $coordY2;
	protected $sourcePath;
	protected $createTnImg = 1;
	protected $createLgImg = 1;
	protected $lastRunDate = '';

	public function __construct() {
		$connection = new DbConnectionService();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function setCollId($id): void
	{
		$this->collid = $id;
		if($this->collid && is_numeric($this->collid) && !$this->collectionName){
			$sql = 'SELECT collid, collectionname, institutioncode, collectioncode, managementtype '.
				'FROM omcollections WHERE (collid = '.$this->collid.')';
			if($rs = $this->conn->query($sql)){
				if($row = $rs->fetch_object()){
					$this->collectionName = $row->collectionname;
					$this->institutionCode = $row->institutioncode;
					$this->collectionCode = $row->collectioncode;
					$this->managementType = $row->managementtype;
				}
				else{
					exit('ABORTED: unable to locate collection in data');
				}
				$rs->close();
			}
			else{
				exit('ABORTED: unable run SQL to obtain collectionName');
			}
		}
	}

	public function editProject($editArr): void
	{
		if(is_numeric($editArr['spprid'])){
			$sqlFrag = '';
			$targetFields = array('title','projecttype','speckeypattern','sourcepath','createtnimg','createlgimg','source');
			if(!isset($editArr['createtnimg'])) {
				$editArr['createtnimg'] = 0;
			}
			if(!isset($editArr['createlgimg'])) {
				$editArr['createlgimg'] = 0;
			}
			foreach($editArr as $k => $v){
				if(in_array($k, $targetFields, true)){
					if(is_numeric($v)){
						$sqlFrag .= ','.$k.' = '.$this->cleanInStr($v);
					}
					elseif($v){
						$sqlFrag .= ','.$k.' = "'.$this->cleanInStr($v).'"';
					}
					else{
						$sqlFrag .= ','.$k.' = NULL';
					}
				}
			}
			$sql = 'UPDATE specprocessorprojects SET '.trim($sqlFrag,' ,').' WHERE (spprid = '.$editArr['spprid'].')';
			if(!$this->conn->query($sql)){
				echo 'ERROR saving project.';
			}
		}
	}

	public function addProject($addArr): void
	{
		$sql = '';
		if(isset($addArr['projecttype'])){
			$sourcePath = $addArr['sourcepath'];
			if($sourcePath === '-- Use Default Path --') {
				$sourcePath = '';
			}
            $sql = 'INSERT INTO specprocessorprojects(collid,title,speckeypattern,projecttype,sourcepath,'.
                'createtnimg,createlgimg) '.
                'VALUES('.$this->collid.',"'.$this->cleanInStr($addArr['title']).'","'.
                $this->cleanInStr($addArr['speckeypattern']).'",'.
                ($addArr['projecttype']?'"'.$this->cleanInStr($addArr['projecttype']).'"':'NULL').','.
                ($sourcePath?'"'.$this->cleanInStr($sourcePath).'"':'NULL').','.
                (isset($addArr['createtnimg'])&&$addArr['createtnimg']?$addArr['createtnimg']:'NULL').','.
                (isset($addArr['createlgimg'])&&$addArr['createlgimg']?$addArr['createlgimg']:'NULL').')';
		}
		if($sql && !$this->conn->query($sql)) {
			echo 'ERROR saving project.';
		}
	}

	public function deleteProject($spprid): void
	{
		$sql = 'DELETE FROM specprocessorprojects WHERE (spprid = '.$spprid.')';
		$this->conn->query($sql);
	}

	public function setProjVariables($crit): void
	{
		$sqlWhere = '';
		if(is_numeric($crit)){
			$sqlWhere .= 'WHERE (spprid = '.$crit.')';
		}
		if($sqlWhere){
			$sql = 'SELECT collid, title, projecttype, speckeypattern, coordx1, coordx2, coordy1, coordy2, sourcepath, '.
				'createtnimg, createlgimg, source '.
				'FROM specprocessorprojects '.$sqlWhere;
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				if(!$this->collid) {
					$this->setCollId($row->collid);
				}
				$this->title = $row->title;
				$this->specKeyPattern = $row->speckeypattern;
				$this->coordX1 = $row->coordx1;
				$this->coordX2 = $row->coordx2;
				$this->coordY1 = $row->coordy1;
				$this->coordY2 = $row->coordy2;
				$this->sourcePath = $row->sourcepath;
				$this->createTnImg = $row->createtnimg;
				$this->createLgImg = $row->createlgimg;
				$this->lastRunDate = $row->source;
                $this->projectType = $row->projecttype;
			}
			$rs->free();

			if($this->sourcePath && substr($this->sourcePath,-1) !== '/' && substr($this->sourcePath,-1) !== '\\') {
				$this->sourcePath .= '/';
			}
		}
	}

	public function getProjects(): array
	{
		$projArr = array();
		if($this->collid){
			$sql = 'SELECT spprid, title '.
				'FROM specprocessorprojects '.
				'WHERE (collid = '.$this->collid.') ';
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$projArr[$row->spprid] = $row->title;
			}
			$rs->free();
		}
		return $projArr;
	}

	public function getSpecWithImage($procStatus = null): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt '.
					'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
					'WHERE (o.collid = '.$this->collid.') ';
			if($procStatus){
				if($procStatus === 'null'){
					$sql .= 'AND processingstatus IS NULL';
				}
				else{
					$sql .= 'AND processingstatus = "'.$this->cleanInStr($procStatus).'"';
				}
			}
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	public function getProcessingStatusList(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT processingstatus '.
				'FROM omoccurrences '.
				'WHERE collid = '.$this->collid;
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->processingstatus) {
					$retArr[] = $r->processingstatus;
				}
			}
			$rs->free();
			sort($retArr);
		}
		return $retArr;
	}

	public function getProcessingStats(): array
	{
		$retArr = array();
		$retArr['total'] = $this->getTotalCount();
		$retArr['ps'] = $this->getProcessingStatusCountArr();
		$retArr['noimg'] = $this->getSpecNoImageCount();
		$retArr['unprocnoimg'] = $this->getUnprocSpecNoImage();
		$retArr['noskel'] = $this->getSpecNoSkel();
		$retArr['unprocwithdata'] = $this->getUnprocWithData();
		return $retArr;
	}

	private function getTotalCount(): int
	{
		$totalCnt = 0;
		if($this->collid){
			$sql = 'SELECT count(*) AS cnt '.
				'FROM omoccurrences '.
				'WHERE collid = '.$this->collid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$totalCnt = $r->cnt;
			}
			$rs->free();
		}
		return $totalCnt;
	}

	private function getProcessingStatusCountArr(): array
	{
		$retArr = array();
		if($this->collid){
			$psArr = array();
			$sql = 'SELECT processingstatus, count(*) AS cnt '.
				'FROM omoccurrences '.
				'WHERE collid = '.$this->collid.' GROUP BY processingstatus';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$key = $r->processingstatus ? strtolower($r->processingstatus) : 'no-status';
                $psArr[$key] = $r->cnt;
			}
			$rs->free();
			$statusArr = array('unprocessed','stage 1','stage 2','stage 3','pending duplicate','pending review-nfn','pending review','expert required','reviewed','closed','empty status');
			foreach($statusArr as $v){
				if(array_key_exists($v,$psArr)){
					$retArr[$v] = $psArr[$v];
					unset($psArr[$v]);
				}
			}
			foreach($psArr as $k => $cnt){
				$retArr[$k] = $cnt;
			}
		}
		return $retArr;
	}

	private function getSpecNoImageCount(): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM omoccurrences o LEFT JOIN images i ON o.occid = i.occid '.
				'WHERE o.collid = '.$this->collid.' AND i.imgid IS NULL ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	public function getUnprocSpecNoImage(): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM omoccurrences o LEFT JOIN images i ON o.occid = i.occid '.
				'WHERE (o.collid = '.$this->collid.') AND (i.imgid IS NULL) AND (o.processingstatus = "unprocessed") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	private function getSpecNoSkel(): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.collid = '.$this->collid.') AND (o.processingstatus = "unprocessed") '.
				'AND (o.sciname IS NULL) AND (o.stateprovince IS NULL)';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	private function getUnprocWithData(): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT count(*) AS cnt FROM omoccurrences '.
				'WHERE (processingstatus = "unprocessed") AND (stateProvince IS NOT NULL) AND (locality IS NOT NULL) AND (collid = '.$this->collid.')';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	public function getUserList(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT u.uid, CONCAT(CONCAT_WS(", ",u.lastname, u.firstname)," (",u.username,")") AS username '.
			'FROM omoccurrences o INNER JOIN omoccuredits e ON o.occid = e.occid '.
			'INNER JOIN users u ON e.uid = u.uid '.
			'WHERE (o.collid = '.$this->collid.') '.
			'ORDER BY u.lastname, u.firstname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		$rs->free();
		return $retArr;
	}

	public function getFullStatReport($getArr): array
	{
		$retArr = array();
		$startDate = (preg_match('/^[\d-]+$/', $getArr['startdate'])?$getArr['startdate']:'');
		$endDate = (preg_match('/^[\d-]+$/', $getArr['enddate'])?$getArr['enddate']:'');
		$uid = (is_numeric($getArr['uid'])?$getArr['uid']:'');
		$interval = $getArr['interval'];
		$processingStatus = $this->cleanInStr($getArr['processingstatus']);

		$dateFormat = '';
		$dfgb = '';
		if($interval === 'hour'){
			$dateFormat = '%Y-%m-%d %Hhr, %W';
			$dfgb = '%Y-%m-%d %H';
		}
		elseif($interval === 'day'){
			$dateFormat= '%Y-%m-%d, %W';
			$dfgb = '%Y-%m-%d';
		}
		elseif($interval === 'week'){
			$dateFormat= '%Y-%m week %U';
			$dfgb = '%Y-%m-%U';
		}
		elseif($interval === 'month'){
			$dateFormat= '%Y-%m';
			$dfgb = '%Y-%m';
		}
		$sql = 'SELECT DATE_FORMAT(e.initialtimestamp, "'.$dateFormat.'") AS timestr, u.username';
		if($processingStatus) {
			$sql .= ', e.fieldvalueold, e.fieldvaluenew, o.processingstatus';
		}
		$sql .= ', count(DISTINCT o.occid) AS cnt ';
		$hasEditType = $this->hasEditType();
		if($hasEditType){
			$sql .= ', COUNT(DISTINCT CASE WHEN e.editType = 0 THEN o.occid ELSE NULL END) as cntexcbatch ';
		}
		$sql .= 'FROM omoccurrences o INNER JOIN omoccuredits e ON o.occid = e.occid '.
			'INNER JOIN users u ON e.uid = u.uid '.
			'WHERE (o.collid = '.$this->collid.') ';
		if($startDate && $endDate){
			$sql .= 'AND (e.initialtimestamp BETWEEN "'.$startDate.'" AND "'.$endDate.'") ';
		}
		elseif($startDate){
			$sql .= 'AND (DATE(e.initialtimestamp) > "'.$startDate.'") ';
		}
		elseif($endDate){
			$sql .= 'AND (DATE(e.initialtimestamp) < "'.$endDate.'") ';
		}
		if($uid){
			$sql .= 'AND (e.uid = '.$uid.') ';
		}
		if($processingStatus){
			$sql .= 'AND e.fieldname = "processingstatus" ';
			if($processingStatus !== 'all'){
				$sql .= 'AND (e.fieldvaluenew = "'.$processingStatus.'") ';
			}
		}
		$sql .= 'GROUP BY DATE_FORMAT(e.initialtimestamp, "'.$dfgb.'"), u.username ';
		if($processingStatus) {
			$sql .= ', e.fieldvalueold, e.fieldvaluenew, o.processingstatus ';
		}
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->timestr][$r->username]['cnt'] = $r->cnt;
			if($hasEditType) {
				$retArr[$r->timestr][$r->username]['cntexcbatch'] = $r->cntexcbatch;
			}
			if($processingStatus){
				$retArr[$r->timestr][$r->username]['os'] = $r->fieldvalueold;
				$retArr[$r->timestr][$r->username]['ns'] = $r->fieldvaluenew;
				$retArr[$r->timestr][$r->username]['cs'] = $r->processingstatus;
			}
		}
		$rs->free();
		return $retArr;
	}

	public function hasEditType(): bool
	{
		$hasEditType = false;
		$rsTest = $this->conn->query('SHOW COLUMNS FROM omoccuredits WHERE field = "editType"');
		if($rsTest->num_rows) {
			$hasEditType = true;
		}
		$rsTest->free();
		return $hasEditType;
	}

 	public function downloadReportData($target): void
	{
		$fileName = 'SymbSpecNoImages_'.time().'.csv';
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		$headerArr = array('occid','catalogNumber','sciname','recordedBy','recordNumber','eventDate','country','stateProvince','county');
		$sqlFrag = '';
		if($target === 'dlnoimg'){
			$sqlFrag .= 'FROM omoccurrences o LEFT JOIN images i ON o.occid = i.occid WHERE o.collid = '.$this->collid.' AND i.imgid IS NULL ';
		}
		elseif($target === 'unprocnoimg'){
			$sqlFrag .= 'FROM omoccurrences o LEFT JOIN images i ON o.occid = i.occid WHERE (o.collid = '.$this->collid.') AND (i.imgid IS NULL) AND (o.processingstatus = "unprocessed") ';
		}
		elseif($target === 'noskel'){
			$sqlFrag .= 'FROM omoccurrences o WHERE (o.collid = '.$this->collid.') AND (o.processingstatus = "unprocessed") AND (o.sciname IS NULL) AND (o.stateprovince IS NULL)';
		}
		elseif($target === 'unprocwithdata'){
			$headerArr[] = 'locality';
			$sqlFrag .= 'FROM omoccurrences o WHERE (o.collid = '.$this->collid.') AND (o.processingstatus = "unprocessed") AND (stateProvince IS NOT NULL) AND (o.locality IS NOT NULL)';
		}
		$headerArr[] = 'processingstatus';
		$sql = 'SELECT o.'.implode(',',$headerArr).' '.$sqlFrag;
		//echo $sql;
		$result = $this->conn->query($sql);
		if($result){
    		$outstream = fopen('php://output', 'wb');
			fputcsv($outstream, $headerArr);
			while($row = $result->fetch_assoc()){
				fputcsv($outstream, $row);
			}
			fclose($outstream);
		}
		else{
			echo "Recordset is empty.\n";
		}
        if($result) {
			$result->close();
		}
	}

	public function getLogListing(): array
	{
		$retArr = array();
		if($this->collid){
			$GLOBALS['LOG_PATHFrag'] = '/imgProccessing';
			if(file_exists($GLOBALS['LOG_PATH'] . $GLOBALS['LOG_PATHFrag']) && $fh = opendir($GLOBALS['LOG_PATH'] . $GLOBALS['LOG_PATHFrag'])) {
				while($fileName = readdir($fh)){
					if(strpos($fileName,$this->collid.'_') === 0){
						$retArr[] = $fileName;
					}
				}
			}
		}
		rsort($retArr);
		return $retArr;
	}

	public function getTitle(){
		return $this->title;
	}

	public function getCollectionName(){
		return $this->collectionName;
	}

	public function getInstitutionCode(){
		return $this->institutionCode;
	}

	public function getCollectionCode(){
		return $this->collectionCode;
	}

	public function getProjectType(){
		return $this->projectType;
	}

	public function getSpecKeyPattern(){
		return $this->specKeyPattern;
	}

	public function getSourcePath(){
		return $this->sourcePath;
	}

	public function getCreateTnImg(): int
	{
		return $this->createTnImg;
	}

	public function getCreateLgImg(): int
	{
		return $this->createLgImg;
	}

	public function getConn(): mysqli
	{
 		return $this->conn;
 	}

 	protected function cleanInStr($str): string
	{
        return $this->conn->real_escape_string(trim($str));
	}
}
