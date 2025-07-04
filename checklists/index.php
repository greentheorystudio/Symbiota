<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']):'';

$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklists</title>
    <meta name="description" content="Checklist index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
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
	echo '<div id="breadcrumbs">';
	echo "<a href='../index.php'>Home</a> &gt;&gt; ";
	echo ' <b>Checklists</b>';
	echo '</div>';
	?>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<h2>Checklists</h2>
        <div style='margin:20px;'>
			<?php 
            $researchArr = $clManager->getChecklists();
			if($researchArr){
				foreach($researchArr as $pid => $projArr){
					?>
					<div style='margin:3px 0 0 15px;'>
						<?php
                        if(array_key_exists('name',$projArr)){
                            ?>
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
                            <?php
                        }
                        ?>
						<div>
							<ul>
								<?php 
								foreach($projArr['clid'] as $clid => $clName){
									echo "<li><a href='checklist.php?clid=".$clid."'>".$clName."</a></li>\n";
								}
								?>
							</ul>
						</div>
					</div>
					<?php
				}
			}
			else{
				echo '<div><b>There are no checklists available at this time.</b></div>';
			}
			?>
		</div>
	</div>
	<?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
