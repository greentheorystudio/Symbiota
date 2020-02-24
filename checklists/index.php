<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:0;

$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Species Lists</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		<?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	</script>
</head>

<body>
    <?php
	include(__DIR__ . '/../header.php');
	echo "<div class='navpath'>";
	echo "<a href='../index.php'>Home</a> &gt;&gt; ";
	echo ' <b>Species Checklists</b>';
	echo '</div>';
	?>
	<div id="innertext">
		<h1>Species Checklists</h1>
        <div style='margin:20px;'>
			<?php 
            $researchArr = $clManager->getChecklists();
			if($researchArr){
				foreach($researchArr as $pid => $projArr){
					?>
					<div style='margin:3px 0 0 15px;'>
						<h3><?php echo $projArr['name']; ?> 
							<a href="<?php echo 'clgmap.php?proj=' .$pid; ?>" title='Show checklists on map'>
								<img src='../images/world.png' style='width:10px;border:0' />
							</a>
						</h3>
						<div>
							<ul>
								<?php 
								foreach($projArr['clid'] as $clid => $clName){
									echo "<li><a href='checklist.php?cl=".$clid."'>".$clName."</a></li>\n";
								}
								?>
							</ul>
						</div>
					</div>
					<?php
				}
			}
			else{
				echo '<div><b>No Checklists returned</b></div>';
			}
			?>
		</div>
	</div>
	<?php
		include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
