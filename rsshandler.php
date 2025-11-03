<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/services/RssService.php');

header('Content-Description: ' . $GLOBALS['DEFAULT_TITLE'] . ' Collections RSS Feed');
header('Content-Type: text/xml; charset=utf-8');

echo (new RssService)->getPortalCollectionRss();
