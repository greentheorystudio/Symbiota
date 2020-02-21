<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');

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
	protected $patternReplace;
	protected $replaceStr;
	protected $coordX1;
	protected $coordX2;
	protected $coordY1;
	protected $coordY2;
	protected $sourcePath;
	protected $targetPath;
	protected $imgUrlBase;
	protected $webPixWidth = '';
	protected $tnPixWidth = '';
	protected $lgPixWidth = '';
	protected $jpgQuality = 80;
	protected $webMaxFileSize = 300000;
	protected $lgMaxFileSize = 3000000;
	protected $createTnImg = 1;
	protected $createLgImg = 2;
	protected $lastRunDate = '';
	protected $processUsingImageMagick = 0;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if(!($this->conn === false)) {
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
			$targetFields = array('title','projecttype','speckeypattern','patternreplace','replacestr','sourcepath','targetpath','imgurl',
				'webpixwidth','tnpixwidth','lgpixwidth','jpgcompression','createtnimg','createlgimg','source');
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
					elseif($k === 'replacestr'){
						$sqlFrag .= ','.$k.' = "'.$this->conn->real_escape_string($v).'"';
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
				echo 'ERROR saving project: '.$this->conn->error;
			}
		}
	}

	public function addProject($addArr): void
	{
		$this->conn->query('DELETE FROM specprocessorprojects WHERE (title = "OCR Harvest") AND (collid = '.$this->collid.')');
		$sql = '';
		if(isset($addArr['projecttype'])){
			$sourcePath = $addArr['sourcepath'];
			if($sourcePath === '-- Use Default Path --') {
				$sourcePath = '';
			}
			if($addArr['projecttype'] === 'idigbio'){
				$sql = 'INSERT INTO specprocessorprojects(collid,title,speckeypattern,patternreplace,replacestr,projecttype,sourcepath) '.
					'VALUES('.$this->collid.',"iDigBio CSV upload","'.$this->cleanInStr($addArr['speckeypattern']).'",'.
					($addArr['patternreplace']?'"'.$this->cleanInStr($addArr['patternreplace']).'"':'NULL').','.
					($addArr['replacestr']?'"'.$this->conn->real_escape_string($addArr['replacestr']).'"':'NULL').','.
					($addArr['projecttype']?'"'.$this->cleanInStr($addArr['projecttype']).'"':'NULL').','.
					($sourcePath?'"'.$this->cleanInStr($sourcePath).'"':'NULL').')';
			}
			elseif($addArr['projecttype'] === 'iplant'){
				$sql = 'INSERT INTO specprocessorprojects(collid,title,speckeypattern,patternreplace,replacestr,projecttype,sourcepath) '.
					'VALUES('.$this->collid.',"IPlant Image Processing","'.$this->cleanInStr($addArr['speckeypattern']).'",'.
					($addArr['patternreplace']?'"'.$this->cleanInStr($addArr['patternreplace']).'"':'NULL').','.
					($addArr['replacestr']?'"'.$this->conn->real_escape_string($addArr['replacestr']).'"':'NULL').','.
					($addArr['projecttype']?'"'.$this->cleanInStr($addArr['projecttype']).'"':'NULL').','.
					($sourcePath?'"'.$this->cleanInStr($sourcePath).'"':'NULL').')';
			}
			elseif($addArr['projecttype'] === 'local'){
				$sql = 'INSERT INTO specprocessorprojects(collid,title,speckeypattern,patternreplace,replacestr,projecttype,sourcepath,targetpath,'.
					'imgurl,webpixwidth,tnpixwidth,lgpixwidth,jpgcompression,createtnimg,createlgimg) '.
					'VALUES('.$this->collid.',"'.$this->cleanInStr($addArr['title']).'","'.
					$this->cleanInStr($addArr['speckeypattern']).'",'.
					($addArr['patternreplace']?'"'.$this->cleanInStr($addArr['patternreplace']).'"':'NULL').','.
					($addArr['replacestr']?'"'.$this->conn->real_escape_string($addArr['replacestr']).'"':'NULL').','.
					($addArr['projecttype']?'"'.$this->cleanInStr($addArr['projecttype']).'"':'NULL').','.
					($sourcePath?'"'.$this->cleanInStr($sourcePath).'"':'NULL').','.
					(isset($addArr['targetpath'])&&$addArr['targetpath']?'"'.$this->cleanInStr($addArr['targetpath']).'"':'NULL').','.
					(isset($addArr['imgurl'])&&$addArr['imgurl']?'"'.$addArr['imgurl'].'"':'NULL').','.
					(isset($addArr['webpixwidth'])&&$addArr['webpixwidth']?$addArr['webpixwidth']:'NULL').','.
					(isset($addArr['tnpixwidth'])&&$addArr['tnpixwidth']?$addArr['tnpixwidth']:'NULL').','.
					(isset($addArr['lgpixwidth'])&&$addArr['lgpixwidth']?$addArr['lgpixwidth']:'NULL').','.
					(isset($addArr['jpgcompression'])&&$addArr['jpgcompression']?$addArr['jpgcompression']:'NULL').','.
					(isset($addArr['createtnimg'])&&$addArr['createtnimg']?$addArr['createtnimg']:'NULL').','.
					(isset($addArr['createlgimg'])&&$addArr['createlgimg']?$addArr['createlgimg']:'NULL').')';
			}
		}
		elseif($addArr['title'] === 'OCR Harvest' && $addArr['newprofile']){
			$sql = 'INSERT INTO specprocessorprojects(collid,title,speckeypattern) '.
				'VALUES('.$this->collid.',"'.$this->cleanInStr($addArr['title']).'","'.
				$this->cleanInStr($addArr['speckeypattern']).'")';
		}
		if($sql && !$this->conn->query($sql)) {
			echo 'ERROR saving project: '.$this->conn->error;
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
		elseif($crit === 'OCR Harvest' && $this->collid){
			$sqlWhere .= 'WHERE (collid = '.$this->collid.') ';
		}
		if($sqlWhere){
			$sql = 'SELECT collid, title, speckeypattern, patternreplace, replacestr,coordx1, coordx2, coordy1, coordy2, sourcepath, targetpath, '.
				'imgurl, webpixwidth, tnpixwidth, lgpixwidth, jpgcompression, createtnimg, createlgimg, source '.
				'FROM specprocessorprojects '.$sqlWhere;
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				if(!$this->collid) {
					$this->setCollId($row->collid);
				}
				$this->title = $row->title;
				$this->specKeyPattern = $row->speckeypattern;
				$this->patternReplace = $row->patternreplace;
				$this->replaceStr = $row->replacestr;
				$this->coordX1 = $row->coordx1;
				$this->coordX2 = $row->coordx2;
				$this->coordY1 = $row->coordy1;
				$this->coordY2 = $row->coordy2;
				$this->sourcePath = $row->sourcepath;
				$this->targetPath = $row->targetpath;
				$this->imgUrlBase = $row->imgurl;
				if($row->webpixwidth) {
					$this->webPixWidth = $row->webpixwidth;
				}
				if($row->tnpixwidth) {
					$this->tnPixWidth = $row->tnpixwidth;
				}
				if($row->lgpixwidth) {
					$this->lgPixWidth = $row->lgpixwidth;
				}
				if($row->jpgcompression) {
					$this->jpgQuality = $row->jpgcompression;
				}
				$this->createTnImg = $row->createtnimg;
				$this->createLgImg = $row->createlgimg;
				$this->lastRunDate = $row->source;
				if($this->title === 'iDigBio CSV upload'){
					$this->projectType = 'idigbio';
				}
				elseif($this->title === 'IPlant Image Processing'){
					$this->projectType = 'iplant';
				}
				elseif($this->title === 'OCR Harvest'){
					break;
				}
				else{
					$this->projectType = 'local';
				}
			}
			$rs->free();

			if($this->sourcePath && substr($this->sourcePath,-1) !== '/' && substr($this->sourcePath,-1) !== '\\') {
				$this->sourcePath .= '/';
			}
			if($this->targetPath && substr($this->targetPath,-1) !== '/' && substr($this->targetPath,-1) !== '\\') {
				$this->targetPath .= '/';
			}
			if($this->imgUrlBase && substr($this->imgUrlBase,-1) !== '/') {
				$this->imgUrlBase .= '/';
			}
		}
	}

	public function getProjects(): array
	{
		$projArr = array();
		if($this->collid){
			$sql = 'SELECT spprid, title '.
				'FROM specprocessorprojects '.
				'WHERE (collid = '.$this->collid.') AND title != "OCR Harvest"';
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$projArr[$row->spprid] = $row->title;
			}
			$rs->free();
		}
		return $projArr;
	}

	public function getSpecWithImage($procStatus = ''): int
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

	public function getSpecNoOcr($procStatus = ''): int
	{
		$cnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt '.
					'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
					'LEFT JOIN specprocessorrawlabels r ON i.imgid = r.imgid '.
					'WHERE o.collid = '.$this->collid.' AND r.imgid IS NULL ';
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
				$psArr[strtolower($r->processingstatus)] = $r->cnt;
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
		$sql = 'SELECT DISTINCT u.uid, CONCAT(CONCAT_WS(", ",u.lastname, u.firstname)," (",l.username,")") AS username '.
			'FROM omoccurrences o INNER JOIN omoccuredits e ON o.occid = e.occid '.
			'INNER JOIN users u ON e.uid = u.uid '.
			'INNER JOIN userlogin l ON u.uid = l.uid '.
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
			'INNER JOIN userlogin u ON e.uid = u.uid '.
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
		global $CHARSET;
		$fileName = 'SymbSpecNoImages_'.time().'.csv';
		header ('Content-Type: text/csv; charset='.$CHARSET);
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
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
		global $LOG_PATH;
		$retArr = array();
		if($this->collid){
			$LOG_PATHFrag = ($this->projectType === 'local'?'imgProccessing':$this->projectType).'/';
			if(file_exists($LOG_PATH . $LOG_PATHFrag) && $fh = opendir($LOG_PATH . $LOG_PATHFrag)) {
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

	public function getPatternReplace(){
		return $this->patternReplace;
	}

	public function getReplaceStr(){
		return $this->replaceStr;
	}

	public function getSourcePath(){
		return $this->sourcePath;
	}

	public function getSourcePathDefault(){
		global $IPLANT_IMAGE_IMPORT_PATH;
		$sourcePath = $this->sourcePath;
		if(!$sourcePath && $this->projectType === 'iplant' && $IPLANT_IMAGE_IMPORT_PATH){
			$sourcePath = $IPLANT_IMAGE_IMPORT_PATH;
			if(strpos($sourcePath, '--INSTITUTION_CODE--')) {
				$sourcePath = str_replace('--INSTITUTION_CODE--', $this->institutionCode, $sourcePath);
			}
			if(strpos($sourcePath, '--COLLECTION_CODE--')) {
				$sourcePath = str_replace('--COLLECTION_CODE--', $this->collectionCode, $sourcePath);
			}
		}
		return $sourcePath;
	}

	public function setTargetPath($p): void
	{
		$this->targetPath = $p;
	}

	public function getTargetPath(){
		return $this->targetPath;
	}

	public function getImgUrlBase(){
		return $this->imgUrlBase;
	}

	public function getWebPixWidth(): string
	{
		return $this->webPixWidth;
	}

	public function getTnPixWidth(): string
	{
		return $this->tnPixWidth;
	}

	public function getLgPixWidth(): string
	{
		return $this->lgPixWidth;
	}

	public function getJpgQuality(): int
	{
		return $this->jpgQuality;
	}

	public function getWebMaxFileSize(): int
	{
		return $this->webMaxFileSize;
	}

	public function getLgMaxFileSize(): int
	{
		return $this->lgMaxFileSize;
	}

	public function getCreateTnImg(): int
	{
		return $this->createTnImg;
	}

	public function getCreateLgImg(): int
	{
		return $this->createLgImg;
	}

	public function getLastRunDate(): string
	{
		return $this->lastRunDate;
	}

	public function getUseImageMagick(): int
	{
 		return $this->processUsingImageMagick;
 	}

 	public function getConn(): mysqli
	{
 		return $this->conn;
 	}

 	protected function cleanInStr($str): string
	{
		$newStr = trim($str);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
