<?php
/** @var int $taxonRank */
/** @var string $editButtonDiv */
/** @var string $scinameHeaderDiv */
/** @var string $ambiguousDiv */
/** @var string $webLinksDiv */
/** @var string $taxonNotesDiv */
/** @var string $taxonSourcesDiv */
/** @var string $familyDiv */
/** @var string $vernacularsDiv */
/** @var string $synonymsDiv */
/** @var string $centralImageDiv */
/** @var string $descTabsDiv */
/** @var string $mapThumbDiv */
/** @var string $imgDiv */
/** @var string $imgTabDiv */
/** @var string $footerLinksDiv */
/** @var string $projectDiv */
/** @var string $imgBoxDiv */
/** @var string $taxonValue */
/** @var string $notFoundDiv */
include(__DIR__ . '/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include(__DIR__ . '/taxaProfileElementsCustom.php');
}

$topRowElements = array();
$leftColumnElements = array();
$rightColumnElements = array();
$bottomRowElements = array();
$footerRowElements = array();

if($taxonRank){
    if($taxonRank > 180){
        $topRowElements = Array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv,$webLinksDiv);
        $leftColumnElements = Array($taxonNotesDiv,$taxonSourcesDiv,$familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
        $rightColumnElements = Array($descTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    elseif($taxonRank === 180){
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    else{
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
}
elseif($taxonValue){
    $topRowElements = Array($notFoundDiv);
}
else{
    $topRowElements = Array('ERROR!');
}
