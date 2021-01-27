<?php
ob_start();
echo '<div id="footerlinkstoggle">';
if($taxonRank > 180 && $links) {
    echo '<a href="#" onclick="toggleLinks(\'links\');return false;">Web Links</a>';
}

$starrValue = '{"usethes":true,"taxontype":"1","taxa":"'.$taxonManager->getSciName().'"}';
echo "<div style='margin-top:15px;text-align:center;nowrap;'>";
echo '<form name="occurrences" id="occurrences" action="../collections/list.php" method="post">';
echo "<input type='hidden' name='starr' value='".$starrValue."' />";
echo '<input type="hidden" name="page" value="1" />';
echo '</form>';

if($taxonRank > 140){
    $parentLink = 'index.php?taxon='.$taxonManager->getParentTid().'&taxauthid='.$taxAuthId;
    if($clValue) {
        $parentLink .= "&cl=" . $taxonManager->getClid();
    }
    if($projValue) {
        $parentLink .= "&proj=" . $projValue;
    }
    $taxonTerm = ((int)$taxonRank === 220?"Genus":"Family");
    echo "&nbsp;&nbsp;&nbsp;<a href='".$parentLink."'>View ".($taxonRank > 220?"Parent":$taxonTerm)."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo '&nbsp;&nbsp;&nbsp;<a href="#" onclick="document.getElementById(\'occurrences\').submit();">View Specimen Records</a>&nbsp;&nbsp;&nbsp;&nbsp;';
}
echo '</div>';

if($taxonRank > 180 && $links){
    echo '<div id="links" style="display:none;"><h1 id="linksbanner">Web Links</h1><ul id="linkslist">';
    foreach($links as $l){
        $urlStr = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$l['url']);
        echo '<li><a href="'.$urlStr.'" target="_blank">'.$l['title'].'</a></li>';
        if($l['notes']) {
            echo ' ' . $l['notes'];
        }
    }
    echo "</ul>\n</div>";
}
$wisFloraFooterLinksDiv = ob_get_clean();
