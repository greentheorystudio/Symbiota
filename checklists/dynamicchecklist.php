<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
 
$lat = (float)$_POST['lat'];
$lng = (float)$_POST['lng'];
$radius = (float)$_POST['radius'];
$radiusunits = htmlspecialchars($_POST['radiusunits']);
$dynamicRadius = ($GLOBALS['DYN_CHECKLIST_RADIUS'] ?? 5);
$tid = (int)$_POST['tid'];
$interface = htmlspecialchars($_POST['interface']);

$dynClManager = new DynamicChecklistManager();

if(is_numeric($radius)){
	$dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $radiusunits, $tid);
}
else{
	$dynClid = $dynClManager->createDynamicChecklist($lat, $lng, $dynamicRadius, $tid);
}

if($interface === 'key'){
	header('Location: ' .$GLOBALS['CLIENT_ROOT']. '/ident/key.php?dynclid=' .$dynClid. '&taxon=All Species');
}
else{
	header('Location: ' .$GLOBALS['CLIENT_ROOT']. '/checklists/checklist.php?dynclid=' .$dynClid);
}
flush();
$dynClManager->removeOldChecklists();
