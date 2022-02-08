<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="en">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']?> Home</title>
	<link href="css/base.css?v=201502" type="text/css" rel="stylesheet" />
	<link href="css/main.css?v=201502" type="text/css" rel="stylesheet" />
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
            <div style="float:right;width:380px;">
				<div style="clear:both;float:right;width:320px;margin-top:8px;margin-right:8px;padding:5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:1px solid black;" >
					<div style="float:left;width:350px;">
						<?php
						$searchText = 'Taxon Search';
						$buttonText = 'Search';
						include_once(__DIR__ . '/classes/PluginsManager.php');
						$pluginManager = new PluginsManager();
						$quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
						echo $quicksearch;
						?>
					</div>
				</div>
				<?php
				$oodID = 1; 
				$ootdGameChecklist = 100;
				$ootdGameTitle = 'Plant of the Day ';
				$ootdGameType = 'plant';
				
				include_once(__DIR__ . '/classes/GamesManager.php');
				$gameManager = new GamesManager();
				$gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
				?>
				<div style="float:right;margin-top:30px;margin-right:10px;margin-bottom:15px;width:350px;text-align:center;">
					<div style="font-size:130%;font-weight:bold;">
						<?php echo $ootdGameTitle; ?>
					</div>
					<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
						<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/temp/ootd/<?php echo $oodID; ?>_organism300_1.jpg" style="width:350px;border:0;" />
					</a><br/>
					<b>What is this <?php echo $ootdGameType; ?>?</b><br/>
					<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
						Click here to test your knowledge
					</a>
				</div>
			</div>
			<h1>Welcome to the Public Lands Flora</h1>
			<div style="padding: 0 15px;">
				The Public Lands Flora is a floristic information system designed to manage the floristic biodiversity data
				for plants found within the boundaries of publicly held lands in the United States. This site provides access
				to data from herbaria and other natural history collections all across the United States. It is designed to
				connect with other Symbiota-based portals to form a multi-phyla portal capable of managing biodiversity data.
            </div>
			<div style="margin-top:15px;padding: 0 10px;">
				The Public Lands Flora draws on millions of publicly held natural history collection records, including small
				agency-held collections, museums of all sizes, small herbaria, and enormous national and regional collections.
				It links a trove of data into a single, searchable system that manages checklists for protected areas and
				provides links to associated voucher specimen data.
			</div>
			<div style="margin-top:15px;padding: 0 10px;">
				This portal is one node of a proposed biodiversity informatics system linking natural history collections and
				observations into a single multi-phyla information system based on the Symbiota software architecture. The system
				draws on the work of over $15 million dollars in previous investment in biodiversity informatics programming,
				databasing, and imaging that has come from a range of public and private sources that includes the National
				Science Foundation.
			</div>
		</div>

	<?php
	include(__DIR__ . '/footer.php');
	?>
</body>
</html>
