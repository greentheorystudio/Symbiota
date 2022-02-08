<?php 
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ImageLibraryManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$pManager = new ImageLibraryManager();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Photographer List</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<meta name='keywords' content='' />
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt; 
		<a href="index.php">Image Library</a> &gt;&gt; 
		<b>Image contributors</b> 
	</div>

	<div id="innertext">
		<?php
		$pList = $pManager->getPhotographerList();
		if($pList){
			echo '<div style="float:left;;margin-right:40px;">';
			echo '<h2>Image Contributors</h2>';
			echo '<div style="margin-left:15px">';
			foreach($pList as $uid => $pArr){
				echo '<div>';
				$phLink = 'search.php?imagedisplay=thumbnail&imagetype=all&phuidstr='.$uid.'&phjson=[{'.urlencode('"name":"'.$pArr['fullname'].'","id":"'.$uid.'"').'}]&submitaction=Load Images';
				echo '<a href="'.$phLink.'">'.$pArr['name'].'</a> ('.$pArr['imgcnt'].')</div>';
			}
			echo '</div>';
			echo '</div>';
		}
		?>

		<div style="float:left">
			<?php
			$collList = $pManager->getCollectionImageList();
			if($collList){
                $specList = $collList['coll'];
                if($specList){
                    echo '<h2>Specimens</h2>';
                    echo '<div style="margin-left:15px;margin-bottom:20px">';
                    foreach($specList as $k => $cArr){
                        echo '<div>';
                        $phLink = 'search.php?nametype=2&taxtp=2&imagecount=all&imagedisplay=thumbnail&imagetype=all&submitaction=Load%20Images&db[]='.$k;
                        echo '<a href="'.$phLink.'">'.$cArr['name'].'</a> ('.$cArr['imgcnt'].')</div>';
                    }
                    echo '</div>';
                }

                $obsList = $collList['obs'];
                if($obsList){
                    echo '<h2>Observations</h2>';
                    echo '<div style="margin-left:15px">';
                    foreach($obsList as $k => $cArr){
                        echo '<div>';
                        $phLink = 'search.php?nametype=2&taxtp=2&imagecount=all&imagedisplay=thumbnail&imagetype=all&submitaction=Load%20Images&db[]='.$k;
                        echo '<a href="'.$phLink.'">'.$cArr['name'].'</a> ('.$cArr['imgcnt'].')</div>';
                    }
                    echo '</div>';
                }
            }
			?>
		</div>
	</div>
	<?php 
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
