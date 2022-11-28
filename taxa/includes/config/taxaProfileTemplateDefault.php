<?php
/** @var int $taxonRank */
/** @var string $editButtonDiv */
/** @var string $scinameHeaderDiv */
/** @var string $ambiguousDiv */
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
        $topRowElements = array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv);
        $leftColumnElements = array($taxonNotesDiv,$taxonSourcesDiv,$familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
        $rightColumnElements = array($descTabsDiv);
        $bottomRowElements = array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = array();
    }
    elseif($taxonRank === 180){
        $topRowElements = array();
        $leftColumnElements = array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = array($imgBoxDiv);
        $footerRowElements = array();
    }
    else{
        $topRowElements = array();
        $leftColumnElements = array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = array($editButtonDiv,$descTabsDiv);
        $bottomRowElements = array($imgBoxDiv);
        $footerRowElements = array();
    }
}
elseif($taxonValue){
    $topRowElements = array($notFoundDiv);
}
else{
    $topRowElements = array('ERROR!');
}
