<?php
include_once('DbConnection.php');

class GlossaryManager{

	private $conn;
	private $glossId = 0;
	private $lang;
	private $glossGroupId = 0;
	private $synonymGroup = array();
	private $translationGroup = array();
	private $tidArr = array();
	private $errArr = array();

	private $sourceGdImg;
	private $sourcePath = '';
	private $targetPath = '';
	private $urlBase = '';
	private $imgName = '';
	private $imgExt = '';
	private $imageRootPath;
	private $imageRootUrl;
	private $sourceWidth = 0;
	private $sourceHeight = 0;
	private $tnPixWidth = 200;
	private $webPixWidth = 1600;
	private $lgPixWidth = 3168;
	private $webFileSizeLimit = 300000;
	private $jpgCompression= 80;
	private $mapLargeImg = false;
	private $fileName;
	
	private $errorStr;
	
 	public function __construct(){
		global $IMAGE_ROOT_URL, $IMAGE_ROOT_PATH, $IMG_WEB_WIDTH, $IMG_TN_WIDTH, $IMG_FILE_SIZE_LIMIT;
 		$connection = new DbConnection();
 		$this->conn = $connection->getConnection();
		$this->imageRootPath = $IMAGE_ROOT_PATH;
		if(substr($this->imageRootPath,-1) !== '/') {
			$this->imageRootPath .= '/';
		}
		$this->imageRootUrl = $IMAGE_ROOT_URL;
		if(substr($this->imageRootUrl,-1) !== '/') {
			$this->imageRootUrl .= '/';
		}
		if($IMG_TN_WIDTH){
			$this->tnPixWidth = $IMG_TN_WIDTH;
		}
		if($IMG_WEB_WIDTH){
			$this->webPixWidth = $IMG_WEB_WIDTH;
		}
		if($IMG_FILE_SIZE_LIMIT){
			$this->webFileSizeLimit = $IMG_FILE_SIZE_LIMIT;
		}
 	}
 	
 	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}
	
	public function getTermSearch($keyword,$language,$tid,$deepSearch = 1): array
	{
		$retArr = array();
		$sqlWhere = '';
		if($keyword){
			$sqlWhere .= 'AND (g.term LIKE "'.$this->cleanInStr($keyword).'%" OR g.term LIKE "% '.$this->cleanInStr($keyword).'%" ';
			if($deepSearch) {
				$sqlWhere .= 'OR g.definition LIKE "%' . $this->cleanInStr($keyword) . '%"';
			}
			$sqlWhere .= ') ';
		}
		if($language){
			$sqlWhere .= 'AND (g.`language` = "'.$this->cleanInStr($language).'") ';
		}
		if(is_numeric($tid)){
			$sqlWhere .= 'AND (t.tid = '.$tid.' OR t2.tid = '.$tid.' OR (t.tid IS NULL AND t2.tid IS NULL)) ';
		}
		$sql = 'SELECT DISTINCT g.glossid, g.term '.
			'FROM glossary g LEFT JOIN glossarytermlink tl ON g.glossid = tl.glossid '.
			'LEFT JOIN glossarytaxalink t ON tl.glossgrpid = t.glossid '.
			'LEFT JOIN glossarytaxalink t2 ON g.glossid = t2.glossid '.
			'WHERE '.substr($sqlWhere, 3).
			'ORDER BY g.term ';
		//echo '<div>'.$sql.'</div>';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->glossid] = $r->term;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getTermArr(): array
	{
		$retArr = array();
		if($this->glossId){
			$sql = 'SELECT glossid, term, definition, `language`, source, notes, resourceurl, author, translator '.
				'FROM glossary '.
				'WHERE glossid = '.$this->glossId;
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					$retArr['term'] = $r->term;
					$retArr['definition'] = $r->definition;
					$retArr['author'] = $r->author;
					$retArr['translator'] = $r->translator;
					$retArr['source'] = $r->source;
					$retArr['notes'] = $r->notes;
					$retArr['resourceurl'] = $r->resourceurl;
					$this->lang = $r->language;
				}
				$rs->free();
			}
			$this->tidArr = $this->getTaxaArr();
		}
		return $retArr;
	}

	public function getTermTaxaArr(): array
	{
		if(!$this->tidArr) {
			$this->tidArr = $this->getTaxaArr();
		}
		return $this->tidArr;
	}

	private function getTaxaArr(): array
	{
		$retArr = array();
		if($this->glossId){
			$sql = 'SELECT t.tid, t.SciName, v.vernacularname '.
				'FROM taxa t INNER JOIN glossarytaxalink gt ON t.tid = gt.tid '.
				'LEFT JOIN taxavernaculars v ON t.tid = v.tid '.
				'WHERE (gt.glossid = '.$this->glossGroupId.')';
			//echo $sql; exit;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$sciname = $r->SciName;
					if($r->vernacularname) {
						$sciname .= ' (' . $r->vernacularname . ')';
					}
					$retArr[$r->tid] = $sciname;
				}
				$rs->free();
			}
			asort($retArr);
		}
		return $retArr;
	}
	
	public function getImgArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT i.glimgid, i.glossid, i.url, i.thumbnailurl, i.structures, i.notes, i.createdBy '.
			'FROM glossaryimages i LEFT JOIN glossarytermlink t ON i.glossid = t.glossid '.
			'LEFT JOIN glossarytermlink t2 ON i.glossid = t2.glossgrpid '.
			'WHERE (i.glossid = '.$this->glossId.') OR (t.relationshiptype IN("translation","synonym") AND t.glossgrpid = '.$this->glossId.') '.
			'OR (t2.relationshiptype IN("translation","synonym") AND t2.glossid = '.$this->glossId.')';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->glimgid]['glimgid'] = $r->glimgid;
				$retArr[$r->glimgid]['glossid'] = $r->glossid;
				$retArr[$r->glimgid]['url'] = $r->url;
				$retArr[$r->glimgid]['thumbnailurl'] = $r->thumbnailurl;
				$retArr[$r->glimgid]['structures'] = $r->structures;
				$retArr[$r->glimgid]['notes'] = $r->notes;
				$retArr[$r->glimgid]['createdBy'] = $r->createdBy;
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function getTranslations(): array
	{
		$retArr = array();
		if($this->glossGroupId){
			$sql = 'SELECT g.glossid, g.term, g.definition, g.`language`, g.source, g.notes, l.gltlinkid '.
				'FROM glossary AS g INNER JOIN glossarytermlink l ON g.glossid = l.glossid '.
				'WHERE (l.glossgrpid IN('.implode(',',$this->translationGroup).')) AND (g.language != "'.$this->lang.'") '.
				'AND (l.relationshiptype = "translation") '.
				'ORDER BY g.`language` ';
			//echo $sql.'<br/>'; exit;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr[$r->glossid]['gltlinkid'] = $r->gltlinkid;
					$retArr[$r->glossid]['term'] = $r->term;
					$retArr[$r->glossid]['definition'] = $r->definition;
					$retArr[$r->glossid]['language'] = $r->language;
					$retArr[$r->glossid]['source'] = $r->source;
					$retArr[$r->glossid]['notes'] = $r->notes;
				}
				$rs->free();
			}
			$sql2 = 'SELECT glossid, term, definition, `language`, source, notes '.
				'FROM glossary '.
				'WHERE (glossid IN('.implode(',',$this->translationGroup).'))';
			//echo $sql2; exit;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$retArr[$r2->glossid]['gltlinkid'] = 0;
				$retArr[$r2->glossid]['term'] = $r2->term;
				$retArr[$r2->glossid]['definition'] = $r2->definition;
				$retArr[$r2->glossid]['language'] = $r2->language;
				$retArr[$r2->glossid]['source'] = $r2->source;
				$retArr[$r2->glossid]['notes'] = $r2->notes;
			}
			$rs2->free();

			unset($retArr[$this->glossId]);
		}
		return $retArr;
	}
	
	public function getSynonyms(): array
	{
		$retArr = array();
		if($this->glossGroupId){
			$sql = 'SELECT g.glossid, g.term, g.definition, g.`language`, g.source, g.notes, l.gltlinkid '.
				'FROM glossary g INNER JOIN glossarytermlink l ON g.glossid = l.glossid '.
				'WHERE (l.glossgrpid IN('.implode(',',$this->synonymGroup).')) AND (g.language = "'.$this->lang.'") '.
				'AND (l.relationshiptype NOT IN("partOf","subClassOf"))';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->glossid]['gltlinkid'] = $r->gltlinkid;
				$retArr[$r->glossid]['term'] = $r->term;
				$retArr[$r->glossid]['definition'] = $r->definition;
				$retArr[$r->glossid]['language'] = $r->language;
				$retArr[$r->glossid]['source'] = $r->source;
				$retArr[$r->glossid]['notes'] = $r->notes;
			}
			$rs->free();
			$sql2 = 'SELECT glossid, term, definition, `language`, source, notes '.
				'FROM glossary '.
				'WHERE (glossid IN('.implode(',',$this->synonymGroup).'))';
			//echo $sql; exit;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$retArr[$r2->glossid]['gltlinkid'] = 0;
				$retArr[$r2->glossid]['term'] = $r2->term;
				$retArr[$r2->glossid]['definition'] = $r2->definition;
				$retArr[$r2->glossid]['language'] = $r2->language;
				$retArr[$r2->glossid]['source'] = $r2->source;
				$retArr[$r2->glossid]['notes'] = $r2->notes;
			}
			$rs2->free();
			unset($retArr[$this->glossId]);
		}
		return $retArr;
	}

	public function getOtherRelatedTerms(): array
	{
		$retArr = array();
		if($this->glossGroupId){
			$sql = 'SELECT g.glossid, g.term, g.definition, g.`language`, g.source, g.notes, l.relationshiptype, l.gltlinkid '.
				'FROM glossary g INNER JOIN glossarytermlink l ON g.glossid = l.glossgrpid '.
				'WHERE (l.glossid = '.$this->glossId.') AND (l.relationshiptype IN("partOf","subClassOf"))';
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->relationshiptype][$r->glossid]['gltlinkid'] = $r->gltlinkid;
				$retArr[$r->relationshiptype][$r->glossid]['term'] = $r->term;
				$retArr[$r->relationshiptype][$r->glossid]['definition'] = $r->definition;
				$retArr[$r->relationshiptype][$r->glossid]['language'] = $r->language;
				$retArr[$r->relationshiptype][$r->glossid]['source'] = $r->source;
				$retArr[$r->relationshiptype][$r->glossid]['notes'] = $r->notes;
			}
			$rs->free();

			$sql2 = 'SELECT g.glossid, g.term, g.definition, g.`language`, g.source, g.notes, l.relationshiptype, l.gltlinkid '.
				'FROM glossary g INNER JOIN glossarytermlink l ON g.glossid = l.glossid '.
				'WHERE (l.glossgrpid = '.$this->glossId.') AND (l.relationshiptype IN("partOf","subClassOf"))';
			//echo $sql; exit;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$relType = $r2->relationshiptype;
				if($relType === 'partOf') {
					$relType = 'hasPart';
				}
				elseif($relType === 'subClassOf') {
					$relType = 'superClassOf';
				}
				$retArr[$relType][$r2->glossid]['gltlinkid'] = $r2->gltlinkid;
				$retArr[$relType][$r2->glossid]['term'] = $r2->term;
				$retArr[$relType][$r2->glossid]['definition'] = $r2->definition;
				$retArr[$relType][$r2->glossid]['language'] = $r2->language;
				$retArr[$relType][$r2->glossid]['source'] = $r2->source;
				$retArr[$relType][$r2->glossid]['notes'] = $r2->notes;
			}
			$rs2->free();
			unset($retArr[$this->glossId]);
		}
		return $retArr;
	}

	public function createTerm($pArr): bool
	{
		global $SYMB_UID;
 		$status = true;
		$term = $this->cleanInStr($pArr['term']);
		$def = $this->cleanInStr($pArr['definition']);
		$lang = $this->cleanInStr($pArr['language']);
		$source = $this->cleanInStr($pArr['source']);
		$author = $this->cleanInStr($pArr['author']);
		$translator = $this->cleanInStr($pArr['translator']);
		$notes = $this->cleanInStr($pArr['notes']);
		$resourceUrl = $this->cleanInStr($pArr['resourceurl']);
		$sql = 'INSERT INTO glossary(term,definition,`language`,source,author,translator,notes,resourceurl,uid) '.
			'VALUES("'.$term.'",'.($def?'"'.$def.'"':'NULL').','.($lang?'"'.$lang.'"':'NULL').','.
			($source?'"'.$source.'"':'NULL').','.($author?'"'.$author.'"':'NULL').','.($translator?'"'.$translator.'"':'NULL').','.
			($notes?'"'.$notes.'"':'NULL').','.($resourceUrl?'"'.$resourceUrl.'"':'NULL').','.$SYMB_UID.') ';
		//echo $sql; exit;
		if($this->conn->query($sql)){
			$this->glossId = $this->conn->insert_id;
			$glossGrpId = $this->glossId;
			if(isset($pArr['relglossid']) && $pArr['relglossid'] && is_numeric($pArr['relglossid'])){
				if($pArr['relation'] === 'synonym'){
					$glossGrpId = min($this->getSynonymGroup($pArr['relglossid']));
				}
				elseif($pArr['relation'] === 'translation'){
					$glossGrpId = min($this->getTranslationGroup($pArr['relglossid']));
				}
				if($pArr['relation']){
					$sql1 = 'INSERT INTO glossarytermlink(glossgrpid,glossid,relationshiptype) '.
						'VALUES('.$glossGrpId.','.$this->glossId.',"'.$pArr['relation'].'") ';
					//echo $sql1; exit;
					if(!$this->conn->query($sql1)){
						$this->errorStr = 'ERROR creating new term group link: '.$this->conn->error;
					}
				}
			}
			if((isset($pArr['tid']) && $pArr['tid']) || (isset($pArr['taxagroup']) && $pArr['taxagroup'])){
				$tid = $pArr['tid'];
				if($tid){
					$taxon = $this->cleanInStr($pArr['taxagroup']);
					if(preg_match('/^(\D+)\s\(/', $taxon, $m)){
						$taxon = $m[1];
					}
					$sql = 'SELECT tid FROM taxa WHERE sciname = "'.$taxon.'"';
					$rs = $this->conn->query($sql);
					if($r = $rs->fetch_object()){
						$tid = $r->tid;
					}
					$rs->free();
				}
				if($tid){
					$sql2 = 'INSERT INTO glossarytaxalink(glossid,tid) '.
						'VALUES('.$glossGrpId.','.$tid.') ';
					if(!$this->conn->query($sql2)){
						$this->errorStr = 'ERROR creating new term taxa link: '.$this->conn->error;
					}
				}
			}
		}
		else{
			$this->errorStr = 'ERROR creating new term: '.$this->conn->error;
			$status = false;
		}
		return $status;
	}

	public function editTerm($pArr): bool
	{
		$status = true;
		if(!is_numeric($pArr['glossid'])) {
			return false;
		}
		$term = $this->cleanInStr($pArr['term']);
		$lang = $this->cleanInStr($pArr['language']);
		$def = $this->cleanInStr($pArr['definition']);
		$source = $this->cleanInStr($pArr['source']);
		$translator = $this->cleanInStr($pArr['translator']);
		$author = $this->cleanInStr($pArr['author']);
		$notes = $this->cleanInStr($pArr['notes']);
		$resourceUrl = $this->cleanInStr($pArr['resourceurl']);
		$sql = 'UPDATE glossary SET term = "'.$term.'",language = "'.$lang.'",definition = '.($def?'"'.$def.'"':'NULL').
			',source = '.($source?'"'.$source.'"':'NULL').
			',translator = '.($translator?'"'.$translator.'"':'NULL').
			',author = '.($author?'"'.$author.'"':'NULL').
			',notes = '.($notes?'"'.$notes.'"':'NULL').
			',resourceurl = '.($resourceUrl?'"'.$resourceUrl.'"':'NULL').
			' WHERE (glossid = '.$pArr['glossid'].')';
		//echo $sql; exit;
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR editing term: '.$this->conn->error;
			$status = false;
		}
		return $status;
	}

	public function addGroupTaxaLink($tid): bool
	{
		if(is_numeric($tid)){
			$sql = 'INSERT INTO glossarytaxalink(glossid,tid) '.
				'VALUES('.$this->glossGroupId.','.$tid.') ';
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR inserting glossaryTaxaLink: '.$this->conn->error;
				return false;
			}
			return true;
		}
		return false;
	}

	public function deleteGroupTaxaLink($tidStr): bool
	{
		$sql = 'DELETE FROM glossarytaxalink WHERE glossid IN('.$this->glossId.','.$this->glossGroupId.') AND tid IN('.$tidStr.') ';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR deleting glossarytaxalink record: '.$this->conn->error;
			return false;
		}
		return true;
	}
	
	public function linkTranslation($relGlossId): bool
	{
		$status = true;
		$rootTerm = min($this->translationGroup);
		$sql = 'INSERT IGNORE INTO glossarytermlink(glossid,glossgrpid,relationshipType) '.
			'VALUES('.$relGlossId.','.$rootTerm.',"translation") ';
		if($relGlossId < $rootTerm){
			$sql = 'INSERT IGNORE INTO glossarytermlink(glossid,glossgrpid,relationshipType) '.
				'VALUES('.$rootTerm.','.$relGlossId.',"translation") ';
		}
		//echo $sql; exit;
		$this->conn->query($sql);

		return $status;
	}

	public function linkRelation($relGlossId,$relationship): bool
	{
		$status = true;
		if($relationship === 'synonym'){
			$sql2 = 'REPLACE INTO glossarytermlink(glossid,glossgrpid,relationshipType) '.
				'VALUES('.$relGlossId.','.($relationship==='synonym'?min($this->synonymGroup):$this->glossGroupId).',"'.$relationship.'") ';
		}
		else{
			$targetId = $this->glossId;
			$targetGroupId = $relGlossId;
			if($relationship === 'superClassOf'){
				$relationship = 'subClassOf';
				$targetId = $relGlossId;
				$targetGroupId = $this->glossId;
			}
			if($relationship === 'hasPart'){
				$relationship = 'partOf';
				$targetId = $relGlossId;
				$targetGroupId = $this->glossId;
			}
			$sql2 = 'INSERT INTO glossarytermlink(glossid,glossgrpid,relationshipType) '.
				'VALUES('.$targetId.','.$targetGroupId.',"'.$relationship.'") ';
		}
		$this->conn->query($sql2);
		return $status;
	}

	public function removeRelation($gltLinkId, $relGlossId = ''): bool
	{
		if(is_numeric($gltLinkId)){
			$status = true;
			$sql1 = 'DELETE FROM glossarytermlink WHERE gltlinkid = '.$gltLinkId;
			//echo $sql1.'<br/>';
			if(!$this->conn->query($sql1)){
				$this->errorStr = 'ERROR removing term relationship: '.$this->conn->error;
				$status = false;
			}
			if($status && $relGlossId && is_numeric($relGlossId)){
				$tidArr = $this->getTaxaArr();
				foreach($tidArr as $taxId => $sciname){
					$sql3 = 'INSERT INTO glossarytaxalink(glossid,tid) VALUES('.$relGlossId.','.$taxId.') ';
					//echo $sql3.'<br/>';
					$this->conn->query($sql3);
				}
			}
			return $status;
		}
		return false;
	}

	public function editImageData($pArr): string
	{
		$statusStr = '';
		$glimgId = $pArr['glimgid'];
		unset($pArr['oldurl']);
		if(is_numeric($glimgId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k !== 'formsubmit' && $k !== 'glossid' && $k !== 'glimgid' && $k !== 'glossgrpid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE glossaryimages SET '.substr($sql,1).' WHERE (glimgid = '.$glimgId.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of image data failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}

	public function deleteTerm(): bool
	{
		$status = true;
		$sql = 'DELETE FROM glossary WHERE (glossid = '.$this->glossId.')';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR deleting term: '.$this->conn->error;
			$status = false;
		}
		return $status;
	}

	public function getTaxonSources($tidStr = ''): array
	{
		$retArr = array();
		if($tidStr && preg_match('/[^,\d]+/', $tidStr)) {
			return $retArr;
		}
		$sql = 'SELECT t.tid, t.sciname, v.vernacularname, g.contributorTerm, g.contributorImage, g.translator, g.additionalSources '.
			'FROM taxa t INNER JOIN glossarysources g ON t.tid = g.tid '.
			'LEFT JOIN taxavernaculars AS v ON t.tid = v.tid ';
		if($tidStr){
			$sql .= 'WHERE t.tid IN('.$tidStr.') ';
		}
		elseif($this->tidArr){
			$sql .= 'WHERE t.tid IN('.implode(',',array_keys($this->tidArr)).') ';
		}
		$sql .= 'ORDER BY t.SciName';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$taxonName = $r->sciname;
			if($r->vernacularname) {
				$taxonName .= ' (' . $r->vernacularname . ')';
			}
			$retArr[$r->tid]['sciname'] = $taxonName;
			$retArr[$r->tid]['contributorTerm'] = $r->contributorTerm;
			$retArr[$r->tid]['contributorImage'] = $r->contributorImage;
			$retArr[$r->tid]['translator'] = $r->translator;
			$retArr[$r->tid]['additionalSources'] = $r->additionalSources;
		}
		$rs->free();
		return $retArr;
	}

	public function addSource($pArr): bool
	{
		$status = true;
		if(is_numeric($pArr['tid'])){
			$terms = $this->cleanInStr($_REQUEST['contributorTerm']);
			$images = $this->cleanInStr($_REQUEST['contributorImage']);
			$translator = $this->cleanInStr($_REQUEST['translator']);
			$sources = $this->cleanInStr($_REQUEST['additionalSources']);
			$sql = 'INSERT INTO glossarysources(tid,contributorTerm,contributorImage,translator,additionalSources) '.
				'VALUES('.$pArr['tid'].','.($terms?'"'.$terms.'"':'NULL').','.($images?'"'.$images.'"':'NULL').','.
				($translator?'"'.$translator.'"':'NULL').','.($sources?'"'.$sources.'"':'NULL').')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR adding source: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}

	public function editSource($pArr): bool
	{
		$status = true;
		if(is_numeric($pArr['tid'])){
			$terms = $this->cleanInStr($_REQUEST['contributorTerm']);
			$images = $this->cleanInStr($_REQUEST['contributorImage']);
			$translator = $this->cleanInStr($_REQUEST['translator']);
			$sources = $this->cleanInStr($_REQUEST['additionalSources']);
			$sql = 'UPDATE glossarysources '.
				'SET contributorTerm = '.($terms?'"'.$terms.'"':'NULL').', contributorImage = '.($images?'"'.$images.'"':'NULL').', '.
				'translator = '.($translator?'"'.$translator.'"':'NULL').', additionalSources = '.($sources?'"'.$sources.'"':'NULL').' '.
				'WHERE (tid = '.$pArr['tid'].')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR editing source: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}

	public function deleteSource($tid): bool
	{
		$status = true;
		if($tid){
			$sql = 'DELETE FROM glossarysources WHERE (tid = '.$tid.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR deleting source: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}

	public function deleteImage($imgIdDel): string
	{
		$imgUrl = '';
		$imgTnUrl = '';
		$status = 'Image deleted successfully';
		$sqlQuery = 'SELECT url, thumbnailurl FROM glossaryimages WHERE (glimgid = '.$imgIdDel.')';
		$result = $this->conn->query($sqlQuery);
		if($row = $result->fetch_object()){
			$imgUrl = $row->url;
			$imgTnUrl = $row->thumbnailurl;
		}
		$result->close();
				
		$sql = 'DELETE FROM glossaryimages WHERE (glimgid = '.$imgIdDel.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$imgUrl2 = '';
			$domain = $this->getServerDomain();
			if(stripos($imgUrl,$domain) == 0){
				$imgUrl2 = $imgUrl;
				$imgUrl = substr($imgUrl,strlen($domain));
			}
			elseif(stripos($imgUrl,$this->imageRootUrl) == 0){
				$imgUrl2 = $domain.$imgUrl;
			}
			
			$sql = "SELECT glimgid FROM glossaryimages WHERE (url = '".$imgUrl."') ";
			if($imgUrl2) {
				$sql .= 'OR (url = "' . $imgUrl2 . '")';
			}
			$rs = $this->conn->query($sql);
			if(!$rs->num_rows){
				$imgDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$imgUrl);
				if((strpos($imgDelPath, 'http') !== 0) && !unlink($imgDelPath)) {
					$this->errArr[] = 'WARNING: Deleted records from database successfully but FAILED to delete image from server (path: '.$imgDelPath.')';
				}
				
				if($imgTnUrl){
					if(stripos($imgTnUrl,$domain) == 0){
						$imgTnUrl = substr($imgTnUrl,strlen($domain));
					}				
					$imgTnDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$imgTnUrl);
					if(file_exists($imgTnDelPath) && strpos($imgTnDelPath, 'http') !== 0) {
						unlink($imgTnDelPath);
					}
				}
			}
		}
		else{
			$status = 'deleteImage: ' .$this->conn->error."\nSQL: ".$sql;
		}
		return $status;
	}

	public function addImage(){
		set_time_limit(120);
		ini_set('max_input_time',120);
		$this->setTargetPath();
		
		if($_REQUEST['imgurl']){
			if(!$this->copyImageFromUrl($_REQUEST['imgurl'])) {
				return false;
			}
		}
		else if(!$this->loadImage()) {
			return false;
		}

		return $this->processImage();
	}
	
	public function processImage(){
		if(!$this->imgName){
			return false;
		}

		$imgTnUrl = '';
		if($this->createNewImage('_tn',$this->tnPixWidth,70)){
			$imgTnUrl = $this->imgName.'_tn.jpg';
		}

		if(!$this->sourceWidth || !$this->sourceHeight){
			[$this->sourceWidth, $this->sourceHeight] = getimagesize(str_replace(' ', '%20', $this->sourcePath));
		}
		$fileSize = $this->getSourceFileSize();

		$imgLgUrl = '';
		if($this->mapLargeImg){
			if($this->sourceWidth > ($this->webPixWidth*1.2) || $fileSize > $this->webFileSizeLimit){
				if(strpos($this->sourcePath, 'http://') == 0 || strpos($this->sourcePath, 'https://') == 0) {
					$imgLgUrl = $this->sourcePath;
				}
				else if($this->sourceWidth < ($this->lgPixWidth*1.2)){
					if(copy($this->sourcePath,$this->targetPath.$this->imgName.'_lg'.$this->imgExt)){
						$imgLgUrl = $this->imgName.'_lg'.$this->imgExt;
					}
				}
				else if($this->createNewImage('_lg',$this->lgPixWidth)){
					$imgLgUrl = $this->imgName.'_lg.jpg';
				}
			}
		}

		$imgWebUrl = '';
		if($this->sourceWidth < ($this->webPixWidth*1.2) && $fileSize < $this->webFileSizeLimit){
			if(stripos($this->sourcePath, 'http://') == 0 || stripos($this->sourcePath, 'https://') == 0){
				if(copy($this->sourcePath, $this->targetPath.$this->imgName.$this->imgExt)){
					$imgWebUrl = $this->imgName.$this->imgExt;
				}
			}
			else{
				$imgWebUrl = $this->imgName.$this->imgExt;
			}
		}
		else{
			$this->createNewImage('',$this->sourceWidth);
			$imgWebUrl = $this->imgName.'.jpg';
		}

		$status = true;
		if($imgWebUrl){
			$status = $this->databaseImage($imgWebUrl,$imgTnUrl);
		}
		return $status;
	}
	
	public function copyImageFromUrl($sourceUri): bool
	{
		if(!$sourceUri){
			$this->errArr[] = 'ERROR: Image source uri NULL in copyImageFromUrl method';
			return false;
		}
		if(!$this->uriExists($sourceUri)){
			$this->errArr[] = 'ERROR: Image source file ('.$sourceUri.') does not exist in copyImageFromUrl method';
			return false;
		}
		if(!$this->targetPath){
			$this->errArr[] = 'ERROR: Image target url NULL in copyImageFromUrl method';
			return false;
		}
		if(!file_exists($this->targetPath)){
			$this->errArr[] = 'ERROR: Image target file ('.$this->targetPath.') does not exist in copyImageFromUrl method';
			return false;
		}
		$fileName = $this->cleanFileName($sourceUri);
		if(copy($sourceUri, $this->targetPath.$fileName.$this->imgExt)){
			$this->sourcePath = $this->targetPath.$fileName.$this->imgExt;
			$this->imgName = $fileName;
			return true;
		}
		$this->errArr[] = 'ERROR: Unable to copy image to target ('.$this->targetPath.$fileName.$this->imgExt.')';
		return false;
	}
	
	public function cleanFileName($fPath){
		$fName = $fPath;
		$imgInfo = null;
		if(stripos($fPath, 'http://') == 0 || stripos($fPath, 'https://') == 0){
			$imgInfo = getimagesize(str_replace(' ', '%20', $fPath));
			[$this->sourceWidth, $this->sourceHeight] = $imgInfo;
		
			if($pos = strrpos($fName,'/')){
				$fName = substr($fName,$pos+1);
			}
		}
		if($p = strrpos($fName, '.')){
			$this->imgExt = strtolower(substr($fName,$p));
			$fName = substr($fName,0,$p);
		}
		
		if(!$this->imgExt && $imgInfo){
			if($imgInfo[2] == IMAGETYPE_GIF){
				$this->imgExt = 'gif';
			}
			elseif($imgInfo[2] == IMAGETYPE_PNG){
				$this->imgExt = 'png';
			}
			elseif($imgInfo[2] == IMAGETYPE_JPEG){
				$this->imgExt = 'jpg';
			}
		}

		$fName = str_replace(array('%20', '%23', ' ', '__', chr(231), chr(232), chr(233), chr(234), chr(260), chr(230), chr(236), chr(237), chr(238), chr(239), chr(240), chr(241), chr(261), chr(247), chr(248), chr(249), chr(262), chr(250), chr(251), chr(263), chr(264), chr(265)), array('_', '_', '_', '_', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'n', 'n'), $fName);
		$fName = preg_replace("/[^a-zA-Z0-9\-_]/", '', $fName);
		$fName = trim($fName,' _-');
		
		if(strlen($fName) > 30) {
			$fName = substr($fName,0,30);
		}
		if($this->targetPath){
			$tempFileName = $fName;
			$cnt = 0;
			while(file_exists($this->targetPath.$tempFileName)){
				$tempFileName = $fName.'_'.$cnt;
				$cnt++;
			}
			if($cnt) {
				$fName = $tempFileName;
			}
		}
		
		return $fName;
 	}
	
	private function loadImage(): bool
	{
		$imgFile = basename($_FILES['imgfile']['name']);
		$fileName = $this->cleanFileName($imgFile);
		if(is_writable($this->targetPath) && move_uploaded_file($_FILES['imgfile']['tmp_name'], $this->targetPath . $fileName . $this->imgExt)) {
            $this->sourcePath = $this->targetPath.$fileName.$this->imgExt;
            $this->imgName = $fileName;
            return true;
        }
		return false;
	}

	private function databaseImage($imgWebUrl,$imgTnUrl): string
	{
		global $SYMB_UID;
		if(!$imgWebUrl) {
			return 'ERROR: web url is null ';
		}
		$urlBase = $this->getUrlBase();
		if(stripos($imgWebUrl, 'http://') !== 0 && stripos($imgWebUrl, 'https://') !== 0){
			$imgWebUrl = $urlBase.$imgWebUrl;
		}
		if($imgTnUrl && stripos($imgTnUrl, 'http://') !== 0 && stripos($imgTnUrl, 'https://') !== 0){
			$imgTnUrl = $urlBase.$imgTnUrl;
		}
		$glossId = $_REQUEST['glossid'];
		$status = 'File added successfully!';
		$sql = 'INSERT INTO glossaryimages(glossid,url,thumbnailurl,structures,notes,createdBy,uid) '.
			'VALUES('.$glossId.',"'.$imgWebUrl.'","'.$imgTnUrl.'","'.$this->cleanInStr($_REQUEST['structures']).'","'.$this->cleanInStr($_REQUEST['notes']).'","'.$this->cleanInStr($_REQUEST['createdBy']).'",'.$SYMB_UID.') ';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$status = 'ERROR Loading Data: ' .$this->conn->error. '<br/>SQL: ' .$sql;
		}
		return $status;
	}
	
	public function uriExists($url){
		global $IMAGE_DOMAIN, $IMAGE_ROOT_URL, $IMAGE_ROOT_PATH;
 		$exists = false;
		$localUrl = '';
		if(strpos($url, '/') == 0){
			if($IMAGE_DOMAIN){
				$url = $IMAGE_DOMAIN.$url;
			}
			elseif($IMAGE_ROOT_URL && strpos($url,$IMAGE_ROOT_URL) == 0){
				$localUrl = str_replace($IMAGE_ROOT_URL,$IMAGE_ROOT_PATH,$url);
			}
			else{
				$url = $this->getServerDomain().$url;
			}
		}
		
		if(file_exists($url) || ($localUrl && file_exists($localUrl))){
			return true;
	    }

	    if(!$exists){
		    $handle   = curl_init($url);
		    curl_setopt($handle, CURLOPT_HEADER, false);
		    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
		    curl_setopt($handle, CURLOPT_HTTPHEADER, Array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15') );
		    curl_setopt($handle, CURLOPT_NOBODY, true);
		    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		    $exists = curl_exec($handle);
		    curl_close($handle);
	    }
	     
	    if(!$exists){
	    	$exists = (@fclose(@fopen($url, 'rb')));
	    }
	    
	    if(function_exists('exif_imagetype') && !exif_imagetype($url)) {
			$exists = false;
		}
		
	    return $exists;
	}
	
	public function createNewImage($subExt, $targetWidth, $qualityRating = 0): bool
	{
		global $USE_IMAGE_MAGICK;
		$status = false;
		if($this->sourcePath && $this->uriExists($this->sourcePath)){
			if(!$qualityRating) {
				$qualityRating = $this->jpgCompression;
			}
			
	        if($USE_IMAGE_MAGICK) {
				$status = $this->createNewImageImagick($subExt,$targetWidth,$qualityRating);
			} 
			elseif(function_exists('gd_info') && extension_loaded('gd')) {
				$status = $this->createNewImageGD($subExt,$targetWidth,$qualityRating);
			}
			else{
				$this->errArr[] = 'ERROR: No appropriate image handler for image conversions';
			}
		}
		return $status;
	}
	
	private function createNewImageImagick($subExt,$newWidth,$qualityRating = 0): bool
	{
		$targetPath = $this->targetPath.$this->imgName.$subExt.$this->imgExt;
		if($newWidth < 300){
			system('convert '.$this->sourcePath.' -thumbnail '.$newWidth.'x'.($newWidth*1.5).' '.$targetPath, $retval);
		}
		else{
			system('convert '.$this->sourcePath.' -resize '.$newWidth.'x'.($newWidth*1.5).($qualityRating?' -quality '.$qualityRating:'').' '.$targetPath, $retval);
		}
		if(file_exists($targetPath)){
			return true;
		}
		return false;
	}

	private function createNewImageGD($subExt, $newWidth, $qualityRating = 0): bool
	{
		$status = false;
		ini_set('memory_limit','512M');

		if(!$this->sourceWidth || !$this->sourceHeight){
			[$this->sourceWidth, $this->sourceHeight] = getimagesize(str_replace(' ', '%20', $this->sourcePath));
		}
		if($this->sourceWidth){
			$newHeight = round($this->sourceHeight*($newWidth/$this->sourceWidth));
			if($newWidth > $this->sourceWidth){
				$newWidth = $this->sourceWidth;
				$newHeight = $this->sourceHeight;
			}
			if(!$this->sourceGdImg){
				if($this->imgExt === '.gif'){
			   		$this->sourceGdImg = imagecreatefromgif($this->sourcePath);
				}
				elseif($this->imgExt === '.png'){
			   		$this->sourceGdImg = imagecreatefrompng($this->sourcePath);
				}
				else{
					$this->sourceGdImg = imagecreatefromjpeg($this->sourcePath);
				}
			}
			
			$tmpImg = imagecreatetruecolor($newWidth,$newHeight);
			imagecopyresized($tmpImg,$this->sourceGdImg,0,0,0,0,$newWidth,$newHeight,$this->sourceWidth,$this->sourceHeight);
	
			$targetPath = $this->targetPath.$this->imgName.$subExt.'.jpg';
			if($qualityRating){
				$status = imagejpeg($tmpImg, $targetPath, $qualityRating);
			}
			else{
				$status = imagejpeg($tmpImg, $targetPath);
			}
				
			if(!$status){
				$this->errArr[] = 'ERROR: failed to create images in target path ('.$targetPath.')';
			}
	
			imagedestroy($tmpImg);
		}
		else{
			$this->errArr[] = 'ERROR: unable to get source image width ('.$this->sourcePath.')';
		}
		return $status;
	}
	
	public function getUrlBase(): string
	{
		global $IMAGE_DOMAIN;
 		$urlBase = $this->urlBase;
		if($IMAGE_DOMAIN){
			$urlBase = $this->getServerDomain().$urlBase;
    	}
		return $urlBase;
	}
	
	public function getSourceFileSize(){
		$fileSize = 0;
		if($this->sourcePath){
			if(stripos($this->sourcePath, 'http://') == 0 || stripos($this->sourcePath, 'https://') == 0){
				$x = array_change_key_case(get_headers($this->sourcePath, 1),CASE_LOWER); 
				if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') !== 0 ) {
					$fileSize = $x['content-length'][1]; 
				}
	 			else { 
	 				$fileSize = $x['content-length']; 
	 			}
	 		}
			else{
				$fileSize = filesize($this->sourcePath);
			}
		}
		return $fileSize;
	}

	private function setTargetPath(): void
	{
 		$folderName = date('Y-m');
		if(!file_exists($this->imageRootPath . 'glossimg') && !mkdir($concurrentDirectory = $this->imageRootPath . 'glossimg', 0775) && !is_dir($concurrentDirectory)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
		}
		if(!file_exists($this->imageRootPath . 'glossimg/' . $folderName) && !mkdir($concurrentDirectory = $this->imageRootPath . 'glossimg/' . $folderName, 0775) && !is_dir($concurrentDirectory)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
		}
		$path = $this->imageRootPath. 'glossimg/' .$folderName. '/';
		$url = $this->imageRootUrl. 'glossimg/' .$folderName. '/';
		
		$this->targetPath = $path;
		$this->urlBase = $url;
	}

	public function getExportArr($language,$taxon,$images,$translations='',$definitions=''): array
	{
		$retArr = array();
		$referencesArr = array();
		$contributorsArr = array();
		$groupMap = array();
		$sql = 'SELECT DISTINCT g.glossid, g.term, g.definition, g.language, g.source, g.translator, g.author, gt.glossgrpid '.
			'FROM glossary g LEFT JOIN glossarytermlink gt ON gt.glossid = g.glossid '. 
			'LEFT JOIN glossarytaxalink gx ON gt.glossgrpid = gx.glossid '.
			'LEFT JOIN glossarytaxalink gx2 ON g.glossid = gx2.glossid '.
			'WHERE ((gx.tid = '.$taxon.') OR (gx2.tid = '.$taxon.')) AND (g.`language` = "'.$language.'") '.
			'ORDER BY g.term ';
		//echo $sql.'<br/>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->source && !in_array($r->source, $referencesArr, true)) {
				$referencesArr[] = $r->source;
			}
			if($r->translator && !in_array($r->translator, $contributorsArr, true)) {
				$contributorsArr[] = $r->translator;
			}
			if($r->author && !in_array($r->author, $contributorsArr, true)) {
				$contributorsArr[] = $r->author;
			}
			$retArr[$r->glossid]['term'] = $r->term;
			if(!$definitions || $definitions !== 'nodef') {
				$retArr[$r->glossid]['definition'] = $r->definition;
			}
			if($r->glossgrpid && $r->glossgrpid !== $r->glossid) {
				$groupMap[$r->glossgrpid][] = $r->glossid;
			}
		}
		$rs->free();

		$glossIdArr = array();
		if($translations){
			$glossIdArr = array_keys($retArr);
			if($groupMap) {
				$glossIdArr = array_unique(array_merge($glossIdArr, array_keys($groupMap)));
			}
			$sql = 'SELECT DISTINCT g.glossid, g.term, g.definition, g.language, g.source, g.translator, g.author, gt.glossgrpid '.
				'FROM glossary g LEFT JOIN glossarytermlink gt ON gt.glossid = g.glossid '.
				'WHERE (g.`language` IN("'.implode((array)'","',$translations).'")) AND (g.`language` != "'.$language.'") '.
				'AND (g.glossid IN('.implode(',',$glossIdArr).') OR gt.glossgrpid IN('.implode(',',$glossIdArr).'))';
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->source && !in_array($r->source, $referencesArr, true)) {
					$referencesArr[] = $r->source;
				}
				if($r->translator && !in_array($r->translator, $contributorsArr, true)) {
					$contributorsArr[] = $r->translator;
				}
				if($r->author && !in_array($r->author, $contributorsArr, true)) {
					$contributorsArr[] = $r->author;
				}
				$targetArr = array();
				if(isset($retArr[$r->glossid])) {
					$targetArr[] = $r->glossid;
				}
				if(isset($groupMap[$r->glossid])){
					$grpArr = $groupMap[$r->glossid];
					foreach($grpArr as $altId){
						if(isset($retArr[$altId])) {
							$targetArr[] = $altId;
						}
					}
				}
				if($r->glossgrpid && $r->glossgrpid !== $r->glossid){
					if(isset($retArr[$r->glossgrpid])) {
						$targetArr[] = $r->glossgrpid;
					}
					if(isset($groupMap[$r->glossgrpid])){
						$grpArr = $groupMap[$r->glossgrpid];
						foreach($grpArr as $altId){
							if(isset($retArr[$altId])) {
								$targetArr[] = $altId;
							}
						}
					}
				}
				$targetArr = array_unique($targetArr);
				
				foreach($targetArr as $targetId){
					$targetTerm = $r->term;
					if(isset($retArr[$targetId]['trans'][$r->language]['term'])){
						$targetTerm .= '; '.$retArr[$targetId]['trans'][$r->language]['term'];
					}
					$retArr[$targetId]['trans'][$r->language]['term'] = $targetTerm;
					if($definitions === 'alldef'){
						$targetDef =  $r->definition;
						$retArr[$targetId]['trans'][$r->language]['definition'] = $targetDef;
					}
				}
			}
			$rs->free();
		}
		
		if($images && $retArr){
			if(!$glossIdArr){
				$glossIdArr = array_keys($retArr);
				if($groupMap) {
					$glossIdArr = array_unique(array_merge($glossIdArr, array_keys($groupMap)));
				}
			}
			$sql2 = 'SELECT glossid, glimgid, url, createdBy, structures, notes '.
				'FROM glossaryimages '.
				'WHERE glossid IN('.implode(',', $glossIdArr).') ';
			//echo $sql2.'<br/>'; exit; 
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$targetId = $r2->glossid;
				if(!isset($retArr[$targetId]) && isset($groupMap[$targetId])){
					$grpArr = $groupMap[$r2->glossid];
					foreach($grpArr as $altId){
						if(isset($retArr[$altId])) {
							$targetId = $altId;
						}
					}
				}
				if(isset($retArr[$targetId]) && $r2->url && !isset($retArr[$targetId]['images'])) {
					$retArr[$targetId]['images'][$r2->glimgid]['url'] = $r2->url;
					$retArr[$targetId]['images'][$r2->glimgid]['createdBy'] = $r2->createdBy;
					$retArr[$targetId]['images'][$r2->glimgid]['structures'] = $r2->structures;
					$retArr[$targetId]['images'][$r2->glimgid]['notes'] = $r2->notes;
				}
			}
			$rs2->free();
		}
		$retArr['meta'] = $this->getExportMetadata($taxon, $referencesArr, $contributorsArr);
		return $retArr;
	}
	
	private function getExportMetadata($taxon,$referencesArr,$contributorsArr): array
	{
		$retArr = array();
		$sql = 'SELECT t.SciName, v.VernacularName '.
			'FROM taxa t LEFT JOIN taxavernaculars v ON t.tid = v.tid '.
			'WHERE (t.tid = '.$taxon.') ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$sciName = $r->SciName;
			if($r->VernacularName) {
				$sciName .= ' (' . $r->VernacularName . ')';
			}
			$retArr['sciname'] = $sciName;
		}
		$rs->free();
		$sourceArr = $this->getGlossarySources($taxon);
		if(isset($sourceArr['ref'])) {
			$referencesArr = array_unique(array_merge($sourceArr['ref'], $referencesArr));
		}
		if(isset($sourceArr['con'])) {
			$contributorsArr = array_unique(array_merge($sourceArr['con'], $contributorsArr));
		}
		
		$retArr['references'] = $referencesArr;
		$retArr['contributors'] = $contributorsArr;
		return $retArr;
	}
	
	private function getGlossarySources($tid): array
	{
		$retArr = array();
		$sql = 'SELECT contributorTerm, contributorImage, translator, additionalSources '.
			'FROM glossarysources '.
			'WHERE tid = '.$tid;
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->additionalSources) {
				$retArr['ref'][] = $r->additionalSources;
			}
			if($r->translator) {
				$retArr['con'][] = $r->translator;
			}
			if($r->contributorTerm) {
				$retArr['con'][] = $r->contributorTerm;
			}
			if($r->contributorImage) {
				$retArr['img'][] = $r->contributorImage;
			}
		}
		$rs->free();
		return $retArr;
	}

	public function getLanguageArr($returnTag = ''){
		$allArr = array();
		$byTid = array();
		$sql = 'SELECT DISTINCT g.`language`, IFNULL(t.tid, t2.tid) as tid, l.iso639_1 as code '.
			'FROM glossary g LEFT JOIN glossarytermlink p ON g.glossid = p.glossid '.
			'LEFT JOIN glossarytaxalink t ON g.glossid = t.glossid '.
			'LEFT JOIN glossarytaxalink t2 ON p.glossgrpid = t2.glossid '.
			'LEFT JOIN adminlanguages l ON g.language = l.`langname`';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$code = $r->code;
				if(!$code) {
					$code = $r->language;
				}
				$allArr[$code] = $r->language;
				if($r->tid) {
					$byTid[$r->tid][] = str_replace(array(',', '"'), '', $r->language);
				}
			}
		}
		asort($allArr);
		$retArr = array();
		$retArr['all'] = $allArr;
		foreach($byTid as $tid => $tArr){
			sort($tArr);
			$retArr[$tid] = '"'.implode('","', $tArr).'"';
		}
		$retArr[0] = '"'.implode('","', $allArr).'"';
		if($returnTag && isset($retArr[$returnTag])) {
			return $retArr[$returnTag];
		}
		return $retArr;
	}
	
	public function getTaxaGroupArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT t.tid, t.sciname, v.vernacularname '.
			'FROM glossarytaxalink g INNER JOIN taxa t ON g.tid = t.TID '.
			'LEFT JOIN taxavernaculars v ON t.TID = v.TID '.
			'ORDER BY t.SciName, v.VernacularName ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$sciname = $r->sciname;
				if($r->vernacularname) {
					$sciname .= ' (' . $r->vernacularname . ')';
				}
				$retArr[$r->tid] = $sciname;
			}
		}
		return $retArr;
	}

	public function getTermList($type,$lang): array
	{
		$retArr = array();
		if(is_numeric($type)){
			$sql = 'SELECT g.glossid, g.term FROM glossary g WHERE glossid = '.$type;
		}
		elseif($lang){
			if($type === 'translation'){
				if($this->tidArr){
					$sql = 'SELECT DISTINCT g.glossid, CONCAT(g.term," (",g.language,")") as term '.
						'FROM glossary g LEFT JOIN glossarytaxalink t ON g.glossid = t.glossid '.
						'LEFT JOIN glossarytermlink l ON g.glossid = l.glossid '.
						'LEFT JOIN glossarytaxalink t2 ON l.glossgrpid = t2.glossid '.
						'WHERE (g.language != "'.$lang.'") '.
						'AND ((t.tid IN('.implode(',',array_keys($this->tidArr)).')) OR (t2.tid IN('.implode(',',array_keys($this->tidArr)).')) OR (t.glossid IS NULL AND t2.glossid IS NULL)) '.
						'ORDER BY g.language, g.term';
				}
				else{
					$sql = 'SELECT DISTINCT g.glossid, CONCAT(g.term," (",g.language,")") as term '.
						'FROM glossary g '.
						'WHERE (g.language != "'.$lang.'") '.
						'ORDER BY g.language, g.term';
				}
			}
			else if($this->tidArr){
				$sql = 'SELECT DISTINCT g.glossid, g.term '.
					'FROM glossary g LEFT JOIN glossarytaxalink t ON g.glossid = t.glossid '.
					'LEFT JOIN glossarytermlink l ON g.glossid = l.glossid '.
					'LEFT JOIN glossarytaxalink t2 ON g.glossid = t.glossid '.
					'WHERE (g.language = "'.$lang.'") '.
					'AND ((t.tid IN('.implode(',',array_keys($this->tidArr)).')) OR (t2.tid IN('.implode(',',array_keys($this->tidArr)).')) OR (t.glossid IS NULL AND t2.glossid IS NULL)) '.
					'ORDER BY g.term';
			}
			else{
				$sql = 'SELECT DISTINCT g.glossid, g.term '.
					'FROM glossary g '.
					'WHERE (g.language = "'.$lang.'") '.
					'ORDER BY g.term';
			}
		}
		else{
			$sql = 'SELECT DISTINCT g.glossid, g.term '.
				'FROM glossary g INNER JOIN glossarytaxalink t ON g.glossid = t.glossid '.
				($this->tidArr?'WHERE (t.tid IN('.implode(',',array_keys($this->tidArr)).')) ':'').
				'ORDER BY g.term';
		}
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->glossid] = $r->term;
		}
		$rs->free();
		return $retArr;
	}

	public function setGlossId($id): void
	{
		if(is_numeric($id) && $id){
			$this->glossId = $id;

			$this->translationGroup = $this->getTranslationGroup($this->glossId);
			$this->synonymGroup = $this->getSynonymGroup($this->glossId);

			$this->glossGroupId = min(array_merge($this->synonymGroup, $this->translationGroup));
				
			$sql3 = 'SELECT glossgrpid FROM glossarytermlink WHERE (glossid = '.($this->glossGroupId?:$this->glossId).') ';
			$rs3 = $this->conn->query($sql3);
			if($r3 = $rs3->fetch_object()){
				$this->glossGroupId = $r3->glossgrpid;
			}
			$rs3->free();

			if(!$this->glossGroupId){
				$this->glossGroupId = $this->glossId;
			}
			if(!$this->translationGroup) {
				$this->translationGroup[] = $this->glossId;
			}
			if(!$this->synonymGroup) {
				$this->synonymGroup[] = $this->glossId;
			}
		}
	}
	
	private function getTranslationGroup($id): array
	{
		$retArr = array($id);
		if($id){
			$sql = 'SELECT glossgrpid '.
				'FROM glossarytermlink l INNER JOIN glossary g1 ON l.glossid = g1.glossid '.
				'INNER JOIN glossary g2 ON l.glossgrpid = g2.glossid '.
				'WHERE (l.glossid = '.$id.') AND (l.glossid != l.glossgrpid) AND (g1.language != g2.language) ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->glossgrpid;
			}
			$rs->free();
		}
		return $retArr;
	}

	private function getSynonymGroup($id): array
	{
		$retArr = array($id);
		if($id){
			$sql = 'SELECT glossgrpid '.
				'FROM glossarytermlink l INNER JOIN glossary g1 ON l.glossid = g1.glossid '.
				'INNER JOIN glossary g2 ON l.glossgrpid = g2.glossid '.
				'WHERE (l.glossid = '.$id.') AND (g1.language = g2.language) AND (l.relationshiptype NOT IN("partOf","subClassOf"))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr[] = $r->glossgrpid;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getGlossId(): int
	{
		return $this->glossId;
	}

	public function getTermLanguage(){
		return $this->lang;
	}

	public function getGlossGroupId(): int
	{
		return $this->glossGroupId;
	}
	
	public function getErrorStr(){
		return $this->errorStr;
	}
	
	private function getServerDomain(): string
	{
		$domain = 'http://';
		if(!(empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
			$domain = 'https://';
		}
		$domain .= $_SERVER['HTTP_HOST'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
			$domain .= ':' . $_SERVER['SERVER_PORT'];
		}
		return $domain;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
