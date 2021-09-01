<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$cleanManager = new OccurrenceCleaner();
if($collid) {
    $cleanManager->setCollId($collid);
}
$collMap = $cleanManager->getCollMap();

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'General Observations') || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($collMap['colltype'] === 'General Observations'){
	$cleanManager->setObsUid($GLOBALS['SYMB_UID']);
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Cleaner</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<style type="text/css">
		table.styledtable {  width: 300px }
		table.styledtable td { white-space: nowrap; }
		h3 { text-decoration:underline }
	</style>
    <script src="../../js/all.min.js" type="text/javascript"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<b>Data Cleaning Tools</b>
	</div>

	<div id="innertext" style="background-color:white;">
		<?php
		if($isEditor){
			echo '<h2>'.$collMap['collectionname'].' ('.$collMap['code'].')</h2>';
			?>
			<div style="color:red;margin:20px 0;font-weight:bold;">It is strongly recommended to download a backup of your
                collection data before using the Duplicate Merging, Geography Cleaning, or Taxonomic Name Resolution modules.
            </div>
			<?php
			if($collMap['colltype'] !== 'General Observations'){
				?>
				<h3>Duplicate Records</h3>
				<div style="margin:0 0 40px 15px;">
					<div>
						This section is meant to assist in searching this collection for duplicate records of the same occurrence.
						If duplicate records exist, this feature offers the ability to merge record values, images,
						and data relationships into a single record. Click on either of the links in the box below to open the Duplicate Merging Module and
                        view a list of potential duplicate records based on either the catalog number or other catalog number
                        fields.
					</div>
					<fieldset style="margin:10px 0;padding:5px;width:450px">
						<ul>
							<li>
								<a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupscatalog">
									Catalog Numbers
								</a>
							</li>
							<li>
								<a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupsothercatalog">
									Other Catalog Numbers
								</a>
							</li>
						</ul>
					</fieldset>
				</div>
				<?php
			}
			?>

			<h3>Political Geography</h3>
			<div style="margin:0 0 40px 15px;">
				<div>
                    This section is meant to help standardize country, state/province, and county designations.
					It is also useful for locating and correcting misspelled geographical political units,
					and even mismatched units, such as a state designation that does not match the wrong country. Use the
                    links in the box below to view the current geographic distributions or open the Geography Cleaning Module
                    to correct or standardize geographic names.
				</div>
				<fieldset style="margin:10px 0;padding:5px;width:450px">
					<ul>
						<li>
							<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&stat=geography#geographystats" target="_blank">View geographic distributions</a>
						</li>
						<li>
							<a href="politicalunits.php?collid=<?php echo $collid; ?>">Open Geography Cleaning Module</a>
						</li>
					</ul>
				</fieldset>
			</div>

			<h3>Specimen Coordinates</h3>
			<div style="margin:0 0 40px 15px;">
				<div>
                    This section is meant to aid collection managers in verifying, ranking, and managing coordinate information associated with occurrence records.
				</div>
				<fieldset style="margin:10px 0;padding:5px;width:450px">
					<legend style="font-weight:bold">Statistics and Action Panel</legend>
					<ul>
						<?php
						$statsArr = $cleanManager->getCoordStats();
						?>
						<li>Georeferenced: <?php echo $statsArr['coord']; ?>
							<?php
							if($statsArr['coord']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
									<i style="height:15px;width:15px;" class="far fa-edit"></i>
								</a>
								<?php
							}
							?>
						</li>
						<li>Lacking coordinates: <?php echo $statsArr['noCoord']; ?>
							<?php
							if($statsArr['noCoord']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i>
								</a>
								<a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>" style="margin-left:5px;" title="Open Batch Georeference Tool" target="_blank">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i><span style="font-size:70%;margin-left:-3px;">b-geo</span>
								</a>
								<?php
							}
							?>
						</li>
						<li style="margin-left:15px">Lacking coordinates with verbatim coordinates: <?php echo $statsArr['noCoord_verbatim']; ?>
							<?php
							if($statsArr['noCoord_verbatim']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i>
								</a>
								<?php
							}
							?>
						</li>
						<li style="margin-left:15px">Lacking coordinates without verbatim coordinates: <?php echo $statsArr['noCoord_noVerbatim']; ?>
							<?php
							if($statsArr['noCoord_noVerbatim']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i>
								</a>
								<?php
							}
							?>
						</li>
						<li>
							<a href="coordinatevalidator.php?collid=<?php echo $collid; ?>">Check coordinates against political boundaries</a>
						</li>
					</ul>
				</fieldset>
				<div style="margin:10px 0;">
					<div style="font-weight:bold">Ranking Statistics</div>
					<?php
					$coordRankingArr = $cleanManager->getRankingStats('coordinate');
					$rankArr = $coordRankingArr['coordinate'];
					echo '<table class="styledtable">';
					echo '<tr><th>Ranking</th><th>Protocol</th><th>Count</th></tr>';
					foreach($rankArr as $rank => $protocolArr){
						foreach($protocolArr as $protocol => $cnt){
							echo '<tr>';
							echo '<td>'.$rank.'</td>';
							echo '<td>'.$protocol.'</td>';
							echo '<td>';
							echo '<a href="coordinatevalidator.php?collid='.$collid.'&ranking='.($rank === 'unranked'?'':$rank).'">';
							echo $cnt;
							echo '</a>';
							echo '</td>';
							echo '</tr>';
						}
					}
					echo '</table>';
					?>
				</div>
			</div>

			<h3>Taxonomy</h3>
			<div style="margin:0 0 40px 15px;">
				<div>
                    This section is meant to aid in locating and fixing taxonomic inconsistencies, and reconciling valid taxonomic
                    names within the occurrence records of this collection with the taxonomic thesaurus of this portal. Use the links
                    in the box below to view the current taxonomic distributions or open the Taxonomic Name Resolution Module to resolve
                    taxonomic names within the occurrence records for this collection that are not currently associated with
                    the taxonomic thesaurus of this portal.
				</div>
				<fieldset style="margin:10px 0;padding:5px;width:450px">
					<ul>
                        <li><a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&stat=taxonomy#taxonomystats">View taxonomic distributions</a></li>
                        <li><a href="taxonomycleaner.php?collid=<?php echo $collid; ?>">Open Taxonomic Name Resolution Module</a></li>
						<?php
						if($cleanManager->hasDuplicateClusters()){
							echo '<li><a href="../datasets/duplicatemanager.php?collid='.$collid.'&dupedepth=3&action=listdupeconflicts">';
							echo 'View duplicate occurrences with potential identification conflicts...';
							echo '</a></li>';
						}
						?>
					</ul>
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
	?>
</body>
</html>
