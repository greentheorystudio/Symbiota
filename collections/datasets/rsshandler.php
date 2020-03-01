<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');

$dwcaManager = new DwcArchiverCore();

header('Content-Description: '.$DEFAULT_TITLE.' Collections RSS Feed');
header('Content-Type: text/xml; charset=utf-8');

echo $dwcaManager->getFullRss();
