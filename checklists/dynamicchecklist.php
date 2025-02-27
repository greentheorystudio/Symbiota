<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset=UTF-8' );
 
$lat = (float)$_POST['lat'];
$lng = (float)$_POST['lng'];
$radius = (float)$_POST['radius'];
$groundRadius = (float)$_POST['groundradius'];
$radiusunits = htmlspecialchars($_POST['radiusunits']);
$dynamicRadius = ($GLOBALS['DYN_CHECKLIST_RADIUS'] ?? 5);
$tid = (int)$_POST['tid'];
$interface = htmlspecialchars($_POST['interface']);

$dynClManager = new DynamicChecklistManager();
$dynClid = 0;

if(is_numeric($groundRadius)){
	$dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $groundRadius, $radiusunits, $tid);
}

if($interface === 'key'){
	header('Location: ' .$GLOBALS['CLIENT_ROOT']. '/ident/key.php?clid=' .$dynClid);
}
else{
	header('Location: ' .$GLOBALS['CLIENT_ROOT']. '/checklists/checklist.php?dynclid=' .$dynClid);
}
flush();
$dynClManager->removeOldChecklists();
