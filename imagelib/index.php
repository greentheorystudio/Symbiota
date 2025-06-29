<?php 
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ImageLibraryManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$taxon = array_key_exists('taxon',$_REQUEST)?trim($_REQUEST['taxon']): '';
$target = array_key_exists('target',$_REQUEST)?trim($_REQUEST['target']): '';

$imgLibManager = new ImageLibraryManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Image Library</title>
    <meta name="description" content="Image library for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<meta name='keywords' content='' />
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
	?>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<h1>Species with Images</h1>
		<div style="margin:0 0 5px 20px;">This page provides a complete list to taxa that have images.
		Use the controls below to browse and search for images by family, genus, or species. 
		</div>
		<div style="float:left;margin:10px 0 10px 30px;">
			<div style=''>
				<a href='index.php?target=family'>Browse by Family</a>
			</div>
			<div style='margin-top:10px;'>
				<a href='index.php?target=genus'>Browse by Genus</a>
			</div>
			<div style='margin-top:10px;'>
				<a href='index.php?target=species'>Browse by Species</a>
			</div>
			<div style='margin:2px 0 0 10px;'>
				<div><a href='index.php?taxon=A'>A</a>|<a href='index.php?taxon=B'>B</a>|<a href='index.php?taxon=C'>C</a>|<a href='index.php?taxon=D'>D</a>|<a href='index.php?taxon=E'>E</a>|<a href='index.php?taxon=F'>F</a>|<a href='index.php?taxon=G'>G</a>|<a href='index.php?taxon=H'>H</a></div>
				<div><a href='index.php?taxon=I'>I</a>|<a href='index.php?taxon=J'>J</a>|<a href='index.php?taxon=K'>K</a>|<a href='index.php?taxon=L'>L</a>|<a href='index.php?taxon=M'>M</a>|<a href='index.php?taxon=N'>N</a>|<a href='index.php?taxon=O'>O</a>|<a href='index.php?taxon=P'>P</a>|<a href='index.php?taxon=Q'>Q</a></div>
				<div><a href='index.php?taxon=R'>R</a>|<a href='index.php?taxon=S'>S</a>|<a href='index.php?taxon=T'>T</a>|<a href='index.php?taxon=U'>U</a>|<a href='index.php?taxon=V'>V</a>|<a href='index.php?taxon=W'>W</a>|<a href='index.php?taxon=X'>X</a>|<a href='index.php?taxon=Y'>Y</a>|<a href='index.php?taxon=Z'>Z</a></div>
			</div>
		</div>
		<div style="float:right;width:250px;">
			<div style="margin:10px 0 0 0;">
				<form name="searchform1" action="index.php" method="post">
					<fieldset style="background-color:#FFFFCC;padding:10px;">
						<legend style="font-weight:bold;">Scientific Name Search</legend>
						<input type="text" name="taxon" value="<?php echo $taxon; ?>" title="Enter family, genus, or scientific name" />
						<input name="submit" value="Search" type="submit">
					</fieldset>
				</form>
			</div>
			<div style="font-weight:bold;margin:15px 10px 0 20px;">
				<div>
					<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/usagepolicy.php">Image Copyright Policy</a>
				</div>
				<div>
					<a href="contributors.php">Image Contributors</a>
				</div>
				<div>
					<a href="search.php">Image Search</a>
				</div>
			</div>
		</div>
		<div style='clear:both;'><hr/></div>
		<?php
			$taxaList = array();
			if($target === 'genus'){
				echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a Genus to see species list.</div>";
				$taxaList = $imgLibManager->getGenusList();
				foreach($taxaList as $value){
					echo "<div style='margin-left:30px;'><a href='index.php?taxon=".$value."'>".$value. '</a></div>';
				}
			}
			elseif($target === 'species'){
				echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a species to access available images.</div>";
				$taxaList = $imgLibManager->getSpeciesList();
				foreach($taxaList as $key => $value){
					echo "<div style='margin-left:30px;font-style:italic;'>";
					echo "<a href='../taxa/index.php?taxon=".$key."' target='_blank'>".$value. '</a>';
					echo '</div>';
				}
			}
			elseif($taxon){
				echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a species to access available images.</div>";
				$taxaList = $imgLibManager->getSpeciesList($taxon);
				foreach($taxaList as $key => $value){
					echo "<div style='margin-left:30px;font-style:italic;'>";
					echo "<a href='../taxa/index.php?taxon=".$key."' target='_blank'>".$value. '</a>';
					echo '</div>';
				}
			}
			else{
				echo "<div style='margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;'>Select a family to see species list.</div>";
				$taxaList = $imgLibManager->getFamilyList();
				foreach($taxaList as $value){
					echo "<div style='margin-left:30px;'><a href='index.php?taxon=".$value."'>".strtoupper($value). '</a></div>';
				}
			}
	?>
	</div>
	<?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
