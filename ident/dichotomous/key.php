<?php 
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
include_once(__DIR__ . '/../../classes/DichoKeyManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$taxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']: '';

$dichoKeyManager = new DichoKeyManager();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
<title><?php echo $DEFAULT_TITLE; ?> Dichotomous Key</title>
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<meta name='keywords' content='' />
	<script>
        let cseXmlHttp;
        let targetStr;

        function toggle(target){
            let obj;
            const divObjs = document.getElementsByTagName("div");
            for (let i = 0; i < divObjs.length; i++) {
                obj = divObjs[i];
                if(obj.getAttribute("class") === target || obj.getAttribute("className") === target){
					if(obj.style.display === "none"){
						obj.style.display="block";
					}
					else {
						obj.style.display="none";
					}
				}
			}

            const spanObjs = document.getElementsByTagName("span");
            for (let i = 0; i < spanObjs.length; i++) {
                obj = spanObjs[i];
                if(obj.getAttribute("class") === target || obj.getAttribute("className") === target){
					if(obj.style.display === "none"){
						obj.style.display="inline";
					}
					else {
						obj.style.display="none";
					}
				}
			}
		}
    </script>
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
