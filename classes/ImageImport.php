<?php
include_once('DbConnection.php');
include_once('ImageShared.php');

class ImageImport{
	
	private $conn;
	private $uploadTargetPath;
	private $uploadFileName;
	private $targetArr;
	private $translationMap = array('imageurl'=>'url','accessuri'=>'url','sciname'=>'scientificname');
	
	public function __construct() {
		set_time_limit(2000);
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
		
		$this->setUploadTargetPath();
		
		$this->targetArr = array('url','originalUrl','scientificName','tid','photographer','photographerUid','caption',
			'locality','sourceUrl','anatomy','notes','owner','copyright','sortSequence',
			'institutionCode','collectionCode','catalogNumber','occid');
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function setUploadFile($ulFileName): void
	{
		if($ulFileName){
			$this->uploadFileName = $ulFileName;
		}
		elseif(array_key_exists('uploadfile',$_FILES)){
			$this->uploadFileName = time().'_'.$_FILES['uploadfile']['name'];
			move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->uploadFileName);
		}
        if(file_exists($this->uploadTargetPath.$this->uploadFileName) && substr($this->uploadFileName,-4) === '.zip'){
			$zip = new ZipArchive;
			$zip->open($this->uploadTargetPath.$this->uploadFileName);
			$zipFile = $this->uploadTargetPath.$this->uploadFileName;
			$fileName = $zip->getNameIndex(0);
			$zip->extractTo($this->uploadTargetPath);
			$zip->close();
			unlink($zipFile);
			$this->uploadFileName = time().'_'.$fileName;
			rename($this->uploadTargetPath.$fileName,$this->uploadTargetPath.$this->uploadFileName);
        }
	}

	public function getUploadFileName(){
		return $this->uploadFileName;
	}
	
	public function getSourceArr(): array
	{
		$sourceArr = array();
		$fh = fopen($this->uploadTargetPath.$this->uploadFileName,'rb') or die("Can't open file");
		$headerArr = fgetcsv($fh);
		foreach($headerArr as $k => $field){
			$fieldStr = strtolower(trim($field));
			if($fieldStr){
				$sourceArr[$k] = $fieldStr;
			}
		}
		return $sourceArr;
	}

	public function getTargetArr(): array
	{
		return $this->targetArr;
	}

	public function getTranslation($inStr){
		$retStr = '';
		$inStr = strtolower($inStr);
		if(array_key_exists($inStr,$this->translationMap)) {
			$retStr = $this->translationMap[$inStr];
		}
		return $retStr;
	}

	private function setUploadTargetPath(): void
	{
		global $SERVER_ROOT, $TEMP_DIR_ROOT;
		$tPath = $TEMP_DIR_ROOT;
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $SERVER_ROOT. '/temp/downloads';
		}
		if(substr($tPath,-1) !== '/') {
			$tPath .= '/';
		}
		$this->uploadTargetPath = $tPath; 
    }
}
