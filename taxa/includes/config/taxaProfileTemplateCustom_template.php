<?php
/*
 ******  Create a custom template below to add to your taxon profile pages  ********************************************
 *
 * DEFAULT TEMPLATE:
 *
 * if($taxonRank){
 *      if($taxonRank > 180){
 *          $topRowElements = array($editButtonDiv,$scinameHeaderDiv,$ambiguousDiv);
 *          $leftColumnElements = array($familyDiv,$vernacularsDiv,$synonymsDiv,$centralImageDiv);
 *          $rightColumnElements = array($descTabsDiv);
 *          $bottomRowElements = array($mapThumbDiv,$imgDiv,$imgTabDiv);
 *          $footerRowElements = array();
 *      }
 *      elseif($taxonRank == 180){
 *          $topRowElements = array();
 *          $leftColumnElements = array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
 *          $rightColumnElements = array($editButtonDiv,$descTabsDiv);
 *          $bottomRowElements = array($imgBoxDiv);
 *          $footerRowElements = array();
 *      }
 *      else{
 *          $topRowElements = array();
 *          $leftColumnElements = array($scinameHeaderDiv,$familyDiv,$projectDiv,$centralImageDiv);
 *          $rightColumnElements = array($editButtonDiv,$descTabsDiv);
 *          $bottomRowElements = array($imgBoxDiv);
 *          $footerRowElements = array();
 *      }
 *  }
 *  elseif($taxonValue){
 *      $topRowElements = array($notFoundDiv);
 *  }
 *  else{
 *      $topRowElements = array('ERROR!');
 *  }
 *
 * ******  Add custom plugins defined in the taxaProfileElementsCustom file  ********************************************
 *
 * EXAMPLE:
 * $topRowElements = array($pluginName);
 *
 ***********************************************************************************************************************
 *
 */

//Enter one to many custom cascading style sheet files 
//$CSSARR = array('example1.css','example2.css');

//Enter one to many custom javascript files
//$JSARR = array('example1.js','example2.js'); 
/** @var int $taxonRank */
/** @var string $taxonValue */
include(__DIR__ . '/taxaProfileElementsDefault.php');
if(file_exists('includes/config/taxaProfileElementsCustom.php')){
    include(__DIR__ . '/taxaProfileElementsCustom.php');
}

$topRowElements = array(); //Top horizontal bar in taxon profile page
$leftColumnElements = array(); //Left column below top horizontal bar in taxon profile page
$rightColumnElements = array(); //Right column below top horizontal bar in taxon profile page
$bottomRowElements = array(); //Horizontal bar below left and right columns in taxon profile page
$footerRowElements = array(); //Bottom horizontal bar in taxon profile page

if($taxonRank){
    if($taxonRank > 180){ //Template for taxa pages below the genus rank.
        $topRowElements = array();
        $leftColumnElements = array();
        $rightColumnElements = array();
        $bottomRowElements = array();
        $footerRowElements = array();
    }
    elseif($taxonRank == 180){ //Template for genera taxa pages.
        $topRowElements = array();
        $leftColumnElements = array();
        $rightColumnElements = array();
        $bottomRowElements = array();
        $footerRowElements = array();
    }
    else{  //Template for taxa pages above the genus rank.
        $topRowElements = array();
        $leftColumnElements = array();
        $rightColumnElements = array();
        $bottomRowElements = array();
        $footerRowElements = array();
    }
}
elseif($taxonValue){
    $topRowElements = array();
}
else{
    $topRowElements = array('ERROR!');
}
