<?php 
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
include_once(__DIR__ . '/../../classes/DichoKeyManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$taxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']: '';

$dichoKeyManager = new DichoKeyManager();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dichotomous Key</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<meta name='keywords' content='' />
</head>
<body>
    <?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id="innertext">
		<?php 
		if($clid && $taxon){
			$dichoKeyManager->buildKey($clid,$taxon);
		}
		?>	
	
	</div>
	<?php 
		include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
