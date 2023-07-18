<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$obsUid = array_key_exists('obsuid',$_REQUEST)?(int)$_REQUEST['obsuid']:0;
$queryCountry = array_key_exists('q_country',$_REQUEST)?$_REQUEST['q_country']:'';
$ranking = array_key_exists('ranking',$_REQUEST)?(int)$_REQUEST['ranking']:0;
$action = array_key_exists('action',$_POST)?htmlspecialchars($_POST['action']):'';

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

if($action && !preg_match('/^[a-zA-Z\s]+$/',$action)) {
    $action = '';
}

$cleanManager = new OccurrenceCleaner();
if($collid) {
    $cleanManager->setCollId($collid);
}
$collMap = $cleanManager->getCollMap();

$statusStr = '';
$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'HumanObservation')
	|| (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($collMap['colltype'] === 'HumanObservation' && $obsUid !== 0){
	$obsUid = $GLOBALS['SYMB_UID'];
	$cleanManager->setObsUid($obsUid);
}

?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Coordinate Validator</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
	</script>
	<style>
		table.styledtable {  width: 300px }
		table.styledtable td { white-space: nowrap; }
	</style>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Data Cleaning Tools</a> &gt;&gt;
		<b>Coordinate Political Units Validator</b>
	</div>

	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (strncmp($statusStr, 'ERROR', 5) === 0 ?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
        echo '<h2>'.$collMap['collectionname'].($collMap['code']?' ('.$collMap['code'].')':'').'</h2>';
		if($isEditor){
			?>
			<div style="margin:15px">
				This tool will loop through all unvalidated georeferenced occurrences and verify that the coordinates actually fall within the defined political units.
			</div>
			<div style="margin:15px">
				<?php
				if($action){
					echo '<fieldset>';
					echo '<legend><b>Action Panel</b></legend>';
					if($action === 'Validate Coordinates'){
						$cleanManager->verifyCoordAgainstPolitical($queryCountry);
					}
					echo '</fieldset>';
				}
				?>
			</div>
			<div style="margin:10px">
				<div style="font-weight:bold">Non-verified by State/Province</div>
				<?php
				$countryArr = $cleanManager->getUnverifiedByCountry();
				echo '<table class="styledtable">';
				echo '<tr><th>Country</th><th>Count</th><th>Action</th></tr>';
				foreach($countryArr as $country => $cnt){
					echo '<tr>';
					echo '<td>'.$country.'</td>';
					echo '<td>'.$cnt.'</td>';
					echo '<td>';
					?>
					<form action="coordinatevalidator.php" method="post" style="margin:10px">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="obsuid" type="hidden" value="<?php echo $obsUid; ?>" />
						<input name="q_country" type="hidden" value="<?php echo $country; ?>" />
						<input name="action" type="submit" value="Validate Coordinates" />
					</form>
					<?php
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
				?>
			</div>
			<div style="margin:10px">
				<fieldset style="width:400px;padding:20px">
					<legend><b>Rank Listing</b></legend>
					<div>
						<form action="coordinatevalidator.php" method="post">
							Select Rank:
							<select name="ranking" onchange="this.form.submit()">
								<option value="">Select Rank</option>
								<option value="">----------------</option>
								<?php
								$rankList = $cleanManager->getRankList();
								foreach($rankList as $rankId){
									echo '<option value="'.$rankId.'" '.($ranking === (int)$rankId?'SELECTED':'').'>'.$rankId.'</option>';
								}
								?>
							</select>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="action" type="hidden" value="displayranklist" />
						</form>
					</div>
					<div>
						<?php
						$occurList = array();
						if($action === 'displayranklist'){
							$occurList = $cleanManager->getOccurrenceRankingArr('coordinate', $ranking);
						}
						if($occurList){
							foreach($occurList as $occid => $inArr){
								echo '<div>';
								echo '<a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.$occid.'</a>';
								echo '- checked by '.$inArr['username'].' on '.$inArr['ts'];
								echo '</div>';
							}
						}
						else{
							echo '<div style="margin:30px;font-weight:bold;">Nothing to be displayed</div>';
						}
						?>
					</div>
				</fieldset>
			</div>
 			<?php
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
	<?php
	include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
	?>
</body>
</html>
