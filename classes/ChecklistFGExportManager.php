<?php
include_once(__DIR__ . '/DbConnection.php');

class ChecklistFGExportManager {

	private $conn;
	private $clid;
	private $dynClid;
	private $childClidArr = array();
	private $pid = '';
    private $linkTable = '';
    private $sqlWhereVar = '';
    private $sqlTaxaStr = '';
	private $dataArr = array();
	private $index = 0;
    private $recLimit = 0;
	private $thesFilter = 1;
	private $imageLimit = 100;
	private $taxaLimit = 500;
    private $photogNameArr = array();
    private $photogIdArr = array();
    private $maxPhoto = 0;

	public function __construct() {
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
            $this->conn->close();
        }
	}

	public function setClValue($clValue): string
    {
		$retStr = '';
		$clValue = $this->conn->real_escape_string($clValue);
		if(is_numeric($clValue)){
			$this->clid = $clValue;
		}
		else{
			$sql = 'SELECT c.clid FROM fmchecklists AS c WHERE (c.Name = "'.$clValue.'")';
			$rs = $this->conn->query($sql);
			if($rs){
				if($row = $rs->fetch_object()){
					$this->clid = $row->clid;
				}
				else{
					$retStr = '<h1>ERROR: invalid checklist identifier supplied ('.$clValue.')</h1>';
				}
				$rs->free();
			}
		}
		$sqlChildBase = 'SELECT clidchild FROM fmchklstchildren WHERE clid IN(';
		$sqlChild = $sqlChildBase.$this->clid.')';
		do{
			$childStr = '';
			$rsChild = $this->conn->query($sqlChild);
			while($rChild = $rsChild->fetch_object()){
				$this->childClidArr[] = $rChild->clidchild;
				$childStr .= ','.$rChild->clidchild;
			}
			$sqlChild = $sqlChildBase.substr($childStr,1).')';
		}while($childStr);
		return $retStr;
	}

	public function setDynClid($did): void
    {
		if(is_numeric($did)){
			$this->dynClid = $did;
		}
	}

    public function setSqlVars(): void
    {
        if($this->clid){
            $clidStr = $this->clid;
            if($this->childClidArr){
                $clidStr .= ','.implode(',',$this->childClidArr);
            }
            $this->linkTable = 'fmchklsttaxalink';
            $this->sqlWhereVar = '(ctl.clid IN('.$clidStr.'))';
        }
        else{
            $this->linkTable = 'fmdyncltaxalink';
            $this->sqlWhereVar = '(ctl.dynclid = '.$this->dynClid.')';
        }
    }

    public function primeDataArr(): void
    {
        $taxaArr = array();
        $sql = 'SELECT DISTINCT t.tid, ts.family, t.sciname, t.author '.
            'FROM '.$this->linkTable.' AS ctl LEFT JOIN taxstatus AS ts ON ctl.tid = ts.tid '.
            'LEFT JOIN taxa AS t ON ts.tidaccepted = t.TID '.
            'WHERE (ts.taxauthid = '.$this->thesFilter.') AND '.$this->sqlWhereVar.' '.
            'ORDER BY ts.family, t.sciname ';
        if($this->index || $this->recLimit) {
            $sql .= 'LIMIT ' . $this->index . ',' . $this->recLimit;
        }
        //echo $sql; exit;
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $this->dataArr[$row->tid]['sciname'] = $row->sciname;
            $this->dataArr[$row->tid]['family'] = $row->family;
            $this->dataArr[$row->tid]['author'] = $row->author;
            if($row->tid) {
                $taxaArr[] = $row->tid;
            }
        }
        $rs->free();
        $this->sqlTaxaStr = implode(',',$taxaArr);
    }

    public function primeOrderData(): void
    {
        if($this->sqlTaxaStr){
            $sql = 'SELECT te.tid, t.SciName AS taxonOrder '.
                'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
                'WHERE te.taxauthid = '.$this->thesFilter.' AND t.RankId = 100 AND te.tid IN('.$this->sqlTaxaStr.') ';
            //echo $sql; exit;
            $rs = $this->conn->query($sql);
            while($row = $rs->fetch_object()){
                $this->dataArr[$row->tid]['order'] = $row->taxonOrder;
            }
            $rs->free();
        }
    }

    public function primeDescData(): void
    {
        if($this->sqlTaxaStr){
            $sql = 'SELECT tdb.tid, tdb.caption, tdb.source, tds.tdsid, tds.heading, tds.statement, tds.displayheader '.
                'FROM taxadescrblock AS tdb LEFT JOIN taxadescrstmts AS tds ON tdb.tdbid = tds.tdbid '.
                'WHERE tdb.tid IN('.$this->sqlTaxaStr.') '.
                'ORDER BY tdb.tid,tdb.displaylevel,tds.sortsequence ';
            //echo $sql; exit;
            $rs = $this->conn->query($sql);
            while($row = $rs->fetch_object()){
                $heading = ($row->displayheader?strip_tags($row->heading):'');
                $statement = strip_tags($row->statement);
                $source = strip_tags($row->source);
                $this->dataArr[$row->tid]['desc'][$row->caption]['source'] = $this->cleanOutStr(htmlspecialchars_decode($source));
                $this->dataArr[$row->tid]['desc'][$row->caption][$row->tdsid]['heading'] = $this->cleanOutStr(htmlspecialchars_decode($heading));
                $this->dataArr[$row->tid]['desc'][$row->caption][$row->tdsid]['statement'] = $this->cleanOutStr(htmlspecialchars_decode($statement));
            }
            $rs->free();
        }
    }

    public function primeVernaculars(): void
    {
        if($this->sqlTaxaStr){
            $sql = 'SELECT v.tid, v.VernacularName '.
                'FROM taxavernaculars AS v '.
                'WHERE v.tid IN('.$this->sqlTaxaStr.') AND (v.SortSequence < 90) AND v.`language` = "en" '.
                'ORDER BY v.tid,v.SortSequence';
            //echo $sql; exit;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $this->dataArr[$row->tid]['vern'][] = strtoupper($row->VernacularName);
            }
            $result->free();
        }
    }

    public function primeImages(): void
    {
        if($this->sqlTaxaStr && ($this->maxPhoto > 0)){
            $photogNameStr = '';
            $photogIdStr = '';
            if($this->photogNameArr){
                $photogNameStr .= '"';
                $photogNameStr .= implode('","',$this->photogIdArr);
                $photogNameStr .= '"';
            }
            if($this->photogIdArr){
                $photogIdStr .= implode(',',$this->photogIdArr);
            }
            $sql = 'SELECT ti.tid, ti.imgid, ti.thumbnailurl, ti.url, ti.`owner`, '.
                'IFNULL(ti.photographer,IFNULL(CONCAT_WS(" ",u.firstname,u.lastname),CONCAT_WS(" ",u2.firstname,u2.lastname))) AS photographer '.
                'FROM images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid '.
                'LEFT JOIN users AS u2 ON ti.username = u2.username '.
                'LEFT JOIN taxstatus AS ts ON ti.tid = ts.tid '.
                'WHERE ts.taxauthid = '.$this->thesFilter.' AND ti.tid IN('.$this->sqlTaxaStr.') AND ti.SortSequence < 500 ';
            if($photogNameStr || $photogIdStr){
                $tempSql = 'AND (';
                if($photogNameStr) {
                    $tempSql .= '(ti.photographer IN(' . $photogNameStr . '))';
                }
                if($photogNameStr && $photogIdStr) {
                    $tempSql .= ' OR ';
                }
                if($photogIdStr) {
                    $tempSql .= '(ti.photographeruid IN(' . $photogIdStr . '))';
                }
                $tempSql .= ') ';
                $sql .= $tempSql;
            }
            $sql .= 'ORDER BY ti.tid, ti.sortsequence ';
            //echo $sql; exit;
            $i = 0;
            $currTid = 0;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                if($currTid !== $row->tid){
                    $currTid = $row->tid;
                    $i = 0;
                }
                if($i < $this->maxPhoto){
                    $imgUrl = $row->thumbnailurl;
                    if((!$imgUrl || $imgUrl === 'empty') && $row->url) {
                        $imgUrl = $row->url;
                    }
                    $this->dataArr[$row->tid]['img'][$row->imgid]['id'] = $row->imgid;
                    $this->dataArr[$row->tid]['img'][$row->imgid]['url'] = $imgUrl;
                    $this->dataArr[$row->tid]['img'][$row->imgid]['owner'] = $row->owner;
                    $this->dataArr[$row->tid]['img'][$row->imgid]['photographer'] = $this->cleanOutStr(htmlspecialchars_decode($row->photographer));
                }
                $i++;
            }
            $result->free();
        }
    }

    public function getImageUrl($imgID){
        $imgUrl = '';
	    $sql = 'SELECT thumbnailurl, url FROM images WHERE imgid = '.$imgID.' ';
        //echo $sql; exit;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $imgUrl = $row->thumbnailurl;
            if((!$imgUrl || $imgUrl === 'empty') && $row->url) {
                $imgUrl = $row->url;
            }
        }
        $result->free();
        return $imgUrl;
    }

    public function getImageDataUrl($url): string
    {
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $dataType = '';
        $base64 = '';
        if(in_array(strtolower($type), array('jpg', 'jpeg'))) {
            $dataType = 'jpg';
        }
        if(strtolower($type) === 'png') {
            $dataType = 'png';
        }
        if($dataType){
            @$data = file_get_contents($url);
            if($data){
                $base64 = 'data:image/'.$dataType.';base64,'.base64_encode($data);
            }
        }

        return $base64;
    }

    public function getDescSourceList(): array
    {
        $descSourceList = array();
        $sql = 'SELECT DISTINCT tdb.caption '.
            'FROM taxadescrblock AS tdb '.
            'WHERE tdb.tid IN('.$this->sqlTaxaStr.') '.
            'ORDER BY tdb.caption ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while ($row = $rs->fetch_object()){
            $descSourceList[] = $row->caption;
        }
        $rs->free();
        return $descSourceList;
    }

    public function getPhotogList(): array
    {
        $photogList = array();
        $sql = 'SELECT DISTINCT ti.photographeruid, ti.photographer, u.firstname, u.lastname '.
            'FROM images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid '.
            'LEFT JOIN taxstatus AS ts ON ti.tid = ts.tid '.
            'WHERE ts.taxauthid = '.$this->thesFilter.' AND ti.tid IN('.$this->sqlTaxaStr.') AND ti.SortSequence < 500 ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while ($row = $rs->fetch_object()){
            $uId = $row->photographeruid;
            $givenName = $row->photographer;
            $lastName = $row->lastname;
            $firstName = $row->firstname;
            if($uId){
                $nameStr = $lastName.', '.$firstName;
                $photogList[$nameStr] = $uId;
            }
            else{
                $photogList[$givenName] = 0;
            }
        }
        $rs->free();
        return $photogList;
    }

    public function setThesFilter($filt): void
    {
		$this->thesFilter = $filt;
	}

	public function getClid(){
		return $this->clid;
	}

    public function setPhotogJson($json): void
    {
        $photogArr = json_decode($json, true);
        if(is_array($photogArr)){
            foreach($photogArr as $str){
                $parts = explode('---',$str);
                if($parts){
                    $id = $parts[0];
                    $name = $parts[1];
                    if($id) {
                        $this->photogIdArr[] = $id;
                    }
                    elseif($name) {
                        $this->photogNameArr[] = $name;
                    }
                }
            }
        }
        elseif($photogArr !== 'all'){
            $this->maxPhoto = 0;
        }
    }

    public function setMaxPhoto($cnt): void
    {
        $this->maxPhoto = $cnt;
    }

    public function setRecIndex($val): void
    {
        $this->index = $val;
    }

    public function setRecLimit($val): void
    {
        $this->recLimit = $val;
    }

    public function getDataArr(): array
    {
        return $this->dataArr;
    }

	private function cleanOutStr($str){
        $str = str_replace(array('&nbsp;', '&ndash;'), array(' ', '-'), $str);
		return $str;
	}

}
