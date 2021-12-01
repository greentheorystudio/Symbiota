<?php
include_once(__DIR__ . '/DwcArchiverCore.php');

class DwcArchiverPublisher extends DwcArchiverCore{

    private function resetCollArr($collTarget): void
	{
		unset($this->collArr);
		$this->collArr = array();
		$this->setCollArr($collTarget);
	}
	
	public function verifyCollRecords($collId): array
	{
		$recArr = array();

		$sql = 'SELECT COUNT(*) as cnt FROM omoccurrences WHERE basisofrecord IS NULL AND collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$recArr['nullBasisRec'] = $r->cnt;
		}
		$rs->free();

		$guidTarget = $this->collArr[$collId]['guidtarget'];
		if($guidTarget){
			$sql = 'SELECT COUNT(o.occid) AS cnt FROM omoccurrences o ';
			if($guidTarget === 'symbiotaUUID'){
				$sql .= 'LEFT JOIN guidoccurrences g ON o.occid = g.occid WHERE g.occid IS NULL ';
			}
			else{
				$sql .= 'WHERE o.'.$guidTarget.' IS NULL ';
			}
			$sql .= 'AND o.collid = '.$collId;
			//echo 'SQL: '.$sql.'<br/>';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$recArr['nullGUIDs'] = $r->cnt;
			}
			$rs->free();
		}
		
		return $recArr;
	}

	public function batchCreateDwca($collIdArr): bool
	{
		$status = false;
		$this->logOrEcho('Starting batch process (' .date('Y-m-d h:i:s A').")\n");
		$this->logOrEcho("\n-----------------------------------------------------\n\n");

		$successArr = array();
		foreach($collIdArr as $id){
			$this->resetCollArr($id);
			if($this->createDwcArchive()){
				$successArr[] = $id;
				$status = true;
			}
		}
		$this->resetCollArr(implode(',',$successArr));
		$this->writeRssFile();
		$this->logOrEcho('Batch process finished! (' .date('Y-m-d h:i:s A').") \n");
		return $status;
	}
	
	public function writeRssFile(): void
	{
        $this->logOrEcho("Mapping data to RSS feed... \n");
		
		$newDoc = new DOMDocument('1.0',$this->charSetOut);

		$rootElem = $newDoc->createElement('rss');
		$rootAttr = $newDoc->createAttribute('version');
		$rootAttr->value = '2.0';
		$rootElem->appendChild($rootAttr);
		$newDoc->appendChild($rootElem);

		$channelElem = $newDoc->createElement('channel');
		$rootElem->appendChild($channelElem);
		
		$titleElem = $newDoc->createElement('title');
		$titleElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' Darwin Core Archive rss feed'));
		$channelElem->appendChild($titleElem);

		$this->setServerDomain();
		$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/');

		$localDomain = $this->serverDomain;
		
		$linkElem = $newDoc->createElement('link');
		$linkElem->appendChild($newDoc->createTextNode($urlPathPrefix));
		$channelElem->appendChild($linkElem);
		$descriptionElem = $newDoc->createElement('description');
		$descriptionElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' Darwin Core Archive rss feed'));
		$channelElem->appendChild($descriptionElem);
		$languageElem = $newDoc->createElement('language','en-us');
		$channelElem->appendChild($languageElem);

		$itemArr = array();
		foreach($this->collArr as $collID => $cArr){
			$cArr = $this->utf8EncodeArr($cArr);
			$itemElem = $newDoc->createElement('item');
			$itemAttr = $newDoc->createAttribute('collid');
			$itemAttr->value = $collID;
			$itemElem->appendChild($itemAttr);
			$instCode = $cArr['instcode'];
			if($cArr['collcode']) {
				$instCode .= '-' . $cArr['collcode'];
			}
			$title = $instCode.' DwC-Archive';
			$itemTitleElem = $newDoc->createElement('title');
			$itemTitleElem->appendChild($newDoc->createTextNode($title));
			$itemElem->appendChild($itemTitleElem);
			if(strncmp($cArr['icon'], 'images/collicons/', 17) === 0){
				$imgLink = $urlPathPrefix.$cArr['icon'];
			}
			elseif(strncmp($cArr['icon'], '/', 1) === 0){
				$imgLink = $localDomain.$cArr['icon'];
			}
			else{
				$imgLink = $cArr['icon'];
			}
			$iconElem = $newDoc->createElement('image');
			$iconElem->appendChild($newDoc->createTextNode($imgLink));
			$itemElem->appendChild($iconElem);
			
			$descTitleElem = $newDoc->createElement('description');
			$descTitleElem->appendChild($newDoc->createTextNode('Darwin Core Archive for '.$cArr['collname']));
			$itemElem->appendChild($descTitleElem);
			$guidElem = $newDoc->createElement('guid');
			$guidElem->appendChild($newDoc->createTextNode($urlPathPrefix.'collections/misc/collprofiles.php?collid='.$collID));
			$itemElem->appendChild($guidElem);
			$guidElem2 = $newDoc->createElement('guid');
			$guidElem2->appendChild($newDoc->createTextNode($cArr['collectionguid']));
			$itemElem->appendChild($guidElem2);
			$fileNameSeed = str_replace(array(' ','"',"'"),'',$instCode).'_DwC-A';
			
			$emlElem = $newDoc->createElement('emllink');
			$emlElem->appendChild($newDoc->createTextNode($urlPathPrefix.'content/dwca/'.$fileNameSeed.'.eml'));
			$itemElem->appendChild($emlElem);
			$typeTitleElem = $newDoc->createElement('type','DWCA');
			$itemElem->appendChild($typeTitleElem);
			$recTypeTitleElem = $newDoc->createElement('recordType','DWCA');
			$itemElem->appendChild($recTypeTitleElem);
			$archivePath = $urlPathPrefix.'content/dwca/'.$fileNameSeed.'.zip';
			$linkTitleElem = $newDoc->createElement('link');
			$linkTitleElem->appendChild($newDoc->createTextNode($archivePath));
			$itemElem->appendChild($linkTitleElem);
			$pubDateTitleElem = $newDoc->createElement('pubDate');
			$pubDateTitleElem->appendChild($newDoc->createTextNode(date('D, d M Y H:i:s')));
			$itemElem->appendChild($pubDateTitleElem);
			$itemArr[$title] = $itemElem;
			
			$sql = 'UPDATE omcollections SET dwcaUrl = "'.$archivePath.'" WHERE collid = '.$collID;
			if(!$this->conn->query($sql)){
				$this->logOrEcho('ERROR updating dwcaUrl while adding new DWCA instance.');
			}
		}

		$rssFile = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) === '/'?'':'/').'webservices/dwc/rss.xml';
		if(file_exists($rssFile)){
			$oldDoc = new DOMDocument();
			$oldDoc->load($rssFile);
			$items = $oldDoc->getElementsByTagName('item');
			foreach($items as $i){
				$t = $i->getElementsByTagName('title')->item(0)->nodeValue;
				if(!array_key_exists($i->getAttribute('collid'),$this->collArr)) {
					$itemArr[$t] = $newDoc->importNode($i, true);
				}
			}
		}

		ksort($itemArr);
		foreach($itemArr as $i){
			$channelElem->appendChild($i);
		}
		
		$newDoc->save($rssFile);

		$this->logOrEcho("Done!!\n");
	}

	public function getDwcaItems($collid = null): array
	{
		$retArr = array();
		$rssFile = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) === '/'?'':'/').'webservices/dwc/rss.xml';
		if(file_exists($rssFile)){
			$xmlDoc = new DOMDocument();
			$xmlDoc->load($rssFile);
			$items = $xmlDoc->getElementsByTagName('item');
			$cnt = 0;
			foreach($items as $i ){
				$id = $i->getAttribute('collid');
                if(!$collid || $collid === (int)$id){
					$titles = $i->getElementsByTagName('title');
					$retArr[$cnt]['title'] = $titles->item(0)->nodeValue;
					$descriptions = $i->getElementsByTagName('description');
					$retArr[$cnt]['description'] = $descriptions->item(0)->nodeValue;
					$types = $i->getElementsByTagName('type');
					$retArr[$cnt]['type'] = $types->item(0)->nodeValue;
					$recordTypes = $i->getElementsByTagName('recordType');
					$retArr[$cnt]['recordType'] = $recordTypes->item(0)->nodeValue;
					$links = $i->getElementsByTagName('link');
					$retArr[$cnt]['link'] = $links->item(0)->nodeValue;
					$pubDates = $i->getElementsByTagName('pubDate');
					$retArr[$cnt]['pubDate'] = $pubDates->item(0)->nodeValue;
					$retArr[$cnt]['collid'] = $id;
					$cnt++;
				}
			}
		}
		$this->aasort($retArr, 'description');
		return $retArr;
	}

	public function getCollectionList($catID): array
	{
		$retArr = array();
		if($catID && !is_numeric($catID)) {
			return $retArr;
		}
		$sql = 'SELECT c.collid, c.collectionname, CONCAT_WS("-",c.institutioncode,c.collectioncode) as instcode, c.guidtarget, c.dwcaurl, c.managementtype '.
			'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
			'LEFT JOIN omcollcatlink l ON c.collid = l.collid '.
			'WHERE (c.colltype = "Preserved Specimens") AND (s.recordcnt > 0) ';
		if($catID) {
			$sql .= 'AND (l.ccpk = ' . $catID . ') ';
		}
		$sql .= 'ORDER BY c.collectionname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['name'] = $r->collectionname.' ('.$r->instcode.')';
			$retArr[$r->collid]['guid'] = $r->guidtarget;
			$retArr[$r->collid]['url'] = $r->dwcaurl;
		}
		return $retArr;
	}

	public function getAdditionalDWCA($catID): array
	{
		$retArr = array();
		if(!$catID || !is_numeric($catID)) {
			return $retArr;
		}
		$sql = 'SELECT substring_index(c.dwcaurl,"/content/",1)  as portalDomain, count(c.collid) as cnt '.
			'FROM omcollections c LEFT JOIN omcollcatlink l ON c.collid = l.collid '.
			'WHERE (c.colltype = "Preserved Specimens") AND (c.dwcaurl IS NOT NULL) AND (l.ccpk IS NULL OR l.ccpk != '.$catID.') '.
			'GROUP BY portalDomain';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$domainName = parse_url($r->portalDomain, PHP_URL_HOST);
			if($domainName){
                if(strncmp($domainName, 'www.', 4) === 0) {
                    $domainName = substr($domainName, 4);
                }
                if(is_string($domainName) || is_int($domainName)){
                    if(isset($retArr[$domainName])){
                        $retArr[$domainName]['cnt'] += $r->cnt;
                        if(strpos($retArr[$domainName]['url'],'/www.') && !strpos($r->portalDomain,'/www.')) {
                            $retArr[$domainName]['url'] = $r->portalDomain;
                        }
                    }
                    else{
                        $retArr[$domainName]['cnt'] = $r->cnt;
                        $retArr[$domainName]['url'] = $r->portalDomain;
                    }
                }
            }
		}
		return $retArr;
	}

	public function getCategoryName($catID): string
    {
		$retStr = '';
		if($catID){
			$sql = 'SELECT ccpk, category FROM omcollcategories WHERE (ccpk = '.$catID.')';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retStr = $r->category;
			}
			$rs->free();
		}
		return $retStr;
	}

	private function aasort(&$array, $key): void
	{
		$sorter = array();
		$ret = array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii] = $va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii] = $array[$ii];
		}
		$array = $ret;
	}

	public function humanFileSize($filePath): string
	{
		if(strncmp($filePath, 'http', 4) === 0) {
			$x = array_change_key_case(get_headers($filePath, 1),CASE_LOWER);
			if($x){
                if( strcasecmp($x[0], 'HTTP/1.1 200 OK') !== 0 ) {
                    $x = $x['content-length'][1];
                }
                else {
                    $x = $x['content-length'];
                }
            }
		}
		else { 
			$x = @filesize($filePath); 
		}
		$x = round($x/1000000, 1);
		if(!$x) {
			$x = 0.1;
		}
		
		return $x.'M ';
	}
}
