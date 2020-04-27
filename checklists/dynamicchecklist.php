<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);
 
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$radius = $_POST['radius'];
$radiusunits = $_POST['radiusunits'];
$dynamicRadius = ($DYN_CHECKLIST_RADIUS ?? 5);
$tid = $_POST['tid'];
$interface = $_POST['interface'];

$dynClManager = new DynamicChecklistManager();

if(is_numeric($radius)){
	$dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $radiusunits, $tid);
}
else{
	$dynClid = $dynClManager->createDynamicChecklist($lat, $lng, $dynamicRadius, $tid);
}

if($interface === 'key'){
	header('Location: ' .$CLIENT_ROOT. '/ident/key.php?dynclid=' .$dynClid. '&taxon=All Species');
}
else{
	header('Location: ' .$CLIENT_ROOT. '/checklists/checklist.php?dynclid=' .$dynClid);
}
flush();
$dynClManager->removeOldChecklists();
