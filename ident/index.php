<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$proj = array_key_exists("proj",$_REQUEST)?$_REQUEST["proj"]:"";
if(!$proj && isset($DEFAULT_PROJ_ID)) $proj = $DEFAULT_PROJ_ID;

$clManager = new ChecklistManager();
$clManager->setProj($proj);
$pid = $clManager->getPid();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Identification Keys</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
</head>

<body>
	<?php
    include($SERVER_ROOT.'/header.php');
	?>
	
	<!-- This is inner text! -->
	<div id="innertext">
		<h2>Identification Keys</h2>
	    <div style='margin:20px;'>
	        <?php
	        $clList = $clManager->getChecklists();
			if($clList){
				$projName = $clList['name'];
				$clArr = $clList['clid'];
				echo '<div style="margin:3px 0px 0px 15px;">';
				echo '<h3>'.$projName;
				echo ' <a href="../checklists/clgmap.php?proj='.$pid.'&target=keys"><img src="../images/world.png" style="width:10px;border:0" /></a>';
				echo '</h3>';
				echo "<div><ul>";
				foreach($clArr as $clid => $clName){
					echo "<li><a href='key.php?cl=$clid&proj=$pid&taxon=All+Species'>".$clName."</a></li>";
				}
				echo "</ul></div>";
				echo "</div>";
			}
			?>
		</div>
	</div>
	<?php 
		include($SERVER_ROOT.'/footer.php');
	?>
</body>
</html>
