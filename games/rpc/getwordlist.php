<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/GamesManager.php');

$clid = array_key_exists('clid',$_POST)?$_POST['clid']: '';
$dynClid = array_key_exists('dynclid',$_POST)?$_POST['dynclid']: '';

$gameManager = new GamesManager();
if($clid) {
    $gameManager->setClid($clid);
}
elseif($dynClid) {
    $gameManager->setDynClid($dynClid);
}

$wordList = $gameManager->getNameGameWordList();

echo json_encode($wordList);
