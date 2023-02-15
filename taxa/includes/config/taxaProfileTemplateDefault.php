<?php
if($taxonRank){
    if($taxonRank > 180){
        $bottomRowElements = array($mapThumbDiv,$imgDiv,$imgTabDiv);
    }
    elseif($taxonRank === 180){
        $bottomRowElements = array($imgBoxDiv);
    }
    else{
        $bottomRowElements = array($imgBoxDiv);
    }
}
