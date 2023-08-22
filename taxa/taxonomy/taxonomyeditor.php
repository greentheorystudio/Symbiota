<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ' . $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$tid = (int)$_REQUEST['tid'];

$taxUtilities = new TaxonomyUtilities();
$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$editable = true;
}

$statusStr = '';
if($editable){
	if(array_key_exists('taxonedits',$_POST)){
		$statusStr = $taxonEditorObj->editTaxon($_POST);
	}
	elseif($submitAction === 'updatetaxparent'){
		$statusStr = $taxonEditorObj->editTaxonParent($_POST['parenttid']);
        $tidArr = $taxUtilities->getChildTidArr($tid);
        $taxUtilities->updateHierarchyTable($tid);
        $taxUtilities->updateHierarchyTable($tidArr);
        $tidArr[] = $tid;
        $taxUtilities->updateFamily($tidArr);
	}
	elseif($submitAction === 'linktoaccepted'){
		$statusStr = $taxonEditorObj->submitAddAcceptedLink($_REQUEST['tidaccepted']);
	}
	elseif(array_key_exists('changetoaccepted',$_REQUEST)){
		$tidAccepted = $_REQUEST['tidaccepted'];
		$statusStr = $taxonEditorObj->submitChangeToAccepted($tid,$tidAccepted);
	}
	elseif($submitAction === 'changetonotaccepted'){
		$tidAccepted = $_REQUEST['tidaccepted'];
		$statusStr = $taxonEditorObj->submitChangeToNotAccepted($tid,$tidAccepted);
	}
	elseif($submitAction === 'Remap Taxon'){
		$statusStr = $taxonEditorObj->transferResources($_REQUEST['remaptid']);
		header('Location: index.php?target='.$_REQUEST['genusstr'].'&statusstr='.$statusStr.'&tabindex=1');
	}
	elseif($submitAction === 'Delete Taxon'){
        $taxUtilities->deleteTidFromHierarchyTable($tid);
        $statusStr = $taxonEditorObj->deleteTaxon();
		header('Location: index.php?statusstr='.$statusStr.'&tabindex=1');
	}

	$taxonEditorObj->setTaxon();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']. ' Taxon Editor: ' .$tid; ?></title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link type="text/css" href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/external/jquery.js"></script>
	<script type="text/javascript" src="../../js/external/jquery-ui.js"></script>
	<script>
		let tid = <?php echo $taxonEditorObj->getTid(); ?>;
		let tabIndex = <?php echo $tabIndex; ?>;
	</script>
    <script src="../../js/taxa.taxonomyeditor.js?ver=20230103"></script>
</head>
<body>
<?php
	include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="index.php">Taxonomy Editor</a> &gt;&gt;
    <b>Editing: <?php echo '<i>' .$taxonEditorObj->getSciName(). '</i> ' .$taxonEditorObj->getAuthor(). ' [' .$taxonEditorObj->getTid(). ']'; ?></b>
</div>
<div id="innertext">
    <?php
    if($statusStr){
        ?>
        <hr/>
        <div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
            <?php echo $statusStr; ?>
        </div>
        <hr/>
        <?php
    }
    if($editable && $tid){
        ?>
        <div style="float:right;" title="Go to taxonomy display">
            <a href="index.php?target=<?php echo $taxonEditorObj->getUnitName1();?>&showsynonyms=1&tabindex=1">
                <i style="height:15px;width:15px;" class="fas fa-level-up-alt"></i>
            </a>
        </div>
        <div style="float:right;" title="Add a New Taxon">
            <a href="index.php">
                <i style="height:20px;width:20px;color:green;" class="fas fa-plus"></i>
            </a>
        </div>
        <h1>
        <?php
            echo "<a href='../profile/tpeditor.php?tid=".$taxonEditorObj->getTid()."' style='color:inherit;text-decoration:none;'>";
            echo '<i>' .$taxonEditorObj->getSciName(). '</i> ' .$taxonEditorObj->getAuthor(). ' [' .$taxonEditorObj->getTid(). ']';
            echo '</a>'
        ?>
        </h1>
        <div id="tabs" class="taxondisplaydiv">
            <ul>
                <li><a href="#editdiv">Editor</a></li>
                <li><a href="#taxonstatusdiv">Taxonomic Status</a></li>
                <li><a href="#hierarchydiv">Hierarchy</a></li>
                <li><a href="taxonomydelete.php?tid=<?php echo $tid; ?>&genusstr=<?php echo $taxonEditorObj->getUnitName1(); ?>">Delete</a></li>
            </ul>

            <!-- EDITOR div -->
            <div id="editdiv" style="min-height:400px;">
                <div style="float:right;cursor:pointer;" onclick="toggleEditFields()" title="Toggle Taxon Editing Functions">
                    <i style="height:20px;width:20px;" class="far fa-edit"></i>
                </div>
                <form id="taxoneditform" name="taxoneditform" action="taxonomyeditor.php" method="post" onsubmit="return validateTaxonEditForm(this)">
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">UnitName1: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getUnitInd1(). ' ' .$taxonEditorObj->getUnitName1();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <div style="float:left;">
                                <input type="text" id="unitind1" name="unitind1" style="width:20px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitInd1(); ?>" />
                            </div>
                            <div>
                                <input type="text" id="unitname1" name="unitname1" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName1(); ?>" />
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">UnitName2: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getUnitInd2(). ' ' .$taxonEditorObj->getUnitName2();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <div style="float:left;">
                                <input type="text" id="unitind2" name="unitind2" style="width:20px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitInd2(); ?>" />
                            </div>
                            <div>
                                <input type="text" id="unitname2" name="unitname2" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName2(); ?>" />
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">UnitName3: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getUnitInd3(). ' ' .$taxonEditorObj->getUnitName3();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <div style="float:left;">
                                <input type="text" id="unitind3" name="unitind3" style="width:50px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitInd3(); ?>" />
                            </div>
                            <div>
                                <input type="text" id="unitname3" name="unitname3" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName3(); ?>" />
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Author: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getAuthor();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <input type="text" id="author" name="author" style="width:400px;border-style:inset;" value="<?php echo $taxonEditorObj->getAuthor(); ?>" />
                        </div>
                    </div>
                    <div id="kingdomdiv" style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Kingdom: </div>
                        <div class="editfield">
                            <?php
                            echo $taxonEditorObj->getKingdomName();
                            ?>
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Rank Name: </div>
                        <div class="editfield">
                            <?php echo ($taxonEditorObj->getRankName()?:'Non-Ranked Node'); ?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <select id="rankid" name="rankid">
                                <option value="0">Non-Ranked Node</option>
                                <option value="">---------------------------------</option>
                                <?php
                                $rankArr = $taxonEditorObj->getRankArr();
                                foreach($rankArr as $rankId => $rName){
                                    echo '<option value="'.$rankId.'" '.($taxonEditorObj->getRankId() === (int)$rankId?'SELECTED':'').'>'.$rName.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Notes: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getNotes();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <input type="text" id="notes" name="notes" value="<?php echo $taxonEditorObj->getNotes(); ?>" style="width:475px;" />
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Source: </div>
                        <div class="editfield">
                            <?php echo $taxonEditorObj->getSource();?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <input type="text" id="source" name="source" style="width:475px;" value="<?php echo $taxonEditorObj->getSource(); ?>" />
                        </div>
                    </div>
                    <div style="clear:both;">
                        <div style="float:left;width:140px;font-weight:bold;">Locality Security: </div>
                        <div class="editfield">
                            <?php
                                switch($taxonEditorObj->getSecurityStatus()){
                                    case 0:
                                        echo 'show all locality data';
                                        break;
                                    case 1:
                                        echo 'hide locality data';
                                        break;
                                    default:
                                        echo 'not set or set to an unknown setting';
                                        break;
                                }
                            ?>
                        </div>
                        <div class="editfield" style="display:none;">
                            <select id="securitystatus" name="securitystatus">
                                <option value="0">select a locality setting</option>
                                <option value="0">---------------------------------</option>
                                <option value="0" <?php echo (($taxonEditorObj->getSecurityStatus() === 0)?'SELECTED':''); ?>>show all locality data</option>
                                <option value="1" <?php echo (($taxonEditorObj->getSecurityStatus() === 1)?'SELECTED':''); ?>>hide locality data</option>
                            </select>
                            <input type='hidden' name='securitystatusstart' value='<?php echo $taxonEditorObj->getSecurityStatus(); ?>' />
                        </div>
                    </div>
                    <div class="editfield" style="display:none;">
                        <input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
                        <input type="submit" id="taxoneditsubmit" name="taxonedits" value="Submit Edits" />
                    </div>
                </form>
            </div>

            <!-- TAXONOMIC STATUS div -->
            <div id="taxonstatusdiv" style="min-height:400px;">
                <fieldset style="width:95%;">
                    <legend><b>Taxonomic Placement</b></legend>
                    <div style="font-weight:bold;">Status:
                        <span style='color:red;'>
                        <?php
                        $acceptance = $taxonEditorObj->getIsAccepted();
                        if($acceptance === 0){
                            echo 'Not Accepted';
                        }
                        if($acceptance === 1){
                            echo 'Accepted';
                        }
                        ?>
                        </span>
                    </div>
                    <div style="clear:both;margin:10px;">
                        <form name="taxparenteditform" action="taxonomyeditor.php" method="post">
                            <div style="float:right;">
                                <a href="" onclick="toggle('tsedit');return false;">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i>
                                </a>
                            </div>
                            <?php
                            if($taxonEditorObj->getRankId() > 140 && $taxonEditorObj->getFamily()){
                                ?>
                                <div>
                                    <div style="float:left;width:120px;font-weight:bold;">Family: </div>
                                    <div style="">
                                        <?php echo $taxonEditorObj->getFamily();?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div>
                                <div style="float:left;width:120px;font-weight:bold;">Parent Taxon: </div>
                                <div class="tsedit">
                                    <?php echo $taxonEditorObj->getParentNameFull();?>
                                </div>
                                <div class="tsedit" style="display:none;margin:3px;">
                                    <input id="parentstr" name="parentstr" type="text" value="<?php echo $taxonEditorObj->getParentName(); ?>" />
                                    <input id="parenttid" name="parenttid" type="hidden" value="<?php echo $taxonEditorObj->getParentTid(); ?>" />
                                </div>
                            </div>
                            <div class="tsedit" style="display:none;clear:both;">
                                <div style="margin-top:8px;margin-bottom:8px;">
                                    <a href="index.php" target="_blank">Click here to add a taxon to the thesaurus</a>
                                </div>
                                <input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
                                <?php
                                    $aArr = $taxonEditorObj->getAcceptedArr();
                                    $aStr = key($aArr);
                                ?>
                                <input type="hidden" name="tidaccepted" value="<?php echo ($taxonEditorObj->getIsAccepted() === 1?$taxonEditorObj->getTid():$aStr); ?>" />
                                <input type="hidden" name="tabindex" value="1" />
                                <input type="hidden" name="submitaction" value="updatetaxparent" />
                                <input type='button' value='Submit Upper Taxonomy Edits' onclick="submitUpperTaxForm(this.form)" />
                            </div>
                        </form>
                    </div>
                    <div id="AcceptedDiv" style="margin-top:30px;clear:both;">
                        <?php
                        if($taxonEditorObj->getIsAccepted() !== 1){
                            $acceptedArr = $taxonEditorObj->getAcceptedArr();
                            ?>
                            <h3>Accepted Taxon:</h3>
                            <div style="float:right;cursor:pointer;" onclick="toggle('acceptedits')">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </div>
                            <?php
                            if($acceptedArr){
                                echo "<ul>\n";
                                foreach($acceptedArr as $tidAccepted => $linkedTaxonArr){
                                    echo "<li id='acclink-".$tidAccepted."'>\n";
                                    echo "<a href='taxonomyeditor.php?tid=".$tidAccepted."'><i>".$linkedTaxonArr['sciname']. '</i></a> ' .$linkedTaxonArr['author']."\n";
                                    if($linkedTaxonArr['usagenotes']){
                                        echo "<div style='margin-left:10px;'>";
                                        echo '<u>Notes</u>: ' . $linkedTaxonArr['usagenotes'];
                                        echo "</div>\n";
                                    }
                                    echo "</li>\n";
                                }

                                echo "</ul>\n";
                            }
                            else{
                                echo "<div style='margin:20px;'>Acceptance not yet designated for this taxon</div>\n";
                            }
                            ?>
                            <div class="acceptedits" style="display:none;">
                                <form id="accepteditsform" name="accepteditsform" action="taxonomyeditor.php" method="post" onsubmit="return verifyAcceptEditsForm(this);" >
                                    <fieldset style="width:380px;margin:20px;">
                                        <legend><b>Change to Another Accepted Taxon</b></legend>
                                        <div>
                                            Accepted Taxon:
                                            <input id="aefacceptedstr" name="acceptedstr" type="text" style="width:300px;" />
                                            <input id="aeftidaccepted" name="tidaccepted" type="hidden" />
                                        </div>
                                        <div style="margin-top:8px;margin-bottom:8px;">
                                            <a href="index.php" target="_blank">Click here to add a taxon to the thesaurus</a>
                                        </div>
                                        <div>
                                            <input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid();?>" />
                                            <input type="hidden" name="tabindex" value="1" />
                                            <input type="hidden" name="submitaction" value="linktoaccepted" />
                                            <input type="submit" value="Change Accepted Taxon" />
                                        </div>
                                    </fieldset>
                                </form>
                                <?php
                                if($acceptedArr && count($acceptedArr) === 1){
                                    ?>
                                    <form id="changetoacceptedform" name="changetoacceptedform" action="taxonomyeditor.php" method="post">
                                        <fieldset style="width:350px;margin:20px;">
                                            <legend><b>Change to Accepted</b></legend>
                                            <div>
                                                <input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid();?>" />
                                                <input type="hidden" name="tidaccepted" value="<?php echo $aStr; ?>" />
                                                <input type="hidden" name="tabindex" value="1" />
                                                <input type='submit' id='changetoacceptedsubmit' name='changetoaccepted' value='Change Status to Accepted' />
                                            </div>
                                        </fieldset>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <div id="SynonymDiv" style="clear:both;padding-top:15px;">
                        <?php
                        if($taxonEditorObj->getIsAccepted() !== 0){
                            ?>
                            <div><u><b>Synonyms</b></u></div>
                            <div style="float:right;cursor:pointer;" onclick="toggle('tonotaccepted');">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </div>
                            <ul>
                            <?php
                            $synonymArr = $taxonEditorObj->getSynonyms();
                            if($synonymArr){
                                foreach($synonymArr as $tidSyn => $synArr){
                                    echo '<li> ';
                                    echo '<a href="taxonomyeditor.php?tid='.$tidSyn.'"><i>'.$synArr['sciname'].'</i></a> '.$synArr['author'].' ';
                                    echo '<a href="#" onclick="toggle(\'syn-'.$tidSyn.'\');">';
                                    echo '<i style="height:15px;width:15px;" class="far fa-edit"></i>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        <?php
                        }
                        else{
                            echo "<div style='margin:20px;'>No Synonyms Linked to this Taxon</div>";
                        }
                        ?>
                            <div id="tonotaccepted" style="display:none;">
                                <form id="changetonotacceptedform" name="changetonotacceptedform" action="taxonomyeditor.php" method="post" onsubmit="return verifyChangeToNotAcceptedForm(this);">
                                    <fieldset style="width:90%;">
                                        <legend><b>Change to Not Accepted</b></legend>
                                        <div style="margin:5px;">
                                            <b>Accepted Taxon:</b>
                                            <input id="ctnafacceptedstr" name="acceptedstr" type="text" style="width:270px;" />
                                            <input id="ctnaftidaccepted" name="tidaccepted" type="hidden" value="" />
                                        </div>
                                        <div style="margin:5px;">
                                            <b>Notes:</b>
                                            <input name="notes" type="text" style="width:400px;" />
                                        </div>
                                        <div style="margin-top:8px;margin-bottom:8px;">
                                            <a href="index.php" target="_blank">Click here to add a taxon to the thesaurus</a>
                                        </div>
                                        <div style="margin:5px;">
                                            <input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid();?>" />
                                            <input type="hidden" name="tabindex" value="1" />
                                            <input type="hidden" name="submitaction" value="changetonotaccepted" />
                                            <input type='submit' value='Change Status to Not Accepted' />
                                        </div>
                                        <div style="margin:5px;">
                                            * Synonyms will be transferred to Accepted Taxon
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </fieldset>
            </div>
            <div id="hierarchydiv" style="min-height:400px;">
                <fieldset style="width:420px;padding:25px;">
                    <legend><b>Quick Query Taxonomic Hierarchy</b></legend>
                    <?php
                    if($hierarchyArr = $taxonEditorObj->getHierarchyArr()){
                        $indent = 0;
                        foreach($hierarchyArr as $hierTid => $hierSciname){
                            echo '<div style="margin-left:'.$indent.'px;">';
                            echo '<a href="taxonomyeditor.php?tid='.$hierTid.'">'.$hierSciname.'</a>';
                            echo "</div>\n";
                            $indent += 10;
                        }
                        echo '<div style="margin-left:'.$indent.'px;">';
                        echo '<a href="taxonomyeditor.php?tid='.$taxonEditorObj->getTid().'">'.$taxonEditorObj->getSciName().'</a>';
                        echo "</div>\n";
                    }
                    else{
                        echo "<div style='margin:10px;'>Empty</div>";
                    }
                    ?>
                </fieldset>
            </div>
        </div>
    <?php
    }
    else if($tid) {
        ?>
        <div style="margin:30px;font-weight:bold;">
            You are not authorized to access this page
        </div>
        <?php
    }
    else if($statusStr !== 'SUCCESS: taxon deleted!'){
        echo '<div>Target Taxon missing</div>';
    }
    ?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
include_once(__DIR__ . '/../../config/footer-includes.php');
?>
</body>
</html>
