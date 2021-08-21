<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class ReferenceManager{

	private $conn;
	private $refId = 0;
	private $refAuthId = 0;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getRefList($keyword,$author): array
	{
		$retArr = array();
		$sql = 'SELECT r.refid, r.ReferenceTypeId, r.title, r.secondarytitle, r.tertiarytitle, r.number, r.pubdate, r.edition, r.volume, '.
			'GROUP_CONCAT(CONCAT(a.lastname,", ",CONCAT_WS("",LEFT(a.firstname,1),LEFT(a.middlename,1))) SEPARATOR ", ") AS authline '.
			'FROM referenceobject AS r LEFT JOIN referenceauthorlink AS l ON r.refid = l.refid '.
			'LEFT JOIN referenceauthors AS a ON l.refauthid = a.refauthorid ';
		if($keyword || $author){
			if($keyword && !$author){
				$sql .= 'WHERE r.title LIKE "%'.$keyword.'%" ';
			}
			if(!$keyword && $author){
				$sql .= 'WHERE a.lastname LIKE "%'.$author.'%" ';
			}
			if($keyword && $author){
				$sql .= 'WHERE r.title LIKE "%'.$keyword.'%" AND a.lastname LIKE "%'.$author.'%" ';
			}
		}
		$sql .= 'GROUP BY r.refid ';
		$sql .= 'ORDER BY r.title';
		//echo '<div>'.$sql.'</div>';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid]['refid'] = $r->refid;
				$retArr[$r->refid]['ReferenceTypeId'] = $r->ReferenceTypeId;
				$retArr[$r->refid]['title'] = $r->title;
				$retArr[$r->refid]['secondarytitle'] = $r->secondarytitle;
				$retArr[$r->refid]['tertiarytitle'] = $r->tertiarytitle;
				$retArr[$r->refid]['number'] = $r->number;
				$retArr[$r->refid]['pubdate'] = $r->pubdate;
				$retArr[$r->refid]['edition'] = $r->edition;
				$retArr[$r->refid]['volume'] = $r->volume;
				$retArr[$r->refid]['authline'] = $r->authline;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getAuthList(): array
	{
		$retArr = array();
		$sql = 'SELECT a.refauthorid, CONCAT_WS(", ",a.lastname,CONCAT_WS(" ",a.firstname,a.middlename)) AS authorName '.
			'FROM referenceauthors AS a '.
			'ORDER BY authorName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refauthorid]['authorName'] = $r->authorName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getAuthInfo($authId): array
	{
		$retArr = array();
		$sql = 'SELECT a.refauthorid, a.firstname, a.middlename, a.lastname '.
			'FROM referenceauthors AS a '.
			'WHERE a.refauthorid = '.$authId.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['refauthorid'] = $r->refauthorid;
				$retArr['firstname'] = $r->firstname;
				$retArr['middlename'] = $r->middlename;
				$retArr['lastname'] = $r->lastname;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getAuthPubList($authId): array
	{
		$retArr = array();
		$sql = 'SELECT a.refid, a.title, a.secondarytitle, a.shorttitle, a.pubdate '.
			'FROM referenceauthorlink AS l LEFT JOIN referenceobject AS a ON l.refid = a.refid '.
			'WHERE l.refauthid = '.$authId.' '.
			'ORDER BY a.title';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid]['refid'] = $r->refid;
				$retArr[$r->refid]['title'] = $r->title;
				$retArr[$r->refid]['secondarytitle'] = $r->secondarytitle;
				$retArr[$r->refid]['shorttitle'] = $r->shorttitle;
				$retArr[$r->refid]['pubdate'] = $r->pubdate;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getRefTypeArr(): array
	{
		$retArr = array();
		$sql = 'SELECT ReferenceTypeId, ReferenceType '. 
			'FROM referencetype '.
			'ORDER BY ReferenceType';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->ReferenceTypeId] = $r->ReferenceType;
			}
		}
		return $retArr;
	}
	
	public function createReference($pArr): string
	{
		$statusStr = '';
		$sql = 'INSERT INTO referenceobject(title,ReferenceTypeId,ispublished,modifieduid,modifiedtimestamp) '.
			'VALUES("'.Sanitizer::cleanInStr($pArr['newreftitle']).'","'.Sanitizer::cleanInStr($pArr['newreftype']).'","'.Sanitizer::cleanInStr($pArr['ispublished']).'",'.$GLOBALS['SYMB_UID'].',now()) ';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->refId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new reference failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function getRefArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT o.refid, o.parentRefId, o.title, o.secondarytitle, o.shorttitle, o.tertiarytitle, o.alternativetitle, o.typework, o.figures, '. 
			'o.pubdate, o.edition, o.volume, o.numbervolumes, o.number, o.pages, o.section, o.placeofpublication, o.publisher, o.isbn_issn, o.url, '.
			'o.guid, o.ispublished, o.notes, t.ReferenceType, t.ReferenceTypeId '.
			'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
			'WHERE o.refid = '.$refId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['refid'] = $r->refid;
				if($r->ReferenceTypeId === 3 || $r->ReferenceTypeId === 6){
					$retArr['parentRefId'] = '';
					$retArr['parentRefId2'] = $r->parentRefId;
				}
				else{
					$retArr['parentRefId'] = $r->parentRefId;
					$retArr['parentRefId2'] = '';
				}
				$retArr['title'] = $r->title;
				$retArr['secondarytitle'] = $r->secondarytitle;
				$retArr['shorttitle'] = $r->shorttitle;
				$retArr['tertiarytitle'] = $r->tertiarytitle;
				$retArr['alternativetitle'] = $r->alternativetitle;
				$retArr['typework'] = $r->typework;
				$retArr['figures'] = $r->figures;
				$retArr['pubdate'] = $r->pubdate;
				$retArr['edition'] = $r->edition;
				$retArr['volume'] = $r->volume;
				$retArr['numbervolumes'] = $r->numbervolumes;
				$retArr['number'] = $r->number;
				$retArr['pages'] = $r->pages;
				$retArr['section'] = $r->section;
				$retArr['placeofpublication'] = $r->placeofpublication;
				$retArr['publisher'] = $r->publisher;
				$retArr['isbn_issn'] = $r->isbn_issn;
				$retArr['url'] = $r->url;
				$retArr['guid'] = $r->guid;
				$retArr['ispublished'] = $r->ispublished;
				$retArr['notes'] = $r->notes;
				$retArr['ReferenceType'] = $r->ReferenceType;
				$retArr['ReferenceTypeId'] = (int)$r->ReferenceTypeId;
			}
			$rs->close();
		}
		if($retArr['parentRefId']){
			$sql = 'SELECT o.parentRefId, o.title, o.shorttitle, o.alternativetitle, '. 
				'o.pubdate, o.edition, o.volume, o.number, o.placeofpublication, o.publisher, o.isbn_issn '.
				'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
				'WHERE o.refid = '.$retArr['parentRefId'];
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['parentRefId2'] = $r->parentRefId;
					$retArr['secondarytitle'] = $r->title;
					$retArr['alternativetitle'] = $r->alternativetitle;
					$retArr['shorttitle'] = $r->shorttitle;
					$retArr['pubdate'] = $r->pubdate;
					$retArr['edition'] = $r->edition;
					$retArr['volume'] = $r->volume;
					$retArr['number'] = $r->number;
					$retArr['placeofpublication'] = $r->placeofpublication;
					$retArr['publisher'] = $r->publisher;
					$retArr['isbn_issn'] = $r->isbn_issn;
				}
				$rs->close();
			}
		}
		if($retArr['parentRefId2']){
			$sql = 'SELECT o.title, o.edition, o.numbervolumes, o.placeofpublication, o.publisher '.
				'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
				'WHERE o.refid = '.$retArr['parentRefId2'];
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['tertiarytitle'] = $r->title;
					$retArr['numbervolumes'] = $r->numbervolumes;
					$retArr['edition'] = $r->edition;
					$retArr['placeofpublication'] = $r->placeofpublication;
					$retArr['publisher'] = $r->publisher;
				}
				$rs->close();
			}
		}
		return $retArr;
	}
	
	public function getChildArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT o.refid '.
			'FROM referenceobject AS o '.
			'WHERE o.parentRefId = '.$refId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid] = $r->refid;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefAuthArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT a.refauthorid, CONCAT_WS(" ",a.firstname,a.middlename,a.lastname) AS authorName '.
			'FROM referenceauthorlink AS l LEFT JOIN referenceauthors AS a ON l.refauthid = a.refauthorid '.
			'WHERE l.refid = '.$refId.' '.
			'ORDER BY authorName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refauthorid] = $r->authorName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefChecklistArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT l.clid, a.Name '.
			'FROM referencechecklistlink AS l LEFT JOIN fmchecklists AS a ON l.clid = a.CLID '.
			'WHERE l.refid = '.$refId.' '.
			'ORDER BY a.Name';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->clid] = $r->Name;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefCollArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT l.collid, a.CollectionName '.
			'FROM referencecollectionlink AS l LEFT JOIN omcollections AS a ON l.collid = a.CollID '.
			'WHERE l.refid = '.$refId.' '.
			'ORDER BY a.CollectionName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->collid] = $r->CollectionName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefOccArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT l.occid, CONCAT_WS("; ",a.sciname, a.catalognumber, CONCAT_WS(" ",a.recordedby,IFNULL(a.recordnumber,a.eventdate))) AS identifier '.
			'FROM referenceoccurlink AS l LEFT JOIN omoccurrences AS a ON l.occid = a.occid '.
			'WHERE l.refid = '.$refId.' '.
			'ORDER BY a.sciname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->occid] = $r->identifier;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefTaxaArr($refId): array
	{
		$retArr = array();
		$sql = 'SELECT l.tid, a.SciName '.
			'FROM referencetaxalink AS l LEFT JOIN taxa AS a ON l.tid = a.TID '.
			'WHERE l.refid = '.$refId.' '.
			'ORDER BY a.SciName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->tid] = $r->SciName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function addAuthor($refId,$refAuthId): string
	{
		$sql = 'INSERT INTO referenceauthorlink(refid,refauthid) '.
			'VALUES('.$refId.','.$refAuthId.') ';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Success!';
		}
		else{
			$statusStr = 'ERROR: Creation of new reference author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function deleteRefAuthor($refId,$refAuthId): string
	{
		$sql = 'DELETE FROM referenceauthorlink '.
				'WHERE (refid = '.$refId.') AND (refauthid = '.$refAuthId.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Reference author deleted.';
		}
		else{
			$statusStr = 'ERROR: Deletion of reference author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function deleteReference($refId): string
	{
		$statusStr = '';
		$sql = 'DELETE FROM referenceauthorlink '.
				'WHERE (refid = '.$refId.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$sql = 'DELETE FROM referenceobject '.
					'WHERE (refid = '.$refId.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'Reference deleted.';
			}
		}
		else{
			$statusStr = 'ERROR: Deletion of reference failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function deleteAuthor($authId): string
	{
		$sql = 'DELETE FROM referenceauthors '.
				'WHERE (refauthorid = '.$authId.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Author deleted.';
		}
		else{
			$statusStr = 'ERROR: Deletion of author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function deleteRefLink($refId,$table,$field,$id): string
	{
		$sql = 'DELETE FROM '.$table.' '.
				'WHERE (refid = '.$refId.') AND ('.$field.' = '.$id.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Success!';
		}
		else{
			$statusStr = 'ERROR: Deletion of reference link failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function createAuthor($firstName,$middleName,$lastName): string
	{
		$statusStr = '';
		$sql = 'INSERT INTO referenceauthors(firstname,middlename,lastname,modifieduid,modifiedtimestamp) '.
			'VALUES("'.Sanitizer::cleanInStr($firstName).'","'.Sanitizer::cleanInStr($middleName).'","'.Sanitizer::cleanInStr($lastName).'",'.$GLOBALS['SYMB_UID'].',now()) ';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->refAuthId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function getRefTypeFieldArr($refTypeId): array
	{
		$retArr = array();
		$sql = 'SELECT ReferenceTypeId, ReferenceType, Title, SecondaryTitle, PlacePublished, '.
			'Publisher, Volume, NumberVolumes, Number, Pages, Section, TertiaryTitle, Edition, `Date`, TypeWork, ShortTitle, '.
			'AlternativeTitle, ISBN_ISSN '.
			'FROM referencetype '.
			'WHERE ReferenceTypeId = '.$refTypeId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['ReferenceTypeId'] = $r->ReferenceTypeId;
				$retArr['ReferenceType'] = $r->ReferenceType;
				$retArr['Title'] = $r->Title;
				$retArr['SecondaryTitle'] = $r->SecondaryTitle;
				$retArr['PlacePublished'] = $r->PlacePublished;
				$retArr['Publisher'] = $r->Publisher;
				$retArr['Volume'] = $r->Volume;
				$retArr['NumberVolumes'] = $r->NumberVolumes;
				$retArr['Number'] = $r->Number;
				$retArr['Pages'] = $r->Pages;
				$retArr['Section'] = $r->Section;
				$retArr['TertiaryTitle'] = $r->TertiaryTitle;
				$retArr['Edition'] = $r->Edition;
				$retArr['Date'] = $r->Date;
				$retArr['TypeWork'] = $r->TypeWork;
				$retArr['ShortTitle'] = $r->ShortTitle;
				$retArr['AlternativeTitle'] = $r->AlternativeTitle;
				$retArr['ISBN_ISSN'] = $r->ISBN_ISSN;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function editReference($pArr): string
	{
		$statusStr = '';
		$refId = $pArr['refid'];
		unset($pArr['parentRefId2'], $pArr['refGroup']);
		$pArr = $this->formatInArr($pArr);
		if(is_numeric($refId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k !== 'formsubmit' && $k !== 'refid'){
					$sql .= ','.$k.'='.($v?'"'.Sanitizer::cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE referenceobject SET '.substr($sql,1).',modifieduid='.$GLOBALS['SYMB_UID'].',modifiedtimestamp=now() WHERE (refid = '.$refId.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of reference failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}

	public function formatInArr($pArr){
		if(!array_key_exists('secondarytitle',$pArr)){
			$pArr['secondarytitle'] = '';
		}
		if(!array_key_exists('shorttitle',$pArr)){
			$pArr['shorttitle'] = '';
		}
		if(!array_key_exists('tertiarytitle',$pArr)){
			$pArr['tertiarytitle'] = '';
		}
		if(!array_key_exists('alternativetitle',$pArr)){
			$pArr['alternativetitle'] = '';
		}
		if(!array_key_exists('typework',$pArr)){
			$pArr['typework'] = '';
		}
		if(!array_key_exists('pubdate',$pArr)){
			$pArr['pubdate'] = '';
		}
		if(!array_key_exists('figures',$pArr)){
			$pArr['figures'] = '';
		}
		if(!array_key_exists('edition',$pArr)){
			$pArr['edition'] = '';
		}
		if(!array_key_exists('volume',$pArr)){
			$pArr['volume'] = '';
		}
		if(!array_key_exists('numbervolumes',$pArr)){
			$pArr['numbervolumes'] = '';
		}
		if(!array_key_exists('number',$pArr)){
			$pArr['number'] = '';
		}
		if(!array_key_exists('pages',$pArr)){
			$pArr['pages'] = '';
		}
		if(!array_key_exists('section',$pArr)){
			$pArr['section'] = '';
		}
		if(!array_key_exists('placeofpublication',$pArr)){
			$pArr['placeofpublication'] = '';
		}
		if(!array_key_exists('publisher',$pArr)){
			$pArr['publisher'] = '';
		}
		if(!array_key_exists('isbn_issn',$pArr)){
			$pArr['isbn_issn'] = '';
		}
		return $pArr;
	}
	
	public function editAuthor($pArr): string
	{
		$statusStr = '';
		$authId = $pArr['authid'];
		if(is_numeric($authId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k !== 'formsubmit' && $k !== 'authid'){
					$sql .= ','.$k.'='.($v?'"'.Sanitizer::cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE referenceauthors SET '.substr($sql,1).',modifieduid='.$GLOBALS['SYMB_UID'].',modifiedtimestamp=now() WHERE (refauthorid = '.$authId.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of author failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}
	
	public function getRefId(): int
	{
		return $this->refId;
	}
	
	public function getRefAuthId(): int
	{
		return $this->refAuthId;
	}
}
