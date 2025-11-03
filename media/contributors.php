<?php 
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ImageLibraryManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$pManager = new ImageLibraryManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Contributor List</title>
    <meta name="description" content="Image contributor list for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
	?>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
        <div id="breadcrumbs">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" tabindex="0">Home</a> &gt;&gt;
            <a href="index.php" tabindex="0">Image Library</a> &gt;&gt;
            <b>Image contributors</b>
        </div>
        <?php
        $pList = $pManager->getPhotographerList();
        if($pList){
            echo '<div style="float:left;;margin-right:40px;">';
            echo '<h2>Image Contributors</h2>';
            echo '<div style="margin-left:15px">';
            foreach($pList as $uid => $pArr){
                echo '<div>';
                $phLink = 'search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"phuid":"'.$uid.'"}';
                echo "<a href='".$phLink."'>".$pArr['name'].'</a> ('.$pArr['imgcnt'].')</div>';
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
                        $phLink = 'search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"db":"'.$k.'"}';
                        echo "<a href='".$phLink."'>".$cArr['name'].'</a> ('.$cArr['imgcnt'].')</div>';
                    }
                    echo '</div>';
                }

                $obsList = array_key_exists('obs',$collList) ? $collList['obs'] : array();
                if($obsList){
                    echo '<h2>Observations</h2>';
                    echo '<div style="margin-left:15px">';
                    foreach($obsList as $k => $cArr){
                        echo '<div>';
                        $phLink = 'search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"db":"'.$k.'"}';
                        echo "<a href='".$phLink."'>".$cArr['name'].'</a> ('.$cArr['imgcnt'].')</div>';
                    }
                    echo '</div>';
                }
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
