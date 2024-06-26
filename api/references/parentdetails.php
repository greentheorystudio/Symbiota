<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');

$connection = new DbService();
$con = $connection->getConnection();
$retArr = array();

$refId = array_key_exists('refid',$_REQUEST)?(int)$_REQUEST['refid']:0;
$refType = array_key_exists('reftype',$_REQUEST)?(int)$_REQUEST['reftype']:0;

if($refId) {
	$sql = 'SELECT o.refid, o.parentRefId, o.title, o.shorttitle, o.alternativetitle, o.numbervolumes, o.ReferenceTypeId, '.
		'o.pubdate, o.edition, o.volume, o.number, o.placeofpublication, o.publisher, o.isbn_issn '.
		'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
		'WHERE o.refid = '.$refId;
	//echo $sql;
	if($rs = $con->query($sql)){
		while($r = $rs->fetch_object()){
			$retArr['parentRefId'] = $r->refid;
			$retArr['parentRefId2'] = $r->parentRefId;
			if($refType === 4 && $r->ReferenceTypeId === 27){
				$retArr['tertiarytitle'] = $r->title;
				$retArr['secondarytitle'] = '';
			}
			else{
				$retArr['secondarytitle'] = $r->title;
				$retArr['tertiarytitle'] = '';
			}
			$retArr['shorttitle'] = $r->shorttitle;
			$retArr['alternativetitle'] = $r->alternativetitle;
			$retArr['pubdate'] = $r->pubdate;
			$retArr['edition'] = $r->edition;
			$retArr['volume'] = $r->volume;
			$retArr['number'] = $r->number;
			$retArr['placeofpublication'] = $r->placeofpublication;
			$retArr['publisher'] = $r->publisher;
			$retArr['isbn_issn'] = $r->isbn_issn;
			$retArr['numbervolumes'] = $r->numbervolumes;
		}
		$rs->close();
	}
	if($retArr['parentRefId2']){
		$sql = 'SELECT o.title, o.edition, o.numbervolumes, o.placeofpublication, o.publisher, o.ReferenceTypeId '.
			'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
			'WHERE o.refid = '.$retArr['parentRefId2'];
		//echo $sql;
		if($rs = $con->query($sql)){
			while($r = $rs->fetch_object()){
				if($refType === 4 && $r->ReferenceTypeId === 27){
					$retArr['tertiarytitle'] = $r->title;
				}
				else{
					$retArr['secondarytitle'] = $r->title;
				}
				$retArr['numbervolumes'] = $r->numbervolumes;
				$retArr['edition'] = $r->edition;
				$retArr['placeofpublication'] = $r->placeofpublication;
				$retArr['publisher'] = $r->publisher;
			}
			$rs->close();
		}
	}
}
echo json_encode($retArr);
