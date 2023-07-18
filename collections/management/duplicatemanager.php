<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDuplicate.php');

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$dupeDepth = array_key_exists('dupedepth',$_REQUEST)?(int)$_REQUEST['dupedepth']:0;
$start = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:1000;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']):'';
$formSubmit = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']):'';

if(!$GLOBALS['SYMB_UID']){
	header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
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
<div id="innertext">
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
        if($action) {
            if($action === 'batchlinkdupes'){
                ?>
                <ul>
                    <?php
                    $dupManager->batchLinkDuplicates($collId);
                    ?>
                </ul>
                <?php
            }
            elseif($action === 'listdupes' || $action === 'listdupeconflicts'){
                $clusterArr = $dupManager->getDuplicateClusterList($collId, $dupeDepth, $start, $limit);
                $totalCnt = $clusterArr['cnt'];
                unset($clusterArr['cnt']);
                if($clusterArr){
                    $paginationStr = '<span>';
                    if($start) {
                        $paginationStr .= '<a href="index.php?tabindex=2&collid=' . $collId . '&action=' . $action . '&start=' . ($start - $limit) . '&limit=' . $limit . '">';
                    }
                    $paginationStr .= '&lt;&lt; Previous';
                    if($start) {
                        $paginationStr .= '</a>';
                    }
                    $paginationStr .= '</span>';
                    $paginationStr .= ' || '.($start+1).' - '.(count($clusterArr)<$limit?$totalCnt:($start + $limit)).' || ';
                    $paginationStr .= '<span>';
                    if($totalCnt >= ($start+$limit)) {
                        $paginationStr .= '<a href="index.php?tabindex=2&collid=' . $collId . '&action=' . $action . '&start=' . ($start + $limit) . '&limit=' . $limit . '">';
                    }
                    $paginationStr .= 'Next &gt;&gt;';
                    if($totalCnt >= ($start+$limit)) {
                        $paginationStr .= '</a>';
                    }
                    $paginationStr .= '</span>';
                    ?>
                    <div style="clear:both;font-weight:bold;">
                        <?php echo $collMap['collectionname']; ?>
                    </div>
                    <div style="float:right;">
                        <?php echo $paginationStr; ?>
                    </div>
                    <div style="font-weight:bold;margin-left:15px;">
                        <?php echo $totalCnt.' Duplicate Clusters '.($action === 'listdupeconflicts'?'with Identification Differences':''); ?>
                    </div>
                    <div style="margin:20px 0;clear:both;">
                        <?php
                        foreach($clusterArr as $dupId => $dupArr){
                            ?>
                            <div style="clear:both;margin:10px 0;">
                                <div style="font-weight:bold;">
                                    <?php echo $dupArr['title']; ?>
                                    <span onclick="toggle('editdiv-<?php echo $dupId; ?>')" title="Display Editing Controls"><i style="height:15px;width:15px;" class="far fa-edit"></i></span>
                                </div>
                                <?php
                                if(isset($dupArr['desc'])) {
                                    echo '<div style="margin-left:10px;">' . $dupArr['desc'] . '</div>';
                                }
                                if(isset($dupArr['notes'])) {
                                    echo '<div style="margin-left:10px;">' . $dupArr['notes'] . '</div>';
                                }
                                ?>
                                <div class="editdiv-<?php echo $dupId; ?>" style="display:none;">
                                    <fieldset style="margin:20px;padding:15px;">
                                        <legend><b>Edit Cluster</b></legend>
                                        <form name="dupeditform-<?php echo $dupId; ?>" method="post" action="index.php" onsubmit="return verifyEditForm(this);">
                                            <b>Title:</b> <input name="title" type="text" value="<?php echo $dupArr['title']; ?>" style="width:300px;" /><br/>
                                            <b>Description:</b> <input name="description" type="text" value="<?php echo $dupArr['desc']; ?>" style="width:400px;" /><br/>
                                            <b>Notes:</b> <input name="notes" type="text" value="<?php echo $dupArr['notes']; ?>" style="width:400px;" /><br/>
                                            <input name="dupid" type="hidden" value="<?php echo $dupId; ?>" />
                                            <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                                            <input name="start" type="hidden" value="<?php echo $start; ?>" />
                                            <input name="limit" type="hidden" value="<?php echo $limit; ?>" />
                                            <input name="action" type="hidden" value="<?php echo $action; ?>" />
                                            <input name="tabindex" type="hidden" value="2" />
                                            <input name="formsubmit" type="hidden" value="clusteredit" />
                                            <input name="submit" type="submit" value="Save Edits" />
                                        </form>
                                        <form name="dupdelform-<?php echo $dupId; ?>" method="post" action="index.php" onsubmit="return confirm('Are you sure you want to delete this duplicate cluster?');">
                                            <input name="deldupid" type="hidden" value="<?php echo $dupId; ?>" />
                                            <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                                            <input name="start" type="hidden" value="<?php echo $start; ?>" />
                                            <input name="limit" type="hidden" value="<?php echo $limit; ?>" />
                                            <input name="action" type="hidden" value="<?php echo $action; ?>" />
                                            <input name="tabindex" type="hidden" value="2" />
                                            <input name="formsubmit" type="hidden" value="clusterdelete" />
                                            <input name="submit" type="submit" value="Delete Cluster" />
                                        </form>
                                    </fieldset>
                                </div>
                                <div style="margin:7px 10px;">
                                    <?php
                                    unset($dupArr['title'], $dupArr['desc'], $dupArr['notes']);
                                    foreach($dupArr as $occid => $oArr){
                                        ?>
                                        <div style="margin:10px">
                                            <div style="float:left;">
                                                <a href="#" onclick="openOccurPopup(<?php echo $occid; ?>); return false;"><b><?php echo $oArr['id']; ?></b></a> =&gt;
                                                <?php echo $oArr['recby']; ?>
                                            </div>
                                            <div class="editdiv-<?php echo $dupId; ?>" style="display:none;float:left;" title="Delete Occurrence from Cluster">
                                                <form name="dupdelform-<?php echo $dupId.'-'.$occid; ?>" method="post" action="index.php" onsubmit="return confirm('Are you sure you want to remove this occurrence record from this cluster?');" style="display:inline;">
                                                    <input name="dupid" type="hidden" value="<?php echo $dupId; ?>" />
                                                    <input name="occid" type="hidden" value="<?php echo $occid; ?>" />
                                                    <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                                                    <input name="start" type="hidden" value="<?php echo $start; ?>" />
                                                    <input name="limit" type="hidden" value="<?php echo $limit; ?>" />
                                                    <input name="action" type="hidden" value="<?php echo $action; ?>" />
                                                    <input name="tabindex" type="hidden" value="2" />
                                                    <input name="formsubmit" type="hidden" value="occdelete" />
                                                    <button style="margin:0;padding:2px;" type="submit">
                                                        <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin-left:15px;clear:both;">
                                                <?php
                                                echo '<b>'.$oArr['sciname'].'</b><br/>';
                                                if($oArr['idby']) {
                                                    echo 'Determined by: ' . $oArr['idby'] . ' ' . $oArr['dateid'];
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    echo $paginationStr;
                }
                else{
                     echo '<div><b>No Duplicate Clusters match the request.</b></div>';
                }
            }
            ?>
            <div>
                <a href="index.php?tabindex=2&collid=<?php echo $collId; ?>">Return to main menu</a>
            </div>
            <?php
        }
        else{
            if($collMap['colltype'] !== 'HumanObservation'){
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
            ?>
            <div style="margin: 10px 0;">
                <h3>Duplicate Linkages</h3>
                <div style="margin:0 0 40px 15px;">
                    <ul>
                        <li>
                            <a href="index.php?tabindex=2&collid=<?php echo $collId; ?>&action=listdupes">
                                List linked duplicates
                            </a>
                        </li>
                        <li>
                            <a href="index.php?tabindex=2&collid=<?php echo $collId; ?>&dupedepth=2&action=listdupeconflicts">
                                List linked duplicates with conflicted identification conflicts
                            </a>
                        </li>
                        <li>
                            <a href="index.php?tabindex=2&collid=<?php echo $collId; ?>&action=batchlinkdupes">
                                Start batch linking duplicates
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        }
    }
    else{
        echo '<h2>You are not authorized to access this page</h2>';
    }
    ?>
</div>
