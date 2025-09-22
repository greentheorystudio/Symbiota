<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDuplicate.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$dupeDepth = array_key_exists('dupedepth',$_REQUEST)?(int)$_REQUEST['dupedepth']:0;
$start = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:1000;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']):'';
$formSubmit = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']):'';

if(!$GLOBALS['SYMB_UID']){
	header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$dupManager = new OccurrenceDuplicate();
$collMap = $dupManager->getCollMap($collId);

$statusStr = '';
$isEditor = 0; 
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'HumanObservation')
	|| (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($isEditor && $formSubmit){
	if($formSubmit === 'clusteredit'){
		$statusStr = $dupManager->editCluster($_POST['dupid'],$_POST['title'],$_POST['description'],$_POST['notes']);
	}
	elseif($formSubmit === 'clusterdelete'){
		$statusStr = $dupManager->deleteCluster($_POST['deldupid']);
	}
	elseif($formSubmit === 'occdelete'){
		$statusStr = $dupManager->deleteOccurFromCluster($_POST['dupid'],$_POST['occid']);
	}
}
?>
<style>
    table.styledtable td { white-space: nowrap; }
</style>
<script type="text/javascript">
    function verifyEditForm(f){
        if(f.title === ""){
            alert("Title field must have a value");
            return false;
        }
        return true;
    }

    function openOccurPopup(occid) {
        const occWindow = open("../individual/index.php?occid=" + occid, "occwin" + occid, "resizable=1,scrollbars=1,toolbar=1,width=750,height=600,left=20,top=20");
        if(occWindow.opener == null) {
            occWindow.opener = self;
        }
    }
</script>
<div id="mainContainer" style="padding: 10px 15px 15px;background-color:white;">
    <?php
    if($statusStr){
        ?>
        <hr/>
        <div style="margin:20px;color:<?php echo (strncmp($statusStr, 'ERROR', 5) === 0 ?'red':'green');?>">
            <?php echo $statusStr; ?>
        </div>
        <hr/>
        <?php
    }
    if($isEditor){
        ?>
        <div style="margin: 10px 0;">
            <h3>Duplicate Records</h3>
            <div style="margin:0 0 40px 15px;">
                <ul>
                    <li>
                        <a href="duplicatesearch.php?collid=<?php echo $collId; ?>&action=listdupscatalog">
                            List Duplicate Catalog Numbers
                        </a>
                    </li>
                    <li>
                        <a href="duplicatesearch.php?collid=<?php echo $collId; ?>&action=listdupsothercatalog">
                            List Duplicate Other Catalog Numbers
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }
    else{
        echo '<h2>You are not authorized to access this page</h2>';
    }
    ?>
</div>
