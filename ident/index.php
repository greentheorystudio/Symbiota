<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$proj = array_key_exists('proj',$_REQUEST)?$_REQUEST['proj']: '';

if(!$proj && isset($DEFAULT_PROJ_ID)) {
    $proj = $DEFAULT_PROJ_ID;
}

$clManager = new ChecklistManager();
$clManager->setProj($proj);
$pid = $clManager->getPid();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Identification Keys</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
</head>

<body>
	<?php
    include(__DIR__ . '/../header.php');
	?>
	
	<div id="innertext">
		<h2>Identification Keys</h2>
	    <div style='margin:20px;'>
	        <?php
	        $clList = $clManager->getChecklists();
			if($clList){
				$projName = $clList['name'];
				$clArr = $clList['clid'];
				echo '<div style="margin:3px 0 0 15px;">';
				echo '<h3>'.$projName.'</h3>';
				echo '<div><ul>';
				foreach($clArr as $clid => $clName){
					echo "<li><a href='key.php?cl=$clid&proj=$pid&taxon=All+Species'>".$clName. '</a></li>';
				}
				echo '</ul></div>';
				echo '</div>';
			}
			?>
		</div>
	</div>
	<?php 
		include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
