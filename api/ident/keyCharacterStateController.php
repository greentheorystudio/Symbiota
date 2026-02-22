<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/KeyCharacterStates.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$csid = array_key_exists('csid', $_REQUEST) ? (int)$_REQUEST['csid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $keyCharacterStates = new KeyCharacterStates();
    if($action === 'getCharacterStatesFromTidArr' && array_key_exists('tidArr', $_POST)){
        echo json_encode($keyCharacterStates->getCharacterStatesFromTidArr(json_decode($_POST['tidArr'], false)));
    }
    elseif($action === 'getCharacterCharacterStateDataFromTid' && array_key_exists('cid', $_POST) && array_key_exists('tid', $_POST)){
        echo json_encode($keyCharacterStates->getCharacterCharacterStateDataFromTid($_POST['cid'], $_POST['tid']));
    }
    elseif($action === 'getKeyCharacterStatesArrFromCsidArr' && array_key_exists('csidArr', $_POST)){
        $includeFullKeyData = array_key_exists('includeFullKeyData',$_POST) && (int)$_POST['includeFullKeyData'] === 1;
        echo json_encode($keyCharacterStates->getKeyCharacterStatesArrFromCsidArr(json_decode($_POST['csidArr'], false), $includeFullKeyData));
    }
    elseif($action === 'getKeyCharacterStatesArrFromCid' && array_key_exists('cid', $_POST)){
        echo json_encode($keyCharacterStates->getKeyCharacterStatesArrFromCid($_POST['cid']));
    }
    elseif($action === 'createKeyCharacterStateRecord' && $isEditor && array_key_exists('characterState', $_POST)){
        echo $keyCharacterStates->createKeyCharacterStateRecord(json_decode($_POST['characterState'], true));
    }
    elseif($action === 'updateKeyCharacterStateRecord' && $csid && $isEditor && array_key_exists('characterStateData', $_POST)){
        echo $keyCharacterStates->updateKeyCharacterStateRecord($csid, json_decode($_POST['characterStateData'], true));
    }
    elseif($action === 'getKeyCharacterStateData' && $csid && $isEditor){
        echo json_encode($keyCharacterStates->getKeyCharacterStateData($csid));
    }
    elseif($action === 'deleteKeyCharacterStateRecord' && $csid && $isEditor){
        echo $keyCharacterStates->deleteKeyCharacterStateRecord($csid);
    }
    elseif($action === 'addTaxonCharacterStateLinkage' && $csid && $isEditor && array_key_exists('cid', $_POST) && array_key_exists('tid', $_POST)){
        echo $keyCharacterStates->addTaxonCharacterStateLinkage($_POST['cid'], $csid, $_POST['tid']);
    }
    elseif($action === 'removeTaxonCharacterStateLinkage' && $csid && $isEditor && array_key_exists('cid', $_POST) && array_key_exists('tid', $_POST)){
        echo $keyCharacterStates->removeTaxonCharacterStateLinkage($_POST['cid'], $csid, $_POST['tid']);
    }
}
