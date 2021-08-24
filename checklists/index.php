<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']):'';

$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Species Lists</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script src="../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        function openSpatialViewerWindow(coordArrJson) {
            let mapWindow = open("../spatial/viewerWindow.php?coordJson=" + coordArrJson,"Spatial Viewer","resizable=0,width=800,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }
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
							<?php
                            if(array_key_exists('coords',$projArr)){
                                ?>
                                <a href="#" onclick="openSpatialViewerWindow('<?php echo $projArr['coords']; ?>');" title='Show checklists on map'>
                                    <i style='width:15px;height:15px;' class="fas fa-globe"></i>
                                </a>
                                <?php
                            }
                            ?>
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
