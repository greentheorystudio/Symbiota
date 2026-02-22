<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/services/RssService.php');
header('Content-Type: text/xml; charset=utf-8');

$feed = array_key_exists('feed', $_REQUEST) ? $_REQUEST['feed'] : '';

if($feed === 'collection'){
    echo (new RssService)->getPortalCollectionRss();
}
elseif($feed === 'dwc'){
    echo (new RssService)->getPortalDwcRss();
}
