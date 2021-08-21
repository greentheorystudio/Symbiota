<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/ImageShared.php');
include_once(__DIR__ . '/Sanitizer.php');

class ImageDetailManager {
	
	private $conn;
	private $imgId;

	public function __construct($id){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
 		if(is_numeric($id)){
	 		$this->imgId = $id;
 		}
	}

 	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}
 	
	public function getImageMetadata(): array
	{
		$retArr = array();
		if($this->imgId){
			$sql = 'SELECT i.imgid, i.tid, i.url, i.thumbnailurl, i.originalurl, i.photographeruid, i.photographer, ' .
				"IFNULL(i.photographer,CONCAT_WS(' ',u.firstname,u.lastname)) AS photographerdisplay, ".
				'i.caption, i.owner, i.sourceurl, i.copyright, i.rights, i.locality, i.notes, i.occid, i.sortsequence, i.username, ' .
				't.sciname, t.author, t.rankid ' .
				'FROM images i LEFT JOIN taxa t ON i.tid = t.tid ' .
				'LEFT JOIN users u ON i.photographeruid = u.uid ' .
				'WHERE (i.imgid = '.$this->imgId.')';
			//echo "<div>$sql</div>";
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$retArr['tid'] = $row->tid;
				$retArr['sciname'] = $row->sciname;
				$retArr['author'] = Sanitizer::cleanOutStr($row->author);
				$retArr['rankid'] = $row->rankid;
				$retArr['url'] = $row->url;
				$retArr['thumbnailurl'] = $row->thumbnailurl;
				$retArr['originalurl'] = $row->originalurl;
				$retArr['photographer'] = Sanitizer::cleanOutStr($row->photographer);
				$retArr['photographerdisplay'] = $row->photographerdisplay;
				$retArr['photographeruid'] = $row->photographeruid;
				$retArr['caption'] = Sanitizer::cleanOutStr($row->caption);
				$retArr['owner'] = Sanitizer::cleanOutStr($row->owner);
				$retArr['sourceurl'] = Sanitizer::cleanOutStr($row->sourceurl);
				$retArr['copyright'] = Sanitizer::cleanOutStr($row->copyright);
				$retArr['rights'] = Sanitizer::cleanOutStr($row->rights);
				$retArr['locality'] = Sanitizer::cleanOutStr($row->locality);
				$retArr['notes'] = Sanitizer::cleanOutStr($row->notes);
				$retArr['sortsequence'] = $row->sortsequence;
				$retArr['occid'] = $row->occid;
				$retArr['username'] = $row->username;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function editImage($postArr): string
	{
		$status = '';
		$searchStr = $GLOBALS['IMAGE_ROOT_URL'];
		if(substr($searchStr,-1) !== '/') {
			$searchStr .= '/';
		}
		$replaceStr = $GLOBALS['IMAGE_ROOT_PATH'];
		if(substr($replaceStr,-1) !== '/') {
			$replaceStr .= '/';
		}
	 	$url = $postArr['url'];
	 	$tnUrl = $postArr['thumbnailurl'];
	 	$origUrl = $postArr['originalurl'];
	 	if(array_key_exists('renameweburl',$postArr)){
	 		$oldUrl = $postArr['oldurl'];
	 		$oldName = str_replace($searchStr,$replaceStr,$oldUrl);
 			$newWebName = str_replace($searchStr,$replaceStr,$url);
	 		if($url !== $oldUrl){
	 			if(file_exists($newWebName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name';
		 			$url = $oldUrl;
	 			}
	 			else if(copy($oldName,$newWebName)){
					 unlink($oldName);
				 }
				 else{
					 $url = $oldUrl;
					 $status = 'Web URL rename FAILED; url address unchanged';
				 }
	 		}
		}
		if(array_key_exists('renametnurl',$postArr)){
	 		$oldTnUrl = $postArr['oldthumbnailurl'];
	 		$oldName = str_replace($searchStr,$replaceStr,$oldTnUrl);
	 		$newName = str_replace($searchStr,$replaceStr,$tnUrl);
	 		if($tnUrl !== $oldTnUrl){
	 			if(file_exists($newName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name';
		 			$tnUrl = $oldTnUrl;
	 			}
	 			else if(copy($oldName,$newName)){
					 unlink($oldName);
				 }
				 else{
					 $tnUrl = $oldTnUrl;
					 $status = 'Thumbnail URL rename FAILED; url address unchanged';
				 }
	 		}
		}
		if(array_key_exists('renameorigurl',$postArr)){
	 		$oldOrigUrl = $postArr['oldoriginalurl'];
	 		$oldName = str_replace($searchStr,$replaceStr,$oldOrigUrl);
	 		$newName = str_replace($searchStr,$replaceStr,$origUrl);
	 		if($origUrl !== $oldOrigUrl){
	 			if(file_exists($newName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name';
	 				$origUrl = $oldOrigUrl;
 	 			}
	 			else if(copy($oldName,$newName)){
					 unlink($oldName);
				 }
				 else{
					 $origUrl = $oldOrigUrl;
					 $status = 'Large image URL rename FAILED; url address unchanged';
				 }
	 		}
		}
	 	$caption = Sanitizer::cleanInStr($postArr['caption']);
		$photographer = Sanitizer::cleanInStr($postArr['photographer']);
		$photographerUid = $postArr['photographeruid'];
		$owner = Sanitizer::cleanInStr($postArr['owner']);
		$locality = Sanitizer::cleanInStr($postArr['locality']);
		$occId = $postArr['occid'];
		$notes = Sanitizer::cleanInStr($postArr['notes']);
		$sourceUrl = Sanitizer::cleanInStr($postArr['sourceurl']);
		$copyRight = Sanitizer::cleanInStr($postArr['copyright']);
		$rights = Sanitizer::cleanInStr($postArr['rights']);
		$sortSequence = (array_key_exists('sortsequence',$postArr)?$postArr['sortsequence']:0);
		
		$sql = 'UPDATE images '.
			'SET caption = '.($caption?'"'.$caption.'"':'NULL').', url = "'.$url.'", thumbnailurl = '.($tnUrl?'"'.$tnUrl.'"':'NULL').','.
			'originalurl = '.($origUrl?'"'.$origUrl.'"':'NULL').', photographer = '.($photographer?'"'.$photographer.'"': 'NULL').','.
			'photographeruid = '.($photographerUid?:'NULL').', owner = '.($owner?'"'.$owner.'"':'NULL').
			', sourceurl = '.($sourceUrl?'"'.$sourceUrl.'"':'NULL').',copyright = '.($copyRight?'"'.$copyRight.'"':'NULL').
			',rights = '.($rights?'"'.$rights.'"':'NULL').', locality = '.($locality?'"'.$locality.'"':'NULL').', occid = '.($occId?:'NULL').', '.
			'notes = '.($notes?'"'.$notes.'"':'NULL').($sortSequence?', sortsequence = '.$sortSequence:'').
			' WHERE (imgid = '.$this->imgId.')';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$status = 'Error:editImage: ' .$this->conn->error."\nSQL: ".$sql;
		}
		return $status;
	}
	
	public function changeTaxon($targetTid,$sourceTid): string
	{
		$status = '';
		$sql = 'UPDATE images SET tid = '.$targetTid.', sortsequence = 50 WHERE imgid = '.$this->imgId.' AND tid = '.$sourceTid;
		if(!$this->conn->query($sql)){
			$sql = 'SELECT i.imgid '.
				'FROM images i INNER JOIN images i2 ON i.url = i2.url '.
				'WHERE (i.tid = '.$targetTid.') AND (i2.imgid = '.$this->imgId.')';
			$rs = $this->conn->query($sql);  
			if($rs->num_rows){
				$sql2 = 'DELETE FROM images WHERE (imgid = '.$this->imgId.') AND (tid = '.$sourceTid.')';
				$this->conn->query($sql2);
			}
			$rs->close();
		}
		return $status;
	}

	public function deleteImage($imgIdDel, $removeImg): string
	{
		$retStr = '';
		$imgManager = new ImageShared();
		if($imgManager->deleteImage($imgIdDel, $removeImg)){
			$retStr = $imgManager->getTid();
		}
		$errArr = $imgManager->getErrArr();
		if($errArr){
			$retStr .= 'ERROR: ('.implode('; ',$errArr).')';
		}
		return $retStr;
	}

	public function echoPhotographerSelect($userId = null): void
	{
		$sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
			'FROM users u ORDER BY u.lastname, u.firstname ';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			echo "<option value='".$row->uid."' ".($row->uid === $userId? 'SELECTED' : ''). '>' .$row->fullname."</option>\n";
		}
		$result->close();
	}
}
