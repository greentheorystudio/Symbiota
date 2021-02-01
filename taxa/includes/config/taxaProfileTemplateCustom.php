<?php
$displayingChildren = array_key_exists('displaychildren',$_REQUEST)?$_REQUEST['displaychildren']: 0;

include('includes/config/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include('includes/config/taxaProfileElementsCustom.php');
}

$topRowElements = Array();
$leftColumnElements = Array();
$rightColumnElements = Array();
$bottomRowElements = Array();
$footerRowElements = Array();

if($taxonRank){
    if($taxonRank > 180 && !$displayingChildren){
        $topRowElements = Array($penaScinameHeaderDiv,$ambiguousDiv);
        $leftColumnElements = Array($penaVernacularsDiv,$penaButtonsDiv,$penaCentralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = Array();
    }
    elseif($taxonRank === 180 || $displayingChildren){
        $topRowElements = Array($editButtonDiv,$penaScinameHeaderDiv);
        $leftColumnElements = Array($penaVernacularsDiv,$penaButtonsDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array();
    }
    else{
        $topRowElements = Array($editButtonDiv,$penaScinameHeaderDiv);
        $leftColumnElements = Array($penaVernacularsDiv,$penaButtonsDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array();
    }
}
elseif($taxonValue){
    $topRowElements = Array($notFoundDiv);
}
else{
    $topRowElements = Array('ERROR!');
}
?>
