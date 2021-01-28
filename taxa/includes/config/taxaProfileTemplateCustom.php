<?php
include('includes/config/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include('includes/config/taxaProfileElementsCustom.php');
}

$parentTaxon = array_key_exists('parenttaxon',$_REQUEST)?$_REQUEST['parenttaxon']: '';
$displayingChildren = 0;

if($parentTaxon) {
    $taxonManager->setTaxon($parentTaxon);
    $taxonManager->setAttributes();
    $displayingChildren = 1;
}

$topRowElements = Array();
$leftColumnElements = Array();
$rightColumnElements = Array();
$bottomRowElements = Array();
$footerRowElements = Array();

if($taxonRank){
    if($taxonRank > 180 && !$displayingChildren){
        $topRowElements = Array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv,$webLinksDiv);
        $leftColumnElements = Array($familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
        $rightColumnElements = Array($descTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = Array($wisFloraFooterLinksDiv);
    }
    elseif($taxonRank == 180){
        $topRowElements = Array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($wisFloraFooterLinksDiv);
    }
    else{
        $topRowElements = Array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($wisFloraFooterLinksDiv);
    }
}
elseif($taxonValue){
    $topRowElements = Array($notFoundDiv);
}
else{
    $topRowElements = Array('ERROR!');
}
?>
