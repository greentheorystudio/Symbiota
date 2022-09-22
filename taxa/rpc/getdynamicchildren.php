<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$taxId = array_key_exists('id',$_REQUEST)?$_REQUEST['id']:'';
$displayAuthor = array_key_exists('authors',$_REQUEST)?(int)$_REQUEST['authors']:0;
$targetId = array_key_exists('targetid',$_REQUEST)?(int)$_REQUEST['targetid']:0;

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$editable = true;
}

$taxonRankArr = array(1=>'Organism',10=>'Kingdom',20=>'Subkingdom',30=>'Phylum',40=>'Subphylum',
    50=>'Superclass',60=>'Class',70=>'Subclass',100=>'Order',110=>'Suborder',140=>'Family',
    150=>'Subfamily',160=>'Tribe',170=>'Subtribe',180=>'Genus',190=>'Subgenus',200=>'Section',
    210=>'Subsection',220=>'Species',230=>'Subspecies',240=>'Variety',250=>'Subvariety',260=>'Form',
    270=>'Subform',300=>'Cultivated');

$retArr = array();
$childArr = array();
if($taxId === 'root'){
	$retArr['id'] = 'root';
	$retArr['label'] = 'root';
	$retArr['name'] = 'root';
	if($editable){
		$retArr['url'] = 'taxonomy/taxonomyeditor.php';
	}
	else{
		$retArr['url'] = 'index.php';
	}
	$retArr['children'] = array();
	$lowestRank = '';
	$sql = 'SELECT MIN(t.RankId) AS RankId '.
		'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
		'LIMIT 1 ';
	//echo $sql."<br>";
	$rs = $con->query($sql);
	while($row = $rs->fetch_object()){
		$lowestRank = $row->RankId;
	}
	$rs->free();
	$sql1 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, tu.rankname '.
		'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.tid = ts.tid '.
		'LEFT JOIN taxonunits AS tu ON (t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid) '.
		'WHERE t.RankId = '.$lowestRank.' ';
	//echo '<div>' .$sql1. '</div>';
	$rs1 = $con->query($sql1);
	$i = 0;
	while($row1 = $rs1->fetch_object()){
        $rankName = $row1->rankname;
        if(!$rankName) {
			$rankName = $taxonRankArr[$row1->rankid];
		}
        if(!$rankName) {
			$rankName = 'Unknown';
		}
		$label = '2-'.$row1->rankid.'-'.$rankName.'-'.$row1->sciname;
		if((int)$row1->tid === $targetId){
			$sciName = '<b>'.$row1->sciname.'</b>';
		}
		else{
			$sciName = $row1->sciname;
		}
		$sciName = "<span style='font-size:75%;'>".$rankName.'</span> '.$sciName.($displayAuthor?' '.$row1->author:'');
		$childArr[$i]['id'] = $row1->tid;
		$childArr[$i]['label'] = $label;
		$childArr[$i]['name'] = $sciName;
		if($editable){
			$childArr[$i]['url'] = 'taxonomy/taxonomyeditor.php?tid='.$row1->tid;
		}
		else{
			$childArr[$i]['url'] = 'index.php?taxon='.$row1->tid;
		}
		$sql3 = 'SELECT tid FROM taxaenumtree WHERE parenttid = '.$row1->tid.' LIMIT 1 ';
		//echo "<div>".$sql3."</div>";
		$rs3 = $con->query($sql3);
		if($row3 = $rs3->fetch_object()){
			$childArr[$i]['children'] = true;
		}
		else{
			$sql4 = 'SELECT DISTINCT tid, tidaccepted FROM taxstatus WHERE tidaccepted = '.$row1->tid.' ';
			//echo "<div>".$sql4."</div>";
			$rs4 = $con->query($sql4);
			while($row4 = $rs4->fetch_object()){
				if($row4->tid !== $row4->tidaccepted){
					$childArr[$i]['children'] = true;
				}
			}
			$rs4->free();
		}
		$rs3->free();
		$i++;
	}
	$rs1->free();
}
else{
	$sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, tu.rankname '.
		'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
		'LEFT JOIN taxonunits AS tu ON (t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid) '.
		'WHERE (ts.tid = ts.tidaccepted) '.
		'AND (ts.parenttid = '.$taxId.' AND t.rankid > 1) ';
	//echo $sql2."<br>";
	$rs2 = $con->query($sql2);
	$i = 0;
	while($row2 = $rs2->fetch_object()){
		$rankName = $row2->rankname;
		if(!$rankName && array_key_exists($row2->rankid, $taxonRankArr)) {
			$rankName = $taxonRankArr[$row2->rankid];
		}
        elseif(!$rankName) {
			$rankName = 'Unknown';
		}
		$label = '2-'.$row2->rankid.'-'.$rankName.'-'.$row2->sciname;
		if($row2->rankid >= 180){
			$sciName = '<i>'.$row2->sciname.'</i>';
		}
		else{
			$sciName = $row2->sciname;
		}
		if((int)$row2->tid === $targetId){
			$sciName = '<b>'.$sciName.'</b>';
		}
		$sciName = "<span style='font-size:75%;'>".$rankName.'</span> '.$sciName.($displayAuthor?' '.$row2->author:'');
		if((int)$row2->tid === $taxId){
			$retArr['id'] = $row2->tid;
			$retArr['label'] = $label;
			$retArr['name'] = $sciName;
			if($editable){
				$retArr['url'] = 'taxonomy/taxonomyeditor.php?tid='.$row2->tid;
			}
			else{
				$retArr['url'] = 'index.php?taxon='.$row2->tid;
			}
			$retArr['children'] = array();
		}
		else{
			$childArr[$i]['id'] = $row2->tid;
			$childArr[$i]['label'] = $label;
			$childArr[$i]['name'] = $sciName;
			if($editable){
				$childArr[$i]['url'] = 'taxonomy/taxonomyeditor.php?tid='.$row2->tid;
			}
			else{
				$childArr[$i]['url'] = 'index.php?taxon='.$row2->tid;
			}
			$sql3 = 'SELECT tid FROM taxaenumtree WHERE parenttid = '.$row2->tid.' LIMIT 1 ';
			//echo "<div>".$sql3."</div>";
			$rs3 = $con->query($sql3);
			if($row3 = $rs3->fetch_object()){
				$childArr[$i]['children'] = true;
			}
			else{
				$sql4 = 'SELECT DISTINCT tid, tidaccepted FROM taxstatus WHERE tidaccepted = '.$row2->tid.' ';
				//echo "<div>".$sql4."</div>";
				$rs4 = $con->query($sql4);
				while($row4 = $rs4->fetch_object()){
					if($row4->tid !== $row4->tidaccepted){
						$childArr[$i]['children'] = true;
					}
				}
				$rs4->free();
			}
			$rs3->free();
			$i++;
		}
	}
	$rs2->free();
	
	$sqlSyns = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, tu.rankname '.
		'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
		'LEFT JOIN taxonunits AS tu ON (t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid) '.
		'WHERE (ts.tid <> ts.tidaccepted) AND (ts.tidaccepted = '.$taxId.')';
	//echo $sqlSyns;
	$rsSyns = $con->query($sqlSyns);
	while($row = $rsSyns->fetch_object()){
        $rankName = $row->rankname;
        if(!$rankName) {
			$rankName = $taxonRankArr[$row->rankid];
		}
        if(!$rankName) {
			$rankName = 'Unknown';
		}
		$label = '1-'.$row->rankid.'-'.$rankName.'-'.$row->sciname;
		if($row->rankid >= 180){
			$sciName = '<i>'.$row->sciname.'</i>';
		}
		else{
			$sciName = $row->sciname;
		}
		if((int)$row->tid === $targetId){
			$sciName = '<b>'.$sciName.'</b>';
		}
		$sciName = '['.$sciName.']';
		$sciName = "<span style='font-size:75%;'>".$rankName.'</span> '.$sciName.($displayAuthor?' '.$row->author:'');
		$childArr[$i]['id'] = $row->tid;
		$childArr[$i]['label'] = $label;
		$childArr[$i]['name'] = $sciName;
		if($editable){
			$childArr[$i]['url'] = 'taxonomy/taxonomyeditor.php?tid='.$row->tid;
		}
		else{
			$childArr[$i]['url'] = 'index.php?taxon='.$row->tid;
		}
		$i++;
	}
	$rsSyns->free();
}

function cmp($a,$b){
    return strnatcmp($a['label'],$b['label']);
}

usort($childArr,'cmp');

$retArr['children'] = $childArr;
	
echo json_encode($retArr);
