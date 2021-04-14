<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceChecklistManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');

$checklistManager = new OccurrenceChecklistManager();
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?(int)$_REQUEST['taxonfilter']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';

if(!is_numeric($taxonFilter)) {
    $taxonFilter = 0;
}

$checklistArr = array();
$taxaCnt = 0;

$solrManager = new SOLRManager();
$checklistManager = new OccurrenceChecklistManager();

if($stArrJson){
    $stArr = json_decode($stArrJson, true);

    if($GLOBALS['SOLR_MODE']){
        $solrManager->setSearchTermsArr($stArr);
        $solrArr = $solrManager->getTaxaArr();
        if($taxonFilter && is_numeric($taxonFilter)){
            $tidArr = $solrManager->getSOLRTidList($solrArr);
            $checklistArr = $checklistManager->getTidChecklist($tidArr,$taxonFilter);
            $taxaCnt = $checklistManager->getChecklistTaxaCnt();
        }
        else{
            $checklistArr = $solrManager->translateSOLRTaxaList($solrArr);
            $taxaCnt = $solrManager->getChecklistTaxaCnt();
        }
    }
    else{
        $checklistManager->setSearchTermsArr($stArr);
        $checklistArr = $checklistManager->getChecklist($taxonFilter);
        $taxaCnt = $checklistManager->getChecklistTaxaCnt();
    }
}
?>
<div>
    <div style="height:20px;float:right;width:400px;display:flex;justify-content:flex-end;align-items:center;">
    <button class="icon-button" title="Download Checklist Data" onclick="processDownloadChecklist();">
        <i style='width:15px;height:15px;' class="fas fa-download"></i>
    </button>
	<?php
	if($GLOBALS['KEY_MOD_IS_ACTIVE']){
	?>
        <button class="icon-button" title='Open in Interactive Key Interface' onclick="submitInteractiveKeyFormTaxaList();">
            <i style='width:15px;height:15px;' class="fas fa-key"></i>
        </button>
        <form id="interactiveKeyForm" style="display:inline;" action="checklistsymbiota.php" method="post">
            <input type="hidden" name="starr" value='<?php echo $stArrJson; ?>' />
            <input type="hidden" id="interactiveKeyFormTaxonfilter" name="taxonfilter" value='<?php echo $taxonFilter; ?>' />
            <input type="hidden" name="interface" value='key' />
        </form>
	<?php
	}
	if($GLOBALS['FLORA_MOD_IS_ACTIVE']){
	?>
        <button class="icon-button" title='Open in Checklist Explorer Interface' onclick="submitChecklistExplorerFormTaxaList();">
            <i style='width:15px;height:15px;' class="fas fa-list"></i>
        </button>
        <form id="checklistExplorerForm" style="display:inline;" action="checklistsymbiota.php" method="post">
            <input type="hidden" name="starr" value='<?php echo $stArrJson; ?>' />
            <input type="hidden" id="checklistExplorerFormTaxonfilter" name="taxonfilter" value='<?php echo $taxonFilter; ?>' />
            <input type="hidden" name="interface" value='checklist' />
        </form>
	<?php
	}
	?>
    </div>
	<div style='margin-bottom:10px;float:left;'>
        Taxonomic Filter:
        <select id="taxonfilter" name="taxonfilter" onchange="getTaxaList(this.value);">
            <option value="">Raw Data</option>
            <?php
            $taxonAuthList = $checklistManager->getTaxonAuthorityList();
            foreach($taxonAuthList as $taCode => $taValue){
                echo "<option value='".$taCode."' ".((int)$taCode === $taxonFilter? 'SELECTED' : ''). '>' .$taValue. '</option>';
            }
            ?>
        </select>
	</div>
	<div style="clear:both;"><hr/></div>
	<?php
		echo '<div style="font-weight:bold;font-size:125%;">Taxa Count: '.$taxaCnt.'</div>';
		$undFamilyArray = array();
		if(array_key_exists('undefined',$checklistArr)){
			$undFamilyArray = $checklistArr['undefined'];
			unset($checklistArr['undefined']);
		}
		ksort($checklistArr);
		foreach($checklistArr as $family => $sciNameArr){
			sort($sciNameArr);
			echo '<div style="margin-left:5px;margin-top:5px;"><h3>'.$family.'</h3></div>';
			foreach($sciNameArr as $sciName){
				echo '<div style="margin-left:20px;font-style:italic;"><a target="_blank" href="../../taxa/index.php?taxon='.$sciName.'">'.$sciName.'</a></div>';
			}
		}
		if($undFamilyArray){
			echo '<div style="margin-left:5px;margin-top:5px;"><h3>Family Not Defined</h3></div>';
			foreach($undFamilyArray as $sciName){
				echo '<div style="margin-left:20px;font-style:italic;"><a target="_blank" href="../../taxa/index.php?taxon='.$sciName.'">'.$sciName.'</a></div>';
			}
		}
	?>
</div>
