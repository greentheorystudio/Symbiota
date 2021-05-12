<?php
/** @var int $taxonRank */
/** @var string $editButtonDiv */
/** @var string $penaButtonsDiv */
/** @var string $penaScinameHeaderDiv */
/** @var string $ambiguousDiv */
/** @var string $webLinksDiv */
/** @var string $taxonNotesDiv */
/** @var string $taxonSourcesDiv */
/** @var string $familyDiv */
/** @var string $penaVernacularsDiv */
/** @var string $synonymsDiv */
/** @var string $centralImageDiv */
/** @var string $penaCentralImageDiv */
/** @var string $penaDescTabsDiv */
/** @var string $mapThumbDiv */
/** @var string $penaImgDiv */
/** @var string $penaImgTabDiv */
/** @var string $footerLinksDiv */
/** @var string $projectDiv */
/** @var string $imgBoxDiv */
/** @var string $taxonValue */
/** @var string $notFoundDiv */

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
        $topRowElements = Array($penaScinameHeaderDiv,$ambiguousDiv,$editButtonDiv);
        $leftColumnElements = Array($familyDiv,$penaVernacularsDiv,$synonymsDiv,$penaButtonsDiv,$penaCentralImageDiv);
        $rightColumnElements = Array($penaDescTabsDiv);
        $bottomRowElements = Array($penaImgDiv,$penaImgTabDiv);
        $footerRowElements = Array();
    }
    elseif($taxonRank === 180 || $displayingChildren){
        $topRowElements = Array($penaScinameHeaderDiv,$editButtonDiv);
        $leftColumnElements = Array($familyDiv,$penaVernacularsDiv,$penaButtonsDiv,$centralImageDiv);
        $rightColumnElements = Array($penaDescTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array();
    }
    else{
        $topRowElements = Array($penaScinameHeaderDiv,$editButtonDiv);
        $leftColumnElements = Array($familyDiv,$penaVernacularsDiv,$penaButtonsDiv,$centralImageDiv);
        $rightColumnElements = Array($penaDescTabsDiv);
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
