<?php
/** @var string $logProcessorPath */
/** @var string $dbMetadata */
/** @var string $sourcePathBase */
/** @var string $targetPathBase */
/** @var string $imgUrlBase */
/** @var int $webPixWidth */
/** @var int $tnPixWidth */
/** @var int $lgPixWidth */
/** @var int $webFileSizeLimit */
/** @var int $lgFileSizeLimit */
/** @var string $jpgQuality */
/** @var string $keepOrig */
/** @var string $createNewRec */
/** @var string $logTitle */
/** @var array $collArr */
date_default_timezone_set('America/Phoenix');

require_once(__DIR__ . '/ImageBatchConf.php');
if(file_exists('../../../config/symbini.php')){
	include_once(__DIR__ . '/../../../config/symbini.php');
	require_once(__DIR__ . '/../../../classes/ImageBatchProcessor.php');
}
elseif(isset($GLOBALS['SERVER_ROOT']) && $GLOBALS['SERVER_ROOT']){
	include_once(__DIR__ . '/../../../config/symbini.php');
	@include(__DIR__ . '/../../../collections/specprocessor/standalone_scripts/ImageBatchConnectionFactory.php');
	require_once(__DIR__ . '/../../../classes/ImageBatchProcessor.php');
}
else{
	@include(__DIR__ . '/ImageBatchConnectionFactory.php');
	require_once(__DIR__ . '/ImageBatchProcessor.php');
}

$imageProcessor = new ImageBatchProcessor();

if(isset($silent) && $silent) {
	$logMode = 2;
}
$imageProcessor->setLogMode($logMode);
if(!$logProcessorPath && $GLOBALS['LOG_PATH']) {
	$logProcessorPath = $GLOBALS['LOG_PATH'];
}
$imageProcessor->setLogPath($logProcessorPath);

$imageProcessor->setDbMetadata($dbMetadata);
$imageProcessor->setSourcePathBase($sourcePathBase);
$imageProcessor->setTargetPathBase($targetPathBase);
$imageProcessor->setImgUrlBase($imgUrlBase);
$imageProcessor->setServerRoot($GLOBALS['SERVER_ROOT']);
if($webPixWidth) {
	$imageProcessor->setWebPixWidth($webPixWidth);
}
if($tnPixWidth) {
	$imageProcessor->setTnPixWidth($tnPixWidth);
}
if($lgPixWidth) {
	$imageProcessor->setLgPixWidth($lgPixWidth);
}
if($webFileSizeLimit) {
	$imageProcessor->setWebFileSizeLimit($webFileSizeLimit);
}
if($lgFileSizeLimit) {
	$imageProcessor->setLgFileSizeLimit($lgFileSizeLimit);
}
$imageProcessor->setJpgQuality($jpgQuality);

if(isset($webImg) && $webImg) {
	$imageProcessor->setWebImg($webImg);
}
elseif(isset($createWebImg) && $createWebImg) {
	$imageProcessor->setCreateWebImg($createWebImg);
}
if(isset($tnImg) && $tnImg) {
	$imageProcessor->setTnImg($tnImg);
}
elseif(isset($createTnImg) && $createTnImg) {
	$imageProcessor->setCreateTnImg($createTnImg);
}
if(isset($lgImg) && $lgImg) {
	$imageProcessor->setLgImg($lgImg);
}
elseif(isset($createLgImg) && $createLgImg) {
	$imageProcessor->setCreateLgImg($createLgImg);
}
$imageProcessor->setKeepOrig($keepOrig);
$imageProcessor->setCreateNewRec($createNewRec);
if(isset($imgExists)) {
	$imageProcessor->setImgExists($imgExists);
}
elseif(isset($copyOverImg)) {
	$imageProcessor->setCopyOverImg($copyOverImg);
}

$imageProcessor->initProcessor($logTitle);
$imageProcessor->setCollArr($collArr);

$imageProcessor->batchLoadImages();
