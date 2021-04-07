<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/FieldGuideManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$action = array_key_exists('action',$_POST)?$_POST['action']: '';
$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$taxon = array_key_exists('taxon',$_POST)?$_POST['taxon']:'';
$jobId = array_key_exists('jobid',$_POST)?$_POST['jobid']:0;

$apiManager = new FieldGuideManager();
$currentJobs = array();
$currentResults = array();
$currentCount = 0;
$statusStr = '';
$imagesExist = $apiManager->checkImages($collId);

$isEditor = 0;		 
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
		$isEditor = 1;
	}
}

if($isEditor){
    if($action === 'Initiate Process'){
        $apiManager->setCollID($collId);
        $apiManager->setTaxon($taxon);
        $statusStr = $apiManager->initiateFGBatchProcess();
    }
    if($action === 'Cancel Job'){
        $statusStr = $apiManager->cancelFGBatchProcess($collId,$jobId);
    }
    if($action === 'Delete Results'){
        $statusStr = $apiManager->deleteFGBatchResults($collId,$jobId);
    }
    $logData = $apiManager->checkFGLog($collId);
    if(isset($logData['jobs'])) {
        $currentJobs = $apiManager->processCurrentJobs($logData['jobs']);
    }
    if(isset($logData['results'])) {
        $currentResults = $logData['results'];
    }
    $currentCount = count($currentJobs);
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $collMetadata['collectionname']; ?> Fieldguide Batch Processing Utility</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            function split( val ) {
                return val.split( /,\s*/ );
            }
            function extractLast( term ) {
                return split( term ).pop();
            }

            if(document.getElementById("taxon")){
                $( "#taxon" )
                    .bind( "keydown", function( event ) {
                        if ( event.keyCode == $.ui.keyCode.TAB &&
                            $( this ).data( "autocomplete" ).menu.active ) {
                            event.preventDefault();
                        }
                    })
                    .autocomplete({
                        source: function( request, response ) {
                            $.getJSON( "rpc/speciessuggest.php", {
                                term: extractLast( request.term )
                            }, response );
                        },
                        search: function() {
                            const term = extractLast(this.value);
                            if ( term.length < 4 ) {
                                return false;
                            }
                        },
                        focus: function() {
                            return false;
                        },
                        select: function( event, ui ) {
                            const terms = split(this.value);
                            terms.pop();
                            terms.push( ui.item.value );
                            this.value = terms.join( ", " );
                            return false;
                        },
                        change: function (event, ui) {
                            if (!ui.item) {
                                this.value = '';
                            }
                        }
                    },
                {});
            }
        });
    </script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
    ?>
    <div class='navpath'>
        <a href='../../index.php'>Home</a> &gt;&gt;
        <a href='collprofiles.php?emode=1&collid=<?php echo $collId; ?>'>Collection Management</a> &gt;&gt;
        <b>Fieldguide Batch Processing</b>
    </div>

	<div id="innertext">
		<?php
        if($statusStr){
            ?>
            <hr/>
            <div style="margin:15px;color:red;">
                <?php echo $statusStr; ?>
            </div>
            <hr/>
            <?php
        }
        if($isEditor && $imagesExist){
            ?>
            <h1>Fieldguide Batch Processing</h1>
            <div style="margin:10px;">
                Use this dialogue to initiate, cancel, or view the results of a Fieldguide Batch Image identification process. Either type a parent taxon into the Parent
                Taxon box to initiate a batch image identification process for a particular taxonomic group, or leave the Parent Taxon box empty and click Initiate Process
                to intiate a batch image identification process for your whole collection. Processes that are currently being identified by Fieldguide will show up in the
                Current Jobs box. Once results are received from Fieldguide, the job will be moved to the Current Results box and you will be able to review the results.
            </div>
            <?php
            if($currentCount < 20){
                ?>
                <form action="fgbatch.php" method="post" style="" onsubmit="">
                    <fieldset style="margin: 15px auto 15px auto;width:600px;padding:15px;">
                        <div style="float:left;">
                            Parent Taxon: <input type="text" id="taxon" size="43" name="taxon" value="" />
                        </div>
                        <div style="float:right;">
                            <input type="submit" name="action" value="Initiate Process" />
                            <input type="hidden" name="collid" value="<?php echo $collId; ?>">
                        </div>
                    </fieldset>
                </form>
                <?php
            }
            if($currentJobs){
                ?>
                <div style="width:650px;margin-left:auto;margin-right:auto;">
                    <h2>Current Jobs:</h2>
                    <table class="styledtable" style="font-family:Arial,serif;font-size:12px;width:570px;margin-left:auto;margin-right:auto;">
                        <tr>
                            <th style="width:100px;">Date Initiated</th>
                            <th style="width:200px;">Status</th>
                            <th style="width:250px;">Parent Taxon</th>
                            <th style="width:20px;">Cancel</th>
                        </tr>
                        <?php
                        foreach($currentJobs as $job => $jArr){
                            $status = $jArr['status'];
                            $progress = $jArr['progress'];
                            echo '<tr>';
                            echo '<td>'.$jArr['date'].'</td>';
                            echo '<td>';
                            if($status) {
                                echo $status . ': ' . $progress['processed'] . ' of ' . $progress['total'] . ' complete';
                            }
                            echo '</td>';
                            echo '<td>'.$jArr['taxon'].'</td>';
                            echo '<td>';
                            echo '<form action="fgbatch.php" method="post">';
                            echo '<button style="margin:0;padding:2px;" type="submit"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
                            echo '<input type="hidden" name="action" value="Cancel Job">';
                            echo '<input type="hidden" name="collid" value="'.$collId.'">';
                            echo '<input type="hidden" name="jobid" value="'.$job.'">';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            if($currentResults){
                ?>
                <div style="width:650px;margin-left:auto;margin-right:auto;">
                    <h2>Current Results:</h2>
                    <table class="styledtable" style="font-family:Arial,serif;font-size:12px;width:570px;margin-left:auto;margin-right:auto;">
                        <tr>
                            <th style="width:100px;"> </th>
                            <th style="width:100px;">Date Initiated</th>
                            <th style="width:100px;">Date Received</th>
                            <th style="width:250px;">Parent Taxon</th>
                            <th style="width:20px;">Delete</th>
                        </tr>
                        <?php
                        foreach($currentResults as $job => $jArr){
                            echo '<tr>';
                            echo '<td><a href="fgresults.php?collid='.$collId.'&resid='.$job.'">View Results</a></td>';
                            echo '<td>'.$jArr['inidate'].'</td>';
                            echo '<td>'.$jArr['recdate'].'</td>';
                            echo '<td>'.$jArr['taxon'].'</td>';
                            echo '<td>';
                            echo '<form action="fgbatch.php" method="post" style="" onsubmit="">';
                            echo '<button style="margin:0;padding:2px;" title="Delete Results" type="submit"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
                            echo '<input type="hidden" name="action" value="Delete Results">';
                            echo '<input type="hidden" name="collid" value="'.$collId.'">';
                            echo '<input type="hidden" name="jobid" value="'.$job.'">';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            ?>
            <?php
		}
		elseif($isEditor && !$imagesExist){
            echo '<div style="font-weight:bold;font-size:120%;">';
            echo 'There are currently no linked image records for this collection.';
            echo '</div>';
        }
		else{
			echo '<div style="font-weight:bold;font-size:120%;">';
			echo 'Unauthorized to view this page. You must have administrative right for this collection.';
			echo '</div>';
		} 
		?>
	</div>
	<?php
		include(__DIR__ . '/../../footer.php');
	?>

</body>
</html>
