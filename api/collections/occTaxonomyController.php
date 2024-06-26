<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$sciname = array_key_exists('sciname',$_REQUEST)?$_REQUEST['sciname']:'';
$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($isEditor && $collid && $action && SanitizerService::validateInternalRequest()){
    $cleanManager = new OccurrenceTaxonomyCleaner();
    $cleanManager->setCollId($collid);
    if($action === 'getUnlinkedScinameCounts'){
        $returnArr = array();
        $returnArr['taxaCnt'] = $cleanManager->getBadTaxaCount();
        $returnArr['occCnt'] = $cleanManager->getBadSpecimenCount();
        echo json_encode($returnArr);
    }
    elseif($action === 'updateOccThesaurusLinkages'){
        echo $cleanManager->updateOccTaxonomicThesaurusLinkages($kingdomid);
    }
    elseif($action === 'updateDetThesaurusLinkages'){
        echo $cleanManager->updateDetTaxonomicThesaurusLinkages($kingdomid);
    }
    elseif($action === 'updateLocalitySecurity'){
        echo $cleanManager->protectGlobalSpecies($collid);
    }
    elseif($action === 'cleanQuestionMarks'){
        echo $cleanManager->cleanQuestionMarks();
    }
    elseif($action === 'cleanTrimNames'){
        echo $cleanManager->cleanTrimNames();
    }
    elseif($action === 'cleanSpNames'){
        echo $cleanManager->cleanSpNames();
    }
    elseif($action === 'cleanInfra'){
        echo $cleanManager->cleanInfraAbbrNames();
    }
    elseif($action === 'cleanQualifierNames'){
        echo $cleanManager->cleanQualifierNames();
    }
    elseif($action === 'cleanDoubleSpaces'){
        echo $cleanManager->cleanDoubleSpaceNames();
    }
    elseif($action === 'getUnlinkedOccSciNames'){
        echo $cleanManager->getUnlinkedSciNames();
    }
    elseif($action === 'updateOccWithNewSciname'){
        echo $cleanManager->updateOccRecordsWithNewScinameTid($sciname,$tid);
    }
    elseif($action === 'updateOccWithCleanedName'){
        echo $cleanManager->updateOccRecordsWithCleanedSciname($_POST['sciname'],$_POST['cleanedsciname'],$_POST['tid']);
    }
    elseif($action === 'undoOccScinameChange'){
        $oldName = str_replace(array('%squot;', '%dquot;'), array("'", '"'), $_POST['oldsciname']);
        $newName = str_replace(array('%squot;', '%dquot;'), array("'", '"'), $_POST['newsciname']);
        echo $cleanManager->undoOccRecordsCleanedScinameChange($oldName,$newName);
    }
}
