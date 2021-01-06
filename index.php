<?php
include_once(__DIR__ . '/config/symbini.php');
include_once(__DIR__ . '/classes/TaxonQuickSearchManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$taxon = array_key_exists("taxon",$_REQUEST)?trim($_REQUEST["taxon"]):"";

$myvars=array("taxon");
foreach ($myvars as $var)
{
if (isset($_GET[$var])) {$$var=$_GET[$var];}
}

$imgLibManager = new TaxonQuickSearchManager();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<meta name='keywords' content='' />
    <script type="text/javascript">
        <?php include_once('config/googleanalytics.php'); ?>
    </script>
</head>
<body>
	<?php
    include(__DIR__ . '/header.php');
	?>
        <div  id="innertext">
            <h1>Welcome to the Online Virtual Flora of Wisconsin</h1>
			<div style="float:right;width:380px;">
				<?php
				$oodID = 1;
				$ootdGameChecklist = 19;
				$ootdGameTitle = "Plant of the Day ";
				$ootdGameType = "plant";
				include_once(__DIR__ . '/classes/GamesManager.php');
				$gameManager = new GamesManager();
				$gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
				?>
				<div style="float:right;margin-top:30px;margin-right:30px;margin-bottom:15px;width:250px;text-align:center;">
					<div style="font-size:130%;font-weight:bold;">
						<?php echo $ootdGameTitle; ?>
					</div>
					<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
						<img src="<?php echo $CLIENT_ROOT; ?>/temp/ootd/<?php echo $oodID; ?>_organism300_1.jpg" style="width:250px;border:0px;" />
					</a><br/>
					<b>What is this <?php echo $ootdGameType; ?>?</b><br/>
					<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
						Click here to test your knowledge
					</a>
				</div>
			</div>

            <div style="margin: 20px; text-align: left; font-size: 14px;">
              <p>This site is a collaborative effort between the herbaria of the UW-Madison (WIS) and the UW-Steven's Point (UWSP), along with most of the other herbaria located in the state of Wisconsin. It contains information on each of the more than 2600 vascular plant species that occurs in Wisconsin, including photos, distribution maps, specimen records, and more.</p>
            <form name='searchform1' action='index.php' method='post'>
				<fieldset style="width:375px;">
					<legend><b>Quick Search</b></legend>
					<div style="clear:both;">
						<input type='text' name='taxon' style="width:300px;" title='Enter family, genus, or scientific name'>
						<input name='submit' value='Search' type='submit'>
					</div>
				</fieldset>
			</form>
			<?php
				$taxaList = Array();
				if($taxon){
					echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a species to access available images.</div>";
					$taxaList = $imgLibManager->getSpeciesListWVernaculars($taxon);
					if($taxaList){
						foreach($taxaList as $key => $value){
							echo "<div style='margin-left:30px;font-style:italic;'>";
							echo "<a href='../taxa/index.php?taxon=".$key."' target='_blank'>".$value."</a>";
							echo "</div>";
						}
						echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'></div>";
					}
				}
			?>
			  <ul>
				<li><b>Enter a genus, species, or common name to view the species description pages.</b></li>
				<li>View detailed species descriptions, photos, interactive maps, and links to specimen records and additional information.</li></ul>
              <p><strong>Advanced Searches</strong>              </p>
              <ul>
                <li>See <strong>Advanced Searches</strong> tab above to <strong>Search for Specimen Records</strong> and to <strong>Browse the Image Library</strong>. <br>
                  </li>
                <li>Search, view, and download nearly 400,000 in-state herbarium specimen records and thousands of images.<br>
                  </li>
              </ul>
              <p><strong>Checklists</strong> (e.g., County Floras, Wildflowers by Color) are under development.  Take a look or create your own!<br>
              </p>
              <p>&nbsp;</p>
              <p><em>NOTE: 'Interactive Maps' will plot only collections with known GPS localities.</em></p>
              <p><em>NOTE: This site was constructed using the </em><a href="http://symbiota.org/docs/" target="_blank">Symbiota</a><em> software platform and was launched in Feb 2015; it is still in development.
                We encourage your comments for improvement, and  appreciate your patience. Thank you.</em></p>
            </div>
        </div>

        <?php
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
