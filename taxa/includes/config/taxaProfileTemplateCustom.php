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
/** @var string $IRLNativeStatus */
/** @var string $IRLVernacularsDiv */
/** @var string $IRLSynonymsDiv */
/** @var string $IRLCentralImageDiv */
/** @var string $IRLDescTabsDiv */
/** @var string $IRLImgDiv */

//Enter one to many custom cascading style sheet files 
//$CSSARR = array('example1.css','example2.css');

//Enter one to many custom javascript files
//$JSARR = array('example1.js','example2.js'); 

include('includes/config/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include('includes/config/taxaProfileElementsCustom.php');
}

$topRowElements = array(); //Top horizontal bar in taxon profile page
$leftColumnElements = array(); //Left column below top horizontal bar in taxon profile page
$rightColumnElements = array(); //Right column below top horizontal bar in taxon profile page
$bottomRowElements = array(); //Horizontal bar below left and right columns in taxon profile page
$footerRowElements = array(); //Bottom horizontal bar in taxon profile page

if($taxonRank){
    if($taxonRank > 180){
        $topRowElements = Array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv,$webLinksDiv);
        $leftColumnElements = Array($taxonNotesDiv,$taxonSourcesDiv,$familyDiv,$IRLNativeStatus,$IRLVernacularsDiv,$IRLSynonymsDiv,$IRLCentralImageDiv);
        $rightColumnElements = Array($IRLDescTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$IRLImgDiv,$imgTabDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    elseif($taxonRank == 180){
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$IRLCentralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    elseif($taxonRank >= 140){
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$IRLCentralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    else{
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$IRLCentralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($IRLImgDiv,$imgTabDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
}
elseif($taxonValue){
    $topRowElements = Array($notFoundDiv);
}
else{
    $topRowElements = Array('ERROR!');
}
