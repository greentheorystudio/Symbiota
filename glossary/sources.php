<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GlossaryManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$tid = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:0;
$searchTerm = array_key_exists('keyword',$_REQUEST)?$_REQUEST['keyword']:'';
$language = array_key_exists('language',$_REQUEST)?$_REQUEST['language']:'';
$taxa = array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:'';
$editMode = array_key_exists('emode',$_REQUEST)?1:0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$glosManager = new GlossaryManager();
$sourceArr = $glosManager->getTaxonSources($tid);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary Sources Management</title>
    <meta name="description" content="Manage glossary sources for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/glossary.index.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
	?>
	<div class='navpath'>
		<a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
		<a href='index.php'> <b>Main Glossary</b></a> &gt;&gt;
		<b>Glossary Contributors</b>
	</div>
	<div id="main-container">
		<?php
		if($editMode){
			if($isEditor){
				?>
				<div id="sourcedetaildiv" style="">
					<div id="termdetails" style="overflow:auto;">
						<form name="sourceeditform" action="index.php" method="post">
							<div style="padding-top:4px">
								<div>
									<b>Terms and Definitions contributed by: </b>
								</div>
								<div>
									<textarea name="contributorTerm" id="contributorTerm" rows="10" maxlength="1000" style="width:95%;height:40px;resize:vertical;" ><?php echo ($sourceArr?$sourceArr[$tid]['contributorTerm']:''); ?></textarea>
								</div>
							</div>
							<div style="padding-top:4px;">
								<div>
									<b>Images contributed by: </b>
								</div>
								<div>
									<textarea name="contributorImage" id="contributorImage" rows="10" maxlength="1000" style="width:95%;height:40px;resize:vertical;" ><?php echo ($sourceArr?$sourceArr[$tid]['contributorImage']:''); ?></textarea>
								</div>
							</div>
							<div style="padding-top:4px;">
								<div>
									<b>Translations by: </b>
								</div>
								<div>
									<textarea name="translator" id="translator" rows="10" maxlength="1000" style="width:95%;height:40px;resize:vertical;" ><?php echo ($sourceArr?$sourceArr[$tid]['translator']:''); ?></textarea>
								</div>
							</div>
							<div style="padding-top:4px;">
								<div>
									<b>Translations and images were also sourced from the following references: </b>
								</div>
								<div>
									<textarea name="additionalSources" id="additionalSources" rows="10" maxlength="1000" style="width:95%;height:150px;resize:vertical;" ><?php echo ($sourceArr?$sourceArr[$tid]['additionalSources']:''); ?></textarea>
								</div>
							</div>
							<div>
								<input name="tid" type="hidden" value="<?php echo $tid; ?>" />
								<input name="searchterm" type="hidden" value="<?php echo $searchTerm; ?>" />
								<input name="searchlanguage" type="hidden" value="<?php echo $language; ?>" />
								<input name="searchtaxa" type="hidden" value="<?php echo $taxa; ?>" />
							</div>
							<?php 
							if($sourceArr){
								?>
								<div style="margin:20px;">
									<button name="formsubmit" type="submit" value="Edit Source">Save Edits</button>
								</div>
								<div style="margin:20px;">
									<button name="formsubmit" type="submit" value="Delete Source" onclick="return confirm('Are you sure you want to delete this source?')">Delete Source</button>
								</div>
								<?php 
							}
							else{
								echo '<div style="margin:20px;"><button name="formsubmit" type="submit" value="Add Source">Add Source</button></div>';
							}
							?>
						</form>
					</div>
				</div>
				<?php 
			}
			else{
				echo '<h2>You need to login or perhaps do not have the necessary permissions to edit glossary data, please contact your portal manager</h2>';
			}
		}
		else if($sourceArr){
            echo '<h1>Contributors</h1>';
            foreach($sourceArr as $tid => $sArr){
                echo '<div style="margin:25px 10px 0 10px;"><i><b><u>'.$sArr['sciname'].'</u></b></i></div>';
                if($sArr['contributorTerm']){
                    echo '<div style="margin:8px 10px 0 20px;"><i>Terms and Definitions contributed by:</i></div>';
                    $termArr = explode(';', $sArr['contributorTerm']);
                    foreach($termArr as $term){
                        $term = '<b>'.str_replace('-', '</b>-', $term).(strpos($term,'-')?'':'</b>');
                        echo '<div style="margin:8px 10px 0 30px;">'.$term.'</div>';
                    }
                }
                if($sArr['contributorImage']){
                    echo '<div style="margin:8px 10px 0 20px;"><i>Images contributed by:</i></div>';
                    $termArr = explode(';', $sArr['contributorImage']);
                    foreach($termArr as $term){
                        $term = '<b>'.str_replace('-', '</b>-', $term).(strpos($term,'-')?'':'</b>');
                        echo '<div style="margin:8px 10px 0 30px;">'.$term.'</div>';
                    }
                }
                if($sArr['translator']){
                    echo '<div style="margin:8px 10px 0 20px;"><i>Translations by:</i></div>';
                    $termArr = explode(';', $sArr['translator']);
                    foreach($termArr as $term){
                        $term = '<b>'.str_replace('-', '</b>-', $term).(strpos($term,'-')?'':'</b>');
                        echo '<div style="margin:8px 10px 0 30px;">'.$term.'</div>';
                    }
                }
                if($sArr['additionalSources']){
                    echo '<div style="margin-top:8px;margin-left:10px;padding: 0 10px;"><i>Translations and images were also sourced from the following references:</i></div>';
                    echo '<div style="margin-top:8px;margin-left:20px;padding: 0 10px;">'.$sArr['additionalSources'].'</div>';
                }
            }
        }
        else{
            echo '<div>Contributor list is not available</div>';
        }
		?>
	</div>
	<?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
