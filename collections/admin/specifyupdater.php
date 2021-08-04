<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecifyManager.php');
header("Content-Type: text/html; charset=".$GLOBALS['CHARSET']);
ini_set('max_execution_time', 6000);

$limit = 10000;
$index = array_key_exists("index",$_POST)?$_POST["index"]:1;
$specifyTotal = array_key_exists("specifytotal",$_POST)?$_POST["specifytotal"]:0;
$action = array_key_exists("action",$_POST)?$_POST["action"]:0;

$uploadManager = new SpecifyManager();

$fromValue = ((($index - 1) * $limit) + 1);
$toValue = $index * $limit;

if(!$specifyTotal){
    $specifyTotal = $uploadManager->getSpecifyTotal();
}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['CHARSET']; ?>">
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Specify Importer</title>
		<link href="../../css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="../../js/jquery.js?ver=20130917"></script>
        <script type="text/javascript" src="../../js/jquery-ui.js?ver=20130917"></script>
		<script src="../../js/symb/shared.js" type="text/javascript"></script>
        <script type="text/javascript">
            <?php
                if($action === 'Upload' && $fromValue < $specifyTotal){
                    ?>
                    $(document).ready(function() {
                        submitForm();
                    });
                    <?php
                }
            ?>

            function submitForm(){
                document.getElementById('importerform').submit();
            }
        </script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div id="innertext">
			<?php
			if($GLOBALS['IS_ADMIN']){
				echo "<h1>Specify Importer Module</h1>";
                if($action === 'Upload'){
                    echo '<ul>';
                    $uploadManager->uploadSpecifyRecords($limit,$index);
                    echo '</ul>';
                }
                ?>
                <form name="importerform" id="importerform" action="specifyupdater.php" method="post">
                    <fieldset style="width:450px;">
                        <legend style="font-weight:bold;font-size:120%;">Uploader</legend>
                        <div style= "margin-top:10px;">
                            <?php
                            if($toValue < $specifyTotal){
                                echo 'Upload records ' . $fromValue . ' to ' . $toValue . ' of ' . $specifyTotal . ' total.';
                            }
                            else{
                                echo 'Upload complete.';
                            }
                            ?>
                        </div>
                        <?php
                        if($toValue < $specifyTotal){
                            ?>
                            <div style="margin:10px;">
                                <input type="hidden" name="index" value="<?php echo $index + 1; ?>" />
                                <input type="hidden" name="specifytotal" value="<?php echo $specifyTotal; ?>" />
                                <input type="hidden" name="action" value="Upload" />
                                <input type="submit" />
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                </form>
                <?php
			}
			else if(!$GLOBALS['SYMB_UID']){
                header("Location: ../../profile/index.php?refurl=../collections/admin/specifyupdater.php");
            }
            else{
                echo '<h2>You do not have permissions to update collections.</h2>';
            }
			?>
		</div>
		<?php 
		include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
