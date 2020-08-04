<?php
/*
 ******  Create a custom template below to add to your taxon profile pages  ********************************************
 *
 * DEFAULT TEMPLATE:
 *
 * if($taxonRank){
 *      if($taxonRank > 180){
 *          $topRowElements = Array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv,$webLinksDiv);
 *          $leftColumnElements = Array($familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
 *          $rightColumnElements = Array($descTabsDiv);
 *          $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
 *          $footerRowElements = Array($footerLinksDiv);
 *      }
 *      elseif($taxonRank == 180){
 *          $topRowElements = array();
 *          $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
 *          $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
 *          $bottomRowElements = Array($imgBoxDiv);
 *          $footerRowElements = Array($footerLinksDiv);
 *      }
 *      else{
 *          $topRowElements = array();
 *          $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
 *          $rightColumnElements = Array($editButtonDiv,$descTabsDiv);
 *          $bottomRowElements = Array($imgBoxDiv);
 *          $footerRowElements = Array($footerLinksDiv);
 *      }
 *  }
 *  elseif($taxonValue){
 *      $topRowElements = Array($notFoundDiv);
 *  }
 *  else{
 *      $topRowElements = Array('ERROR!');
 *  }
 *
 * ******  Add custom plugins defined in the taxaProfileElementsCustom file  ********************************************
 *
 * EXAMPLE:
 * $topRowElements = Array($pluginName);
 *
 ***********************************************************************************************************************
 *
 */

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
        $leftColumnElements = Array($taxonNotesDiv,$taxonSourcesDiv,$familyDiv,$IRLVernacularsDiv,$synonymsDiv,$centralImageDiv);
        $rightColumnElements = Array($IRLDescTabsDiv);
        $bottomRowElements = Array($mapThumbDiv,$imgDiv,$imgTabDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    elseif($taxonRank == 180){
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    elseif($taxonRank >= 140){
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$familyDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($imgBoxDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
    else{
        $topRowElements = array();
        $leftColumnElements = Array($scinameHeaderDiv,$taxonNotesDiv,$taxonSourcesDiv,$projectDiv,$centralImageDiv);
        $rightColumnElements = Array($editButtonDiv,$IRLDescTabsDiv);
        $bottomRowElements = Array($imgDiv,$imgTabDiv);
        $footerRowElements = Array($footerLinksDiv);
    }
}
elseif($taxonValue){
    $topRowElements = Array($notFoundDiv);
}
else{
    $topRowElements = Array('ERROR!');
}
