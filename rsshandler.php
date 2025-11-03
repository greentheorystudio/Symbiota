<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/services/RssService.php');

$feed = array_key_exists('feed', $_REQUEST) ? $_REQUEST['feed'] : '';

header('Content-Type: text/xml; charset=utf-8');

if($feed === 'collection'){
    echo (new RssService)->getPortalCollectionRss();
}
