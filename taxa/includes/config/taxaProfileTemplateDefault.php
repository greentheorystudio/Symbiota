<?php
include('includes/config/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include('includes/config/taxaProfileElementsCustom.php');
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
    elseif($taxonRank == 180){
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
?>
