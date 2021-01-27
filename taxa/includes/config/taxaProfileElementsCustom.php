<?php
ob_start();
echo '<div id="footerlinkstoggle">';
if($taxonRank > 180){
    if($taxonRank > 180 && $links){
        echo '<a href="#" onclick="toggleLinks(\'links\');return false;">'.$LANG['WEB_LINKS'].'</a>';
    }
}

// View Parent Taxon (original code)
/*if($taxonRank > 140){
    $parentLink = "index.php?taxon=".$taxonManager->getParentTid()."&taxauthid=".$taxAuthId;
    if($clValue) $parentLink .= "&cl=".$taxonManager->getClid();
    if($projValue) $parentLink .= "&proj=".$projValue;
    echo '<a href="'.$parentLink.'" class="parentlink">'.$LANG['VIEW_PARENT'].'</a>';
}
*/

/* START OF Robert's code for specimen navigation */
/* --------------------------------------------------- */

// Adds hidden form that searches for occurrences within taxon
echo "<div style='margin-top:15px;text-align:center;nowrap;'>";
echo '<form name="occurrences" id="occurrences" action="../collections/list.php" method="post">';
echo '<input type="hidden" name="taxontype" value="3" />';
echo '<input type="hidden" name="reset" value="1" />';
echo '<input type="hidden" name="taxonoccurrences" value="1" />';
echo '<input type="hidden" name="taxa" value="'.$taxonManager->getTid().'" />';
echo '</form>';

// Adds more images link for records with more than 5 imgs
// WORK ON THIS
/*if($taxonRank > 180){
    if($taxonManager->getimageCount() > 5){
        ?>
        <span id="moreimages">
            <a href="#" onclick="expandExtraImages();return false;">More Images</a>
        </span>
        <?php
    }
}*/

// Gets parent taxon link (and parent taxon category) and adds view specimen records link
if($taxonRank > 140){
    $parentLink = "index.php?taxon=".$taxonManager->getParentTid()."&taxauthid=".$taxAuthId;
    if($clValue) $parentLink .= "&cl=".$taxonManager->getClid();
    if($projValue) $parentLink .= "&proj=".$projValue;
    echo "&nbsp;&nbsp;&nbsp;<a href='".$parentLink."'>View ".($taxonRank > 220?"Parent":($taxonRank == 220?"Genus":"Family"))."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo '&nbsp;&nbsp;&nbsp;<a href="#" onclick=document.getElementById("occurrences").submit();return false;>View Specimen Records</a>&nbsp;&nbsp;&nbsp;&nbsp;';
}

/* END OF Robert's code for specimen navigation */
/* --------------------------------------------------- */


//Search tool failing to open properly; will debug shortly
//echo "<a href='../imagelib/search.php?nametype=1&imagedisplay=thumbnail&taxastr=".$taxonManager->getSciName()."&submitaction=Load+Images' style='margin-left:30px;'>Open Image Search Tool</a>";
echo "</div>";

//List Web Links as a list
if($taxonRank > 180 && $links){
    echo '<div id="links" style="display:none;"><h1 id="linksbanner">'.$LANG['WEB_LINKS'].'</h1><ul id="linkslist">';
    foreach($links as $l){
        $urlStr = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$l['url']);
        echo '<li><a href="'.$urlStr.'" target="_blank">'.$l['title'].'</a></li>';
        if($l['notes']) echo ' '.$l['notes'];
    }
    echo "</ul>\n</div>";
}
$wisFloraFooterLinksDiv = ob_get_clean();
