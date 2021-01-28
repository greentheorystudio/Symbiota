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
        $topRowElements = Array($editButtonDiv,$penaScinameHeaderDiv,$ambiguousDiv,$webLinksDiv);
        $leftColumnElements = Array($familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
        $rightColumnElements = Array($descTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = Array($wisFloraFooterLinksDiv);
    }
    elseif($taxonRank === 180 || $displayingChildren){
        $topRowElements = Array();
        $leftColumnElements = Array($penaScinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($wisFloraFooterLinksDiv);
    }
    else{
        $topRowElements = Array();
        $leftColumnElements = Array($penaScinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
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
